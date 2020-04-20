let task = document.querySelector('.tarefa')
let div_list = document.querySelector('#lista')

//Instancia a requisição HTTP padrão do Javascript
let xhttp = new this.XMLHttpRequest()

//Função executada a partir do "onclick" presente em cada tarefa
function deleteTask(getid) {

    //método responsável por realizar as alterações de estado na página
    xhttp.onreadystatechange = function(){
        if(xhttp.readyState == 4 && xhttp.status == 200){

            //O xhttp.responseText pega o conteúdo da página de destino
            let task = xhttp.responseText

            let render = task

            div_list.innerHTML = render

        }
    }
    //Abre a requisição HTTP
    xhttp.open('POST', '/recebe', true)

    //Define o id da tarefa como o dado a ser enviado via POST
    let data = new FormData()
    data.append('id', getid)
    
    //Envia o id como método POST
    xhttp.send(data)
}