<?php

namespace App\model;

use App\dao\ProdutoDAO;
use App\model\Retorno;

if (! defined('ABSPATH')){
    header("Location: /");
    exit();
}

class Produto
{
	private $codigo;
	private $descricao;
	private $preco;
	private $retorno;

    public function __construct($codigo = 0, $descricao = "", $preco = "")
    {
    	$this->codigo = $codigo;
    	$this->descricao = $descricao;
    	$this->preco = $preco;
    }

    public function getCodigo() {
        return $this->codigo;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function getPreco() {
        return $this->preco;
    }

    public function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    public function setPreco($preco) {
        $this->preco = $preco;
    }

    public function getRetorno() {
    	return $this->retorno;
    }

    public function setRetorno($codigo = 0, $tp = 0, $msg = ""){
        $this->retorno = new Retorno();
        $this->retorno->setRetorno( $codigo , $tp , $msg );
    }

     /**
     *Metodo que retorno produtos de um determinado intervalo, usado para fazer paginação
     */
    public function obterPorLimite($pagina, $itens){
        $prodDAO = new ProdutoDAO();
        $result = $prodDAO->obterLimite($pagina, $itens);
        if(count($result) >= 0) :
            return $result;
        else :
            $this->setRetorno($prodDAO->getRetorno()->getCodigo(),$prodDAO->getRetorno()->getTipo(),$prodDAO->getRetorno()->getMensagem());
            return null;
        endif;
    }

    public function obterPorCliente($codigoCliente, $token)
    {
        $prodDAO = new ProdutoDAO();

        if(!empty($codigoCliente)) :
            if(strcmp($token, $_SESSION["token"]) === 0) :
                $result = $prodDAO->obterPorCliente($codigoCliente);
                if(count($result) >= 0) :
                    $_SESSION['clienteAtual'] = $codigoCliente;
                    return $result;
                else :
                    $this->setRetorno($prodDAO->getRetorno()->getCodigo(),$prodDAO->getRetorno()->getTipo(),$prodDAO->getRetorno()->getMensagem());
                endif;
            else :
                $this->setRetorno(0,3,"Token de Autenticação Inválido");
            endif;
        else :
            $this->setRetorno(0,3,"Código Inválido para Consulta");
        endif;

        return null;
    }

    public static function obterTodos()
    {
        $prodDAO = new ProdutoDAO();
        $result = $prodDAO->obterTodos();
        if(count($result) >= 0) :
            return $result;
        else :
            $this->setRetorno($prodDAO->getRetorno()->getCodigo(),$prodDAO->getRetorno()->getTipo(),$prodDAO->getRetorno()->getMensagem());
            return null;
        endif;
    }

    public static function obterTodosCliente($codigoCliente)
    {
        $prodDAO = new ProdutoDAO();
        $result = $prodDAO->obterTodosCliente($codigoCliente);
        if(count($result) >= 0) :
            return $result;
        else :
            $this->setRetorno($prodDAO->getRetorno()->getCodigo(),$prodDAO->getRetorno()->getTipo(),$prodDAO->getRetorno()->getMensagem());
            return null;
        endif;
    }

    public function countTotalProdutos(){
        $prodDAO = new ProdutoDAO();
        return $prodDAO->countTotal();
    }

    public function alterar($codigo, $token, $preco)
    {
        $prodDAO = new ProdutoDAO();

        /*Tratando valor chegado para o tipo flutuante no PHP*/
        $preco = str_replace(".", "", $preco);
        $preco = str_replace(",", ".", $preco);
        $preco = number_format(filter_var($preco, FILTER_VALIDATE_FLOAT), 2);

        if(!empty($codigo)) :
            if(strcmp($token, $_SESSION["token"]) === 0) :
                if (!empty($preco)) :
                    if (!empty($prodDAO->alterar($codigo, $preco))) :
                        return true;
                    else :
                        $this->setRetorno($prodDAO->getRetorno()->getCodigo(),$prodDAO->getRetorno()->getTipo(),$prodDAO->getRetorno()->getMensagem());
                    endif;
                else :
                    $this->setRetorno(0,3,"Valor do Novo Preço Inválido");
                endif;
            else :
                $this->setRetorno(0,3,"Token de Autenticação Inválido");
            endif;
        else :
            $this->setRetorno(0,3,"Código Inválido para Alteração");
        endif;

        return false;
    }

    public function adicionarProduto($codigo, $qtde)
    {
        $prodDAO = new ProdutoDAO();

        if (!empty($codigo)) :
            if (!empty($qtde) && $qtde > 0) {
                if (!empty($prodDAO->verificaProduto($codigo))) :
                    if (!isset($_SESSION['produtos'])) :
                        $_SESSION['produtos'] = array();
                    endif;

                    if (!isset($_SESSION['produtos'][$codigo])) :
                        $_SESSION['produtos'][$codigo] = $qtde;
                    else :
                        $_SESSION['produtos'][$codigo] += $qtde;
                    endif;
                    return $this->retornoSession();
                else :
                    $this->setRetorno($prodDAO->getRetorno()->getCodigo(),$prodDAO->getRetorno()->getTipo(),$prodDAO->getRetorno()->getMensagem());
                endif;
            } else {
                $this->setRetorno(0,3,"Quantidade Informada Inválida");
            }
        else :
            $this->setRetorno(0,3,"Código Inválido para o Produto");
        endif;
    }

    public function retornoSession()
    {
        $lista = array();
        $total = 0.0;
        
        if (!empty($_SESSION['config'])) {
            foreach ($_SESSION['produtos'] as $codigo => $qtde) {
                $prodDAO = new ProdutoDAO();
                $result = $prodDAO->obterProduto($codigo, $_SESSION['clienteAtual']);
                $preco = 0.0;
                $subTotal = 0.0;
                if (!empty($result['cli_pro_preco'])) :
                    $preco = $result['cli_pro_preco'];
                else :
                    $preco = $result['prod_preco'];
                endif;
                $subTotal = floatval($preco * $qtde);
                $total += $subTotal;
                array_push($lista, [$codigo, $result['prod_descricao'], $qtde, number_format($preco, 2, ',', '.'),
                    number_format($subTotal, 2, ',', '.'), number_format($total, 2, ',', '.')]);
            }
        } else {
            foreach ($_SESSION['produtos'] as $codigo => $qtde) {
                $prodDAO = new ProdutoDAO();
                $result = $prodDAO->obterProduto2($codigo);
                array_push($lista, [$codigo, $result['prod_descricao'], $qtde]);
            }
        }        

        return $lista;
    }
}