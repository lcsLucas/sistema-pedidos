<?php

namespace App;

use ProjetoMvc\init\Bootstrap;

if (! defined('ABSPATH'))
    header("Location: /");
/**
 * Classe de inicio do sistema, onde é inicializado as rotas que o site tem.
 */
class Init extends Bootstrap
{
    /**
     * metodo que inicia todas as rotas pré definidas do site
     *'route' = rota esperada.
     *'method' = metodo da onde a rota virá, podendo ser 'GET' ou 'POST'.
     *'controller' = controller que atenderá a rota recebida.
     *'action' = Action dessa controller que será executada.
     *'param' = os parametros passado na URL, junto com a rota.
     */
    protected function initRoutes()
    {
        $array = array();

        $array[] = array(
            'route' => '',
            'method' => 'GET',
            'controller' => 'home',
            'action' => 'index',
            'param' => []
        );

        $array[] = array(
            'route' => 'Area-Restrita/Login',
            'method' => 'GET',
            'controller' => 'home',
            'action' => 'pageLogin',
            'param' => []
        );

        $array[] = array(
            'route' => 'Area-Restrita/Login',
            'method' => 'POST',
            'controller' => 'home',
            'action' => 'login',
            'param' => []
        );

        $array[] = array(
            'route' => 'Area-Restrita/Dashboard',
            'method' => 'GET',
            'controller' => 'home',
            'action' => 'dashboard',
            'param' => []
        );

        $array[] = array(
            'route' => 'Area-Restrita/Logout',
            'method' => 'GET',
            'controller' => 'home',
            'action' => 'logout',
            'param' => []
        );

        /*Rotas de Usuário*/

        $array[] = array(
            'route' => 'Area-Restrita/Usuario/GerenciarUsuarios',
            'method' => 'GET',
            'controller' => 'usuario',
            'action' => 'gerenciarUsuarios',
            'param' => []
        );

        $array[] = array(
            'route' => 'Area-Restrita/Usuario/AlterarSenha',
            'method' => 'GET',
            'controller' => 'usuario',
            'action' => 'alterarSenha',
            'param' => []
        );

        $array[] = array(
            'route' => 'Area-Restrita/Usuario/ExcluirUsuario',
            'method' => 'POST',
            'controller' => 'usuario',
            'action' => 'excluir',
            'param' => []
        );

        $array[] = array(
            'route' => 'Area-Restrita/Usuario/AdicionarUsuario',
            'method' => 'POST',
            'controller' => 'usuario',
            'action' => 'adicionar',
            'param' => []
        );

        $array[] = array(
            'route' => 'Area-Restrita/Usuario/Alterar-Senha',
            'method' => 'POST',
            'controller' => 'usuario',
            'action' => 'reqAlterarSenha',
            'param' => []
        );

        //Rotas de Produtos

        $array[] = array(
            'route' => 'Area-Restrita/Produto/GerenciarProdutos',
            'method' => 'GET',
            'controller' => 'produto',
            'action' => 'gerenciarProdutos',
            'param' => []
        );

        $array[] = array(
            'route' => 'Area-Restrita/Produto/Alterar',
            'method' => 'POST',
            'controller' => 'produto',
            'action' => 'alterar',
            'param' => []
        );

        $array[] = array(
            'route' => 'Area-Restrita/Produto/ObterPorCliente',
            'method' => 'POST',
            'controller' => 'produto',
            'action' => 'obterPorCliente',
            'param' => []
        );

        $array[] = array(
            'route' => 'Area-Restrita/Produto/AddProduto',
            'method' => 'POST',
            'controller' => 'produto',
            'action' => 'adicionarProduto',
            'param' => []
        );

        //Rotas de Clientes
        $array[] = array(
            'route' => 'Area-Restrita/Cliente/GerenciarClientes',
            'method' => 'GET',
            'controller' => 'cliente',
            'action' => 'gerenciarClientes',
            'param' => []
        );

        $array[] = array(
            'route' => 'Area-Restrita/Cliente/AlterarProduto',
            'method' => 'POST',
            'controller' => 'cliente',
            'action' => 'alterarClienteProduto',
            'param' => []
        );

        $array[] = array(
            'route' => 'Area-Restrita/Cliente/RecProduto',
            'method' => 'POST',
            'controller' => 'cliente',
            'action' => 'recClienteProduto',
            'param' => []
        );

        $array[] = array(
            'route' => 'Area-Restrita/Cliente/RemProduto',
            'method' => 'POST',
            'controller' => 'cliente',
            'action' => 'remClienteProduto',
            'param' => []
        );

        //Rotas Importação de Dados
        $array[] = array(
            'route' => 'Admin/Dados/Importacao',
            'method' => 'GET',
            'controller' => 'dados',
            'action' => 'importarDados',
            'param' => []
        );

        $array[] = array(
            'route' => 'Admin/Dados/ImportarDados',
            'method' => 'POST',
            'controller' => 'dados',
            'action' => 'reqImportarDados',
            'param' => []
        );

        $array[] = array(
            'route' => 'Admin/Dados/UploadsImportarDados',
            'method' => 'POST',
            'controller' => 'dados',
            'action' => 'uploadsImportarDados',
            'param' => []
        );

//Rotas de Pedido
        $array[] = array(
            'route' => 'Area-Restrita/Pedido/FazerPedido',
            'method' => 'GET',
            'controller' => 'pedido',
            'action' => 'fazerPedido',
            'param' => []
        );

        $array[] = array(
            'route' => 'Area-Restrita/Pedido/remTodosProdutos',
            'method' => 'POST',
            'controller' => 'pedido',
            'action' => 'removerTodosProdutos',
            'param' => []
        );

        $array[] = array(
            'route' => 'Area-Restrita/Pedido/remProduto',
            'method' => 'POST',
            'controller' => 'pedido',
            'action' => 'removerProduto',
            'param' => []
        );

        $array[] = array(
            'route' => 'Area-Restrita/Pedido/LimparPedido',
            'method' => 'POST',
            'controller' => 'pedido',
            'action' => 'limparPedido',
            'param' => []
        );

        $array[] = array(
            'route' => 'Area-Restrita/Pedido/EnviarPedido',
            'method' => 'POST',
            'controller' => 'pedido',
            'action' => 'reqFazerPedido',
            'param' => []
        );

        $array[] = array(
            'route' => 'Area-Restrita/Pedido/VisualizarPedidos',
            'method' => 'GET',
            'controller' => 'pedido',
            'action' => 'visualizarPedidos',
            'param' => []
        );

        $array[] = array(
            'route' => 'Area-Restrita/Pedido/MostrarMais',
            'method' => 'POST',
            'controller' => 'pedido',
            'action' => 'mostrarMais',
            'param' => []
        );

        $array[] = array(
            'route' => 'Area-Restrita/Pedido/DetalhesPedido',
            'method' => 'POST',
            'controller' => 'pedido',
            'action' => 'detalhesPedido',
            'param' => []
        );

        $array[] = array(
            'route' => 'Area-Restrita/Pedido/BuscarDocumentos',
            'method' => 'POST',
            'controller' => 'pedido',
            'action' => 'buscarDocumentos',
            'param' => []
        );

        $array[] = array(
            'route' => 'Area-Restrita/Pedido/BuscarMaisDocumentos',
            'method' => 'POST',
            'controller' => 'pedido',
            'action' => 'buscarMaisDocumentos',
            'param' => []
        );
        
        //Rotas Config
        $array[] = array(
            'route' => 'Area-Restrita/Config/Monetario',
            'method' => 'POST',
            'controller' => 'home',
            'action' => 'modificaMonetario',
            'param' => []
        );

        /*Setando na propriedades as rotas definidas à cima.*/
        $this->setRoutes($array);
    }
}