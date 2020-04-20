window.onload = function(){

    let xhttp = new this.XMLHttpRequest()
    
    getTasks();

    let div_content = document.querySelector('#container')

    function getTasks() {

        xhttp.onreadystatechange = function(){
            if(xhttp.readyState == 4 && xhttp.status == 200){

                let tasks = JSON.parse(xhttp.responseText)

                console.log(tasks)

                let list = ''
                tasks.forEach(function(task) {
                    list += '<p>'
                    list += `<span>Id: ${task['id']} </span><br>`
                    list += `<span>Tarefa: ${task['task']}</span><br>`

                    if (task['done'] == 1){
                        var state = 'Concluído'
                    } else {
                        var state = 'A fazer'
                    }

                    list += `<span>Estado: ${state}</span>`
                    list += '</p>'
                })

                console.log(list)

                div_content.innerHTML = list

            }
        }

        xhttp.open('GET', 'http://localhost:4444/api-tasks', true)
        
        xhttp.send()
    }
}