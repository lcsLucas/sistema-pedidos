<?php

namespace App\Controllers;

use ProjetoMvc\Render\Action;
use App\model\Dado;
use App\model\Retorno;

if (! defined('ABSPATH')){
    header("Location: /");
    exit();
}

class DadosController extends Action
{

    public function __construct($param)
    {
        parent::__construct($param);
        /**
         * caminho com o arquivo do layout padrão que todasas paginas dessa controller poderá usar
         */
        $this->layoutPadrao = PATH_VIEWS."shared/layoutPadrao";
    }

	public function importarDados()
	{
        $this->dados->title = "Importação de Dados";
        $this->dados->todasTabelas = (new Dado())->obterTodasTabelas();
    	$this->render('importar_dados');
	}

	public function reqImportarDados()
	{
		$tabela = filter_input(INPUT_POST, "selTabela", FILTER_SANITIZE_SPECIAL_CHARS);
		$metodo = filter_input(INPUT_POST, "selMetodo", FILTER_SANITIZE_SPECIAL_CHARS);
		$token = filter_input(INPUT_POST, "txtToken", FILTER_SANITIZE_SPECIAL_CHARS);

		// Pasta onde o arquivo vai ser salvo
		$_UP['pasta'] = ABSPATH.'/public/arquivos/uploads/';
		// Tamanho máximo do arquivo (em Bytes)
		$_UP['tamanho'] = 1024 * 1024 * 2; // 2Mb
		// Array com as extensões permitidas
		$_UP['extensao'] = 'csv';
		$_UP['tipo_arquivo'] = 'application/vnd.ms-excel';
		//Array com os metodos
		$_UP['metodos'] = array('insert', 'update','delete_all_insert', 'delete_insert', 'delete','zerar_tudo', 'zerar_tabela');

		// Array com os tipos de erros de upload do PHP
		$_UP['erros'][0] = 'Não houve erro';
		$_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
		$_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
		$_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
		$_UP['erros'][4] = 'Erro Desconhecido';

		$arr = explode('.', $_FILES['txtArq']['name']);
		$extensao = strtolower(end($arr));

		if ($_FILES['txtArq']['error'] != 0) :
			die("Não Foi Possível Fazer o Upload do arquivo<br />Erro: "
				.$_UP['erros'][$_FILES['txtArq']['error']]);
			exit;
		elseif(empty($tabela)) :
			die("Não Foi Possível Fazer o Upload do arquivo<br />Erro: "
				."Não Informado Nenhuma Tabela");
			exit;
		elseif (strcmp($token, $_SESSION["token"]) !== 0) :
			die("Não Foi Possível Fazer o Upload do arquivo<br />Erro: "
				."Token de Autenticação Inválido");
			exit;
		elseif ((strcmp($_FILES['txtArq']['type'], $_UP['tipo_arquivo']) !== 0) && (strcmp($extensao, $_UP['extensao']) !== 0)) :
			die("Não Foi Possível Fazer o Upload do arquivo<br />Erro: "
				."Tipo do Arquivo Enviado Inválido");
			exit;
		elseif (!in_array($metodo, $_UP['metodos'])) :
			die("Não Foi Possível Fazer o Upload do arquivo<br />Erro: "
				."Não Informado um metódo de Inserção no Banco Válido");
			exit;
		elseif ($_UP['tamanho'] < $_FILES['txtArq']['size']) :
			die("Não Foi Possível Fazer o Upload do arquivo<br />Erro: "
				."O arquivo Enviado é muito grande, Envie arquivos até 2Mb.");
			exit;
		else :
			$nome_final = time().'_'.$_FILES['txtArq']['name'];

			if (move_uploaded_file($_FILES['txtArq']['tmp_name'], $_UP['pasta'].$nome_final)) :
				$dado = new Dado();
				if (!empty($dado->importarDados($tabela, $metodo, $_UP['pasta'] . $nome_final))) :
					echo "A Importação Foi Realizada Com Sucesso!";
				else :
					echo "Erro Ao Importar os Dados. Inseridos ".$dado->getInserido()." Registro.";
					echo "<br />".$dado->getRetorno()->getMensagem();
				endif;
			else :
				echo "Não foi possível enviar o arquivo, tente novamente";
			endif;
		endif;
	}

	public function uploadsImportarDados()
	{
		$dado = new Dado();
		if (!empty($dado->uploadsImportarDados())) :
			echo "A Importação Foi Realizada Com Sucesso!";
		else :
			echo "Erro Ao Importar os Dados. Inseridos ".$dado->getInserido()." Registro.";
			echo "<br />".$dado->getRetorno()->getMensagem();
		endif;
	}
}