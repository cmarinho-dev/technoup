// gerenciar_produtos.js — Lógica do painel do lojista (CRUD de produtos)

let lojaId = null; // ID da loja do lojista logado

async function iniciarGerenciar() {
    // Verifica sessão e garante que é lojista
    const resposta = await fetch(CAMINHO_API + '/auth/sessao.php', { credentials: 'include' });
    const json     = await resposta.json();

    if (json.status !== 'ok' || !json.data.usuario) {
        window.location.href = CAMINHO_FRONTEND + '/login.html';
        return;
    }

    if (json.data.usuario.tipo !== 'lojista') {
        window.location.href = CAMINHO_FRONTEND + '/catalogo.html';
        return;
    }

    if (!json.data.loja) {
        // Lojista sem loja cadastrada — redireciona para novo cadastro
        window.location.href = CAMINHO_FRONTEND + '/protegido/lojas/novo.html';
        return;
    }

    lojaId = json.data.loja.id;

    // Carrega os produtos da loja
    await carregarProdutos();

    // Conecta os botões
    document.getElementById('btnNovoProduct').addEventListener('click', abrirModalNovo);
    document.getElementById('btnFecharModal').addEventListener('click', fecharModal);
    document.getElementById('btnSalvarProduto').addEventListener('click', salvarProduto);
}

async function carregarProdutos() {
    const resposta = await fetch(CAMINHO_API + '/produtos/get.php?loja_id=' + lojaId, { credentials: 'include' });
    const json     = await resposta.json();
    const produtos = json.data || [];

    renderizarTabela(produtos);
}

function renderizarTabela(produtos) {
    const tbody = document.getElementById('tabelaProdutosBody');

    if (produtos.length === 0) {
        tbody.innerHTML = `<tr><td colspan="10" class="px-3 py-8 text-center text-slate-400">Nenhum produto cadastrado ainda.</td></tr>`;
        return;
    }

    tbody.innerHTML = produtos.map(produto => {
        const desconto = produto.desconto > 0 ? produto.desconto + '%' : '-';
        const precoFinal = produto.preco_final
            ? 'R$ ' + parseFloat(produto.preco_final).toFixed(2).replace('.', ',')
            : '-';

        return `
            <tr class="align-center *:px-4 *:py-4 *:text-sm *:text-slate-700">
                <td class="whitespace-nowrap">
                    <div class="flex gap-2">
                        <button onclick="abrirModalEditar(${produto.id})"
                            class="rounded-xl bg-blue-600 px-3 py-3 text-xs font-semibold text-white shadow-sm transition-all hover:bg-blue-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">
                            <i data-lucide="pencil" class="size-4"></i>
                        </button>
                        <button onclick="deletarProduto(${produto.id})"
                            class="rounded-xl bg-red-600 px-3 py-3 text-xs font-semibold text-white shadow-sm transition-all hover:bg-red-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2">
                            <i data-lucide="trash" class="size-4"></i>
                        </button>
                    </div>
                </td>
                <td class="font-medium text-slate-900">${produto.nome}</td>
                <td class="">${produto.tipo || '-'}</td>
                <td class="">${produto.marca || '-'}</td>
                <td class="">${produto.modelo || '-'}</td>
                <td class="whitespace-nowrap">${produto.criado_em || '-'}</td>
                <td class="">${produto.descricao || '-'}</td>
                <td class="">R$ ${parseFloat(produto.preco).toFixed(2).replace('.', ',')}</td>
                <td class="">${desconto}</td>
                <td class="">${precoFinal}</td>
            </tr>
        `;
    }).join('');

    lucide.createIcons();
}

function abrirModalNovo() {
    document.getElementById('modalTitulo').textContent = 'Novo produto';
    document.getElementById('campoIdProduto').value    = '';
    limparCamposModal();
    document.getElementById('modalProduto').style.display = 'flex';
}

