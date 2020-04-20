<?php

namespace App;

use mysqli;

class Conn
{
    public static function getDB()
    {
        return new mysqli("localhost:4406", "root", "root", "todo_list_db");
    }
}