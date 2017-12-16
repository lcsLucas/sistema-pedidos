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
					<td class="text-center definirBackHeader textoHeader" colspan="2">Lista dos Clientes</td>
	        	</tr>
	            <tr>
	                <th class="col-xs-6 text-center">#</th>
	                <th class="col-xs-6">Nome <span class="caret"></span></th>
	            </tr>
	            <?php
	                if(empty(count($this->dados->resultado))){
	            ?>
		            <tr>
		                <td colspan="4" class="text-center">
		                    Nenhum Cliente à Listar.
		                </td>
		            </tr>
		        <?php
	                } else{
	                    foreach ($this->dados->resultado as $cli) {
	            ?>
		        <tr>
	                <td class="text-center"><?= $cli["cli_codigo"] ?></td>
	                <td><a href="javascript:;" data-alterar="aAlterar" data-id="<?= $cli["cli_codigo"] ?>" data-nome="<?= str_replace("\"", "''", $cli["cli_nome"]);  ?>" data-toggle="modal" data-target="#myModal" ><?= $cli["cli_nome"] ?><img style="margin-left:7px" src="/Public/img/img_alter.png" /></a></td>
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
        <?= paginacao($this->dados->totalClientes, $this->dados->itensPagina, $this->dados->numPagExibe); ?>
    </ul>
</nav>
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form action="#" method="post" id="formCli" autocomplete="off" name="formCli">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" id="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Mudar Preço de Produto para o </h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="txtToken" id="txtToken" value="<?= $_SESSION["token"] ?>" />
                    <input type="hidden" name="txtCodigo" id="txtCodigo" value="0" />
                    <div class="form-group">
                    	<label class="control-label" for="selProduto">Informe o Produto</label>
                    	<select style="width: 100%" id="selProduto" class="form-control select2" required name="selProduto">
                    		<option value="">Selecione um Produto</option>
                    		<?php
                    			foreach ($this->dados->todosProdutos as $prod) {
                			?>
                    			<option data-preco="<?= number_format($prod["prod_preco"], 2, ',', '.') ?>" value="<?= $prod['prod_codigo'] ?>"><?= $prod['prod_descricao'] ?></option>
                    		<?php
                    			}
                    		?>
                    	</select>
                    </div>
					<div class="form-group">
                        <label class="control-label" for="txtPrecoAtual">Preço Geral:</label>
                        <div class="input-group">
                        	<span class="input-group-addon">R$</span>
                        	<input type="text" readonly autocomplete="off" title="Esse Campo Não Pode Ser Alterado" id="txtPrecoAtual" name="txtPrecoAtual" class="form-control" />
                        </div>
                    </div>
					<div style="display:none" class="form-group mostrar">
                        <label class="control-label" for="txtPrecoDef">Preço Definido:</label>
                        <div class="input-group">
                        	<span class="input-group-addon">R$</span>
                        	<input type="text" readonly autocomplete="off" title="Esse Campo Não Pode Ser Alterado" id="txtPrecoDef" name="txtPrecoDef" class="form-control" />
                        </div>
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
                	<a style="display:none" title="Remover Preço Definido" role="button" class="btn btn-danger mostrar" id="btnRemover" name="btnRemover">Remover</a>
                    <button type="submit" class="btn btn-primary btn-lg" id="btnEnviar" name="btnEnviar">Gravar</button>
                </div>
            </div>
        </form>
    </div>
</div>
