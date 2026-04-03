document.addEventListener("DOMContentLoaded", () => {
    valida_sessao();

    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get("id");
    if (!id) {
        window.location.href = "./";
        return;
    }

    buscar(id);

    document.getElementById("enviar").addEventListener("click", (event) => {
        event.preventDefault();
        alterar();
    });
});

async function buscar(id) {
    try {
        const retorno = await fetch("../../../php/catalogo_get.php?id=" + encodeURIComponent(id));
        const resposta = await retorno.json();

        if (resposta.status === "ok" && resposta.data.length > 0) {
            const registro = resposta.data[0];
            document.getElementById("nome").value = registro.nome;
            document.getElementById("preco").value = registro.preco;
            document.getElementById("tipo").value = registro.tipo;
            document.getElementById("id").value = registro.id;
        } else {
            alert("Produto não encontrado.");
            window.location.href = "./";
        }
    } catch (erro) {
        console.error(erro);
        alert("Erro ao buscar os dados do produto.");
        window.location.href = "./";
    }
}

async function alterar() {
    const form = document.getElementById("form");
    const fd = new FormData(form);

    try {
        const retorno = await fetch("../../../php/catalogo_alterar.php", {
            method: "POST",
            body: fd
        });
        const resposta = await retorno.json();

        if (resposta.status === "ok") {
            alert("Produto atualizado com sucesso!");
            window.location.href = "index.php";
        } else {
            alert("Erro: " + resposta.mensagem);
        }
    } catch (erro) {
        console.error(erro);
        alert("Erro ao salvar alterações.");
    }
}
