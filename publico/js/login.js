// login.js — Lógica da página de login

document.getElementById('btnEntrar').addEventListener('click', fazerLogin);

// Permite enviar com a tecla Enter nos inputs
document.getElementById('inputSenha').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') fazerLogin();
});

async function fazerLogin() {
    const email = document.getElementById('inputEmail').value.trim();
    const senha = document.getElementById('inputSenha').value.trim();
    const erroEl = document.getElementById('mensagemErro');

    // Esconde erro anterior
    erroEl.classList.add('hidden');

    if (!email || !senha) {
        mostrarErro('Preencha o email e a senha.');
        return;
    }

    // Desabilita o botão durante a requisição
    const btn = document.getElementById('btnEntrar');
    btn.disabled = true;
    btn.textContent = 'Entrando...';

    const resposta = await fetch(API + '/auth/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, senha }),
        credentials: 'include'
    });

    const json = await resposta.json();

    if (json.status === 'ok') {
        const usuario = json.data.usuario;
        const loja    = json.data.loja;

        // Redireciona conforme o tipo de conta
        if (usuario.tipo === 'lojista') {
            if (loja) {
                window.location.href = PAGINAS + '/lojas/gerenciar.html';
            } else {
                window.location.href = PAGINAS + '/lojas/cadastro.html';
            }
        } else if (usuario.tipo === 'administrador') {
            window.location.href = PAGINAS + '/admin/lojas.html';
        } else {
            window.location.href = PAGINAS + '/catalogo.html';
        }
    } else {
        mostrarErro(json.mensagem || 'Credenciais inválidas. Tente novamente.');
        btn.disabled = false;
        btn.textContent = 'Entrar';
    }
}

function mostrarErro(texto) {
    const erroEl = document.getElementById('mensagemErro');
    erroEl.textContent = texto;
    erroEl.classList.remove('hidden');
}
