<?php
if (! defined('ABSPATH') || !isset($_SESSION["UsuarioCodigo"])){
    header("Location: /");
    exit();
}
    $exibeDiv = (empty($_SESSION['clienteAtual'])) ? "style=\"display:none\"" : "";
?>
<article id="parteCliente" class="panel panel-default">
    <div class="panel-heading">
        <span class="textoHeader">Selecione o Cliente</span>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <select style="width: 100%" id="selCliente" class="form-control select2" required name="selCliente">
                <option value=""></option>
                <?php
                    foreach ($this->dados->todosClientes as $cli) {
                ?>
                    <option value="<?= $cli['cli_codigo'] ?>" <?= (!empty($_SESSION['clienteAtual']) && strcmp($cli['cli_codigo'], $_SESSION['clienteAtual']) === 0) ? "selected" : "" ?> ><?= $cli['cli_nome'] ?></option>
                <?php
                    }
                ?>
            </select>
        </div>
        <div class="text-center">
            <button class="btn btn-success btn-lg" id="btnConfCliente" name="btnConfCliente">Confirmar</button>
            <br /><a style="display:none" role="button" class="reset link link-trocar btn btn-default btn-lg" href="/Area-Restrita/Pedido/LimparPedido">Trocar Cliente</a>
        </div>
    </div>
</article>
<article <?= $exibeDiv ?> id="parteProduto" class="panel panel-default">
    <div class="panel-heading">
        <span class="textoHeader">Escolha os Produtos</span>
    </div>
    <div style="margin-bottom:20px" class="panel-body">
        <form class="form-inline" id="formaddProd" name="formaddProd" method="post" action="#">
            <div class="row">
                <div class="form-group text-center col-sm-6">
                    <label class="control-label" for="selProduto">Produto</label>
                    <select style="width: 100%;" id="selProduto" class="form-control select2" required name="selProduto">
                        <option value=""></option>
                        <?php
                            foreach ($this->dados->todosProdutos as $prod) {
                        ?>
                            <option data-preco="<?= number_format($prod["prod_preco"], 2, ',', '.') ?>" value="<?= $prod['prod_codigo'] ?>"><?= $prod['prod_descricao'] ?></option>
                        <?php
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group text-center col-sm-2">
                    <label class="control-label" for="txtQtde">Qtde</label>
                    <input style="width: 100%" type="tel" class="form-control text-center" id="txtQtde" name="txtQtde" maxlength="4" required />
                </div>
                <div class="form-group text-center col-sm-2">
                    <label class="control-label" for="txtPreco">Valor Unitário</label>
                    <input style="width: 100%" type="tel" readonly class="form-control text-center" id="txtValUni" name="txtValUni" />
                </div>
                <div class="form-group text-center col-sm-2">
                    <label class="control-label" for="txtSubTot">SubTotal</label>
                    <input style="width: 100%" type="tel" readonly class="form-control text-center" id="txtSubTot" name="txtSubTot" />
                </div>
            </div>
            <div class="row">
                <div class="text-center col-sm-12">
                    <input type="submit" class="btn btn-success btn-lg" id="addProduto" name="addProduto" value="Adicionar Produto" />
                </div>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table id="tabela-pedidos" class="table table-condensed table-striped table-hover">
            <thead>
                <tr>
                    <th class="vertical center-header-table col-xs-1">#</th>
                    <th class="vertical center-header-table col-xs-4">Produtos</th>
                    <th class="vertical center-header-table col-xs-2 text-center">Qtde</th>
                    <th class="vertical center-header-table col-xs-2 text-center">Valor Unitário</th>
                    <th class="vertical center-header-table col-xs-2">SubTotal</th>
                    <th class="vertical col-xs-1 text-center"><a class="link-excluir" href="/Area-Restrita/Pedido/remTodosProdutos"><img src="/Public/img/img_delete.png" /><br />Excluir Todos</a></th>
                </tr>
            </thead>
           <tfoot>
                <tr>
                    <td colspan="6" class="text-right"><b>Valor Total: <span id="valorTotal" ><?= !empty($this->dados->listaProdutos) ? $this->dados->listaProdutos[count($this->dados->listaProdutos)-1][5] : "0,00" ?></span></b></td>
                </tr>
           </tfoot>
           <tbody>
                <?php
                    if(empty($_SESSION['produtos'])):
                ?>
                <tr>
                    <td colspan="6">Nenhum Produto Adicionado</td>
                </tr>
                <?php
                    else:
                        foreach ($this->dados->listaProdutos as $key => $prod) {
                ?>
                <tr>
                    <td class="center-column"><?= $key+1 ?></td>
                    <td class="center-column"><?= $prod[1] ?></td>
                    <td class="center-column"><?= $prod[2] ?></td>
                    <td class="center-column"><?= $prod[3] ?></td>
                    <td class="center-column"><?= $prod[4] ?></td>
                    <td><a data-id="<?= $prod[0] ?>" href="/Area-Restrita/Pedido/remProduto"><img title="Excluir Produto" class="exc" alt="Excluir Produto" src="/Public/img/img_delete.png" /><br />Excluir</a></td>
                </tr>
                <?php
                        }//fimForeach
                    endif;
                ?>
           </tbody>
        </table>
    </div>
</article>

<form id="frmEnviarPedido" name="frmEnviarPedido" method="POST" action="#">
    <article <?= $exibeDiv ?> id="parteAdicionais" class="panel panel-default">
        <div class="panel-heading">
            <span class="textoHeader">Dados Complementares</span>
        </div>
        <div class="panel-body">
            <input type="hidden" name="txtToken" id="txtToken" value="<?= $_SESSION["token"] ?>" />
            <div class="form-group">
                <label class="control-label" for="selForm">Forma de Pagto:</label>
                <select style="width: 100%" id="selForm" class="form-control select2" name="selForm">
                    <?php
                        foreach ($this->dados->todasFormPagto as $form) {
                    ?>
                        <option value="<?= $form['form_codigo'] ?>"><?= $form['form_descricao'] ?></option>
                    <?php
                        }
                    ?>
                </select>
            </div>
            <div class="text-center form-group">
                <a id="mostrar" class="btn-lg" name="mostrar" href="#">Adicionar Observação</a>
            </div>
            <div id="camadaefeitos" class="form-group text-right">
                <textarea cols="65" rows="3"  class="form-control" name="txtObs" id="txtObs"></textarea>
                Limite: <span name="remLen" id="remLen">255</span>
            </div>
        </div>
    </article>
    <div <?= $exibeDiv ?> id="botoesPedido" class="row">
        <div class="form-group text-right">
            <a role="button" class="btn btn-default reset btn-lg" href="/Area-Restrita/Pedido/LimparPedido">Cancelar Pedido</a>
            <input type="submit" class="btn btn-success btn-lg" id="btnEnviarPed" name="btnEnviarPed" value="Enviar Pedido" />
        </div>
    </div>
</form>
<form id="formVis" action="/Area-Restrita/Pedido/DetalhesPedido" method="POST" >
    <input type="hidden" name="codigo-pedido" id="codigo-pedido" value="" />
</form>