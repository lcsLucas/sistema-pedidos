<?php

namespace App\dao;

use App\model\Banco;
use App\model\Retorno;

if (! defined('ABSPATH')){
    header("Location: /");
    exit();
}

class UsuarioDAO extends Banco{

    function Login($email)
    {
       if(!empty($this->Conectar())) :
           try
            {
                $stms = $this->getCon()->prepare("SELECT usu_codigo,usu_nome,usu_senha, usu_status FROM usuario WHERE usu_email = :email AND usu_ativo = '1' LIMIT 1");
                $stms->bindValue(":email", $email, \PDO::PARAM_STR);
                $stms->execute();
                return $stms->fetch();
           }
           catch (\PDOException $e)
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
            $stms = $this->getCon()->prepare("SELECT usu_codigo, usu_nome, usu_email, usu_status FROM usuario WHERE usu_ativo = '1'");
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
            $stms = $this->getCon()->prepare("SELECT count(*) Total FROM usuario WHERE usu_ativo = '1'");
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

    function verificaEmailUsu($email)
    {
      if(!empty($this->Conectar())) :
          try
          {
            $stms = $this->getCon()->prepare("SELECT usu_codigo codigo FROM usuario WHERE usu_email = :email AND usu_ativo = '1' LIMIT 1");
            $stms->bindValue(":email", $email, \PDO::PARAM_STR);
            $stms->execute();
            $result = $stms->fetch();
            return $result["codigo"];
          }
          catch(\PDOException $e)
          {
              $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
          }
      endif;
      return -1;
    }

    function obterSenha($codigo)
    {
      if(!empty($this->Conectar())) :
          try
          {
            $stms = $this->getCon()->prepare("SELECT usu_senha FROM usuario WHERE usu_codigo = :codigo AND usu_ativo = '1'");
            $stms->bindValue(":codigo", $codigo);
            $stms->execute();
            return $stms->fetch();
          }
          catch(\PDOException $e)
          {
              $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
          }
      endif;
      return false;
    }

    function obterLimite($pagina, $itens)
    {
      settype($pagina, "Integer");
      settype($itens, "Integer");

      if(!empty($this->Conectar())) :
          try
          {
            $stms = $this->getCon()->prepare("SELECT usu_codigo, usu_nome, usu_email, usu_status FROM usuario WHERE usu_ativo = '1' LIMIT :pagina, :itens");
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

    function incluirAlterar($usu)
    {
      if(!empty($this->Conectar())) :
        try
        {
          if($usu->getCodigo() > 0) :
            $stms = $this->getCon()->prepare("UPDATE usuario SET usu_nome = :nome, usu_email = :email, usu_senha = :senha, usu_status = :status WHERE usu_codigo = :codigo;");
            $stms->bindValue(":codigo", $usu->getCodigo());
          else :
            $stms = $this->getCon()->prepare("INSERT INTO usuario(usu_nome, usu_dtCad, usu_email, usu_senha, usu_status, usu_ativo) VALUES(:nome, :data, :email, :senha, :status, '1');");
            $stms->bindValue(":data", $usu->getDtCadastro());
          endif;
          $stms->bindValue(":nome", $usu->getNome(), \PDO::PARAM_STR);
          $stms->bindValue(":email", $usu->getEmail(), \PDO::PARAM_STR);
          $stms->bindValue(":senha", $usu->getSenha(), \PDO::PARAM_STR);
          $stms->bindValue(":status", $usu->getStatus());

          return $stms->execute();
        }
        catch(\PDOException $e)
        {
          $this->setRetorno($e->getCode(),2,"Erro Ao Executar o Comando No Banco de Dados | ".$e->getMessage());
        }
      endif;
      return false;
    }

    function ExclusaoLogica($codigo)
    {
      if(!empty($this->Conectar())) :
        try
        {
          $stms = $this->getCon()->prepare("UPDATE usuario SET usu_ativo = '0' WHERE usu_codigo = :codigo;");
          $stms->bindValue(":codigo", $codigo, \PDO::PARAM_INT);

          return $stms->execute();
        }
        catch(\PDOException $e)
        {
          $this->setRetorno($e->getCode(),2,"Erro Ao Executar o Comando No Banco de Dados | ".$e->getMessage());
        }
      endif;
      return false;
    }

    function alterarSenha($codigo, $senha)
    {
      if(!empty($this->Conectar())) :
        try
        {
          $stms = $this->getCon()->prepare("update usuario set usu_senha = :senha where usu_codigo = :codigo");
          $stms->bindValue(":codigo", intval($codigo), \PDO::PARAM_INT);
          $stms->bindValue(":senha", $senha, \PDO::PARAM_STR);

          return $stms->execute();
        }
        catch(\PDOException $e)
        {
          $this->setRetorno($e->getCode(),2,"Erro Ao Executar o Comando No Banco de Dados | ".$e->getMessage());
        }
      endif;
      return false;
    }
}
