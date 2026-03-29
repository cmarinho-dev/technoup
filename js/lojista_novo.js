document.addEventListener("DOMContentLoaded", () => {
    valida_sessao();
});

document.getElementById("enviar").addEventListener("click", () => {
    novo();
});

async function novo(){
    var email    = document.getElementById("email").value;
    var senha   = document.getElementById("senha").value;
    var nome_loja   = document.getElementById("nome_loja").value;
    var telefone   = document.getElementById("telefone").value;
    var ativo   = document.getElementById("ativo").value;

    const fd = new FormData();
    fd.append("email", email);
    fd.append("senha", senha);
    fd.append("nome_loja", nome_loja);
    fd.append("telefone", telefone);
    fd.append("ativo", ativo);

    const retorno = await fetch("../../../php/lojista_novo.php",
        {
          method: 'POST',
          body: fd  
        });
    const resposta = await retorno.json();
    if(resposta.status == "ok"){
        // alert("SUCESSO: " + resposta.mensagem);
        window.location.href = "./";
    }else{
        alert("ERRO: " + resposta.mensagem);
    }
}