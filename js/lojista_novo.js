document.addEventListener("DOMContentLoaded", () => {
    valida_sessao();
});

document.getElementById("enviar").addEventListener("click", () => {
    novo();
});

async function novo(){
    var nome_loja    = document.getElementById("nome_loja").value;
    var logradouro   = document.getElementById("logradouro").value;
    var nome_lojista = document.getElementById("nome_lojista").value;
    var cpf          = document.getElementById("cpf").value;
    var cnpj         = document.getElementById("cnpj").value;
    var cep_lojista  = document.getElementById("cep_lojista").value;
    var estado       = document.getElementById("estado").value;
    var cidade       = document.getElementById("cidade").value;
    var bairro       = document.getElementById("bairro").value;
    var numero       = document.getElementById("numero").value;
    var genero       = document.getElementById("genero").value;
    var email        = document.getElementById("email").value;
    var senha        = document.getElementById("senha").value;
    var telefone     = document.getElementById("telefone").value;
    var ativo        = document.getElementById("ativo").value;  

    const fd = new FormData();
    fd.append("nome_loja", nome_loja);
    fd.append("logradouro", logradouro);
    fd.append("nome_lojista", nome_lojista);
    fd.append("cpf", cpf);
    fd.append("cnpj", cnpj);
    fd.append("cep_lojista", cep_lojista);
    fd.append("estado", estado);
    fd.append("cidade", cidade);
    fd.append("bairro", bairro);
    fd.append("numero", numero);
    fd.append("genero", genero);
    fd.append("email", email);
    fd.append("senha", senha);
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