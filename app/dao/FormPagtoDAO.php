<?php
namespace App\dao;

use App\model\Banco;
use App\model\Retorno;

if (! defined('ABSPATH')){
    header("Location: /");
    exit();
}

class FormPagtoDAO extends Banco
{
	function obterTodos()
    {
      if(!empty($this->Conectar())) :
          try
          {
            $stms = $this->getCon()->prepare("SELECT form_codigo, form_descricao FROM form_pagto");
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