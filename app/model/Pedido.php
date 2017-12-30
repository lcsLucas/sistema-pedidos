<?php

namespace App\model;

use App\dao\PedidoDAO;
use App\model\Retorno;

if (! defined('ABSPATH')){
    header("Location: /");
    exit();
}

class Pedido
{
    private $retorno;

    public function __construct(){}

    public function getRetorno() {
        return $this->retorno;
    }

    public function setRetorno($codigo = 0, $tp = 0, $msg = ""){
        $this->retorno = new Retorno();
        $this->retorno->setRetorno( $codigo , $tp , $msg );
    }

    public function fazerPedido($token, $form, $obs)
    {
        if(strcmp($token, $_SESSION["token"]) === 0) :
            if (!empty($_SESSION["produtos"])) :
                $pedDAO = new PedidoDAO();

                $numPedido = $pedDAO->fazerPedido($form, $obs);
                if (!empty($numPedido) && $numPedido > 0) :
                    return $this->gerarArquivoPedido($numPedido);
                else :
                    $this->setRetorno($pedDAO->getRetorno()->getCodigo(),$pedDAO->getRetorno()->getTipo(),$pedDAO->getRetorno()->getMensagem());
                endif;
            else :
                $this->setRetorno(0,3,"Nenhum Produto Foi Informado");
            endif;
        else :
            $this->setRetorno(0,3,"Token de Autenticação Inválido");
        endif;
        return false;
    }

    public function gerarArquivoPedido($numPedido)
    {
        $pedDAO = new PedidoDAO();
        $pedido = $pedDAO->obterPedido($numPedido);

        $erro = false;
        if (!empty($pedido)) :
            $arq = fopen(PATH_TMP_PEDIDOS.$numPedido.".txt", "w");
            if (!empty($arq)) :
                try
                {
                    $erro = (fwrite($arq,"ped_num:".$numPedido."\r\n") && !$erro) ? $erro : true;
                    $erro = (fwrite($arq,"ped_data:".$pedido['ped_datahora']."\r\n") && !$erro) ? $erro : true;
                    if (!empty($_SESSION['config'])) :
                        $erro = (fwrite($arq,"ped_pag:".$pedido['form_codigo']."\r\n") && !$erro) ? $erro : true;
                    endif;
                    $erro = (fwrite($arq,"ped_obs:".$pedido['ped_obs']."\r\n") && !$erro) ? $erro : true;
                    $erro = (fwrite($arq,"ven_cod:".$pedido['usu_codigo']."\r\n") && !$erro) ? $erro : true;
                    $erro = (fwrite($arq,"cli_cod:".$pedido['cli_codigo']."\r\n") && !$erro) ? $erro : true;

                    $pedDAO->desconectar();
                    if (!empty($_SESSION['config'])) :
                        $itens = $pedDAO->obterItensPedido($numPedido);
                    else :
                        $itens = $pedDAO->obterItensPedido2($numPedido);
                    endif;
                    if (!empty($itens)) :
                        foreach ($itens as $key => $produto) {
                            $txtItem = "\r\nprod_cod:".$produto["prod_codigo"];
                            $txtItem .= "\r\nprod_qtde:".$produto["item_qtde"];
                            if (!empty($_SESSION['config'])) :
                                $txtItem .= "\r\nprod_val:".$produto["item_preco"];
                                $txtItem .= "\r\nprod_subT:".number_format($produto["item_preco"] * $produto["item_qtde"],2,',','.');
                            endif;
                            $erro = (fwrite($arq, $txtItem) && !$erro) ? $erro : true;
                        }
                        fclose($arq);
                        $pedDAO->desconectar();
                        /**
                         *Status do pedido: 0 = criado no banco, mas sem o arquivo gerado
                         *                  1 = criado no banco, com o arquivo gerado
                         */
                        if(!$erro) :
                            if(!empty($pedDAO->alterarStatus($numPedido,'1'))) :
                                if (rename(PATH_TMP_PEDIDOS.$numPedido.".txt", PATH_PEDIDOS.$numPedido.".txt")) :
                                    return $numPedido;
                                else :
                                    $erro = true;
                                    $this->setRetorno(0,3,"Erro Ao Criar o Arquivo do Pedido.92");
                                endif;
                            else :
                                $this->setRetorno($pedDAO->getRetorno()->getCodigo(),$pedDAO->getRetorno()->getTipo(),$pedDAO->getRetorno()->getMensagem());
                            endif;
                        endif;
                    else :
                        $this->setRetorno($pedDAO->getRetorno()->getCodigo(),$pedDAO->getRetorno()->getTipo(),$pedDAO->getRetorno()->getMensagem());
                    endif;
                }
                catch (\Exception $e)
                {
                    $this->setRetorno(0,3,"Erro Ao Criar o Arquivo do Pedido.101");
                }
            else :
                $this->setRetorno(0,3,"Erro Ao Criar o Arquivo do Pedido.105");
            endif;
        else :
            $this->setRetorno($pedDAO->getRetorno()->getCodigo(),$pedDAO->getRetorno()->getTipo(),$pedDAO->getRetorno()->getMensagem());
        endif;

        if ($erro) :
            if (file_exists(PATH_TMP_PEDIDOS.$numPedido.".txt")) :
                unlink(PATH_TMP_PEDIDOS.$numPedido.".txt");
            endif;
            if (empty($this->getRetorno())) :
                $this->setRetorno(0,3,"Erro Ao Criar o Arquivo do Pedido.117");
            endif;

            $pedDAO->desconectar();
            if(empty($pedDAO->excluirPedido($numPedido))) :
                $this->setRetorno($pedDAO->getRetorno()->getCodigo(),$pedDAO->getRetorno()->getTipo(),$pedDAO->getRetorno()->getMensagem());
            endif;
        endif;
        return false;
    }

