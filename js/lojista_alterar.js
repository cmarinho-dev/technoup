// Fase 1
// a) PEGA o ID da URL
// b) Requisita o BACKEND por GET
// c) Preenche o formulário com os dados do BACKEND
// ----------------------------------------------
document.addEventListener("DOMContentLoaded", () => {
  // pega a URL e armazena em um const
  // busca nessa URL a variável id e armazana no const id.
  valida_sessao();
  const url = new URLSearchParams(window.location.search);
  const id = url.get("id");
  buscar(id);
});

async function buscar(id) {
  const retorno = await fetch("../../../php/lojista_get.php?id=" + id);
  const resposta = await retorno.json();
  if (resposta.status == "ok") {
    // alert("SUCESSO:" + resposta.mensagem);
    var registro = resposta.data[0];
    document.getElementById("email").value = registro.email;
    document.getElementById("senha").value = registro.senha;
    document.getElementById("cnpj").value = registro.cnpj;
    document.getElementById("cep_loja").value = registro.cep_loja;
    document.getElementById("nome_loja").value = registro.nome_loja;
    document.getElementById("telefone").value = registro.telefone;
    document.getElementById("ativo").value = registro.ativo;
    document.getElementById("id").value = id;
  } else {
    // alert("ERRO:" + resposta.mensagem);
    window.location.href = "./";
  }
}

// ----------------------------------------------
// Fase 2
document.getElementById("enviar").addEventListener("click", () => {
  alterar();
});

async function alterar() {
  var email = document.getElementById("email").value;
  var senha = document.getElementById("senha").value;
  var cnpj = document.getElementById("cnpj").value;
  var cep_loja = document.getElementById("cep_loja").value;
  var nome_loja = document.getElementById("nome_loja").value;
  var telefone = document.getElementById("telefone").value;
  var ativo = document.getElementById("ativo").value;
  var id = document.getElementById("id").value;

  const fd = new FormData();
  fd.append("email", email);
  fd.append("senha", senha);
  fd.append("cnpj", cnpj);
  fd.append("cep_loja", cep_loja); 
  fd.append("nome_loja", nome_loja);
  fd.append("telefone", telefone);
  fd.append("ativo", ativo);

  const retorno = await fetch("../../../php/lojista_alterar.php?id=" + id, {
    method: "POST",
    body: fd,
  });
  const resposta = await retorno.json();
  if (resposta.status == "ok") {
    // alert("SUCESSO: " + resposta.mensagem);
    window.location.href = "./";
  } else {
    alert("ERRO: " + resposta.mensagem);
  }
}
