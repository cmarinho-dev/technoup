document.addEventListener("DOMContentLoaded", () => {
    valida_sessao();

    const form = document.getElementById("form");
    if (form) {
        form.addEventListener("submit", (event) => {
            event.preventDefault();
            novo();
        });
    }
});

async function novo() {
    try {
        const email = document.getElementById("email").value.trim();
        const senha = document.getElementById("senha").value.trim();
        const cnpj = document.getElementById("cnpj").value.trim();
        const cep_loja = document.getElementById("cep_loja").value.trim();
        const nome_loja = document.getElementById("nome_loja").value.trim();
        const telefone = document.getElementById("telefone").value.trim();
        const ativo = document.getElementById("ativo").value;

        // Validação básica no frontend
        if (!email || !senha || !cnpj || !cep_loja || !nome_loja || !telefone) {
            alert("Por favor, preencha todos os campos obrigatórios.");
            return;
        }

        const fd = new FormData();
        fd.append("email", email);
        fd.append("senha", senha);
        fd.append("cnpj", cnpj);
        fd.append("cep_loja", cep_loja);
        fd.append("nome_loja", nome_loja);
        fd.append("telefone", telefone);
        fd.append("ativo", ativo);

        const retorno = await fetch("../../../php/lojista_novo.php", {
            method: 'POST',
            body: fd
        });

        if (!retorno.ok) {
            throw new Error(`Erro HTTP: ${retorno.status}`);
        }

        const resposta = await retorno.json();

        if (resposta.status === "ok") {
            alert("Lojista cadastrado com sucesso!");
            window.location.href = "./";
        } else {
            alert("Erro: " + resposta.mensagem);
        }
    } catch (erro) {
        console.error("Erro ao cadastrar lojista:", erro);
        alert("Erro ao processar a solicitação. Verifique o console para mais detalhes.");
    }
}