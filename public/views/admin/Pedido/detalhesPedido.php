<?php
if (! defined('ABSPATH') || !isset($_SESSION["UsuarioCodigo"])){
    header("Location: /");
    exit();
}
?>
<article class="text-center" id="exibeDocumentos">

</article>

<article id="exibeDetalhes">
	<div class="table-responsive">
		<input type="hidden" id="codigo-pedido" value="<?= $this->dados->dadosPedido["ped_codigo"] ?>" />
	    <table id="tabela-detalhes" class="table table-striped table-bordered table-hover">
	    	<thead>
	    		<tr>
	    			<td style="color:#FFF; text-align: center !important;" class="definirBackHeader textoHeader" colspan="2">Informações do Pedido</td>
	    		</tr>
	    	</thead>
			<tbody>
				<?php
					if(!empty($this->dados->dadosPedido)) :
				?>
				<tr>
					<th class="col-xs-2">Nº Pedido:</th>
					<td class="col-xs-10"><?= $this->dados->dadosPedido["ped_codigo"] ?></td>
				</tr>
				<tr>
					<th>Data e Hora:</th>
					<td><?= date("d/m/Y H:i:s", strtotime($this->dados->dadosPedido["ped_datahora"])) ?></td>
				</tr>
				<tr>
					<th>Cliente:</th>
					<td><?= $this->dados->dadosPedido["cli_nome"] ?></td>
				</tr>
				<tr>
					<th>Forma Pagto:</th>
					<td><?= $this->dados->dadosPedido["form_descricao"] ?></td>
				</tr>
				<tr>
					<th>Vendedor:</th>
					<td><?= $this->dados->dadosPedido["usu_nome"] ?></td>
				</tr>
				<tr>
					<th>Valor Total:</th>
					<td><?= number_format($this->dados->dadosPedido["Total"], 2, ',', '.') ?></td>
				</tr>
				<?php endif; ?>
			</tbody>
	    </table>
	</div>
</article>

<article id="exibeItens">
	<div class="table-responsive">
        <table id="tabela-itens-pedidos" class="table table-condensed table-striped table-hover">
            <thead>
            	<tr>
            		<td style="color:#FFF;" class="text-center definirBackHeader textoHeader" colspan="6">Informações dos Itens</td>
            	</tr>
                <tr>
                    <th class="center-header-table col-xs-1 text-center">#</th>
                    <th class="center-header-table col-xs-5">Produtos</th>
                    <th class="center-header-table col-xs-2 text-center">Qtde</th>
                    <th class="center-header-table col-xs-2 text-center">Valor Unitário</th>
                    <th class="center-header-table col-xs-2 text-center">SubTotal</th>
                </tr>
            </thead>
           <tbody>
				<?php
					foreach ($this->dados->itensPedido as $key => $itens) :
				?>
			        <tr>
			            <td class="center-column"><?= $key+1 ?></td>
			            <td class="center-column text-left"><?= $itens["prod_descricao"] ?></td>
			            <td class="center-column"><?= $itens["item_qtde"] ?></td>
			            <td class="center-column"><?= number_format($itens["item_preco"], 2, ',', '.') ?></td>
			            <td class="center-column"><?= number_format($itens["item_total"], 2, ',', '.') ?></td>
			        </tr>
				<?php
					endforeach;
				?>
           </tbody>
        </table>
    </div>
</article>