// registro.js — Lógica da página de criação de conta

document.getElementById('btnCriarConta').addEventListener('click', criarConta);

async function criarConta() {
    const nome            = document.getElementById('inputNome').value.trim();
    const email           = document.getElementById('inputEmail').value.trim();
    const senha           = document.getElementById('inputSenha').value.trim();
    const confirmarSenha  = document.getElementById('inputConfirmarSenha').value.trim();
    const erroSenhaEl     = document.getElementById('erroSenha');
    const mensagemErroEl  = document.getElementById('mensagemErro');

    // Esconde erros anteriores
    erroSenhaEl.classList.add('hidden');
    mensagemErroEl.classList.add('hidden');

    // Determina o tipo selecionado
    const tipoSelecionado = document.querySelector('input[name="tipo_conta"]:checked').value;

    if (!nome || !email || !senha) {
        mostrarErro('Preencha todos os campos obrigatórios.');
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
    const respostaConta = await fetch(API + '/contas/criar.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ nome, email, senha, tipo: tipoSelecionado }),
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
        const respostaLogin = await fetch(API + '/auth/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, senha }),
            credentials: 'include'
        });
        const jsonLogin = await respostaLogin.json();

        if (jsonLogin.status === 'ok') {
            window.location.href = PAGINAS + '/lojas/cadastro.html';
        } else {
            window.location.href = PAGINAS + '/login.html';
        }
    } else {
        // Consumidor: redireciona para o login
        window.location.href = PAGINAS + '/login.html';
    }
}

function mostrarErro(texto) {
    const el = document.getElementById('mensagemErro');
    el.textContent = texto;
    el.classList.remove('hidden');
}
