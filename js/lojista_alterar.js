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
    document.getElementById("id").value           = registro.id;
    document.getElementById("nome_loja").value    = registro.nome_loja;
    document.getElementById("logradouro").value   = registro.logradouro;
    document.getElementById("nome_lojista").value = registro.nome_lojista;
    document.getElementById("cpf").value          = registro.cpf;
    document.getElementById("cnpj").value         = registro.cnpj;
    document.getElementById("cep_lojista").value  = registro.cep_lojista;
    document.getElementById("estado").value       = registro.estado;
    document.getElementById("cidade").value       = registro.cidade;
    document.getElementById("bairro").value       = registro.bairro;
    document.getElementById("numero").value       = registro.numero;
    document.getElementById("genero").value       = registro.genero;
    document.getElementById("email").value        = registro.email;
    document.getElementById("senha").value        = registro.senha;
    document.getElementById("telefone").value     = registro.telefone;
    document.getElementById("ativo").value        = registro.ativo;
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
  var id           = document.getElementById("id").value;

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
  fd.append("id", id);

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
