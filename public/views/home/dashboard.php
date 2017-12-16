<?php
if (! defined('ABSPATH') || !isset($_SESSION["UsuarioCodigo"])){
    header("Location: /");
    exit();
}
?>
<div class="panel panel-primary">
    <div class="panel-heading">
        Área Administrativa
    </div>
    <div class="panel-body" style="padding:0">
        <div id="content">
            <header>
                <h4>Últimos Pedidos Feitos</h4>
                <?php
                if(isset($_COOKIE["usuario_email"]) && isset($_COOKIE["usuario_senha"]))
                    echo "Existe COOKIE: ".$_COOKIE["usuario_email"];
                ?>
            </header>
        </div>
    </div>
</div>