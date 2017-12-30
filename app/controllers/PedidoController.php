<?php

namespace App\Controllers;

use ProjetoMvc\Render\Action;
use App\model\Retorno;
use App\model\Pedido;

if (! defined('ABSPATH') || !isset($_SESSION["UsuarioCodigo"])){
    header("Location: /");
    exit();
}

class PedidoController extends Action
{

    public function __construct($param)
    {
        parent::__construct($param);
        /**
         * caminho com o arquivo do layout padrão que todasas paginas dessa controller poderá usar
         */
        $this->js = "partial_Pedidos";
        $this->layoutPadrao = PATH_VIEWS."shared/layoutPadrao";
    }

	public function fazerPedido()
    {
    	if (!empty($_SESSION['produtos'])) :
    		$prod = new \App\model\Produto();
    		$this->dados->listaProdutos = $prod->retornoSession();
    	endif;
        $this->dados->todosProdutos = \App\model\Produto::obterTodos();
		$this->dados->todosClientes = \App\model\Cliente::obterTodos();
        $this->dados->todasFormPagto = \App\model\FormPagto::obterTodos();

        $this->dados->title = "Página de Pedidos";
        $this->css = "partial_Pedidos";
        $this->render('pedidos');
    }

    public function visualizarPedidos()
    {
        $this->dados->itensPagina = 10;
        $this->dados->pagina = 0;
        $this->dados->pagina *= $this->dados->itensPagina;
        $this->dados->resultado = $this->obterPorLimite($this->dados->pagina, $this->dados->itensPagina);
        $this->dados->title = "Pedidos Realizados";
        $this->render('visualizarPedidos');
    }

    public function detalhesPedido()
    {
        $codigoPedido = filter_input(INPUT_POST, "codigo-pedido", FILTER_VALIDATE_INT);

        if (!empty($codigoPedido) && $codigoPedido > 0) :
            $this->dados->title = "Detalhes do Pedido";
            $this->dados->dadosPedido = (new \App\model\Pedido)->obterPedidoExibir($codigoPedido);
            $this->dados->itensPedido = (new \App\model\Pedido)->obterItensPedidoExibir($codigoPedido);            
            $this->css = "partial_Pedidos";
            $this->js = "partial_descricaoPedido";
            $this->render('detalhesPedido');
        else :
            header("Location: /Area-Restrita/Pedido/VisualizarPedidos");
            exit();
        endif;
    }

    public function mostrarMais()
    {
        $retorno = new Retorno();

        $this->dados->pagina = filter_input(INPUT_POST, "pagina", FILTER_VALIDATE_INT);
        $this->dados->itensPagina = 10;

        if(!empty($this->dados->pagina)) :
            $this->dados->pagina *= $this->dados->itensPagina;
            $this->dados->resultado = $this->obterPorLimite($this->dados->pagina, $this->dados->itensPagina);
            if (count($this->dados->resultado) >= 0) :
                echo json_encode($this->dados->resultado);
            else :
                $retorno->setRetorno($pedido->getRetorno()->getCodigo(),$pedido->getRetorno()->getTipo(),$pedido->getRetorno()->getMensagem());
                echo json_encode($retorno->toArray());
            endif;
        endif;
    }

    public function buscarDocumentos()
    {
        $pedido = new Pedido();
        $retorno = new Retorno();

        $codigoPedido = filter_input(INPUT_POST, "codigo-pedido", FILTER_VALIDATE_INT);

        if (!empty($codigoPedido) && $codigoPedido > 0) :
                echo json_encode($pedido->obterDocumentos($codigoPedido));
        endif;
    }

    public function buscarMaisDocumentos()
    {
        $pedido = new Pedido();
        $codigoPedido = filter_input(INPUT_POST, "codigo-pedido", FILTER_VALIDATE_INT);

        if (!empty($codigoPedido) && $codigoPedido > 0) :
            echo json_encode($pedido->obterMaisDocumentos($codigoPedido));
        endif;
    }

    public function reqFazerPedido()
    {
        if (filter_has_var(INPUT_POST, 'btnEnviarPed') && isset($_SESSION['UsuarioCodigo'])) :
            $token = filter_input(INPUT_POST, "txtToken", FILTER_SANITIZE_SPECIAL_CHARS);
            $form = filter_input(INPUT_POST, "selForm", FILTER_VALIDATE_INT);
            $obs = filter_input(INPUT_POST, "txtObs", FILTER_SANITIZE_SPECIAL_CHARS);

            $pedido = new Pedido();
            $retorno = new Retorno();

            $result = $pedido->fazerPedido($token, $form, $obs);
            if (!empty(intval($result))) :
                $this->limparPedido();
                $retorno->setRetorno(0,1,"");
                echo json_encode(["Tipo" => 1, "Mensagem" => "Pedido Realizado Com Sucesso", "Resultado" => $result]);
            else :
                $retorno->setRetorno($pedido->getRetorno()->getCodigo(),$pedido->getRetorno()->getTipo(),$pedido->getRetorno()->getMensagem());
                echo json_encode($retorno->toArray());
            endif;
        endif;
    }

    private function obterPorLimite($pagina, $itens)
    {
        $ped = new Pedido();
        return $ped->obterPorLimite($pagina, $itens);
    }

    public function limparPedido()
    {
        unset($_SESSION['clienteAtual']);
        unset($_SESSION['produtos']);
    }

    public function removerProduto()
    {
        $codigo = filter_input(INPUT_POST, "codProduto", FILTER_SANITIZE_SPECIAL_CHARS);
        unset($_SESSION['produtos'][$codigo]);

        echo json_encode((new \App\model\Produto)->retornoSession());
    }

    public function removerTodosProdutos()
    {
        unset($_SESSION['produtos']);

        $retorno = new Retorno();
        $retorno->setRetorno(0,1,"OK");
        echo json_encode($retorno->toArray());
    }
}
