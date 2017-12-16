<?php

namespace App\dao;

use App\model\Banco;
use App\model\Retorno;

if (! defined('ABSPATH')){
    header("Location: /");
    exit();
}

class ClienteDAO extends Banco
{
    function obterLimite($pagina, $itens)
    {
      settype($pagina, "Integer");
      settype($itens, "Integer");

      if(!empty($this->Conectar())) :
          try
          {
            $stms = $this->getCon()->prepare("SELECT * FROM cliente ORDER BY cli_nome LIMIT :pagina, :itens");
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

    function obterTodos()
    {
        if(!empty($this->Conectar())) :
          try
          {
            $stms = $this->getCon()->prepare("SELECT * FROM cliente ORDER BY cli_nome");
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

    function countTotal()
    {
      if(!empty($this->Conectar())) :
        try
        {
            $stms = $this->getCon()->prepare("SELECT count(*) Total FROM cliente");
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

    function alterarClienteProduto($codigoCliente, $codigoProd, $precoNovo)
    {
        if(!empty($this->Conectar())) :
            try
            {
                $sql = "SELECT count(*) Total FROM cliente_produto
                    WHERE cli_codigo = :cli AND prod_codigo = :pro";

                $stms = $this->getCon()->prepare($sql);
                $stms->bindValue(":cli", $codigoCliente, \PDO::PARAM_STR);
                $stms->bindValue(":pro", $codigoProd, \PDO::PARAM_STR);
                $stms->execute();
                $result = $stms->fetch();

                if (!empty($result["Total"])) :
                    $sql = "UPDATE cliente_produto SET cli_pro_preco = :preco WHERE
                        cli_codigo = :cli AND prod_codigo = :pro";
                    $stms = $this->getCon()->prepare($sql);
                else :
                    $sql = "INSERT INTO cliente_produto(cli_codigo,prod_codigo,cli_pro_preco)
                        VALUES(:cli,:pro,:preco)";
                    $stms = $this->getCon()->prepare($sql);
                endif;
                $stms->bindValue(":cli", $codigoCliente, \PDO::PARAM_STR);
                $stms->bindValue(":pro", $codigoProd, \PDO::PARAM_STR);
                $stms->bindValue(":preco", $precoNovo);
                return $stms->execute();
            }
            catch(\PDOException $e)
            {
                $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
            }
        endif;
        return null;
    }

    function recClienteProduto($codigoCliente, $codigoProd)
    {
        if(!empty($this->Conectar())) :
            try
            {
                $sql = "SELECT cli_pro_preco FROM cliente_produto
                    WHERE cli_codigo = :cli AND prod_codigo = :pro";

                $stms = $this->getCon()->prepare($sql);
                $stms->bindValue(":cli", $codigoCliente, \PDO::PARAM_STR);
                $stms->bindValue(":pro", $codigoProd, \PDO::PARAM_STR);
                $stms->execute();
                $result = $stms->fetch();

                if(empty($result["cli_pro_preco"]))
                  return -1;
                return $result["cli_pro_preco"];
            }
            catch(\PDOException $e)
            {
                $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
            }
        endif;
        return null;
    }

    function remClienteProduto($codigoCliente, $codigoProd)
    {
        if(!empty($this->Conectar())) :
            try
            {
                $sql = "DELETE FROM cliente_produto
                    WHERE cli_codigo = :cli AND prod_codigo = :pro";

                $stms = $this->getCon()->prepare($sql);
                $stms->bindValue(":cli", $codigoCliente, \PDO::PARAM_STR);
                $stms->bindValue(":pro", $codigoProd, \PDO::PARAM_STR);

                return $stms->execute();
            }
            catch(\PDOException $e)
            {
                $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
            }
        endif;
        return null;
    }
}