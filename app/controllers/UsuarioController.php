<?php

namespace App\Controllers;

use App\model\Usuario;
use App\model\Retorno;
use ProjetoMvc\Render\Action;

if (! defined('ABSPATH') || !isset($_SESSION["UsuarioCodigo"])){
    header("Location: /");
    exit();
}

class UsuarioController extends Action
{
    public function __construct($param)
    {
        parent::__construct($param);
        /**
         * caminho com o arquivo do layout padrão que todasas paginas dessa controller poderá usar
         */
        $this->layoutPadrao = PATH_VIEWS."shared/layoutPadrao";
        $this->js = "partial_gerenciarUsuarios";
    }
    /**
     * tela de gerenciamento dos usuários
     * carrega variaveis para mostrar todos os usuarios com paginação
     * sem retorno
     */
    public function gerenciarUsuarios()
    {
        /*Setando variaveis de paginação*/
        $this->dados->itensPagina = 10;
        /*Veirifica se foi passado o parametro "pag", e trata.*/
        $param = filter_input(INPUT_GET,"pagina",FILTER_VALIDATE_INT);
        $paginaAtual = ! empty($param) ? $param : 0;
        $this->dados->totalUsuarios = $this->countTotalUsuario();

        /**
         * verifica se o parametro pagina passado via GET está no intervalo de páginas
         * a exibir, se nao tiver vai pra primeira pagina
         * exemplo: total de paginas = 3, e o usuario quer para a 5 (que não existe)
         */
        if($paginaAtual > floor($this->dados->totalUsuarios / $this->dados->itensPagina))
            $paginaAtual = 0;

        $paginaAtual *= $this->dados->itensPagina;

        $this->dados->resultado = $this->obterPorLimite($paginaAtual, $this->dados->itensPagina);
        $this->dados->numPagExibe = 5;

    	$this->dados->title = "Gerenciar Usuários";
        /**
         * definindo arquivos incorporados da página
         * @var string
         */
        $this->render('gerenciarUsuarios');
    }

    public function alterarSenha()
    {
    	$this->dados->title = "Alterar Senha";
        $this->render('alterarSenha');
    }

    private function obterPorLimite($pagina, $itens)
    {
        $usu = new Usuario();
        return $usu->obterPorLimite($pagina, $itens);
    }

    private function countTotalUsuario()
    {
        $usu = new Usuario();
        return $usu->countTotalUsuario();
    }

    public function excluir()
    {
        if(filter_has_var(INPUT_POST, "apagar") && isset($_SESSION['UsuarioCodigo'])) :
            $codigo = filter_input(INPUT_POST, "apagar", FILTER_VALIDATE_INT);
            $token = filter_input(INPUT_POST, "token", FILTER_SANITIZE_SPECIAL_CHARS);

            $usu = new Usuario();
            $retorno = new Retorno();

            if(!empty($usu->Excluir($codigo, $token))) :
                $retorno->setRetorno(0,1,"Usuário Excluído com Sucesso");
                echo json_encode($retorno->toArray());
            else :
                $retorno->setRetorno($usu->getRetorno()->getCodigo(),$usu->getRetorno()->getTipo(),$usu->getRetorno()->getMensagem());
                echo json_encode($retorno->toArray());
            endif;
        endif;
    }

    public function reqAlterarSenha()
    {
        if(filter_has_var(INPUT_POST, "btnEnviar") && isset($_SESSION['UsuarioCodigo'])) :
            $token = trim(filter_input(INPUT_POST, 'txtToken', FILTER_SANITIZE_SPECIAL_CHARS));
            $senhaAtual = trim(filter_input(INPUT_POST, 'txtSenhaAtual', FILTER_SANITIZE_SPECIAL_CHARS));
            $senhanova = trim(filter_input(INPUT_POST, 'txtNovaSenha', FILTER_SANITIZE_SPECIAL_CHARS));
            $confSenha = trim(filter_input(INPUT_POST, 'txtConfSenha', FILTER_SANITIZE_SPECIAL_CHARS));

            $usu = new Usuario();
            $retorno = new Retorno();

            if(!empty($usu->alterarSenha($token, $senhaAtual, $senhanova, $confSenha))) :
                $retorno->setRetorno(0,1,"Senha Alterada Com Sucesso.<br />Faça o Login Novamente com a Nova Senha.");
                echo json_encode($retorno->toArray());
            else :
                $retorno->setRetorno($usu->getRetorno()->getCodigo(),$usu->getRetorno()->getTipo(),$usu->getRetorno()->getMensagem());
                echo json_encode($retorno->toArray());
            endif;

        endif;
    }

    public function adicionar()
    {
        if(filter_has_var(INPUT_POST, "btnEnviarUsu") && isset($_SESSION['UsuarioCodigo'])){//Cadastro / Alteração de Usuário
            $token = trim(filter_input(INPUT_POST, 'txtToken', FILTER_SANITIZE_SPECIAL_CHARS));
            $codigo = trim(filter_input(INPUT_POST, 'txtCodigo', FILTER_SANITIZE_SPECIAL_CHARS));
            $nome = trim(filter_input(INPUT_POST, 'txtNome', FILTER_SANITIZE_SPECIAL_CHARS));
            $email = trim(filter_input(INPUT_POST, 'txtEmail', FILTER_VALIDATE_EMAIL));
            $senha = trim(filter_input(INPUT_POST, 'txtSenha', FILTER_SANITIZE_SPECIAL_CHARS));
            $confSenha = trim(filter_input(INPUT_POST, 'txtConfSenha', FILTER_SANITIZE_SPECIAL_CHARS));
            $status = (filter_has_var(INPUT_POST, "chkAtivo")) ? '1' : '0';

            $usu = new Usuario();
            $retorno = new Retorno();

            if(!empty($usu->incluirAlterar($codigo, $nome, $email, $senha, $confSenha, $status, $token))) :
                $retorno->setRetorno(0,1,"Operação Realizada Com Sucesso");
                echo json_encode($retorno->toArray());
            else :
                $retorno->setRetorno($usu->getRetorno()->getCodigo(),$usu->getRetorno()->getTipo(),$usu->getRetorno()->getMensagem());
                echo json_encode($retorno->toArray());
            endif;
        }
    }
}
