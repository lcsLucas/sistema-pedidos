<?php
if (! defined('ABSPATH') || !isset($_SESSION["UsuarioCodigo"])){
    header("Location: /");
    exit();
}

    require_once ABSPATH.'/App/FuncoesGlobais/paginacao.php';
?>
<div class="panel panel-primary">
	<div class="panel-body">
		<div class="table-responsive">
	        <table style="margin:0" class="table table-condensed table-striped table-hover">
	        	<tr>
	        		<td class="text-center definirBackHeader textoHeader" colspan="3">Lista dos Produtos</td>
	        	</tr>
	            <tr>
	                <th class="col-xs-1 text-center">#</th>
	                <th class="col-xs-6">Descrição <span class="caret"></span></th>
	                <th class="col-xs-5 text-center">Preço</th>
	            </tr>
	            <?php
	                if(empty(count($this->dados->resultado))){
	            ?>
		            <tr>
		                <td colspan="4" class="text-center">
		                    Nenhum Produto Cadastrado
		                </td>
		            </tr>
		        <?php
	                } else{
	                    foreach ($this->dados->resultado as $prod) {
	            ?>
		        <tr>
	                <td class="text-center"><?= $prod["prod_codigo"] ?></td>
	                <td><a href="javascript:;" data-alterar="aAlterar" data-id="<?= $prod["prod_codigo"] ?>" data-descricao="<?= str_replace("\"", "''", $prod["prod_descricao"]);  ?>" data-preco="<?= number_format($prod["prod_preco"], 2, ',', '.') ?>" data-toggle="modal" data-target="#myModal" ><?= $prod["prod_descricao"] ?><img style="margin-left:7px" src="/Public/img/img_alter.png" /></a></td>
	                <td class="text-center"><?= number_format($prod["prod_preco"], 2, ',', '.') ?></td>
	            </tr>
	            <?php
	                    }//end Foreach
	                }//end Else ?>
	        </table>
	    </div>
	</div>
</div>
<!-- Paginação da tabela -->
<nav aria-label="Page navigation">
    <ul style="margin:0" class="pagination pull-right">
        <?= paginacao($this->dados->totalProdutos, $this->dados->itensPagina, $this->dados->numPagExibe); ?>
    </ul>
</nav>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form action="#" method="post" id="formProd" autocomplete="off" name="formProd">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" id="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Alterar Preço do Produto</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="txtToken" id="txtToken" value="<?= $_SESSION["token"] ?>" />
                    <input type="hidden" name="txtCodigo" id="txtCodigo" value="" />
					<div class="form-group">
                        <label class="control-label" for="txtPrecoAtual">Preço Atual:</label>
                        <div class="input-group">
                        	<span class="input-group-addon">R$</span>
                        	<input type="text" readonly autocomplete="off" title="Esse Campo Não Pode Ser Alterado" id="txtPrecoAtual" name="txtPrecoAtual" class="form-control" required />                    	</div>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="txtPrecoNovo">Novo Preço:</label>
                        <div class="input-group">
                        	<span class="input-group-addon">R$</span>
                        	<input type="tel" autocomplete="off" title="Informe Apenas Valores Numéricos" id="txtPrecoNovo" name="txtPrecoNovo" maxlength="10" class="form-control" required />
                    	</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="btnEnviarProd" name="btnEnviarProd">Gravar</button>
                </div>
            </div>
        </form>
    </div>
</div>