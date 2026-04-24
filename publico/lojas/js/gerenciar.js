// gerenciar.js — Lógica do painel do lojista (CRUD de produtos)

let lojaId = null; // ID da loja do lojista logado

async function iniciarGerenciar() {
    // Verifica sessão e garante que é lojista
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

    if (!json.data.loja) {
        // Lojista sem loja cadastrada — redireciona para cadastro
        window.location.href = PAGINAS + '/lojas/cadastro.html';
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
    const resposta = await fetch(API + '/produtos/listar.php?loja_id=' + lojaId, { credentials: 'include' });
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
            <tr class="*:text-gray-900">
                <td class="px-3 py-2 whitespace-nowrap">
                    <div class="flex gap-2 w-min">
                        <button onclick="abrirModalEditar(${produto.id})"
                            class="p-2 bg-blue-600 hover:shadow-xl text-white rounded-xl shadow-sm transition-all">
                            <i data-lucide="pencil" class="size-4"></i>
                        </button>
                        <button onclick="deletarProduto(${produto.id})"
                            class="p-2 bg-red-500 hover:shadow-xl text-white rounded-xl shadow-sm transition-all">
                            <i data-lucide="trash" class="size-4"></i>
                        </button>
                    </div>
                </td>
                <td class="px-3 py-2 whitespace-nowrap">${produto.nome}</td>
                <td class="px-3 py-2 whitespace-nowrap">${produto.tipo || '-'}</td>
                <td class="px-3 py-2 whitespace-nowrap">${produto.marca || '-'}</td>
                <td class="px-3 py-2 whitespace-nowrap">${produto.modelo || '-'}</td>
                <td class="px-3 py-2 whitespace-nowrap">${produto.criado_em || '-'}</td>
                <td class="px-3 py-2">${produto.descricao || '-'}</td>
                <td class="px-3 py-2 whitespace-nowrap">R$ ${parseFloat(produto.preco).toFixed(2).replace('.', ',')}</td>
                <td class="px-3 py-2 whitespace-nowrap">${desconto}</td>
                <td class="px-3 py-2 whitespace-nowrap">${precoFinal}</td>
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
    const resposta = await fetch(API + '/produtos/listar.php?loja_id=' + lojaId, { credentials: 'include' });
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

    if (!nome || !preco) {
        mostrarFeedback('Nome e preço são obrigatórios.', false);
        return;
    }

    const endpoint = id
        ? API + '/produtos/atualizar.php'
        : API + '/produtos/criar.php';

    const corpo = { nome, preco, tipo, modelo, marca, descricao, desconto };
    if (id) corpo.id = parseInt(id);

    const resposta = await fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(corpo),
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

    const resposta = await fetch(API + '/produtos/deletar.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id }),
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
