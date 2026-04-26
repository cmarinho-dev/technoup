// novo.js — formulário de criação de loja (apenas criação)

async function iniciarNovoLoja() {
    const resposta = await fetch(CAMINHO_API + '/auth/sessao.php', { credentials: 'include' });
    const json = await resposta.json();

    if (json.status !== 'ok' || !json.data.usuario) {
        window.location.href = CAMINHO_FRONTEND + '/login.html';
        return;
    }

    if (json.data.usuario.tipo !== 'lojista') {
        window.location.href = CAMINHO_FRONTEND + '/catalogo.html';
        return;
    }

    // Se já tem loja, vai para a página de alterar
    if (json.data.loja) {
        window.location.href = CAMINHO_FRONTEND + '/protegido/lojas/alterar.html';
        return;
    }

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

    const fd = new FormData();
    fd.append('nome_loja', nomeLoja);
    fd.append('telefone', telefone);
    fd.append('cpf', cpf);
    fd.append('cnpj', cnpj);
    fd.append('cep', cep);
    fd.append('estado', estado);
    fd.append('cidade', cidade);
    fd.append('bairro', bairro);
    fd.append('logradouro', logradouro);
    fd.append('numero', numero);

    const resposta = await fetch(CAMINHO_API + '/lojas/novo.php', {
        method: 'POST',
        body: fd,
        credentials: 'include'
    });
    const json = await resposta.json();

    if (json.status === 'ok') {
        mostrarFeedback('Loja cadastrada!', true);
        setTimeout(() => { window.location.href = CAMINHO_FRONTEND + '/protegido/lojas/gerenciar_produtos.html'; }, 1200);
    } else {
        mostrarFeedback(json.mensagem || 'Erro ao salvar.', false);
        btn.disabled = false;
        btn.textContent = 'Cadastrar';
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

function esconderFeedback() { document.getElementById('mensagemFeedback').classList.add('hidden'); }

// Inicia a página
iniciarNovoLoja();
