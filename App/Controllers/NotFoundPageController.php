<?php

namespace App\Controllers;

//Esta classe foi criada ser executada quando uma página não estiver
//especificada na classe Router
class NotFoundPageController
{
    public function __construct()
    {
        
    }

    public function notFoundPage()
    {
        require_once __DIR__ . '/../Views/404.php';
    }
}