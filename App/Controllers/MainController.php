<?php

namespace App\Controllers;

use App\Models\TodoList;

//Esta classe tem como objetivo receber as rotas das páginas principais
class MainController
{
    public function __construct()
    {
        
    }

    public function index()
    {
        require_once __DIR__ . '/../Views/index.phtml';
    }

    public function insertText()
    {
        require_once __DIR__ . '/../Views/insertText.phtml';
    }

    public function todo()
    {
        $db = new TodoList;
        $db->saveTask();
        $list = $db->getList();

        include_once __DIR__ . '/../Views/todo.phtml';
    }

    public function recebe()
    {
        $db = new TodoList;
        $db->deleteTask();
        $list = $db->getList();
        include_once __DIR__ . '/../Views/recebe.phtml';
    }

    public function apiTasks()
    {
        include_once __DIR__ . '/../Views/api-tasks.phtml';
    }
}