// lojas.js — Lógica do painel de administração de lojas

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

    await carregarLojistas();
}

function cls(name) {
    return window.TechnoUpStyle?.cls(name) || '';
}

async function carregarLojistas() {
    const resposta = await fetch(CAMINHO_API + '/contas_loja/get.php', { credentials: 'include' });
    const json     = await resposta.json();

    renderizarTabela(json.data || []);
}

function renderizarTabela(lojistas) {
    const tbody = document.getElementById('tabelaLojistasBody');

    if (lojistas.length === 0) {
        tbody.innerHTML = `<tr><td colspan="10" class="${cls('tableEmptyAdmin')}">Nenhuma conta de lojista encontrada.</td></tr>`;
        return;
    }

    tbody.innerHTML = lojistas.map(lojista => {
        const ativo        = parseInt(lojista.ativo) === 1;
        const statusLabel  = ativo ? 'Ativa'     : 'Inativa';
        const statusClass  = ativo ? cls('tableStatusOn') : cls('tableStatusOff');
        const toggleLabel  = ativo ? 'Desativar'  : 'Ativar';
        const toggleClass  = ativo ? cls('tableActionToggleOn') : cls('tableActionToggleOff');

        const cidade   = [lojista.cidade, lojista.estado].filter(Boolean).join(' / ') || '-';
        const endereco = [lojista.logradouro, lojista.numero].filter(Boolean).join(', ').trim() || '-';
        const nomeLoja = lojista.nome_loja || 'Sem loja cadastrada';

        return `
            <tr class="${cls('tableRow')}">
                <td class="${cls('tableCellNowrap')}">
                    <div class="${cls('tableActionWrap')}">
                        <button onclick="alternarStatus(${lojista.conta_id})"
                            class="${cls('tableActionToggleBase')} ${toggleClass}">
                            ${toggleLabel}
                        </button>
                        <button onclick="deletarConta(${lojista.conta_id})"
                            class="${cls('tableActionDelete')}">
                            Deletar
                        </button>
                    </div>
                </td>
                <td>
                    <span class="${cls('tableStatusBase')} ${statusClass}">${statusLabel}</span>
                </td>
                <td class="${cls('tableStrong')}">${lojista.nome_conta}</td>
                <td>${lojista.email}</td>
                <td>${nomeLoja}</td>
                <td>${lojista.telefone || '-'}</td>
                <td>${lojista.cnpj || '-'}</td>
                <td>${cidade}</td>
                <td>${endereco}</td>
                <td class="${cls('tableCellNowrap')}">${lojista.criado_em || '-'}</td>
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
        await carregarLojistas();
    } else {
        mostrarFeedback(json.mensagem || 'Erro ao alterar status.', false);
    }
}

async function deletarConta(contaId) {
    if (!confirm('Deseja deletar esta conta? Isso remove também a loja e todos os produtos vinculados.')) return;

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
        await carregarLojistas();
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
