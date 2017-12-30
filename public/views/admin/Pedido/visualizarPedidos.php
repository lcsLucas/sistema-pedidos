<?php
if (! defined('ABSPATH') || !isset($_SESSION["UsuarioCodigo"])){
    header("Location: /");
    exit();
}
 ?>
 <article class="panel panel-primary">
	<div class="panel-body">
		<div class="table-responsive">
	        <table style="margin:0" id="tabela-vis" class="table table-striped table-hover">
	        	<tr>
	        		<td class="text-center definirBackHeader textoHeader" colspan="5">Últimos Pedidos Realizados</td>
	        	</tr>
	            <tr>
                        <?php
                            if (!empty($_SESSION["config"])) :
                        ?>
	                <th class="col-xs-2 text-center">Data <span class="caret"></span></th>
	                <th class="col-xs-5">Cliente</th>
	                <th class="col-xs-3 text-center">Valor Total</th>
	                <th class="col-xs-1 text-center">Status</th>
	                <th class="col-xs-1 text-center"></th>
                        <?php
                            else :
                        ?>
	                <th class="col-xs-2 text-center">Data <span class="caret"></span></th>
	                <th class="col-xs-8">Cliente</th>
	                <th class="col-xs-1 text-center">Status</th>
	                <th class="col-xs-1 text-center"></th>
                        <?php
                            endif;
                        ?>
	            </tr>
	            <?php
	                if(empty(count($this->dados->resultado))){
	            ?>
		            <tr>
		                <td colspan="4" class="text-center">
		                    Nenhum Pedido Feito Ultimamente
		                </td>
		            </tr>
		        <?php
	                } else{
	                    foreach ($this->dados->resultado as $ped) {
	            ?>
	            	<tr>
	            		<td class="text-center"><?= date("d/m/Y H:i:s", strtotime($ped["ped_datahora"])) ?></td>
	            		<td><?= $ped["cli_nome"] ?></td>
                                <?php
                                    if (!empty($_SESSION["config"])) :
                                ?>
	            		<td class="text-center"><?= number_format($ped["Total"], 2, ',', '.') ?></td>
                                <?php
                                    endif;
                                ?>
	            		<?php
	            			$icon = "";
	            			if ($ped["ped_status"] == "0") :
	            				$icon = "<img title=\"Pedido não criado corretamente.\" src=\"/Public/img/img_warning.png\" /><br />Pendente";
	            			elseif($ped["ped_status"] == "1"):
	            				$icon = "<img title=\"Pedido Enviado\" src=\"/Public/img/img_check.png\" /><br />Enviado";
	            			endif;
	            		?>
	            		<td class="text-center"><?= $icon ?></td>
	            		<td class="text-center btn-lg"><a data-id="<?= $ped["ped_codigo"] ?>" href="">Detalhes</a></td>
	            	</tr>
	            <?php
	                    }//end Foreach
	                }//end Else ?>
	        </table>
	    </div>
	</div>
	<a class="pull-right  btn-lg" data-pagina="<?= $this->dados->pagina+1 ?>" id="mostrarMais" href="">Mostrar Mais</a>
</article>
<form id="formVis" action="/Area-Restrita/Pedido/DetalhesPedido" method="POST" >
	<input type="hidden" name="codigo-pedido" id="codigo-pedido" value="" />
</form>