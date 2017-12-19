<?php

namespace App\controllers;

use ProjetoMvc\render\Action;
use App\model\Usuario;
use App\model\Retorno;

if (! defined('ABSPATH')){
    header("Location: /");
    exit();
}

class HomeController extends Action
{
    public function __construct($param)
    {
        parent::__construct($param);
        /**
         * caminho com o arquivo do layout padrão que todasas paginas dessa controller poderá usar
         */
        $this->layoutPadrao = PATH_VIEWS."shared/layoutPadrao";
    }

    /**
     * chama a view de pagina nao encontrada
     */
    public function pageError404()
    {
            $this->dados->title = "Página Não Encontrada";
            $this->render('error404');
    }
    
    /*renderiza pagina login*/
    public function pageLogin()
    {
            $this->dados->title = "Página de Login";
            $this->render('login');
    }
    
    /*renderiza pagina dashboard*/
    public function dashboard()
    {
        $ped = new \App\model\Pedido();
        
        $this->dados->itensPagina = 10;
        $this->dados->pagina = 0;
        $this->dados->pagina *= $this->dados->itensPagina;
        $this->dados->resultado = $ped->obterPorLimite($this->dados->pagina, $this->dados->itensPagina);
        
        $this->dados->title = "Dashboard";
        $this->render('dashboard');
    }
    
    public function index()
    {
        /**
         * Se o usuario nao estiver logado chama o layout login,
         * senao redireciona para Dashboard
         */
        if(!isset($_SESSION["UsuarioStatus"])) :
            if(isset($_COOKIE["usuario_email"]) && isset($_COOKIE["usuario_senha"])) :
                $email = $_COOKIE["usuario_email"];
                $senha = $_COOKIE["usuario_senha"];
                $logado = true;
                $op_logado = true;

                $usu = new Usuario();
                $usu->Login($email, $senha, $logado, $op_logado);
            endif;
        endif;

        if (isset($_SESSION["UsuarioStatus"])) :
            if(empty($_SESSION["UsuarioStatus"])) : //status = 0, usuario comum
                header("Location: /Area-Restrita/Pedido/FazerPedido");
                exit();
            else :
                header("Location: /Area-Restrita/Dashboard");
                exit();
            endif;
        else :
                $this->pageLogin();
        endif;
    }
    
    /**
     * metodo que faz o login do usuário recebendo os campos do formulário
     * e passando para o Model fazer todas as validações e o login.
     * @return retorna via Ajax um array resposta contendo codigo,tipo e mensagem da resposta.
     */
    public function login()
    {
        if(filter_has_var(INPUT_POST, 'btnLogar')): //Logar
            $email = trim(filter_input(INPUT_POST, 'txtEmail', FILTER_VALIDATE_EMAIL));
            $senha = trim(filter_input(INPUT_POST, 'txtSenha', FILTER_SANITIZE_SPECIAL_CHARS));
            $logado = filter_has_var(INPUT_POST, "ckLogado");

            $usu = new Usuario();
            $retorno = new Retorno();

            if(!empty($usu->Login($email, $senha, $logado, false))) :
                $retorno->setRetorno(0,1,"OK");
                echo json_encode($retorno->toArray());
            else :
                $retorno->setRetorno($usu->getRetorno()->getCodigo(),$usu->getRetorno()->getTipo(),$usu->getRetorno()->getMensagem());
                echo json_encode($retorno->toArray());
            endif;
        else:
            $this->index();
        endif;
    }
    
    /**
     * Metodo de logout, destroi as session setadas e redireciona para o Index.
     * @return sem retorno
     */
    public function logOut()
    {
        if(isset($_SESSION["UsuarioStatus"])){
            unset($_SESSION["UsuarioCodigo"],$_SESSION["UsuarioNome"],$_SESSION['clienteAtual'],
                $_SESSION["UsuarioEmail"],$_SESSION["UsuarioStatus"],$_SESSION["token"],$_SESSION['produtos']);

            setcookie("usuario_email", "", time() - 3600);
            setcookie("usuario_senha", "", time() - 3600);
        }
        
        header("Location: /");
        exit();
    }
    
    public function modificaMonetario() {
        $monetario = filter_input(INPUT_POST, 'optMonetario', FILTER_SANITIZE_SPECIAL_CHARS);
        $monetario = (strcmp($monetario,"true") === 0) ? 1 : 0;
        
        $usu = new Usuario();
        $retorno = new Retorno();
        
        if(!empty($usu->modificaMonetario($monetario))) :
            $retorno->setRetorno(0,1,"Operação Modifica Com Sucesso.<br />Faça o Login Novamente.");
            echo json_encode($retorno->toArray());
        else :
            $retorno->setRetorno($usu->getRetorno()->getCodigo(),$usu->getRetorno()->getTipo(),$usu->getRetorno()->getMensagem());
            echo json_encode($retorno->toArray());
        endif;
    }
}
