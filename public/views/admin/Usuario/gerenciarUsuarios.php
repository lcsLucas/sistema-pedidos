<?php
if (! defined('ABSPATH') || empty($_SESSION["UsuarioStatus"])){
    header("Location: /");
    exit();
}

    require_once ABSPATH.'/App/FuncoesGlobais/paginacao.php';
?>
<form method="get" class="form-inline">
    <a role="button" id="btnNovoAcesso" class="btn btn-success btn-lg" data-toggle="modal" data-target="#myModal" href="javascript:;">Novo Usuário</a>
</form>
<div class="panel panel-primary">
    <div class="panel-body">
        <div class="table-responsive">
            <table style="margin:0" class="table table-condensed table-striped table-hover">
                <tr>
                    <td class="text-center definirBackHeader textoHeader" colspan="6">Lista de Usuários</td>
                </tr>
                <tr>
                    <th class="col-xs-1 text-center"># <span class="caret"></span></th>
                    <th class="col-xs-5">Nome</th>
                    <th class="col-xs-4">Email</th>
                    <th class="col-xs-1">Status</th>
                    <th class="col-xs-1 text-center">Excluir</th>
                </tr>
                <?php
                    if(empty(count($this->dados->resultado))){
                ?>
                <tr>
                    <td colspan="4" class="text-center">
                        Nenhum Usuário Cadastrado
                    </td>
                </tr>
                <?php
                    } else{
                        foreach ($this->dados->resultado as $usu) {
                ?>
                <tr>
                    <td class="text-center"><?= $usu["usu_codigo"] ?></td>
                    <td><a href="javascript:;" data-alterar="aAlterar" data-id="<?= $usu["usu_codigo"] ?>" data-nome="<?= $usu["usu_nome"] ?>" data-email="<?= $usu["usu_email"] ?>" data-status="<?= $usu["usu_status"] ?>" data-toggle="modal" data-target="#myModal" ><?= $usu["usu_nome"] ?><img style="margin-left:7px" src="/Public/img/img_alter.png" /></a></td>
                    <td><?= $usu["usu_email"] ?></td>
                    <td><?= ($usu["usu_status"]) ? "Admin" : "" ?></td>

                    <?php if ($_SESSION["UsuarioCodigo"] !== $usu["usu_codigo"]) : ?>
                        <td class="text-center"><a href="javascript:Confirmar('<?= $usu["usu_nome"] ?>','<?= $usu["usu_codigo"] ?>')"><img src="/Public/img/img_delete.png" /></a></td>
                    <?php else: ?>
                        <td title="Não Pode Excluir usuário Logado" class="text-center"><img class="desabilitaImg" src="/Public/img/img_delete.png" /></td>
                    <?php endif; ?>
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
        <?= paginacao($this->dados->totalUsuarios, $this->dados->itensPagina, $this->dados->numPagExibe); ?>
    </ul>
</nav>
<!-- Modal para Cadastrar ou Alterar Usuário -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form action="#" method="post" id="formUsu" autocomplete="off" name="formUsu">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" id="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Cadastrar novo usuário</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="txtToken" id="txtToken" value="<?= $_SESSION["token"] ?>" />
                    <input type="hidden" name="txtCodigo" id="txtCodigo" value="0" />
                    <div class="form-group">
                        <label class="control-label" for="txtNome">Nome:</label>
                        <input type="text" title="O Nome é Obrigatório" id="txtNome" name="txtNome" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="txtEmail">Email:</label>
                        <input type="email" title="O Email é Obrigatório" id="txtEmail" name="txtEmail" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="txtSenha">Senha:</label>
                        <input type="password" autocomplete="off" title="A Senha é Obrigatório" id="txtSenha" name="txtSenha" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="txtConfSenha">Confirme a Senha:</label>
                        <input type="password" autocomplete="off" title="Confirmar a Senha é Obrigatório" id="txtConfSenha" name="txtConfSenha" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label for="chkAtivo">Administrador?</label>
                        <input type="checkbox" id="chkAtivo"  name="chkAtivo" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-lg" id="btnEnviarUsu" name="btnEnviarUsu">Gravar</button>
                </div>
            </div>
        </form>
    </div>
</div>