    public function obterItensPedidoExibir($codigoPedido)
    {
        $PedDAO = new PedidoDAO();
        if(!empty($_SESSION['config']))
            $result = $PedDAO->obterItensPedidoExibir($codigoPedido);
        else
            $result = $PedDAO->obterItensPedidoExibir2($codigoPedido);
            
        if(count($result) >= 0) :
            return $result;
        else :
            $this->setRetorno($PedDAO->getRetorno()->getCodigo(),$PedDAO->getRetorno()->getTipo(),$PedDAO->getRetorno()->getMensagem());
            return null;
        endif;
    }

    public function obterPedidoExibir($codPedido)
    {
        $PedDAO = new PedidoDAO();
        if(!empty($_SESSION['config']))
            $result = $PedDAO->obterPedidoExibir($codPedido);
        else
            $result = $PedDAO->obterPedidoExibir2($codPedido);
            
        if(count($result) >= 0) :
            return $result;
        else :
            $this->setRetorno($PedDAO->getRetorno()->getCodigo(),$PedDAO->getRetorno()->getTipo(),$PedDAO->getRetorno()->getMensagem());
            return null;
        endif;
    }

    public function obterPorLimite($pagina, $itens){
        $PedDAO = new PedidoDAO();
        if(!empty($_SESSION['config']))
            $result = $PedDAO->obterTodosLimite($pagina, $itens,$_SESSION["UsuarioStatus"]);
        else
            $result = $PedDAO->obterTodosLimite2($pagina, $itens,$_SESSION["UsuarioStatus"]);
        
        if(count($result) >= 0) :
            return $result;
        else :
            $this->setRetorno($PedDAO->getRetorno()->getCodigo(),$PedDAO->getRetorno()->getTipo(),$PedDAO->getRetorno()->getMensagem());
            return null;
        endif;
    }

    public function obterDocumentos($codigoPedido)
    {
        $PedDAO = new PedidoDAO();

        if (empty($PedDAO->verificaDocumentosPedido($codigoPedido))) :
            $parteArquivo = "pedido";
            $parteArquivo .= str_pad($codigoPedido, 6, "0", STR_PAD_LEFT);

            $arquivos = $this->buscarArquivos($parteArquivo);

            foreach ($arquivos as $key => $value) {
                $PedDAO->desconectar();
                $PedDAO->gravarDocumentoPedido($codigoPedido, $value["arquivo"], $value["tipo"]);
            }
        else :
            $PedDAO->desconectar();
            $arquivos = $PedDAO->obterDocumentosPedido($codigoPedido);
        endif;

        return $arquivos;
    }

    public function obterMaisDocumentos($codigoPedido)
    {
        $PedDAO = new PedidoDAO();

        $parteArquivo = "pedido";
        $parteArquivo .= str_pad($codigoPedido, 6, "0", STR_PAD_LEFT);

        $arqsBD = $PedDAO->verificaDocumentosPedido($codigoPedido);
        if (count($arqsBD) > 0) :
            $arquivos = array();

            $dir = array_diff(scandir(PATH_PDFS, 1), array('..', '.'));//pega dos arquivos e diretorio da pasta pdfs
            foreach ($dir as $key => $arquivo) {
                if (strcmp(mime_content_type(PATH_PDFS.$arquivo),"application/pdf") === 0) :
                    //$nome_arquivo -> pega o numero do pedido, $tipo_codumento -> pega o tipo do documento na string
                    $nome_arquivo = substr($arquivo, 0, 12);
                    $tipo_documento = substr($arquivo, 12, 2);
                    if (strcasecmp($nome_arquivo, $parteArquivo) === 0 && !in_array($arquivo, $arqsBD)) :
                        $arquivos[] = array("arquivo" => $arquivo, "tipo" => $tipo_documento);
                    endif;
                endif;
            }
            foreach ($arquivos as $key => $value) {
                $PedDAO->desconectar();
                $PedDAO->gravarDocumentoPedido($codigoPedido, $value["arquivo"], $value["tipo"]);
            }
        endif;

        return $arquivos;
    }

    private function buscarArquivos($parteArquivo)
    {
        $arquivos = array();

        $dir = array_diff(scandir(PATH_PDFS, 1), array('..', '.'));//pega dos arquivos e diretorio da pasta pdfs
        foreach ($dir as $key => $arquivo) {
            if (is_file(PATH_PDFS.$arquivo) && strcmp(mime_content_type(PATH_PDFS.$arquivo),"application/pdf") === 0) :
                //$nome_arquivo -> pega o numero do pedido, $tipo_codumento -> pega o tipo do documento na string
                $nome_arquivo = substr($arquivo, 0, 12);
                $tipo_documento = substr($arquivo, 12, 2);
                if (strcasecmp($nome_arquivo, $parteArquivo) === 0) :
                    $arquivos[] = array("arquivo" => $arquivo, "tipo" => $tipo_documento);
                endif;
            endif;
        }

        return $arquivos;
    }
}
