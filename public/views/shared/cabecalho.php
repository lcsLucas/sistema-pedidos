<?php
if (! defined('ABSPATH')){
    header("Location: /");
    exit();
}
?>
<header id="cabecalho">
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <?php
                    if(!empty($_SESSION["UsuarioCodigo"])){
                ?>
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <?php
                    }
                ?>
                <a class="navbar-brand pull-left" href="/">Dura-Lex SS</a>
            </div>
            <?php
              if(!empty($_SESSION["UsuarioCodigo"])){
            ?>
            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="/Area-Restrita/Cliente/GerenciarClientes">Gerenciar Clientes</a></li>
                    <?php if(!empty($_SESSION["UsuarioStatus"]) && $_SESSION["UsuarioStatus"] === '1') : ?>
                        <li><a href="/Area-Restrita/Produto/GerenciarProdutos">Gerenciar Produtos</a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" title="Pedidos" data-toggle="dropdown">
                                Pedidos
                                <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="/Area-Restrita/Pedido/FazerPedido">Fazer Pedido</a></li>
                                <li><a href="/Area-Restrita/Pedido/VisualizarPedidos">Vizualizar Pedidos Feitos</a></li>
                            </ul>
                        </li>
                    <?php
                        //Se usuario for Admin mostra os itens do menu acima, senao os itens de baixo
                        else :
                    ?>
                        <li><a href="/Area-Restrita/Pedido/FazerPedido">Fazer Pedido</a></li>
                        <li><a href="/Area-Restrita/Pedido/VisualizarPedidos">Vizualizar Pedidos Feitos</a></li>
                    <?php
                     endif;
                    ?>
                </ul>
                <ul id="navbar2" class="nav navbar-nav navbar-right">
                    <?php
                        //Corta o Nome se For Maior que 7
                        $nome = $_SESSION["UsuarioNome"];
                        if(strlen($nome) > 7){
                            $nome = substr($nome, 0,8);
                            $nome .= "...";
                        }
                    ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" title="<?= $_SESSION['UsuarioNome'] ?>" data-toggle="dropdown">
                            <span class="glyphicon glyphicon-user"></span>
                            <?= $nome ?>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <?php if(!empty($_SESSION["UsuarioStatus"]) && $_SESSION["UsuarioStatus"] === '1'){ ?>
                            <li class="text-center">
                                <label for="ckMonetario">Valor Monetário</label>
                                <br />
                                <label class="switch">
                                    <input class="<?= !empty($_SESSION["config"])? "initil-input-checked" : "" ?>" id="ckMonetario" type="checkbox" <?= !empty($_SESSION["config"])? "checked" : "" ?>>
                                  <span class="slider round"></span>
                                </label>
                            </li>
                            <li role="separator" class="divider"></li>
                            <li><a href="/Area-Restrita/Usuario/GerenciarUsuarios">Gerenciar Usuários</a></li>
                            <li><a href="/Admin/Dados/Importacao">Importação de Dados</a></li>
                            <?php } ?>
                            <li>
                                <input type="hidden" id="txtMonetario" value="<?= !empty($_SESSION["config"])? "1" : "0" ?>" />
                                <a href="/Area-Restrita/Usuario/AlterarSenha">Mudar a Senha</a>
                            </li>
                            <li><a href="/Area-Restrita/Logout">Sair</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <?php
              }
            ?>
        </div>
    </nav>
</header>
