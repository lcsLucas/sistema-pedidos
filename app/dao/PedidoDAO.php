<?php

namespace App\dao;

use App\model\Banco;
use App\model\Retorno;

if (! defined('ABSPATH')){
    header("Location: /");
    exit();
}

class PedidoDAO extends Banco
{
	public function fazerPedido($form, $obs)
	{
        if(!empty($this->Conectar())) :
            try
            {
            	$sql = "INSERT INTO pedido(ped_datahora, ped_obs, cli_codigo, usu_codigo, form_codigo, ped_status) VALUES(?,?,?,?,?,?)";
            	$this->beginTransaction();
            	$stms = $this->getCon()->prepare($sql);
            	$stms->bindValue(1, date('Y-m-d H:i:s'));
            	$stms->bindValue(2, $obs, \PDO::PARAM_STR);
            	$stms->bindValue(3, $_SESSION['clienteAtual'], \PDO::PARAM_STR);
            	$stms->bindValue(4, (int) $_SESSION['UsuarioCodigo'], \PDO::PARAM_INT);
            	$stms->bindValue(5, $form, \PDO::PARAM_INT);
            	$stms->bindValue(6, '0');

            	if (!empty($stms->execute())) :
            		$id_inserido = $this->getLastInsertId();
            		if (!empty($id_inserido)) {
            			foreach ($_SESSION['produtos'] as $codigoProduto => $qtde) {
            				$prodDAO = new ProdutoDAO();
            				$result = $prodDAO->obterProduto($codigoProduto, $_SESSION['clienteAtual']);
				            $preco = 0.0;
				            if (!empty($result['cli_pro_preco'])) :
				                $preco = $result['cli_pro_preco'];
				            else :
				                $preco = $result['prod_preco'];
				            endif;
	            			$sql = "INSERT INTO itens_pedido(ped_codigo, prod_codigo, item_qtde, item_preco) VALUES(?,?,?,?)";
	            			$stms = $this->getCon()->prepare($sql);
	            			$stms->bindValue(1, (int) $id_inserido, \PDO::PARAM_INT);
	            			$stms->bindValue(2, $codigoProduto, \PDO::PARAM_STR);
	            			$stms->bindValue(3, $qtde, \PDO::PARAM_INT);
	            			$stms->bindValue(4, $preco);

	            			$stms->execute();
            			}

            			$this->commit();
            			return $id_inserido;
            		}
            	endif;
            }
            catch(\PDOException $e)
            {
                $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
            }
        endif;
        $this->rollBack();
        return null;
	}

	public function alterarStatus($numPedido, $status)
	{
		if (!empty($this->Conectar())) :
			try {
				$stms = $this->getCon()->prepare("UPDATE pedido SET ped_status = ? WHERE ped_codigo = ?");
				$stms->bindValue(1, $status);
				$stms->bindValue(2, $numPedido);

				return $stms->execute();
			} catch (\PDOException $e) {
				$this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
			}
		endif;
		return null;
	}

	public function obterPedido($codPedido)
	{
		if (!empty($this->Conectar())) :
			try {
				$sql = "SELECT cli_codigo, form_codigo, ped_datahora, ped_obs, usu_codigo FROM pedido WHERE ped_codigo = ? LIMIT 1";
				$stms = $this->getCon()->prepare($sql);
				$stms->bindValue(1, $codPedido);

				$stms->execute();
				return $stms->fetch();
			} catch (\PDOException $e) {
				$this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
			}
		endif;
		return null;
	}

	public function obterItensPedido($pedCodigo)
	{
            if (!empty($this->Conectar())) :
                try {
                        $stms = $this->getCon()->prepare("SELECT prod_codigo, item_qtde, item_preco FROM itens_pedido WHERE ped_codigo = ?");
                        $stms->bindValue(1, $pedCodigo);

                        $stms->execute();
                        return $stms->fetchAll();
                } catch (\PDOException $e) {
                        $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
                }
            endif;
            return null;
	}
        
	public function obterItensPedido2($pedCodigo)
	{
            if (!empty($this->Conectar())) :
                try {
                        $stms = $this->getCon()->prepare("SELECT prod_codigo, item_qtde FROM itens_pedido WHERE ped_codigo = ?");
                        $stms->bindValue(1, $pedCodigo);

                        $stms->execute();
                        return $stms->fetchAll();
                } catch (\PDOException $e) {
                        $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
                }
            endif;
            return null;
	}

	public function verificaDocumentosPedido($codigoPedido)
	{
		if (!empty($this->Conectar())) :
			try {
				$stms = $this->getCon()->prepare("SELECT doc_nome FROM documento WHERE ped_codigo = ?");
				$stms->bindValue(1, $codigoPedido);

				$stms->execute();
				return $stms->fetchAll(\PDO::FETCH_COLUMN);
			} catch (\PDOException $e) {
				$this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
			}
		endif;
		return null;
	}

