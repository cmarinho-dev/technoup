// registro.js — Lógica da página de criação de conta

document.getElementById('btnCriarConta').addEventListener('click', criarConta);
document.getElementById('inputCpf').addEventListener('input', (evento) => {
    evento.target.value = formatarCpf(evento.target.value);
});

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

async function criarConta() {
    const nome = document.getElementById('inputNome').value.trim();
    const cpf = document.getElementById('inputCpf').value.trim();
    const email = document.getElementById('inputEmail').value.trim();
    const senha = document.getElementById('inputSenha').value.trim();
    const confirmarSenha = document.getElementById('inputConfirmarSenha').value.trim();
    const erroSenhaEl = document.getElementById('erroSenha');
    const mensagemErroEl = document.getElementById('mensagemErro');

    // Esconde erros anteriores
    erroSenhaEl.classList.add('hidden');
    mensagemErroEl.classList.add('hidden');

    // Determina o tipo selecionado
    const tipoSelecionado = document.querySelector('input[name="tipo_conta"]:checked').value;

    if (!nome || !cpf || !email || !senha) {
        mostrarErro('Preencha todos os campos obrigatórios.');
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

    if (senha.length < 8) {
        mostrarErro('A senha deve ter pelo menos 8 caracteres.');
        return;
    }

    if (senha !== confirmarSenha) {
        erroSenhaEl.classList.remove('hidden');
        return;
    }

    const btn = document.getElementById('btnCriarConta');
    btn.disabled = true;
    btn.textContent = 'Criando conta...';

    // Cria a conta no banco
    const fdConta = new FormData();
    fdConta.append('nome', nome);
    fdConta.append('cpf', somenteDigitos(cpf));
    fdConta.append('email', email);
    fdConta.append('senha', senha);
    fdConta.append('tipo', tipoSelecionado);

    const respostaConta = await fetch(CAMINHO_API + '/contas/novo.php', {
        method: 'POST',
        body: fdConta,
        credentials: 'include'
    });
    const jsonConta = await respostaConta.json();

    if (jsonConta.status !== 'ok') {
        mostrarErro(jsonConta.mensagem || 'Erro ao criar a conta.');
        btn.disabled = false;
        btn.textContent = 'Criar conta';
        return;
    }

    // Se for lojista: faz login automaticamente e redireciona ao cadastro da loja
    if (tipoSelecionado === 'lojista') {
        const fdLogin = new FormData();
        fdLogin.append('email', email);
        fdLogin.append('senha', senha);

        const respostaLogin = await fetch(CAMINHO_API + '/auth/login.php', {
            method: 'POST',
            body: fdLogin,
            credentials: 'include'
        });
        const jsonLogin = await respostaLogin.json();

        if (jsonLogin.status === 'ok') {
            window.location.href = CAMINHO_FRONTEND + '/protegido/lojas/novo.html';
        } else {
            window.location.href = CAMINHO_FRONTEND + '/login.html';
        }
    } else {
        // Consumidor: redireciona para o login
        window.location.href = CAMINHO_FRONTEND + '/login.html';
    }
}

function mostrarErro(texto) {
    const el = document.getElementById('mensagemErro');
    el.textContent = texto;
    el.classList.remove('hidden');
}
