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

function cls(name) {
    return window.TechnoUpStyle?.cls(name) || '';
}

async function carregarContas() {
    const resposta = await fetch(CAMINHO_API + '/contas/get.php', { credentials: 'include' });
    const json     = await resposta.json();

    renderizarTabela(json.data || []);
}

function renderizarTabela(contas) {
    const tbody = document.getElementById('tabelaContasBody');

    if (contas.length === 0) {
        tbody.innerHTML = `<tr><td colspan="10" class="${cls('tableEmptyAdmin')}">Nenhuma conta encontrada.</td></tr>`;
        return;
    }

    tbody.innerHTML = contas.map(conta => {
        const ativo        = parseInt(conta.ativo) === 1;
        const statusLabel  = ativo ? 'Ativa'     : 'Inativa';
        const statusClass  = ativo ? cls('tableStatusOn') : cls('tableStatusOff');
        const toggleLabel  = ativo ? 'Desativar'  : 'Ativar';
        const toggleClass  = ativo ? cls('tableActionToggleOn') : cls('tableActionToggleOff');
        return `
            <tr class="${cls('tableRow')}">
                <td class="${cls('tableCellNowrap')}">
                    <div class="${cls('tableActionWrap')}">
                        <button onclick="alternarStatus(${conta.id})"
                            class="${cls('tableActionToggleBase')} ${toggleClass}">
                            ${toggleLabel}
                        </button>
                        <button onclick="deletarConta(${conta.id})"
                            class="${cls('tableActionDelete')}">
                            Deletar
                        </button>
                    </div>
                </td>
                <td>
                    <span class="${cls('tableStatusBase')} ${statusClass}">${statusLabel}</span>
                </td>
                <td>${conta.tipo || '-'}</td>
                <td class="${cls('tableStrong')}">${conta.nome}</td>
                <td>${conta.email || '-'}</td>
                <td class="${cls('tableCellNowrap')}">${conta.criado_em || '-'}</td>
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
        ? cls('feedbackSuccess')
        : cls('feedbackError');
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 4000);
}

// Inicia a página
iniciarAdmin();
