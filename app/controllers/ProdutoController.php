<?php

namespace App\Controllers;

use ProjetoMvc\Render\Action;
use App\model\Produto;
use App\model\Retorno;

if (! defined('ABSPATH') || !isset($_SESSION["UsuarioCodigo"])){
    header("Location: /");
    exit();
}

class ProdutoController extends Action
{

    public function __construct($param)
    {
        parent::__construct($param);
        /**
         * caminho com o arquivo do layout padrão que todasas paginas dessa controller poderá usar
         */
        $this->layoutPadrao = PATH_VIEWS."shared/layoutPadrao";
        $this->js = "partial_gerenciarProdutos";
    }

	public function gerenciarProdutos()
    {
    	/*Setando variaveis de paginação*/
        $this->dados->itensPagina = 10;
        /*Veirifica se foi passado o parametro "pag", e trata.*/
        $param = filter_input(INPUT_GET,"pagina",FILTER_VALIDATE_INT);
        $paginaAtual = ! empty($param) ? $param : 0;
        $this->dados->totalProdutos = $this->countTotalProdutos();

        /**
         * verifica se o parametro pagina passado via GET está no intervalo de páginas
         * a exibir, se nao tiver vai pra primeira pagina
         * exemplo: total de paginas = 3, e o usuario quer para a 5 (que não existe)
         */
        if($paginaAtual > floor($this->dados->totalProdutos / $this->dados->itensPagina))
            $paginaAtual = 0;

        $paginaAtual *= $this->dados->itensPagina;

        $this->dados->resultado = $this->obterPorLimite($paginaAtual, $this->dados->itensPagina);
        $this->dados->numPagExibe = 5;

        $this->dados->title = "Gerenciar Produtos";
        $this->render('gerenciarProdutos');
    }

    public function obterPorCliente()
    {
        if (filter_has_var(INPUT_POST, "cliente") && isset($_SESSION['UsuarioCodigo'])) :
            $codigoCliente = filter_input(INPUT_POST, "cliente", FILTER_SANITIZE_SPECIAL_CHARS);
            $token = filter_input(INPUT_POST, "token", FILTER_SANITIZE_SPECIAL_CHARS);

            $prod = new Produto();
            $retorno = new Retorno();

            $result = $prod->obterPorCliente($codigoCliente, $token);
            if (count($result) >= 0) :
                echo json_encode($result);
            else :
                $retorno->setRetorno($prod->getRetorno()->getCodigo(),$prod->getRetorno()->getTipo(),$prod->getRetorno()->getMensagem());
                echo json_encode($retorno->toArray());
            endif;
        endif;
    }

    public function alterar()
    {
        if(filter_has_var(INPUT_POST, "btnEnviarProd") && isset($_SESSION['UsuarioCodigo'])) :
            $codigo = filter_input(INPUT_POST, "txtCodigo", FILTER_SANITIZE_SPECIAL_CHARS);
            $token = filter_input(INPUT_POST, "txtToken", FILTER_SANITIZE_SPECIAL_CHARS);
            $preco = filter_input(INPUT_POST, "txtPrecoNovo", FILTER_SANITIZE_SPECIAL_CHARS);

            $prod = new Produto();
            $retorno = new Retorno();

            if (!empty($prod->alterar($codigo, $token, $preco))) :
                $retorno->setRetorno(0,1,"Alteração realizada Com Sucesso");
                echo json_encode($retorno->toArray());
            else :
                $retorno->setRetorno($prod->getRetorno()->getCodigo(),$prod->getRetorno()->getTipo(),$prod->getRetorno()->getMensagem());
                echo json_encode($retorno->toArray());
            endif;
        endif;
    }
/**
 * Adicionar Produto na Session
 * @return array
 */
    public function adicionarProduto()
    {
        $prod = new Produto();
        $retorno = new Retorno();

        $codigo = filter_input(INPUT_POST, "selProduto", FILTER_SANITIZE_SPECIAL_CHARS);
        $qtde = (int) filter_input(INPUT_POST, "txtQtde", FILTER_SANITIZE_NUMBER_INT);

        $result = $prod->adicionarProduto($codigo, $qtde);
        if (!empty($result)) :
            echo json_encode($result);
        else :
            $retorno->setRetorno($prod->getRetorno()->getCodigo(),$prod->getRetorno()->getTipo(),$prod->getRetorno()->getMensagem());
            echo json_encode($retorno->toArray());
        endif;
    }

    private function obterPorLimite($pagina, $itens)
    {
        $prod = new Produto();
        return $prod->obterPorLimite($pagina, $itens);
    }

    private function countTotalProdutos()
    {
        $prod = new Produto();
        return $prod->countTotalProdutos();
    }
}
