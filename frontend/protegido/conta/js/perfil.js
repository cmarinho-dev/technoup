// conta.js — Lógica da página de conta do usuário

async function iniciarPerfil() {
    // Verifica se o usuário está logado
    const resposta = await fetch(CAMINHO_API + '/auth/sessao.php', { credentials: 'include' });
    const json     = await resposta.json();

    if (json.status !== 'ok' || !json.data.usuario) {
        // Redireciona para login se não estiver autenticado
        window.location.href = CAMINHO_FRONTEND + '/login.html';
        return;
    }

    const usuario = json.data.usuario;

    // Preenche os campos com os dados atuais
    document.getElementById('inputNome').value  = usuario.nome;
    document.getElementById('inputCpf').value   = formatarCpf(usuario.cpf || '');
    document.getElementById('inputEmail').value = usuario.email;
    document.getElementById('subtitulo').textContent =
        `Edite nome, email e senha de acesso. O tipo de conta atual é "${usuario.tipo}".`;

    // Habilita o botão de salvar
    document.getElementById('inputCpf').addEventListener('input', (evento) => {
        evento.target.value = formatarCpf(evento.target.value);
    });
    document.getElementById('btnSalvar').addEventListener('click', salvarAlteracoes);
}

function somenteDigitos(valor) {
    return String(valor || '').replace(/\D+/g, '');
}

function formatarCpf(valor) {
    const digitos = somenteDigitos(valor).slice(0, 11);
    return digitos
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
}

function cpfValido(valor) {
    const cpf = somenteDigitos(valor);
    if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false;

    for (let t = 9; t < 11; t++) {
        let soma = 0;
        for (let i = 0; i < t; i++) {
            soma += Number(cpf[i]) * ((t + 1) - i);
        }
        const digito = ((10 * soma) % 11) % 10;
        if (Number(cpf[t]) !== digito) return false;
    }

    return true;
}

async function salvarAlteracoes() {
    const nome  = document.getElementById('inputNome').value.trim();
    const cpf   = document.getElementById('inputCpf').value.trim();
    const email = document.getElementById('inputEmail').value.trim();
    const senha = document.getElementById('inputSenha').value.trim();

    esconderMensagens();

    if (!nome || !cpf || !email) {
        mostrarErro('Nome, CPF e email são obrigatórios.');
        return;
    }

    if (nome.length < 3) {
        mostrarErro('Informe seu nome completo.');
        return;
    }

    if (!cpfValido(cpf)) {
        mostrarErro('Digite um CPF válido.');
        return;
    }

    if (!email.includes('@')) {
        mostrarErro('Digite um email válido.');
        return;
    }

    const btn = document.getElementById('btnSalvar');
    btn.disabled = true;
    btn.textContent = 'Salvando...';

    const fd = new FormData();
    fd.append('nome', nome);
    fd.append('cpf', somenteDigitos(cpf));
    fd.append('email', email);
    fd.append('senha', senha);

    const resposta = await fetch(CAMINHO_API + '/contas/alterar.php', {
        method: 'POST',
        body: fd,
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
