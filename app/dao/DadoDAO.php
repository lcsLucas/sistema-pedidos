<?php

namespace App\dao;

use App\model\Banco;
use App\model\Retorno;

if (! defined('ABSPATH')){
    header("Location: /");
    exit();
}

class DadoDAO extends Banco
{
    public function obterTodasTabelas()
    {
        if(!empty($this->Conectar())) :
          try {
                $stms = $this->getCon()->prepare("SELECT TABLE_NAME tabelas FROM information_schema.tables WHERE table_schema = ?");
                $stms->bindValue(1, DB_NAME, \PDO::PARAM_STR);
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

    public function verificarTabela($tabela)
    {
        if(!empty($this->Conectar())) :
            try {
                $stms = $this->getCon()->prepare("SELECT COUNT(*) tabela FROM information_schema.tables WHERE table_schema = ? AND TABLE_NAME = ?");
                $stms->bindValue(1, DB_NAME, \PDO::PARAM_STR);
                $stms->bindValue(2, $tabela, \PDO::PARAM_STR);
                $stms->execute();
                $result = $stms->fetch();
                return $result["tabela"];
            }
            catch(\PDOException $e) {
                $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
            }
        endif;
        return null;
    }

    public function deleteAllInsertLote($tabela)
    {
        if(!empty($this->Conectar())) :
            try {
                $stms = $this->getCon()->prepare("DELETE FROM ".$tabela);

                return $stms->execute();

            } catch(\PDOException $e) {
                $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
            }
        endif;

        return false;
    }

    public function colunasSimplesTabela($tabela)
    {
        if(!empty($this->Conectar())) :
            try {
                $stms = $this->getCon()->prepare("SELECT COLUMN_NAME coluna FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_KEY <> 'pri'");
                $stms->bindValue(1, DB_NAME, \PDO::PARAM_STR);
                $stms->bindValue(2, $tabela, \PDO::PARAM_STR);

                $stms->execute();
                return $stms->fetchAll(\PDO::FETCH_COLUMN);

            } catch(\PDOException $e) {
                $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
            }
        endif;

        return false;
    }

    /**
     * Esse metodo retirna a coluna que é a chave primaria da tabela informada,
     * ela vai servi para as condições WHERE, dos outros metodos,
     * a qual tem que executar os comandos de dados que vem de arquivos,
     * a qual não vem com os mesmo nomes das colunas do banco de dados.
     * @var string
     */
    public function colunaPrimaria($tabela)
    {
        if(!empty($this->Conectar())) :
            try {
                $stms = $this->getCon()->prepare("SELECT COLUMN_NAME coluna FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_KEY = 'pri'");
                $stms->bindValue(1, DB_NAME, \PDO::PARAM_STR);
                $stms->bindValue(2, $tabela, \PDO::PARAM_STR);

                $stms->execute();
                $result = $stms->fetch();
                return $result["coluna"];

            } catch(\PDOException $e) {
                $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
            }
        endif;

        return false;
    }

    public function zerarTabela($tabela)
    {
        if(!empty($this->Conectar())) :
            try {
                $stms = $this->getCon()->prepare("DELETE FROM $tabela");

                return $stms->execute();
            }
            catch(\PDOException $e) {
                $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
            }
        endif;

        return false;
    }

    /**
     * Zera todos os dados das tabelas "produto", "pedido", "cliente".
     */
    public function zerarTudo()
    {
        if(!empty($this->Conectar())) :
            try {
                $stms = $this->getCon()->prepare("DELETE FROM pedido; DELETE FROM cliente; DELETE FROM produto; DELETE FROM form_pagto");

                return $stms->execute();
            } catch (\PDOException $e) {
                $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
            }
        endif;

        return false;
    }

    public function deleteLote($tabela, $dados)
    {
        $erro = false;

        $coluna_tabela = $this->colunaPrimaria($tabela);
        $this->desconectar();
        if(!empty($this->Conectar())) :
            try {
                $this->beginTransaction();
                $chaves = array_column($dados,0);
                $str_chaves = implode(",", $chaves);

                $stms = $this->getCon()->prepare("DELETE FROM ".$tabela." WHERE ".$coluna_tabela." IN (".$str_chaves.")");
                $stms->execute();
            }
            catch(\PDOException $e) {
                $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
                $erro = true;
            }
        endif;

        if ($erro) :
            $this->rollBack();
        else :
            $this->commit();
        endif;

        return !$erro;
    }

    public function insertLote($tabela, $dados)
    {
        $erro = false;

        if(!empty($this->Conectar())) :
            try {
                $this->beginTransaction();
                foreach ($dados as $key => $valor) {
                    $param = str_pad("", count($dados[0])*2,"?,");
                    $param = substr_replace($param, "", strripos($param, ","));//remove o ultimo caracter que é ','

                    $stms = $this->getCon()->prepare("INSERT INTO ".$tabela." VALUES(".$param.")");
                    $stms->execute($valor);
                }
            }
            catch(\PDOException $e) {
                $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
                $erro = true;
            }
        endif;

        if ($erro) :
            $this->rollBack();
        else :
            $this->commit();
        endif;

        return !$erro;
    }

    public function alterarLote($tabela, $dados)
    {
        $erro = false;

        $chave_unica = $this->colunaPrimaria($tabela);
        $this->desconectar();
        $arr_colunas = $this->colunasSimplesTabela($tabela);
        $this->desconectar();

        if (count($arr_colunas) === count($dados[0])-1) :
            $num_colunas = count($arr_colunas);
            if(!empty($this->Conectar())) :
                try {
                    $this->beginTransaction();
                    foreach ($dados as $key => $valor) {
                        $comando = "UPDATE ".$tabela." SET";

                        for ($i=0; $i < $num_colunas; $i++) {
                            $comando .= " ".$arr_colunas[$i]." = '".$valor[$i+1]."'";
                            $comando .= (($i+1) < $num_colunas) ? ", " : " ";
                        }

                        $comando .= " WHERE ".$chave_unica." = ".$valor[0];
                        $stms = $this->getCon()->prepare($comando);
                        $stms->execute();
                        $comando = "";
                    }
                }
                catch(\PDOException $e) {
                    $this->setRetorno($e->getCode(),2,"Erro Ao Fazer a Consulta No Banco de Dados | ".$e->getMessage());
                    $erro = true;
                }
            endif;

            if ($erro) :
                $this->rollBack();
            else :
                $this->commit();
            endif;
        else :
            $erro = true;
            $this->setRetorno(0,3,"Erro: O numero de colunas informados não é igual as colunas do banco");
        endif;



        return !$erro;
    }
}