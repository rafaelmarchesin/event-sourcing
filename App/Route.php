<?php

namespace App;

//Classe responsável por receber a requisição do "index.php"
class Route
{
    private $url;
    private $send_controller = []; //Criei esse array para ser usado no swich/case

    public function __construct()
    {
        //Chama e instancia a Classe de controle a partir do cainho acessado pelo navegador
        $class = "App\\Controllers\\" . $this->callController(); //Cria o caminho da classe
        $new_class = new $class; //Instancia a classe

        //Executa o método referente ao caminho
        $method = $this->callMethod(); //Chama a string referente ao método
        $new_class->$method(); //Basta colocar "()" na frente da string para ela se tornar método

    }

    //Método responsável por definir as rotas válidas na nossa aplicação
    public function routeController()
    {
        $this->url = $this->getURL();
        $this->url = '/' . str_replace('/', '', $this->url); //Esse tratamento serve para tirar a última barra do caminho do navegador
        $header = 'Location: ' . $this->url;

        switch ($this->url)
        {
            //Os novos caminhos deverão ser explicitados aqui
            case '/':
                $this->send_controller = [
                    'controller' => 'MainController',
                    'method' => 'index'
                ];
                return $this->send_controller;
                break;
            
            //Caminho para a página de publicação de conteúdo
            case "/publish":
                $this->send_controller = [
                    'controller' => 'MainController',
                    'method' => 'insertText'
                ];
                return $this->send_controller;
                break;

            case "/todo":
                $this->send_controller = [
                    'controller' => 'MainController',
                    'method' => 'todo'
                ];
                return $this->send_controller;
                break;
            
            case "/recebe":
                $this->send_controller = [
                    'controller' => 'MainController',
                    'method' => 'recebe'
                ];
                return $this->send_controller;
                break;

            case "/api-tasks":
                $this->send_controller = [
                    'controller' => 'MainController',
                    'method' => 'apiTasks'
                ];
                return $this->send_controller;
                break;


            //Caso o caminho acessado seja inválido, é executada
            //uma página específica para caminhos inválidos
            default:
                $this->send_controller = [
                    'controller' => 'NotFoundPageController',
                    'method' => 'notFoundPage'
                ];
                return $this->send_controller;
                break;
        }
    }

    //Método que retorna o nome do Controller
    protected function callController()
    {
        return $this->routeController()['controller'];
    }

    //Método que retorna o nome do método que será executado
    protected function callMethod()
    {
        return $this->routeController()['method'];
    }

    //Método responsável por pegar o caminho no navegador
    protected function getURL()
    {
        return parse_url($_SERVER['REQUEST_URI'])['path'];
    }
}