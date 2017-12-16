<?php
if (! defined('ABSPATH') || !isset($_SESSION["UsuarioCodigo"])){
    header("Location: /");
    exit();
}
?>
<article id="parteImportacao" class="panel panel-default">
    <div class="panel-body">
        <form name="formDados" id="formDados" action="/Admin/Dados/ImportarDados" enctype="multipart/form-data" method="POST">
            <div class="form-group">
                <label class="control-label" for="selTabela">Informe a Tabela do Banco:</label>
                <select id="selTabela" name="selTabela" class="form-control" required>
                    <option value="">Selecione uma opção</option>
                    <?php foreach ($this->dados->todasTabelas as $key => $value) {
                    ?>
                        <option value="<?= $value["tabelas"] ?>"><?= $value["tabelas"] ?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label class="control-label" for="txtArq">Informe Um Arquivo de Dados do tipo ".csv":</label>
                <input type="hidden" name="MAX_FILE_SIZE" value="4194304" />
                <input type="file" id="txtArq" required class="form-control" accept=".csv" name="txtArq"/>
            </div>
            <div class="form-group">
                <label class="control-label" for="selMetodo">Metodo de Inclusão dos Dados:</label>
                <select id="selMetodo" required="required" name="selMetodo" class="form-control">
                    <option value="">Selecione uma opção</option>
                    <option value="insert">Inserir Registros</option>
                    <option value="update">Atualizar Registros</option>
                    <option value="delete_insert">Deletar e Inserir Registros</option>
                    <option value="delete_all_insert">Deletar Todos os Registro e Inserir</option>
                    <option value="delete">Deletar Registros</option>
                    <option value="zerar_tabela">Zerar Tabela</option>
                    <option value="zerar_tudo">Zerar Tudo (Pedido, Produto, Cliente)</option>
                </select>
            </div>
            <div class="form-group pull-right">
                <input type="hidden" name="txtToken" id="txtToken" value="<?= $_SESSION["token"] ?>" />
                <a role="button" class="btn btn-default btn-lg" href="javascript:location.reload();">Cancelar</a>
                <input type="submit" class="btn btn-success btn-lg" id="btnEnviar" name="btnEnviar" value="Enviar" />
            </div>
        </form>
    </div>
</article>