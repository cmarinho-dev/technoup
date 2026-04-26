// alterar.js — formulário para atualizar loja existente

async function iniciarAlterarLoja() {
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

    // Busca a loja pelo conta_id via API (não depende de dados completos na sessão)
    const contaId = json.data.usuario.id;
    const retornoLoja = await fetch(CAMINHO_API + '/lojas/get.php?conta_id=' + contaId, { credentials: 'include' });
    const jsonLoja = await retornoLoja.json();

    if (jsonLoja.status !== 'ok' || !Array.isArray(jsonLoja.data) || jsonLoja.data.length === 0) {
        // sem loja — vai para novo
        window.location.href = CAMINHO_FRONTEND + '/protegido/lojas/novo.html';
        return;
    }

    const loja = jsonLoja.data[0];

    document.getElementById('tituloCadastro').textContent = 'Atualizar dados da loja';
    document.getElementById('btnSalvarLoja').textContent  = 'Atualizar';

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

    const resposta = await fetch(CAMINHO_API + '/lojas/alterar.php', {
        method: 'POST',
        body: fd,
        credentials: 'include'
    });
    const json = await resposta.json();

    if (json.status === 'ok') {
        mostrarFeedback('Loja atualizada!', true);
        setTimeout(() => { window.location.href = CAMINHO_FRONTEND + '/protegido/lojas/gerenciar_produtos.html'; }, 1200);
    } else {
        mostrarFeedback(json.mensagem || 'Erro ao salvar.', false);
        btn.disabled = false;
        btn.textContent = 'Atualizar';
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
iniciarAlterarLoja();
