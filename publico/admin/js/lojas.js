// lojas.js — Lógica do painel de administração de lojas

async function iniciarAdmin() {
    // Verifica sessão: deve ser administrador
    const resposta = await fetch(API + '/auth/sessao.php', { credentials: 'include' });
    const json     = await resposta.json();

    if (json.status !== 'ok' || !json.data.usuario) {
        window.location.href = PAGINAS + '/login.html';
        return;
    }

    if (json.data.usuario.tipo !== 'administrador') {
        window.location.href = PAGINAS + '/catalogo.html';
        return;
    }

    await carregarLojistas();
}

async function carregarLojistas() {
    const resposta = await fetch(API + '/lojas/listar_lojistas.php', { credentials: 'include' });
    const json     = await resposta.json();

    renderizarTabela(json.data || []);
}

function renderizarTabela(lojistas) {
    const tbody = document.getElementById('tabelaLojistasBody');

    if (lojistas.length === 0) {
        tbody.innerHTML = `<tr><td colspan="10" class="px-4 py-12 text-center text-sm text-slate-500">Nenhuma conta de lojista encontrada.</td></tr>`;
        return;
    }

    tbody.innerHTML = lojistas.map(lojista => {
        const ativo        = parseInt(lojista.ativo) === 1;
        const statusLabel  = ativo ? 'Ativa'     : 'Inativa';
        const statusClass  = ativo ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600';
        const toggleLabel  = ativo ? 'Desativar'  : 'Ativar';
        const toggleClass  = ativo
            ? 'bg-amber-500 hover:bg-amber-600 focus:ring-amber-500'
            : 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-600';

        const cidade   = [lojista.cidade, lojista.estado].filter(Boolean).join(' / ') || '-';
        const endereco = [lojista.logradouro, lojista.numero].filter(Boolean).join(', ').trim() || '-';
        const nomeLoja = lojista.nome_loja || 'Sem loja cadastrada';

        return `
            <tr class="align-top *:px-4 *:py-4 *:text-sm *:text-slate-700">
                <td class="whitespace-nowrap">
                    <div class="flex gap-2">
                        <button onclick="alternarStatus(${lojista.conta_id})"
                            class="rounded-xl px-3 py-2 text-xs font-semibold text-white shadow-sm transition-all hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 ${toggleClass}">
                            ${toggleLabel}
                        </button>
                        <button onclick="deletarConta(${lojista.conta_id})"
                            class="rounded-xl bg-red-500 px-3 py-2 text-xs font-semibold text-white shadow-sm transition-all hover:bg-red-600 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            Deletar
                        </button>
                    </div>
                </td>
                <td>
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ${statusClass}">${statusLabel}</span>
                </td>
                <td class="font-medium text-slate-900">${lojista.nome_conta}</td>
                <td>${lojista.email}</td>
                <td>${nomeLoja}</td>
                <td>${lojista.telefone || '-'}</td>
                <td>${lojista.cnpj || '-'}</td>
                <td>${cidade}</td>
                <td>${endereco}</td>
                <td class="whitespace-nowrap">${lojista.criado_em || '-'}</td>
            </tr>
        `;
    }).join('');
}

async function alternarStatus(contaId) {
    const resposta = await fetch(API + '/lojas/toggle_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ conta_id: contaId }),
        credentials: 'include'
    });
    const json = await resposta.json();

    if (json.status === 'ok') {
        mostrarFeedback('Status alterado com sucesso.', true);
        await carregarLojistas();
    } else {
        mostrarFeedback(json.mensagem || 'Erro ao alterar status.', false);
    }
}

async function deletarConta(contaId) {
    if (!confirm('Deseja deletar esta conta? Isso remove também a loja e todos os produtos vinculados.')) return;

    const resposta = await fetch(API + '/lojas/deletar_conta.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ conta_id: contaId }),
        credentials: 'include'
    });
    const json = await resposta.json();

    if (json.status === 'ok') {
        mostrarFeedback('Conta deletada com sucesso.', true);
        await carregarLojistas();
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
