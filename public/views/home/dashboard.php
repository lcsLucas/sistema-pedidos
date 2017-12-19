<?php
if (! defined('ABSPATH') || !isset($_SESSION["UsuarioCodigo"])){
    header("Location: /");
    exit();
}
?>
<div class="table-responsive">
    <table style="margin:0" id="tabela-vis" class="table table-striped table-hover">
            <tr>
                    <td class="text-center definirBackHeader textoHeader" colspan="5">Últimos Pedidos Realizados</td>
            </tr>
        <tr>
            <th class="col-xs-2 text-center">Data <span class="caret"></span></th>
            <th class="col-xs-5">Cliente</th>
            <th class="col-xs-3 text-center">Valor Total</th>
            <th class="col-xs-1 text-center">Status</th>
            <th class="col-xs-1 text-center"></th>
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
                    <td class="text-center"><?= number_format($ped["Total"], 2, ',', '.') ?></td>
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