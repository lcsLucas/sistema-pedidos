<?php
namespace App\model;

use App\dao\FormPagtoDAO;
use App\model\Retorno;

if (! defined('ABSPATH')){
    header("Location: /");
    exit();
}

class FormPagto
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

	public static function obterTodos()
    {
        $formDAO = new FormPagtoDAO();
        $result = $formDAO->obterTodos();
        if(count($result) >= 0) :
            return $result;
        else :
            $this->setRetorno($formDAO->getRetorno()->getCodigo(),$formDAO->getRetorno()->getTipo(),$formDAO->getRetorno()->getMensagem());
            return null;
        endif;
    }
}
