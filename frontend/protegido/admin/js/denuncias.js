let statusAtual = '';

const listaDenuncias = document.getElementById('listaDenuncias');
const mensagemFeedback = document.getElementById('mensagemFeedback');

function escaparHtml(texto) {
    const div = document.createElement('div');
    div.textContent = texto || '';
    return div.innerHTML;
}

function formatarData(data) {
    if (!data) return '-';
    return new Intl.DateTimeFormat('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(new Date(data.replace(' ', 'T')));
}

function mostrarFeedback(mensagem, sucesso = true) {
    mensagemFeedback.textContent = mensagem;
    mensagemFeedback.className = sucesso
        ? 'rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700'
        : 'rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700';
    setTimeout(() => mensagemFeedback.classList.add('hidden'), 4000);
}

function textoStatus(status) {
    const mapa = {
        pendente: 'Pendente',
        em_analise: 'Em análise',
        resolvida: 'Resolvida',
        recusada: 'Recusada'
    };
    return mapa[status] || 'Pendente';
}

function classeStatus(status) {
    if (status === 'resolvida') return 'bg-emerald-50 text-emerald-700';
    if (status === 'recusada') return 'bg-red-50 text-red-700';
    if (status === 'em_analise') return 'bg-blue-50 text-blue-700';
    return 'bg-amber-50 text-amber-700';
}

function optionsStatus(statusAtualDenuncia) {
    const opcoes = [
        ['pendente', 'Pendente'],
        ['em_analise', 'Em análise'],
        ['resolvida', 'Resolvida'],
        ['recusada', 'Recusada']
    ];

    return opcoes.map(([valor, label]) => {
        const selected = valor === statusAtualDenuncia ? 'selected' : '';
        return `<option value="${valor}" ${selected}>${label}</option>`;
    }).join('');
}

function nomeDenunciado(denuncia) {
    if (denuncia.denunciado_tipo === 'lojista' && denuncia.denunciado_nome_loja) {
        return `${denuncia.denunciado_nome_loja} (${denuncia.denunciado_nome})`;
    }

    return denuncia.denunciado_nome;
}

function cardDenuncia(denuncia) {
    return `
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-xl font-bold text-slate-950">${escaparHtml(nomeDenunciado(denuncia))}</h2>
                        <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-[0.16em] ${classeStatus(denuncia.status)}">${textoStatus(denuncia.status)}</span>
                    </div>
                    <p class="mt-1 text-sm text-slate-500">
                        Denunciado: ${escaparHtml(denuncia.denunciado_email)} · ${escaparHtml(denuncia.denunciado_tipo)}
                    </p>
                    <p class="mt-1 text-sm text-slate-500">
                        Denunciante: ${escaparHtml(denuncia.denunciante_nome)} · ${escaparHtml(denuncia.denunciante_email)}
                    </p>
                </div>
                <p class="text-sm text-slate-400">${formatarData(denuncia.criado_em)}</p>
            </div>

            <div class="mt-5 rounded-xl bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Motivo informado</p>
                <p class="mt-2 whitespace-pre-wrap text-sm leading-6 text-slate-700">${escaparHtml(denuncia.motivo)}</p>
            </div>

            <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <label for="statusDenuncia${denuncia.id}" class="mb-1.5 block text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Status</label>
                    <select id="statusDenuncia${denuncia.id}" data-id="${denuncia.id}" class="campoStatus w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-blue-600 focus:outline-none focus:ring-1 focus:ring-blue-600">
                        ${optionsStatus(denuncia.status)}
                    </select>
                </div>
                <button type="button" data-id="${denuncia.id}" class="btnSalvarStatus rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Salvar status
                </button>
            </div>
        </article>
    `;
}

async function verificarAdmin() {
    const resposta = await fetch(`${CAMINHO_API}/auth/sessao.php`, { credentials: 'include' });
    const json = await resposta.json();

    if (json.status !== 'ok' || !json.data.usuario) {
        window.location.href = `${CAMINHO_FRONTEND}/login.html`;
        return false;
    }

    if (json.data.usuario.tipo !== 'administrador') {
        window.location.href = `${CAMINHO_FRONTEND}/catalogo.html`;
        return false;
    }

    return true;
}

async function carregarDenuncias() {
    listaDenuncias.innerHTML = '<div class="rounded-2xl border border-slate-200 bg-white p-6 text-sm text-slate-500 shadow-sm">Carregando denúncias...</div>';

    const query = statusAtual ? `?status=${encodeURIComponent(statusAtual)}` : '';
    const resposta = await fetch(`${CAMINHO_API}/denuncias/listar.php${query}`, { credentials: 'include' });
    const json = await resposta.json();

    if (json.status !== 'ok') {
        listaDenuncias.innerHTML = `<div class="rounded-2xl border border-red-200 bg-red-50 p-6 text-sm text-red-700">${escaparHtml(json.mensagem || 'Não foi possível carregar denúncias.')}</div>`;
        return;
    }

    const denuncias = json.data || [];
    if (denuncias.length === 0) {
        listaDenuncias.innerHTML = '<div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-500 shadow-sm">Nenhuma denúncia encontrada.</div>';
        return;
    }

    listaDenuncias.innerHTML = denuncias.map(cardDenuncia).join('');
}

async function atualizarStatus(botao) {
    const denunciaId = botao.dataset.id;
    const campo = document.querySelector(`.campoStatus[data-id="${denunciaId}"]`);
    if (!campo) return;

    const dados = new FormData();
    dados.append('id', denunciaId);
    dados.append('status', campo.value);

    botao.disabled = true;

    try {
        const resposta = await fetch(`${CAMINHO_API}/denuncias/atualizar_status.php`, {
            method: 'POST',
            body: dados,
            credentials: 'include'
        });
        const json = await resposta.json();

        if (json.status !== 'ok') {
            mostrarFeedback(json.mensagem || 'Não foi possível atualizar a denúncia.', false);
            return;
        }

        mostrarFeedback(json.mensagem || 'Status atualizado.');
        await carregarDenuncias();
    } catch (erro) {
        mostrarFeedback('Falha ao atualizar a denúncia.', false);
    } finally {
        botao.disabled = false;
    }
}

document.querySelectorAll('.filtroStatus').forEach((botao) => {
    botao.addEventListener('click', () => {
        statusAtual = botao.dataset.status;
        document.querySelectorAll('.filtroStatus').forEach((item) => {
            item.classList.toggle('bg-blue-50', item === botao);
            item.classList.toggle('text-blue-700', item === botao);
            item.classList.toggle('text-slate-600', item !== botao);
        });
        carregarDenuncias();
    });
});

listaDenuncias.addEventListener('click', (evento) => {
    const botao = evento.target.closest('.btnSalvarStatus');
    if (botao) atualizarStatus(botao);
});

verificarAdmin().then((autorizado) => {
    if (autorizado) carregarDenuncias();
});
