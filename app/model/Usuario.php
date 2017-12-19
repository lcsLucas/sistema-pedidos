<?php

namespace App\model;

use App\dao\UsuarioDAO;
use App\model\Retorno;

if (! defined('ABSPATH')){
    header("Location: /");
    exit();
}

  class Usuario {
    private $codigo;
    private $dtCadastro;
    private $nome;
    private $email;
    private $senha;
    private $status;
    private $retorno;

    public function __construct($codigo = 0, $nome = "", $email = "", $senha = "", $status = ''){
        $this->codigo = $codigo;
        $this->dtCadastro = date("Y-m-d");
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->status = $status;
    }

    public function getCodigo() {
        return $this->codigo;
    }

    public function getDtCadastro() {
        return $this->dtCadastro;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getSenha() {
        return $this->senha;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    public function setDtCadastro($dtCadastro) {
        $this->dtCadastro = $dtCadastro;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setSenha($senha) {
        $this->senha = $senha;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getRetorno() {
        return $this->retorno;
    }

    public function setRetorno($codigo = 0, $tp = 0, $msg = ""){
        $this->retorno = new Retorno();
        $this->retorno->setRetorno( $codigo , $tp , $msg );
    }

    /**
     *Metodo que retorno usuarios de um determinado intervalo, usado para fazer paginação
     */
    public function obterPorLimite($pagina, $itens){
        $usuDAO = new UsuarioDAO();
        $result = $usuDAO->obterLimite($pagina, $itens);
        if(count($result) >= 0) :
            return $result;
        else :
            $this->setRetorno($usuDAO->getRetorno()->getCodigo(),$usuDAO->getRetorno()->getTipo(),$usuDAO->getRetorno()->getMensagem());
            return null;
        endif;
    }

    public function countTotalusuario(){
        $usuDAO = new UsuarioDAO();
        return $usuDAO->countTotal();
    }

    /**
     * Metodo para fazer login do usuario
     */
    public function Login($email, $senha, $logado, $op_logado){
        if(!empty($this->validaDadosLogin($email, $senha)) || $op_logado) :
            $usuDAO = new UsuarioDAO();
            $result = $usuDAO->Login($email);
            if(!empty($result)) :
                if(password_verify($senha, $result["usu_senha"]) || ($op_logado && strcmp($senha, $result["usu_senha"]) === 0)) :
                    $_SESSION["UsuarioCodigo"] = $result["usu_codigo"];
                    $_SESSION["UsuarioNome"] = $result["usu_nome"];
                    $_SESSION["UsuarioStatus"] = $result["usu_status"];
                    $_SESSION["token"] = password_hash($result["usu_codigo"].$result["usu_nome"], PASSWORD_DEFAULT);
                    $_SESSION["config"] = $result["option_monetaria"];
                    if ($logado) :
                        setcookie("usuario_email", $email, time() + (86400 * 30), "/", "", 0, 1);//30 dias
                        setcookie("usuario_senha", $result["usu_senha"], time() + (86400 * 30), "/", "", 0, 1);//30 dias
                    endif;

                    session_write_close();

                    return true;
                else :
                    $this->setRetorno(0,3,"Senha está incorreta");
                endif;
            else :
                if(!empty($usuDAO->getRetorno())) :
                    $this->setRetorno($usuDAO->getRetorno()->getCodigo(),$usuDAO->getRetorno()->getTipo(),$usuDAO->getRetorno()->getMensagem());
                else :
                    $this->setRetorno(0,3,"Usuário Não Encontrado. Verifique o Email e Senha.");
                endif;
            endif;
        endif;

        return false;
    }

    /**
     * Metodo para incluir ou alterar um usuario
     */
    public function IncluirAlterar($codigo, $nome, $email, $senha, $confSenha, $status, $token){

        if(!empty($this->validaDadosUsuario($codigo, $nome, $email, $senha, $confSenha, $status, $token))) :
            $usuDAO = new UsuarioDAO();

            $this->codigo = $codigo;
            $this->nome = $nome;
            $this->email = $email;
            $this->senha = password_hash($senha, PASSWORD_DEFAULT);
            $this->status = $status;

            if(!empty($usuDAO->IncluirAlterar($this)))
                return true;
            else
                $this->setRetorno($usuDAO->getRetorno()->getCodigo(),$usuDAO->getRetorno()->getTipo(),$usuDAO->getRetorno()->getMensagem());
        endif;

        return false;
    }
    /**
     * Metodo para Excluir Usuário
     */
    public function Excluir($codigo, $token)
    {
        settype($codigo, "int");
        $usuDAO = new UsuarioDAO();

        if(!empty($codigo) && $codigo > 0) :
            if(strcmp($token, $_SESSION["token"]) === 0) :
                if ($codigo != $_SESSION["UsuarioCodigo"]) :
                    if(!empty($usuDAO->ExclusaoLogica($codigo))) :
                        return true;
                    else :
                        $this->setRetorno($usuDAO->getRetorno()->getCodigo(),$usuDAO->getRetorno()->getTipo(),$usuDAO->getRetorno()->getMensagem());
                    endif;
                else :
                    $this->setRetorno(0,3,"Erro: Não pode Excluir Usuário Logado");
                endif;
            else :
                $this->setRetorno(0,3,"Token de Autenticação Inválido");
            endif;
        else :
            $this->setRetorno(0,3,"Código Inválido para Exclusão");
        endif;
        return false;
    }

    public function alterarSenha($token, $senhaAtual, $senhanova, $confSenha)
    {
        $usuDAO = new UsuarioDAO();
        if(!empty($senhaAtual) && !empty($senhanova) && !empty($confSenha)) :
            if(strcmp($token, $_SESSION["token"]) === 0) :
                $result = $usuDAO->obterSenha($_SESSION["UsuarioCodigo"]);
                //verifica se a senha passada é a mesma que a do banco de dados
                if(password_verify($senhaAtual, $result["usu_senha"])) :
                    if(strcmp($senhanova, $confSenha) === 0) :
                        $usuDAO->desconectar();
                        if(!empty($usuDAO->alterarSenha($_SESSION["UsuarioCodigo"], password_hash($senhanova, PASSWORD_DEFAULT)))) :
                            return true;
                        else :
                            $this->setRetorno($usuDAO->getRetorno()->getCodigo(),$usuDAO->getRetorno()->getTipo(),$usuDAO->getRetorno()->getMensagem());
                        endif;
                    else :
                        $this->setRetorno(0,3,"Valores informados nos Campos para Nova Senha estão Diferentes.");
                    endif;
                else :
                    $this->setRetorno(0,3,"Senha Atual Incorreta");
                endif;
            else :
                $this->setRetorno(0,3,"Token de Autenticação Inválido");
            endif;
        else :
            $this->setRetorno(0,3,"Todos os Campos Devem ser Preenchidos");
        endif;
        return false;
    }
    
    public function modificaMonetario($monetario) {
        $usuDAO = new UsuarioDAO();
        if(!empty($usuDAO->modificaMonetario($monetario))) :
            return true;
        else :
            $this->setRetorno($usuDAO->getRetorno()->getCodigo(),$usuDAO->getRetorno()->getTipo(),$usuDAO->getRetorno()->getMensagem());
        endif;
    }

    /*Valida Dados do Login*/
    private function validaDadosLogin($email, $senha){
        if(!empty($email)) :
            if(!empty($senha) && ctype_alnum($senha)) :
                if(strlen($email) <= 50 && strlen($senha) <= 30) :
                    return true;
                else :
                    $this->setRetorno(0,3,"Ultrapassado o Limite de Caracteres do Email e/ou Senha.");
                endif;
            else :
                $this->setRetorno(0,3,"Senha está incorreta.");
            endif;
        else :
            $this->setRetorno(0,3,"Informe o Email Corretamente.");
        endif;
        return false;
    }
    /**
     * Valida dados do novo usuário à cadastrar
     */
    private function validaDadosUsuario($codigo, $nome, $email, $senha, $confSenha, $status, $token)
    {
        if(strcmp($token, $_SESSION["token"]) === 0) ://Token do formulario, deve ser igual ao do usuário logado.
            if(!empty(intval($codigo) >= 0)) ://intval->pega o valor como Int, caso nao consiga, retorna 0
                if(!empty($nome)) :
                    if(!empty($email)) :
                        $usuDAO = new UsuarioDAO();
                        $result = $usuDAO->verificaEmailUsu($email);

                        if($result >= 0) :
                            if($result == 0 || $codigo != 0) :
                                if(!empty($senha) && ctype_alnum($senha)) :
                                    if(strlen($nome) <= 50 && strlen($email) <= 50 && strlen($senha) <= 30) :
                                        if(strcasecmp($senha, $confSenha) == 0) :
                                            return true;
                                        else :
                                            $this->setRetorno(2,3,"Erro no envio dos Parametros. \"As senhas não são iguais\"");
                                        endif;
                                    else :
                                        $this->setRetorno(2,3,"Ultrapassado o Limite de Caracteres do Nome e/ou Email e/ou Senha.");
                                    endif;
                                else :
                                    $this->setRetorno(2,3,"Erro no envio dos Parametros. \"Senha está Inválida\"");
                                endif;
                            else :
                                $this->setRetorno(2,3,"Esse Email Já está cadastrado.");
                            endif;
                        else :
                            $this->setRetorno($usuDAO->getRetorno()->getCodigo(),$usuDAO->getRetorno()->getTipo(),$usuDAO->getRetorno()->getMensagem());
                        endif;
                    else :
                        $this->setRetorno(2,3,"Erro no envio dos Parametros. \"Email está Inválido\"");
                    endif;
                else :
                    $this->setRetorno(2,3,"Erro no envio dos Parametros. \"Nome não foi preenchido\"");
                endif;
            else :
                $this->setRetorno(2,3,"Erro no envio dos Parametros. \"Código Inválido\"");
            endif;
        else :
            $this->setRetorno(2,3,"Erro no envio dos Parametros. \"Falha na autenticação do Token\"");
        endif;

        return false;
    }
}
