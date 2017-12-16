<?php

namespace App\model;

use App\dao\DadoDAO;
use App\model\Retorno;

if (! defined('ABSPATH')){
    header("Location: /");
    exit();
}

class Dado
{
	private $retorno;
	private $inserido;

	public function setRetorno($codigo = 0, $tp = 0, $msg = ""){
        $this->retorno = new Retorno();
        $this->retorno->setRetorno( $codigo , $tp , $msg );
    }

    public function getInserido()
    {
    	return $this->inserido;
    }

    public function getRetorno() {
		return $this->retorno;
	}

	public function obterTodasTabelas()
	{
		$DadoDao = new DadoDAO();
		$result = $DadoDao->obterTodasTabelas();
		if(!empty($result)) :
			return $result;
		else :
			$this->setRetorno($DadoDao->getRetorno()->getCodigo(),$DadoDao->getRetorno()->getTipo(),$DadoDao->getRetorno()->getMensagem());
		endif;

		return null;
	}

	public function importarDados($tabela, $metodo, $caminho_arquivo)
	{
		$DadoDao = new DadoDAO();

		if (file_exists($caminho_arquivo)) :
			if ($DadoDao->verificarTabela($tabela) || $tabela === "tudo") :
				if(!empty($this->processaArquivo($tabela, $metodo, $caminho_arquivo))) :
					return true;
				else :
					$this->reportarErroEmail($tabela, $metodo, $this->getRetorno()->getMensagem());
				endif;
			else :
				$this->setRetorno( 0 , 3 , "Erro: A Tabela Informada Está Incorreta. tabela: ".$tabela." - metodo: ".$metodo );
			endif;
		else :
			$this->setRetorno( 0 , 3 , "Erro: Arquivo Não Existe No Servidor" );
		endif;
		return null;
	}

	private function processaArquivo($tabela, $metodo, $caminho_arquivo)
	{
		$i = 0;
		$num_coluna = 0;
		$erro = false;
		$this->inserido = 0;

		//ftell($fp) ->ponteiro atual do arquivo
		//fseek(...) -> setar ponteiro do arquivo

		if (strcasecmp($metodo, "zerar") !== 0) :
			$arquivo = fopen($caminho_arquivo, "r");

			if ($arquivo) :
				$dados = array();
				$linha = fgetcsv($arquivo, 1024, ";");
				if ($linha === null) {
					$erro = true;
					$this->setRetorno( 0 , 3 , "Erro: Não Foi Possível Ler o Arquivo." );
				}

				while ($linha !== false && !$erro) {
					$valores = array_filter($linha);//limpar resultado

					if (!empty($valores)) :
						if ($i === 0) :
							$num_coluna = count($valores);
						else :
							if (count($valores) === $num_coluna) :
								$dados[] = $valores;
								if (count($dados) >= 300) :
									if(!empty($this->processaLoteBanco($tabela, $metodo, $dados))) :
										$this->inserido += count($dados);
										$dados = array();
									else :
										$erro = true;
									endif;
								endif;
							else :
								$erro = true;
								$this->setRetorno( 0 , 3 , "Erro: A Linha ".($i+1)." não contém o mesmo número de colunas que o cabeçalho." );
							endif;
						endif;
						$i++;
					endif;
					if (!$erro) {
						$linha = fgetcsv($arquivo, 1024, ";");
						$erro = ($linha === null) ? true : $erro;
					}
				}

				if (!$erro && count($dados) > 0) {
					if(!empty($this->processaLoteBanco($tabela, $metodo, $dados))) :
						$dados = array();
						$this->inserido += count($dados);
					else :
						$erro = true;
					endif;
				}
			else
				$erro = true;
			endif;
		else :
			$this->processaLoteBanco($tabela, $metodo, null);
		endif;

		unlink($caminho_arquivo);

		return !$erro;
	}

	private function processaLoteBanco($tabela, $metodo, $dados)
	{
		$DadoDao = new DadoDAO();

		if (strcasecmp($metodo, "insert") === 0) :
			$result = $DadoDao->insertLote($tabela, $dados);
			if (!empty($result)) :
				return $result;
			else :
				$this->setRetorno($DadoDao->getRetorno()->getCodigo(),$DadoDao->getRetorno()->getTipo(),$DadoDao->getRetorno()->getMensagem());
			endif;

		elseif (strcasecmp($metodo, "update") === 0) :
			if (empty($DadoDao->alterarLote($tabela, $dados))) :
				$this->setRetorno($DadoDao->getRetorno()->getCodigo(),$DadoDao->getRetorno()->getTipo(),$DadoDao->getRetorno()->getMensagem());
			else :
				return true;
			endif;

		elseif (strcasecmp($metodo, "delete") === 0) :
			if (empty($DadoDao->deleteLote($tabela, $dados))) :
				$this->setRetorno($DadoDao->getRetorno()->getCodigo(),$DadoDao->getRetorno()->getTipo(),$DadoDao->getRetorno()->getMensagem());
			else :
				return true;
			endif;

		elseif (strcasecmp($metodo, "zerar") === 0) :			
			if (strcasecmp($tabela, "tudo") === 0) :
				if (empty($DadoDao->zerarTudo())) :
					$this->setRetorno($DadoDao->getRetorno()->getCodigo(),$DadoDao->getRetorno()->getTipo(),$DadoDao->getRetorno()->getMensagem());
				else :
					$this->zerarDocumentos();
					return true;
				endif;
			else :
				if (empty($DadoDao->zerarTabela($tabela))) :
					$this->setRetorno($DadoDao->getRetorno()->getCodigo(),$DadoDao->getRetorno()->getTipo(),$DadoDao->getRetorno()->getMensagem());
				else :
					return true;
				endif;
			endif;

		elseif (strcasecmp($metodo, "delete_all_insert") === 0) :
			if (empty($DadoDao->deleteAllInsertLote($tabela))) :
				$this->setRetorno($DadoDao->getRetorno()->getCodigo(),$DadoDao->getRetorno()->getTipo(),$DadoDao->getRetorno()->getMensagem());
			else :
				$DadoDao->desconectar();
				$result = $DadoDao->insertLote($tabela, $dados);
				if (!empty($result)) :
					return $result;
				else :
					$this->setRetorno($DadoDao->getRetorno()->getCodigo(),$DadoDao->getRetorno()->getTipo(),$DadoDao->getRetorno()->getMensagem());
				endif;
			endif;

		elseif (strcasecmp($metodo, "delete_insert") === 0) :
			if (empty($DadoDao->deleteLote($tabela, $dados))) :
				$this->setRetorno($DadoDao->getRetorno()->getCodigo(),$DadoDao->getRetorno()->getTipo(),$DadoDao->getRetorno()->getMensagem());
			else :
				$DadoDao->desconectar();
				$result = $DadoDao->insertLote($tabela, $dados);
				if (!empty($result)) :
					return $result;
				else :
					$this->setRetorno($DadoDao->getRetorno()->getCodigo(),$DadoDao->getRetorno()->getTipo(),$DadoDao->getRetorno()->getMensagem());
				endif;
			endif;
		endif;

		return false;
	}

