<?php
/**
 * PHP version 7
 *
 * @category PHP
 * @package  PHP_ProjetoMVC
 * @author   Lucas S. Rosa <lucas.tarta@hotmail.com>
 */
namespace ProjetoMVC\init;

if (! defined('ABSPATH')){
    header("Location: /");
    exit();
}

/**
 * Classe responsavel pelo funcionanmento do padrão MVC
 * Captura todas as urls passadas e define quem deve chamar para responder a requisição, e qual Action
 * que deve chamar para ser executada.
 */
abstract class Bootstrap
{
    /**
     * propriedade privada
     * @var array -> contém todas as rotas do site.
     */
    private $routes;

    /**
     * Chama a função initRoutes que define
     * as rotas do site e chama o metodo 'Run', que define a Controller e a Action a ser executada.
     */
    public function __construct()
    {
        $this->initRoutes();
        $this->run($this->getUrl());
    }

    /**
     * Descrição: Define todas as rotas que o site terá,
     * e seta na propriedade 'routes' da classe.
     */
    abstract protected function initRoutes();

    /**
     * Seta a propriedade 'routes' da classe
     * @param array 'routes' [Recebe um array de rotas]
     * @return  void
     */
    protected function setRoutes(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Captura a URL que o usuário acessou no site.
     * @return [string] [retorna o caminho da URI, com excessão do dominío]
     */
    protected function getUrl()
    {
        return filter_var(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), FILTER_SANITIZE_URL);
    }

    /**
     * Procura no array de rotas do site, se a url acessada existe,
     * caso encontre, instancia a classe controller responsavel
     *dessa rota e executa a Action responsavel pela requisição.
     *
     * @param  string $url caminho da url
     */
    protected function run($url)
    {
        $vet_param = [];
        //a função 'trim', removerá espaços e/ou '/'
        // no começo da url, deixando só o nome da rota
        $url = ltrim($url,'/');

        /**
         * Se por acaso não encontrar nenhuma rota no site, que corresponde ao que o usuário passou,
         * então já é definido que será instanciado a controller 'home', e será chamado a Action 'pageError404'
         * a qual exibe a página de error 404, que é de página não encontrada.
         */
        $rota['controller'] = 'home';
        $rota['action'] = 'pageError404';

        //busca a rota informada pela url no array de rotas do site
        foreach ($this->routes as $key => $value) {
            $urlaux = substr($url, 0, strlen($value['route']));
            if (strcmp($urlaux, $value['route']) === 0) :
                if (strcmp($value['method'], $_SERVER["REQUEST_METHOD"]) === 0) :                    
                    $urlaux = substr($url, strlen($value['route']));
                    echo substr($url, strlen($value['route'])." - Bootstrap.php - Arrumar aqui");
                    $url_param = array_filter(explode("/", $urlaux));
                    if (count($value['param']) === count($url_param)) :
                        for ($i=0; $i < count($value['param']); $i++) {
                            $vet_param[$value['param'][$i]] =  $url_param[$i];
                        }

                        $rota['controller'] = $value['controller'];
                        $rota['action'] = $value['action'];
                        break;
                    endif;
                endif;
            endif;
        }
        /** Segue abaixo um código onde a parte ai de cima está funcionando 
        obs: tomar cuiddo por esse código debaixo não funciona exatamente como o decima, então prestar atenção pra ver as diferenças
            
            $urlaux = substr($url, 0, strlen($rotas[$i]["url"]));
            if ((strcmp($urlaux, $rotas[$i]["url"])) === 0) : 
                $urlaux = substr($url, strlen($rotas[$i]["url"]));
                $urlaux = trim($urlaux,"/");                
                $url_param = array_filter(explode("/", $urlaux));

                if ($rotas[$i]["numParam"] === count($url_param)) {
                    $achou = true;
                    $pagina = $rotas[$i]["pagina"];
                }
                
        **/

        /*Instancia a controller da rota, e chama a action passada*/
        /*obs: caso nao tenha encontrado nehuma rota, chama a action da página 'error404'
        da controller 'home', a qual foi definido lá em cima.
        */
        $class = "App\\controllers\\"
        .ucfirst($rota['controller']."Controller");
        $rota['controller'] = new $class($vet_param);
        $action = $rota['action'];
        $rota['controller']->$action();
    }
}
