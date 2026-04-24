// perfil.js — Lógica da página de perfil do usuário

async function iniciarPerfil() {
    // Verifica se o usuário está logado
    const resposta = await fetch(API + '/auth/sessao.php', { credentials: 'include' });
    const json     = await resposta.json();

    if (json.status !== 'ok' || !json.data.usuario) {
        // Redireciona para login se não estiver autenticado
        window.location.href = PAGINAS + '/login.html';
        return;
    }

    const usuario = json.data.usuario;

    // Preenche os campos com os dados atuais
    document.getElementById('inputNome').value  = usuario.nome;
    document.getElementById('inputEmail').value = usuario.email;
    document.getElementById('subtitulo').textContent =
        `Edite nome, email e senha de acesso. O tipo de conta atual é "${usuario.tipo}".`;

    // Habilita o botão de salvar
    document.getElementById('btnSalvar').addEventListener('click', salvarAlteracoes);
}

async function salvarAlteracoes() {
    const nome  = document.getElementById('inputNome').value.trim();
    const email = document.getElementById('inputEmail').value.trim();
    const senha = document.getElementById('inputSenha').value.trim();

    esconderMensagens();

    if (!nome || !email) {
        mostrarErro('Nome e email são obrigatórios.');
        return;
    }

    const btn = document.getElementById('btnSalvar');
    btn.disabled = true;
    btn.textContent = 'Salvando...';

    const resposta = await fetch(API + '/contas/atualizar.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ nome, email, senha }),
        credentials: 'include'
    });
    const json = await resposta.json();

    if (json.status === 'ok') {
        mostrarSucesso('Dados atualizados com sucesso!');
        document.getElementById('inputSenha').value = '';
    } else {
        mostrarErro(json.mensagem || 'Não foi possível atualizar os dados.');
    }

    btn.disabled = false;
    btn.textContent = 'Salvar alterações';
}

function mostrarSucesso(texto) {
    const el = document.getElementById('mensagemSucesso');
    el.textContent = texto;
    el.classList.remove('hidden');
}

function mostrarErro(texto) {
    const el = document.getElementById('mensagemErro');
    el.textContent = texto;
    el.classList.remove('hidden');
}

function esconderMensagens() {
    document.getElementById('mensagemSucesso').classList.add('hidden');
    document.getElementById('mensagemErro').classList.add('hidden');
}

// Inicia a página
iniciarPerfil();