	public function zerarDocumentos()
	{
		$dir = array_diff(scandir(PATH_PDFS, 1), array('..', '.'));//pega dos arquivos e diretorio

		foreach ($dir as $key => $arquivo) {
			if (is_file(PATH_PDFS.$arquivo)) :
				unlink(PATH_PDFS.$arquivo);
			endif;
		}
	}

	public function uploadsImportarDados()
	{
		$this->setRetorno( 0 , 3 , "Não Encontrado Nenhum Arquivo Para Ser Importado." );

		$extensao = 'csv';
		$tipo_arquivo = 'text/plain';
		$caminho_comandos = ABSPATH."/Public/arquivos/comandos/";

		$retorno = "";
		$erro = false;

		$arquivos = array();
		$dir = array_diff(scandir($caminho_comandos, 1), array('..', '.'));//pega dos arquivos e diretorio da pasta pdfs
		foreach ($dir as $key => $arquivo) {
			if (is_file($caminho_comandos.$arquivo)) :
				$arr = explode('.', $arquivo);
				$ext = strtolower(end($arr));

				if (strcmp(mime_content_type($caminho_comandos.$arquivo),$tipo_arquivo) === 0 && strcmp($ext, $extensao) === 0) :
					$partes = array_filter(explode("-", $arquivo));
					$metodo = "";

					if (count($partes) === 2) :
						$nome_arquivo = substr($partes[1], 0, -4);
						if (strcasecmp($partes[0], "inserir") === 0) :
							$metodo = "insert";
						elseif(strcasecmp($partes[0], "atualizar") === 0) :
							$metodo = "update";
						elseif(strcasecmp($partes[0], "deletar") === 0) :
							$metodo = "delete";
						elseif(strcasecmp($partes[0], "zerar") === 0) :
							$metodo = "zerar";
						else :
							$this->setRetorno( 0 , 3 , "Não Informado o Metodo de Importação." );
						endif;

						if (strcmp("zerar", $metodo) === 0) :
							unlink($caminho_comandos.$arquivo);
							$this->processaLoteBanco($nome_arquivo, $metodo, null);
						else :
							$result = $this->importarDados($nome_arquivo, $metodo, $caminho_comandos.$arquivo);
							if(empty($result)) :
								$erro = true;
								$retorno .= "Erro no arquivo: $arquivo, ao executar o $metodo";
								$retorno .= "<br /><br />";
								$retorno = "Mensagem retornada: ".$this->getRetorno()->getMensagem();
								$retorno .= "<br /><br />";
							endif;
						endif;
					endif;
				endif;
			endif;
		}
		$this->getRetorno()->setMensagem($retorno);

		return !$erro;
	}

	/**
	 * Envia os detalhes do erro por email causado
	 */
	public function reportarErroEmail($tabela, $metodo, $msg)
	{
		if(PHP_OS === "Linux")
			$quebra_linha = "\n"; //Se for Linux
        elseif(PHP_OS === "WINNT")
        	$quebra_linha = "\r\n"; // Se for Windows

		$remetente = "lucas.tarta@hotmail.com";
		$emitente  = "lucas.tarta.lcs@gmail.com";
		$assunto   = "Alerta no Site";

		$mensagem = "";
		$mensagem .= "Não Foi Possível Executar o comando: <b>$metodo</b> Na tabela <b>$tabela</b>.";
		$mensagem .= "<br /><br />";
		$mensagem .= "<p><b>Mensagem Recebida:<b><p>";
		$mensagem .= "<br /><br />";
		$mensagem .= wordwrap($msg,70);

		// Montando o cabeçalho da mensagem
		$headers = "MIME-Version: 1.1".$quebra_linha;
		$headers .= "Content-type: text/html; charset=utf-8".$quebra_linha;
		$headers .= "From: ".$emitente.$quebra_linha;
		$headers .= "Return-Path: " . $emitente.$quebra_linha;

		$envio = mail($remetente, $assunto, $mensagem, $headers, "-r". $emitente);

		if ($envio) :
			$this->getRetorno()->setMensagem($this->getRetorno()->getMensagem()."<br /><br />Um Email foi enviado para $remetente, com esses detalhes do erro.");
		else :
			$this->getRetorno()->setMensagem($this->getRetorno()->getMensagem()."<br /><br />Não Foi Possível Enviar um Email para $remetente, com esses detalhes do erro.");
		endif;
	}
}