async function abrirModalEditar(id) {
    // Busca os dados atuais do produto para preencher o modal
    const resposta = await fetch(CAMINHO_API + '/produtos/get.php?loja_id=' + lojaId, { credentials: 'include' });
    const json     = await resposta.json();
    const produto  = (json.data || []).find(p => p.id === id);

    if (!produto) return;

    document.getElementById('modalTitulo').textContent    = 'Editar produto';
    document.getElementById('campoIdProduto').value       = produto.id;
    document.getElementById('campoNome').value            = produto.nome;
    document.getElementById('campoPreco').value           = produto.preco;
    document.getElementById('campoTipo').value            = produto.tipo     || '';
    document.getElementById('campoModelo').value          = produto.modelo   || '';
    document.getElementById('campoMarca').value           = produto.marca    || '';
    document.getElementById('campoDescricao').value       = produto.descricao || '';
    document.getElementById('campoDesconto').value        = produto.desconto || 0;
    document.getElementById('modalProduto').style.display = 'flex';
}

function fecharModal() {
    document.getElementById('modalProduto').style.display = 'none';
}

function limparCamposModal() {
    ['campoNome', 'campoPreco', 'campoTipo', 'campoModelo', 'campoMarca', 'campoDescricao', 'campoDesconto']
        .forEach(id => { document.getElementById(id).value = ''; });
}

async function salvarProduto() {
    const id       = document.getElementById('campoIdProduto').value;
    const nome     = document.getElementById('campoNome').value.trim();
    const preco    = parseFloat(document.getElementById('campoPreco').value);
    const tipo     = document.getElementById('campoTipo').value.trim();
    const modelo   = document.getElementById('campoModelo').value.trim();
    const marca    = document.getElementById('campoMarca').value.trim();
    const descricao = document.getElementById('campoDescricao').value.trim();
    const desconto = parseInt(document.getElementById('campoDesconto').value || 0);

    if (!nome || !preco || !tipo) {
        mostrarAlert('Nome, preço e tipo são obrigatórios.', false);
        //mostrarFeedback('Nome, preço e tipo são obrigatórios.', false);
        return;
    }

    const endpoint = id
        ? CAMINHO_API + '/produtos/alterar.php'
        : CAMINHO_API + '/produtos/novo.php';

    const fd = new FormData();
    fd.append('nome', nome);
    fd.append('preco', preco);
    fd.append('tipo', tipo);
    fd.append('modelo', modelo);
    fd.append('marca', marca);
    fd.append('descricao', descricao);
    fd.append('desconto', desconto);
    if (id) fd.append('id', parseInt(id));

    const resposta = await fetch(endpoint, {
        method: 'POST',
        body: fd,
        credentials: 'include'
    });
    const json = await resposta.json();

    fecharModal();

    if (json.status === 'ok') {
        mostrarFeedback(json.mensagem, true);
        await carregarProdutos();
    } else {
        mostrarFeedback(json.mensagem || 'Erro ao salvar.', false);
    }
}

async function deletarProduto(id) {
    if (!confirm('Deseja realmente deletar este produto?')) return;

    const fd = new FormData();
    fd.append('id', id);

    const resposta = await fetch(CAMINHO_API + '/produtos/excluir.php', {
        method: 'POST',
        body: fd,
        credentials: 'include'
    });
    const json = await resposta.json();

    if (json.status === 'ok') {
        mostrarFeedback('Produto deletado com sucesso.', true);
        await carregarProdutos();
    } else {
        mostrarFeedback(json.mensagem || 'Erro ao deletar.', false);
    }
}

function mostrarAlert(mensagem) {
    alert(mensagem);
}

function mostrarFeedback(mensagem, sucesso) {
    const el = document.getElementById('mensagemFeedback');
    el.textContent = mensagem;
    el.className = sucesso
        ? 'rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700'
        : 'rounded-xl border border-red-200   bg-red-50   px-4 py-3 text-sm text-red-700';
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 4000);
}

// Fecha o modal ao clicar no fundo escuro
document.getElementById('modalProduto').addEventListener('click', function (e) {
    if (e.target === this) fecharModal();
});

// Inicia a página
iniciarGerenciar();
