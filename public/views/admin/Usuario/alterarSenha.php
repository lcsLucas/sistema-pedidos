<?php
if (! defined('ABSPATH') || !isset($_SESSION["UsuarioCodigo"])){
    header("Location: /");
    exit();
}
?>
<div class="panel panel-primary">
	<div class="text-center definirBackHeader textoHeader">
		<span>Tela de Mudança da Senha</span>
	</div>
	<div style="padding:20px" class="panel-body">
		<form method="POST" autocomplete="off" action="#" id="formAltSenha" name="formAltSenha">
			<div class="form-group">
				<label class="control-label" for="txtSenhaAtual">Senha Atual:</label>
				<input autocomplete="off" title="Informe a Senha Atual" type="password" id="txtSenhaAtual" name="txtSenhaAtual" class="form-control" />
				<input type="hidden" name="txtToken" id="txtToken" value="<?= $_SESSION["token"] ?>" />
			</div>
			<div class="form-group">
				<label class="control-label" for="txtNovaSenha">Nova Senha:</label>
				<input autocomplete="off" title="Informe a Nova Senha" type="password" id="txtNovaSenha" name="txtNovaSenha" class="form-control" />
			</div>
			<div class="form-group">
				<label class="control-label" for="txtConfSenha">Confirme a Nova Senha:</label>
				<input autocomplete="off" title="Confirme a Nova Senha" type="password" id="txtConfSenha" name="txtConfSenha" class="form-control" />
			</div>
			<div class="form-group">
				<input type="submit" class="btn btn-success btn-lg pull-right" id="btnEnviar" name="btnEnviar" value="Mudar Senha" />
			</div>
		</form>
	</div>
</div>