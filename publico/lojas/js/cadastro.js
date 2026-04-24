// cadastro.js — Lógica do cadastro e atualização de loja

let modoAtualizar = false; // true se o lojista já tem loja e está atualizando

async function iniciarCadastroLoja() {
    // Verifica sessão: deve ser lojista
    const resposta = await fetch(API + '/auth/sessao.php', { credentials: 'include' });
    const json     = await resposta.json();

    if (json.status !== 'ok' || !json.data.usuario) {
        window.location.href = PAGINAS + '/login.html';
        return;
    }

    if (json.data.usuario.tipo !== 'lojista') {
        window.location.href = PAGINAS + '/catalogo.html';
        return;
    }

    // Se o lojista já tem loja, preenche o formulário para atualização
    if (json.data.loja) {
        modoAtualizar = true;
        const loja = json.data.loja;

        document.getElementById('tituloCadastro').textContent = 'Atualizar dados da loja';
        document.getElementById('btnSalvarLoja').textContent  = 'Atualizar';

        // Preenche os campos com os dados existentes
        document.getElementById('inputNomeLoja').value   = loja.nome_loja   || '';
        document.getElementById('inputTelefone').value   = loja.telefone    || '';
        document.getElementById('inputCpf').value        = loja.cpf         || '';
        document.getElementById('inputCnpj').value       = loja.cnpj        || '';
        document.getElementById('inputCep').value        = loja.cep         || '';
        document.getElementById('inputEstado').value     = loja.estado      || '';
        document.getElementById('inputCidade').value     = loja.cidade      || '';
        document.getElementById('inputBairro').value     = loja.bairro      || '';
        document.getElementById('inputLogradouro').value = loja.logradouro  || '';
        document.getElementById('inputNumero').value     = loja.numero      || '';
    }

    // Conecta o botão de salvar
    document.getElementById('btnSalvarLoja').addEventListener('click', salvarLoja);
}

async function salvarLoja() {
    const nomeLoja   = document.getElementById('inputNomeLoja').value.trim();
    const cnpj       = document.getElementById('inputCnpj').value.trim();
    const telefone   = document.getElementById('inputTelefone').value.trim();
    const cpf        = document.getElementById('inputCpf').value.trim();
    const cep        = document.getElementById('inputCep').value.trim();
    const estado     = document.getElementById('inputEstado').value.trim();
    const cidade     = document.getElementById('inputCidade').value.trim();
    const bairro     = document.getElementById('inputBairro').value.trim();
    const logradouro = document.getElementById('inputLogradouro').value.trim();
    const numero     = document.getElementById('inputNumero').value.trim();

    esconderFeedback();

    if (!nomeLoja || !cnpj) {
        mostrarFeedback('Nome da loja e CNPJ são obrigatórios.', false);
        return;
    }

    const btn = document.getElementById('btnSalvarLoja');
    btn.disabled = true;
    btn.textContent = 'Salvando...';

    const corpo = { nome_loja: nomeLoja, telefone, cpf, cnpj, cep, estado, cidade, bairro, logradouro, numero };

    // Usa endpoint de criar ou atualizar conforme o modo
    const endpoint = modoAtualizar
        ? API + '/lojas/atualizar.php'
        : API + '/lojas/criar.php';

    const resposta = await fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(corpo),
        credentials: 'include'
    });
    const json = await resposta.json();

    if (json.status === 'ok') {
        mostrarFeedback(modoAtualizar ? 'Loja atualizada!' : 'Loja cadastrada!', true);
        // Após cadastrar nova loja, redireciona para gerenciar produtos
        if (!modoAtualizar) {
            setTimeout(() => {
                window.location.href = PAGINAS + '/lojas/gerenciar.html';
            }, 1200);
        }
    } else {
        mostrarFeedback(json.mensagem || 'Erro ao salvar.', false);
        btn.disabled = false;
        btn.textContent = modoAtualizar ? 'Atualizar' : 'Cadastrar';
    }
}

function mostrarFeedback(mensagem, sucesso) {
    const el = document.getElementById('mensagemFeedback');
    el.textContent = mensagem;
    el.className = sucesso
        ? 'rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700'
        : 'rounded-xl border border-red-200   bg-red-50   px-4 py-3 text-sm text-red-700';
    el.classList.remove('hidden');
}

function esconderFeedback() {
    document.getElementById('mensagemFeedback').classList.add('hidden');
}

// Inicia a página
iniciarCadastroLoja();
