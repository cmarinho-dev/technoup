// contas.js — Lógica do painel de administração de contas

async function iniciarAdmin() {
    // Verifica sessão: deve ser administrador
    const resposta = await fetch(CAMINHO_API + '/auth/sessao.php', { credentials: 'include' });
    const json     = await resposta.json();

    if (json.status !== 'ok' || !json.data.usuario) {
        window.location.href = CAMINHO_FRONTEND + '/login.html';
        return;
    }

    if (json.data.usuario.tipo !== 'administrador') {
        window.location.href = CAMINHO_FRONTEND + '/catalogo.html';
        return;
    }

    await carregarContas();
}

async function carregarContas() {
    const resposta = await fetch(CAMINHO_API + '/contas/get.php', { credentials: 'include' });
    const json     = await resposta.json();

    renderizarTabela(json.data || []);
}

function renderizarTabela(contas) {
    const tbody = document.getElementById('tabelaContasBody');

    if (contas.length === 0) {
        tbody.innerHTML = `<tr><td colspan="10" class="px-4 py-12 text-center text-sm text-slate-500">Nenhuma conta encontrada.</td></tr>`;
        return;
    }

    tbody.innerHTML = contas.map(conta => {
        const ativo        = parseInt(conta.ativo) === 1;
        const statusLabel  = ativo ? 'Ativa'     : 'Inativa';
        const statusClass  = ativo ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600';
        const toggleLabel  = ativo ? 'Desativar'  : 'Ativar';
        const toggleClass  = ativo
            ? 'bg-amber-500 hover:bg-amber-600 focus:ring-amber-500'
            : 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-600';
        return `
            <tr class="align-center *:px-4 *:py-4 *:text-sm *:text-slate-700">
                <td class="whitespace-nowrap">
                    <div class="flex gap-2">
                        <button onclick="alternarStatus(${conta.id})"
                            class="rounded-xl px-3 py-2 text-xs font-semibold text-white shadow-sm transition-all hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 ${toggleClass}">
                            ${toggleLabel}
                        </button>
                        <button onclick="deletarConta(${conta.id})"
                            class="rounded-xl bg-red-500 px-3 py-2 text-xs font-semibold text-white shadow-sm transition-all hover:bg-red-600 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            Deletar
                        </button>
                    </div>
                </td>
                <td>
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ${statusClass}">${statusLabel}</span>
                </td>
                <td>${conta.tipo || '-'}</td>
                <td class="font-medium text-slate-900">${conta.nome}</td>
                <td>${conta.email || '-'}</td>
                <td class="whitespace-nowrap">${conta.criado_em || '-'}</td>
            </tr>
        `;
    }).join('');
}

async function alternarStatus(contaId) {
    const fd = new FormData();
    fd.append('id', contaId);

    const resposta = await fetch(CAMINHO_API + '/contas/alterar_status.php', {
        method: 'POST',
        body: fd,
        credentials: 'include'
    });
    const json = await resposta.json();

    if (json.status === 'ok') {
        mostrarFeedback('Status alterado com sucesso.', true);
        await carregarContas();
    } else {
        mostrarFeedback(json.mensagem || 'Erro ao alterar status.', false);
    }
}

async function deletarConta(contaId) {
    if (!confirm('Deseja realmente deletar esta conta? Caso seja uma conta de lojista, isso remove também a loja e todos os produtos vinculados.')) return;

    const fd = new FormData();
    fd.append('id', contaId);

    const resposta = await fetch(CAMINHO_API + '/contas/excluir.php', {
        method: 'POST',
        body: fd,
        credentials: 'include'
    });
    const json = await resposta.json();

    if (json.status === 'ok') {
        mostrarFeedback('Conta deletada com sucesso.', true);
        await carregarContas();
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

// Inicia a página
iniciarAdmin();
