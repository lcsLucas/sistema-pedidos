<?php

namespace App\dao;

use App\model\Banco;
use App\model\Retorno;

if (! defined('ABSPATH')){
    header("Location: /");
    exit();
}

class ProdutoDAO extends Banco
{

    public function obterTodos()
    {
        if(!empty($this->Conectar())) :
            try
            {
                $stms = $this->getCon()->prepare("SELECT * FROM produto ORDER BY prod_descricao");
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

    public function obterPorCliente($codigoCliente)
    {
        if(!empty($this->Conectar())) :
            try
            {
                $sql = "SELECT prod_codigo, cli_pro_preco FROM cliente_produto
                            WHERE cli_codigo = :cli";

                $stms = $this->getCon()->prepare($sql);
                $stms->bindValue(":cli", $codigoCliente, \PDO::PARAM_STR);
                $stms->execute();
                return $stms->fetchAll(\PDO::FETCH_CLASS);
            }
            catch(\PDOException $e)
            {
                $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
            }
        endif;
        return null;
    }

    public function obterLimite($pagina, $itens)
    {
      settype($pagina, "Integer");
      settype($itens, "Integer");

      if(!empty($this->Conectar())) :
          try
          {
            $stms = $this->getCon()->prepare("SELECT * FROM produto ORDER BY prod_descricao LIMIT :pagina, :itens");
            $stms->bindValue(":pagina", $pagina, \PDO::PARAM_INT);
            $stms->bindValue(":itens", $itens, \PDO::PARAM_INT);
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

    public function alterar($codigo, $preco)
    {
        if(!empty($this->Conectar())) :
            try
            {
                $stms = $this->getCon()->prepare("UPDATE produto SET prod_preco = :preco WHERE prod_codigo = :codigo");
                $stms->bindValue(":preco", $preco);
                $stms->bindValue(":codigo", $codigo, \PDO::PARAM_STR);

                return $stms->execute();
            }
            catch(\PDOException $e)
            {
              $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
            }
        endif;

        return false;
    }

    public function countTotal()
    {
      if(!empty($this->Conectar())) :
        try
        {
            $stms = $this->getCon()->prepare("SELECT count(*) Total FROM produto");
            $stms->execute();
            $result = $stms->fetch();
            return $result["Total"];
          }
          catch(\PDOException $e)
          {
            $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
          }
      endif;
      return null;
    }

    public function verificaProduto($codigo)
    {
        if(!empty($this->Conectar())) :
            try
            {
                $sql = "SELECT COUNT(*) Total FROM produto
                            WHERE prod_codigo = :codigo";

                $stms = $this->getCon()->prepare($sql);
                $stms->bindValue(":codigo", $codigo, \PDO::PARAM_STR);
                $stms->execute();

                $result = $stms->fetch();
                return $result["Total"];
            }
            catch(\PDOException $e)
            {
                $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
            }
        endif;
        return null;
    }

    public function obterProduto($codigoProd, $codigoCliente)
    {
        if(!empty($this->Conectar())) :
            try
            {
                $sql = "select pro.prod_codigo, prod_descricao, prod_preco, cli_pro_preco
                    from produto pro left join (select prod_codigo, cli_pro_preco
                        from cliente_produto where prod_codigo = :produto and cli_codigo = :cliente)
                            clipro    on pro.prod_codigo = clipro.prod_codigo
                                where pro.prod_codigo = :produto";

                $stms = $this->getCon()->prepare($sql);
                $stms->bindValue(":produto", $codigoProd, \PDO::PARAM_STR);
                $stms->bindValue(":cliente", $codigoCliente, \PDO::PARAM_STR);
                $stms->execute();
                return $stms->fetch();
            }
            catch(\PDOException $e)
            {
                $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
            }
        endif;
        return null;
    }
    public function obterProduto2($codigoProd)
    {
        if(!empty($this->Conectar())) :
            try
            {
                $sql = "select prod_codigo, prod_descricao
                    from produto where prod_codigo = :produto";

                $stms = $this->getCon()->prepare($sql);
                $stms->bindValue(":produto", $codigoProd, \PDO::PARAM_STR);
                $stms->execute();
                return $stms->fetch();
            }
            catch(\PDOException $e)
            {
                $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
            }
        endif;
        return null;
    }
    public function obterTodosCliente($codigoCliente)
    {
        if(!empty($this->Conectar())) :
            try
            {
                $sql = "select pro.prod_codigo prod_codigo, prod_descricao, prod_preco, cli_pro_preco
                    from produto pro left join
                    (select prod_codigo, cli_pro_preco from cliente_produto where
                        cli_codigo = :cliente)
                    clipro on pro.prod_codigo = clipro.prod_codigo
                    order by prod_descricao";

                $stms = $this->getCon()->prepare($sql);
                $stms->bindValue(":cliente", $codigoCliente, \PDO::PARAM_STR);
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