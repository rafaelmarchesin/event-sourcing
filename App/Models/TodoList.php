<?php

namespace App\Models;

use App\Conn;
use mysqli;

class TodoList
{
    private $db;
    private $list;

    /**
     * Método que realiza a conexão com o Bando de Dados.
     * Necessida abstração.
    */
    private function getDB()
    {
        return new mysqli("localhost:4406", "root", "root", "todo_list_db");
    }

    /**
     * Método que recupera os dados contidos no Banco de Dados com a condição
     * de que o valor de "done" seja 0.
    */
    public function getList()
    {
        $db = $this->getDB();
        return $db->query('SELECT * FROM todo_list_table WHERE done = 0 AND version = 1;');
    }

    /**
     * Este método é responsável por gravar a tarefa no Banco de Dados.
    */
    public function saveTask()
    {
        $db = $this->getDB();

        //Identifica o registro que tenha o maior valor de "task_id"
        $max_task_id = $db->query('SELECT MAX(task_id) FROM todo_list_table WHERE version = 1;');

        $max_task_id = mysqli_fetch_array($max_task_id);


        //Adidiona mais uma unidade ao max_task_id
        //O objetivo é gerar um task_id para a nova tarefa inserida no banco de dados
        //O task_id é responsável por controlar todos os registros relacionados à essa tarefa
        $new_task_id = $max_task_id['MAX(task_id)'] + 1;

        $task = isset($_POST['task']) ? $_POST['task'] : null;
        
        //Query responsável por gravar a nova tarefa no Banco de Dados
        $query = 'INSERT INTO todo_list_table (task_id, task) VALUES ("'. $new_task_id .'", "'. $task .'")';
        
        //Se o Get não receber nada, ele não roda a Query.
        if ($task != null) {
            $db->query($query);
        }
    }

    /**
     * Método responsável por alterar o valor de "done" de 0 para 1.
     * Quando está configurado como 1, não aparece para o usuáŕio final,
     * por isso é considerado como se a tarefa tivesse sido deletada.
    */
    public function deleteTask()
    {
        $db = $this->getDB();

        $task = isset($_POST['id']) ? $_POST['id'] : null;
        $task = (int)$task;
        
        //Para implementar o Event Sourcing, não é mais feito o UPDATE, mas, sim, a criação de um novo registro
        //A query abaixo, portanto, foi descartada
        //$query = "UPDATE todo_list_table SET done=1 WHERE id={$task};";

        //A query abaixo é responsável por selecionar o registro com última versão da tarefa
        $task_id = $db->query('SELECT * FROM todo_list_table WHERE task_id = ' . $task .' AND version = (SELECT MAX(version) FROM todo_list_table WHERE task_id = ' . $task . ');');

        //Converte o registro selellcionado em array
        $array_task_id = mysqli_fetch_array($task_id);

        //A query abaixo utiliza os dados do último registro relacionado à tarefa para inserir um novo registro atualizado
        //sendo que "done" igual a "1" significa que a tarefa foi concluída
        $query = 'INSERT INTO todo_list_table (task_id, version, task, done) VALUES ("' . $array_task_id['task_id'] .'",' . ($array_task_id['version'] + 1) . ', "'. $array_task_id['task'] .'", 1)';

        //Se o Get não receber nada, ele não roda a Query.
        if ($task != null) {
            $db->query($query);
        }
    }
}