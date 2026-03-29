document.getElementById("form").addEventListener("submit", () => {
  login();
});
async function login() {
  var email = document.getElementById("email").value;
  var senha = document.getElementById("senha").value;
  const fd = new FormData();
  fd.append("email", email);
  fd.append("senha", senha);
  const retorno = await fetch("../../../php/_login.php", {
    method: "POST",
    body: fd,
  });
  const resposta = await retorno.json();
    console.log(resposta);

  if (resposta.status == "ok") {
    window.location.href = "../lojista/";
  } else {
    alert("Credenciais invalidas.");
  }
}
