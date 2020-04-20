<?php

namespace App\Models;

use mysqli;

class ApiGetTasks
{
    public function getTasks()
    {
        $db = $this->getDB();
        $tasks = $db->query('SELECT * FROM todo_list_table;');
        $json_tasks = mysqli_fetch_all($tasks, MYSQLI_ASSOC);
        
        return json_encode($json_tasks);
    }

    public function getDB()
    {
        return new mysqli('localhost:4406', 'root', 'root', 'todo_list_db');
    }
}