document.addEventListener("DOMContentLoaded", () => {
    valida_sessao();
    buscar();
});

document.getElementById("novo").addEventListener("click", () => {
    window.location.href = './novo.php';
});

document.getElementById("logoff").addEventListener("click", () => {
    logoff();
});

async function logoff(){
    const retorno = await fetch("../../../php/_logoff.php");
    const resposta = await retorno.json();
    if(resposta.status == "ok"){
        window.location.href = '../login/';   
    }
}
async function buscar(){
    const retorno = await fetch("../../../php/lojista_get.php");
    const resposta = await retorno.json();
    if(resposta.status == "ok"){
        preencherTabela(resposta.data);    
    }
}

async function excluir(id){
    const retorno = await fetch("../../../php/lojista_excluir.php?id="+id);
    const resposta = await retorno.json();
    if(resposta.status == "ok"){
        alert(resposta.mensagem);
        window.location.reload();    
    }else{
        alert(resposta.mensagem);
    }
}

function preencherTabela(tabela){
    var html = `
        <table class="table table-striped">
            <tr>
                <th> Email </th>
                <th> Senha </th>
                <th> Nome loja </th>
                <th> Telefone </th>
                <th> Ativo </th>
                <th> # </th>
            </tr>`;
    for(var i=0;i<tabela.length;i++){
        html += `
            <tr>
                <td>${tabela[i].email}</td>
                <td>${tabela[i].senha}</td>
                <td>${tabela[i].nome_loja}</td>
                <td>${tabela[i].telefone}</td>
                <td>${tabela[i].ativo}</td>
                <td>
                    <a href='alterar.php?id=${tabela[i].id}'>Alterar</a>
                    <a href='#' onclick='excluir(${tabela[i].id})'>Excluir</a>
                </td>
            </tr>
        `;
    }
    html += '</table>';
    document.getElementById("lista").innerHTML = html;
}