	public function gravarDocumentoPedido($codigoPedido, $arquivo, $tipo)
	{
		if (!empty($this->Conectar())) :
			try {
				//uma condição que chama "verificarDocumentosPedido", e apagar se houver algum documento registrado
				$stms = $this->getCon()->prepare("INSERT INTO documento(ped_codigo, doc_nome, doc_tipo) VALUES(:ped, :nome, :tipo)");
				$stms->bindValue(":ped", $codigoPedido);
				$stms->bindValue(":nome", $arquivo);
				$stms->bindValue(":tipo", $tipo);

				return $stms->execute();
			} catch (\PDOException $e) {
				$this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
			}
		endif;
		return null;
	}

	public function obterDocumentosPedido($codigoPedido)
	{
		if (!empty($this->Conectar())) :
			try {
				$stms = $this->getCon()->prepare("SELECT doc_nome arquivo, doc_tipo tipo FROM documento WHERE ped_codigo = :ped ORDER BY doc_nome DESC");
				$stms->bindValue(":ped", (int)$codigoPedido);

				$stms->execute();
				return $stms->fetchAll();
			} catch (\PDOException $e) {
				$this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
			}
		endif;
		return null;
	}

	public function excluirPedido($codigoPed)
    {
        if (!empty($this->Conectar())) :
            try {
                $stms = $this->getCon()->prepare("DELETE FROM pedido WHERE ped_codigo = ?");
                $stms->bindValue(1, $codigoPed);

                return $stms->execute();
            } catch (\PDOException $e) {
                    $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
            }
        endif;
        return null;
}

public function obterPedidoExibir($codPedido)
{
            if (!empty($this->Conectar())) :
                    try {
                        if (!empty($_SESSION["UsuarioStatus"])) :
                            $sql = "SELECT ped.ped_codigo, ped_datahora, cli_nome, form_descricao, usu_nome, sum(item_preco * item_qtde) Total 
                                            FROM pedido ped INNER JOIN cliente cli ON ped.cli_codigo = cli.cli_codigo
                                                    LEFT JOIN form_pagto form ON ped.form_codigo = form.form_codigo
                                                            INNER JOIN usuario usu ON ped.usu_codigo = usu.usu_codigo
                                                                    INNER JOIN itens_pedido itens ON ped.ped_codigo = itens.ped_codigo
                                                                    WHERE ped.ped_codigo = :pedido
                                                    GROUP BY ped.ped_codigo, ped_datahora, cli_nome, form_descricao, usu_nome";
                        else :
                            $sql = "SELECT ped.ped_codigo, ped_datahora, cli_nome, form_descricao, usu_nome, sum(item_preco * item_qtde) Total 
                                            FROM pedido ped INNER JOIN cliente cli ON ped.cli_codigo = cli.cli_codigo
                                                    LEFT JOIN form_pagto form ON ped.form_codigo = form.form_codigo
                                                            INNER JOIN usuario usu ON ped.usu_codigo = usu.usu_codigo
                                                                    INNER JOIN itens_pedido itens ON ped.ped_codigo = itens.ped_codigo
                                                                    WHERE ped.ped_codigo = :pedido AND usu_codigo = :usuario
                                                    GROUP BY ped.ped_codigo, ped_datahora, cli_nome, form_descricao, usu_nome";
                        endif;

                            $stms = $this->getCon()->prepare($sql);
                            $stms->bindValue(":pedido", $codPedido, \PDO::PARAM_INT);
                            if (empty($_SESSION["UsuarioStatus"]))
                                $stms->bindValue(":usuario", $_SESSION["UsuarioCodigo"], \PDO::PARAM_INT);

                            $stms->execute();
                            return $stms->fetch();
                    } catch (\PDOException $e) {
                            $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
                    }
            endif;
            return null;
    }
    
    public function obterPedidoExibir2($codPedido)
    {
		if (!empty($this->Conectar())) :
			try {
                            if (!empty($_SESSION["UsuarioStatus"])) :
                                $sql = "SELECT ped.ped_codigo, ped_datahora, cli_nome, usu_nome
                                                FROM pedido ped INNER JOIN cliente cli ON ped.cli_codigo = cli.cli_codigo						
                                                    INNER JOIN usuario usu ON ped.usu_codigo = usu.usu_codigo
                                                            WHERE ped.ped_codigo = :pedido";
                            else :
                                $sql = "SELECT ped.ped_codigo, ped_datahora, cli_nome, usu_nome
                                                FROM pedido ped INNER JOIN cliente cli ON ped.cli_codigo = cli.cli_codigo						
                                                    INNER JOIN usuario usu ON ped.usu_codigo = usu.usu_codigo
                                                            WHERE ped.ped_codigo = :pedido AND usu_codigo = :usuario";
                            endif;

	    		$stms = $this->getCon()->prepare($sql);
				$stms->bindValue(":pedido", $codPedido);
                                if (empty($_SESSION["UsuarioStatus"]))
                                    $stms->bindValue(":usuario", $_SESSION["UsuarioCodigo"], \PDO::PARAM_INT);

				$stms->execute();
				return $stms->fetch();
			} catch (\PDOException $e) {
				$this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
			}
		endif;
		return null;
    }
    
