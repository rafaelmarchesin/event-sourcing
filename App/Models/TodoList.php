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

        $task = isset($_POST['task']) ? $_POST['task'] : null;
        
        //Query responsável por gravar a nova tarefa no Banco de Dados
        $query = 'INSERT INTO todo_list_table (task) VALUES ("'. $task .'")';
        
        //Se o Get não receber nada, ele não roda a Query.
        if ($task != null) {

            //Cria a tarefa no banco de dados
            $db->query($query);

            //Atualiza o task_id com o mesmo valor do id da tarefa atual
            $set_task_id = $db->query('SELECT MAX(id) FROM todo_list_table;');
            $set_task_id = mysqli_fetch_array($set_task_id);
            $db->query('UPDATE todo_list_table SET task_id =' . $set_task_id['MAX(id)'] . ' WHERE id = ' . $set_task_id['MAX(id)']);

            //Cria a tarefa na tabela de projeções a partir da versão criada na tabela de eventos
            $query_new_task = 'SELECT * FROM todo_list_table WHERE task_id = ' . $set_task_id['MAX(id)'] .' AND version = (SELECT MAX(version) FROM todo_list_table WHERE task_id = ' . $set_task_id['MAX(id)'] . ');';
            $new_task = $db->query($query_new_task);
            $new_task = mysqli_fetch_array($new_task);

            $db->query('INSERT INTO todo_list_projections (
                task_id, 
                version, 
                task, 
                done
            ) 
            VALUES (
                "'. $new_task['task_id'] .'", 
                ' . $new_task['version'] . ', 
                "' . $new_task['task'] . '", 
                ' . $new_task['done'] . '
            )');

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

            /**
             * As querys abaixo são responsáveis por guardar os dados na tabela de projeções
            */
            $query_recent_version = 'SELECT * FROM todo_list_table WHERE task_id = ' . $task .' AND version = (SELECT MAX(version) FROM todo_list_table WHERE task_id = ' . $task . ');';
            $recent_version = $db->query($query_recent_version);
            $recent_version = mysqli_fetch_array($recent_version);
            
            //Usado apenas para realizar a validação
            $task_in_projections = $db->query('SELECT * FROM todo_list_projections WHERE task_id =' . $task);
            $task_in_projections = mysqli_fetch_array($task_in_projections);

            if ($task_in_projections != null)
            {
                //Se não for nulo, apenas atualiza o registro na tabela projections
                $db->query('UPDATE todo_list_projections SET version = ' . $recent_version['version'] . ', done = ' . $recent_version['done'] . '  WHERE task_id=' . $task . ';');

            } else {
                //Se for nulo, cria um novo registro na tabela projections
                $db->query('INSERT INTO todo_list_projections (
                    task_id, 
                    version, 
                    task, 
                    done
                ) 
                VALUES (
                    "'. $recent_version['task_id'] .'", 
                    ' . $recent_version['version'] . ', 
                    "' . $recent_version['task'] . '", 
                    ' . $recent_version['done'] . '
                )');
            }

        }
    }
}