<?php

namespace App\Controllers;

use ProjetoMvc\Render\Action;
use App\model\Cliente;
use App\model\Retorno;

if (! defined('ABSPATH') || !isset($_SESSION["UsuarioCodigo"])){
    header("Location: /");
    exit();
}

class ClienteController extends Action
{

    public function __construct($param)
    {
        parent::__construct($param);
        /**
         * caminho com o arquivo do layout padrão que todasas paginas dessa controller poderá usar
         */
        $this->layoutPadrao = PATH_VIEWS."shared/layoutPadrao";
        $this->js = "partial_gerenciarClientes";
    }

	public function gerenciarClientes()
	{
        /*Setando variaveis de paginação*/
        $this->dados->itensPagina = 10;
        /*Veirifica se foi passado o parametro "pag", e trata.*/
        $param = filter_input(INPUT_GET,"pagina",FILTER_VALIDATE_INT);
        $paginaAtual = ! empty($param) ? $param : 0;
        $this->dados->totalClientes = $this->countTotalClientes();
        $this->dados->todosProdutos = \App\model\Produto::obterTodos();

        /**
         * verifica se o parametro pagina passado via GET está no intervalo de páginas
         * a exibir, se nao tiver vai pra primeira pagina
         * exemplo: total de paginas = 3, e o usuario quer para a 5 (que não existe)
         */
        if($paginaAtual > floor($this->dados->totalClientes / $this->dados->itensPagina))
            $paginaAtual = 0;

        $paginaAtual *= $this->dados->itensPagina;

        $this->dados->resultado = $this->obterPorLimite($paginaAtual, $this->dados->itensPagina);
        $this->dados->numPagExibe = 5;

        $this->dados->title = "Gerenciar Clientes";
    	$this->render('gerenciarClientes');
	}

    public function alterarClienteProduto()
    {
        if(filter_has_var(INPUT_POST, 'btnEnviar') && isset($_SESSION['UsuarioCodigo'])) :
            $token = filter_input(INPUT_POST, "txtToken", FILTER_SANITIZE_SPECIAL_CHARS);
            $codigoCliente = filter_input(INPUT_POST, "txtCodigo", FILTER_SANITIZE_SPECIAL_CHARS);
            $codigoProd = filter_input(INPUT_POST, "selProduto", FILTER_SANITIZE_SPECIAL_CHARS);
            $precoNovo = filter_input(INPUT_POST, "txtPrecoNovo", FILTER_SANITIZE_SPECIAL_CHARS);

            $cli = new Cliente();
            $retorno = new Retorno();

            if(!empty($cli->alterarClienteProduto($token, $codigoCliente, $codigoProd, $precoNovo))) :
                $retorno->setRetorno(0,1,"Alteração realizada Com Sucesso");
                echo json_encode($retorno->toArray());
            else :
                $retorno->setRetorno($cli->getRetorno()->getCodigo(),$cli->getRetorno()->getTipo(),$cli->getRetorno()->getMensagem());
                echo json_encode($retorno->toArray());
            endif;
        endif;
    }

    public function recClienteProduto()
    {
        if(filter_has_var(INPUT_POST, 'recProd') && isset($_SESSION['UsuarioCodigo'])) :
            $token = filter_input(INPUT_POST, "token", FILTER_SANITIZE_SPECIAL_CHARS);
            $codigoCliente = filter_input(INPUT_POST, "cli", FILTER_SANITIZE_SPECIAL_CHARS);
            $codigoProd = filter_input(INPUT_POST, "recProd", FILTER_SANITIZE_SPECIAL_CHARS);

            $cli = new Cliente();
            $retorno = new Retorno();

            $result = $cli->recClienteProduto($token, $codigoCliente, $codigoProd);
            if(!empty($result)) :
                if($result > 0)
                    $retorno->setRetorno(0,1, number_format($result, 2, ',', '.'));
                else
                    $retorno->setRetorno(0,1, "");
                echo json_encode($retorno->toArray());
            else :
                $retorno->setRetorno($cli->getRetorno()->getCodigo(),$cli->getRetorno()->getTipo(),$cli->getRetorno()->getMensagem());
                echo json_encode($retorno->toArray());
            endif;
        endif;
    }

    public function remClienteProduto()
    {
        if(filter_has_var(INPUT_POST, 'remProd') && isset($_SESSION['UsuarioCodigo'])) :
            $token = filter_input(INPUT_POST, "token", FILTER_SANITIZE_SPECIAL_CHARS);
            $codigoCliente = filter_input(INPUT_POST, "cli", FILTER_SANITIZE_SPECIAL_CHARS);
            $codigoProd = filter_input(INPUT_POST, "remProd", FILTER_SANITIZE_SPECIAL_CHARS);

            $cli = new Cliente();
            $retorno = new Retorno();

            if(!empty($cli->remClienteProduto($token, $codigoCliente, $codigoProd))) :
                $retorno->setRetorno(0,1, "Preço do Produto Restaurado com Sucesso");
                echo json_encode($retorno->toArray());
            else :
                $retorno->setRetorno($cli->getRetorno()->getCodigo(),$cli->getRetorno()->getTipo(),$cli->getRetorno()->getMensagem());
                echo json_encode($retorno->toArray());
            endif;
        endif;
    }

    private function obterPorLimite($pagina, $itens)
    {
        $cli = new Cliente();
        return $cli->obterPorLimite($pagina, $itens);
    }

    private function countTotalClientes()
    {
        $cli = new Cliente();
        return $cli->countTotalClientes();
    }
}