    public function obterItensPedidoExibir($pedCodigo)
    {
            if (!empty($this->Conectar())) :
                    try {
                            $sql = "SELECT produto.prod_codigo, prod_descricao, item_qtde, item_preco, SUM(item_qtde * item_preco) item_total FROM itens_pedido
                                    INNER JOIN produto ON itens_pedido.prod_codigo = produto.prod_codigo
                                    WHERE ped_codigo = ?
                                            GROUP BY produto.prod_codigo, prod_descricao, item_qtde, item_preco";
                            $stms = $this->getCon()->prepare($sql);
                            $stms->bindValue(1, $pedCodigo);

                            $stms->execute();
                            return $stms->fetchAll();
                    } catch (\PDOException $e) {
                            $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
                    }
            endif;
            return null;
    }
    
    public function obterItensPedidoExibir2($pedCodigo)
    {
            if (!empty($this->Conectar())) :
                    try {
                            $sql = "SELECT produto.prod_codigo, prod_descricao, item_qtde FROM itens_pedido
                                    INNER JOIN produto ON itens_pedido.prod_codigo = produto.prod_codigo
                                    WHERE ped_codigo = ?";
                            $stms = $this->getCon()->prepare($sql);
                            $stms->bindValue(1, $pedCodigo);

                            $stms->execute();
                            return $stms->fetchAll();
                    } catch (\PDOException $e) {
                            $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
                    }
            endif;
            return null;
    }

    public function obterTodosLimite($pagina, $itens, $statusUsu)
    {
      settype($pagina, "Integer");
      settype($itens, "Integer");

      if(!empty($this->Conectar())) :
          try
          {
          	$sql = "";
          	if (!empty($statusUsu)) :
	          	$sql = "SELECT pedido.ped_codigo, ped_datahora, ped_status, cli_nome,sum(item_preco*item_qtde) Total FROM pedido
					INNER JOIN itens_pedido ON pedido.ped_codigo = itens_pedido.ped_codigo
					INNER JOIN cliente ON pedido.cli_codigo =cliente.cli_codigo
					GROUP BY pedido.ped_codigo, ped_datahora, ped_status, cli_nome ORDER BY ped_datahora DESC LIMIT :pagina, :itens;";
          	else :
	          	$sql = "SELECT pedido.ped_codigo, ped_datahora, ped_status, cli_nome,sum(item_preco*item_qtde) Total FROM pedido
					INNER JOIN itens_pedido ON pedido.ped_codigo = itens_pedido.ped_codigo
					INNER JOIN cliente ON pedido.cli_codigo =cliente.cli_codigo
					WHERE pedido.usu_codigo = :usuario
					GROUP BY pedido.ped_codigo, ped_datahora, ped_status, cli_nome ORDER BY ped_datahora DESC LIMIT :pagina, :itens;";
          	endif;

            $stms = $this->getCon()->prepare($sql);
            $stms->bindValue(":pagina", $pagina, \PDO::PARAM_INT);
            $stms->bindValue(":itens", $itens, \PDO::PARAM_INT);
			if (empty($statusUsu))
            	$stms->bindValue(":usuario", (int) $_SESSION["UsuarioCodigo"], \PDO::PARAM_INT);
            $stms->execute();
            return $stms->fetchAll();
          }
          catch(\PDOException $e)
          {
            $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
          }
      endif;
      return null;
    }
    
    public function obterTodosLimite2($pagina, $itens, $statusUsu)
    {
      settype($pagina, "Integer");
      settype($itens, "Integer");

      if(!empty($this->Conectar())) :
          try
          {
          	$sql = "";
          	if (!empty($statusUsu)) :
	          	$sql = "SELECT pedido.ped_codigo, ped_datahora, ped_status, cli_nome FROM pedido
					INNER JOIN cliente ON pedido.cli_codigo =cliente.cli_codigo
					ORDER BY ped_datahora DESC LIMIT :pagina, :itens;";
          	else :
	          	$sql = "SELECT pedido.ped_codigo, ped_datahora, ped_status FROM pedido
					INNER JOIN cliente ON pedido.cli_codigo =cliente.cli_codigo
					WHERE pedido.usu_codigo = :usuario
					ORDER BY ped_datahora DESC LIMIT :pagina, :itens;";
          	endif;

            $stms = $this->getCon()->prepare($sql);
            $stms->bindValue(":pagina", $pagina, \PDO::PARAM_INT);
            $stms->bindValue(":itens", $itens, \PDO::PARAM_INT);
			if (empty($statusUsu))
            	$stms->bindValue(":usuario", (int) $_SESSION["UsuarioCodigo"], \PDO::PARAM_INT);
            $stms->execute();
            return $stms->fetchAll();
          }
          catch(\PDOException $e)
          {
            $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
          }
      endif;
      return null;
    }
}