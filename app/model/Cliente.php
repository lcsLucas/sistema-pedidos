<?php

namespace App\model;

use App\dao\ClienteDAO;
use App\model\Retorno;

if (! defined('ABSPATH')){
    header("Location: /");
    exit();
}

class Cliente
{
	private $codigo;
	private $nome;
	private $retorno;

	public function __construct($codigo = 0, $nome = "")
	{
		$this->codigo = $codigo;
		$this->nome = $nome;
	}

	public function getCodigo() {
    	return $this->codigo;
    }

    public function getNome() {
    	return $this->nome;
    }

    public function getRetorno() {
		return $this->retorno;
	}

	public function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    public function setNome($nome) {
    	$this->nome = $nome;
    }

    public function setRetorno($codigo = 0, $tp = 0, $msg = ""){
        $this->retorno = new Retorno();
        $this->retorno->setRetorno( $codigo , $tp , $msg );
    }

    /**
     *Metodo que retorno clientes de um determinado intervalo, usado para fazer paginação
     */
    public function obterPorLimite($pagina, $itens){
        $cliDAO = new ClienteDAO();
        $result = $cliDAO->obterLimite($pagina, $itens);
        if(count($result) >= 0) :
            return $result;
        else :
            $this->setRetorno($cliDAO->getRetorno()->getCodigo(),$cliDAO->getRetorno()->getTipo(),$cliDAO->getRetorno()->getMensagem());
            return null;
        endif;
    }

    public static function obterTodos()
    {
        $cliDAO = new ClienteDAO();
        $result = $cliDAO->obterTodos();
        if(count($result) >= 0) :
            return $result;
        else :
            $this->setRetorno($cliDAO->getRetorno()->getCodigo(),$cliDAO->getRetorno()->getTipo(),$cliDAO->getRetorno()->getMensagem());
            return null;
        endif;
    }

    public function countTotalClientes(){
        $cliDAO = new ClienteDAO();
        return $cliDAO->countTotal();
    }

    public function alterarClienteProduto($token, $codigoCliente, $codigoProd, $precoNovo)
    {
        $cliDAO = new ClienteDAO();

        /*Tratando valor chegado para o tipo flutuante no PHP*/
        $precoNovo = str_replace(".", "", $precoNovo);
        $precoNovo = str_replace(",", ".", $precoNovo);
        $precoNovo = number_format(filter_var($precoNovo, FILTER_VALIDATE_FLOAT), 2);

        if(!empty($codigoProd)) :
            if(!empty($codigoCliente)) :
                if(strcmp($token, $_SESSION["token"]) === 0) :
                    if (!empty($precoNovo)) :
                        if (!empty($cliDAO->alterarClienteProduto($codigoCliente, $codigoProd, $precoNovo))) :
                            return true;
                        else :
                            $this->setRetorno($cliDAO->getRetorno()->getCodigo(),$cliDAO->getRetorno()->getTipo(),$cliDAO->getRetorno()->getMensagem());
                        endif;
                    else :
                        $this->setRetorno(0,3,"Valor do Novo Preço Inválido");
                    endif;
                else :
                    $this->setRetorno(0,3,"Token de Autenticação Inválido");
                endif;
            else :
                $this->setRetorno(0,3,"Código do Cliente Inválido para Alteração");
            endif;
        else :
            $this->setRetorno(0,3,"Código do Produto Inválido para Alteração");
        endif;
        return false;
    }

    public function recClienteProduto($token, $codigoCliente, $codigoProd)
    {
        $cliDAO = new ClienteDAO();

        if(!empty($codigoProd)) :
            if(!empty($codigoCliente)) :
                if(strcmp($token, $_SESSION["token"]) === 0) :
                    $result = $cliDAO->recClienteProduto($codigoCliente, $codigoProd);
                    if (!empty($result)) :
                        return $result;
                    else :
                        $this->setRetorno($cliDAO->getRetorno()->getCodigo(),$cliDAO->getRetorno()->getTipo(),$cliDAO->getRetorno()->getMensagem());
                    endif;
                else :
                    $this->setRetorno(0,3,"Token de Autenticação Inválido");
                endif;
            else :
                $this->setRetorno(0,3,"Código do Cliente Inválido para Busca");
            endif;
        else :
            $this->setRetorno(0,3,"Código do Produto Inválido para Busca");
        endif;
        return false;
    }

    public function remClienteProduto($token, $codigoCliente, $codigoProd)
    {
        $cliDAO = new ClienteDAO();

        if(!empty($codigoProd)) :
            if(!empty($codigoCliente)) :
                if(strcmp($token, $_SESSION["token"]) === 0) :
                    if (!empty($cliDAO->remClienteProduto($codigoCliente, $codigoProd))) :
                        return true;
                    else :
                        $this->setRetorno($cliDAO->getRetorno()->getCodigo(),$cliDAO->getRetorno()->getTipo(),$cliDAO->getRetorno()->getMensagem());
                    endif;
                else :
                    $this->setRetorno(0,3,"Token de Autenticação Inválido");
                endif;
            else :
                $this->setRetorno(0,3,"Código do Cliente Inválido para Busca");
            endif;
        else :
            $this->setRetorno(0,3,"Código do Produto Inválido para Busca");
        endif;
        return false;
    }
}