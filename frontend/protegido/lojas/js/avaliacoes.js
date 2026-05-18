let statusAtual = '';

const listaAvaliacoes = document.getElementById('listaAvaliacoes');
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

function mostrarFeedback(texto, sucesso = true) {
    mensagemFeedback.textContent = texto;
    mensagemFeedback.className = sucesso
        ? 'rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700'
        : 'rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700';
}

function classeStatus(status) {
    if (status === 'aceita') return 'bg-emerald-50 text-emerald-700';
    if (status === 'recusada') return 'bg-red-50 text-red-700';
    return 'bg-amber-50 text-amber-700';
}

function textoStatus(status) {
    if (status === 'aceita') return 'Aceita';
    if (status === 'recusada') return 'Recusada';
    return 'Pendente';
}

function cardAvaliacao(avaliacao) {
    const pendente = avaliacao.status === 'pendente';
    const aceita = avaliacao.status === 'aceita';
    const detalhes = avaliacao.detalhes || 'Sem detalhes adicionais.';

    return `
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-xl font-bold text-slate-950">${escaparHtml(avaliacao.nome_item)}</h2>
                        <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-[0.16em] ${classeStatus(avaliacao.status)}">${textoStatus(avaliacao.status)}</span>
                    </div>
                    <p class="mt-1 text-sm text-slate-500">${escaparHtml(avaliacao.consumidor_nome)} · ${escaparHtml(avaliacao.consumidor_email)}</p>
                </div>
                <p class="text-sm text-slate-400">${formatarData(avaliacao.criado_em)}</p>
            </div>

            <div class="mt-5 grid gap-3 md:grid-cols-3">
                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Categoria</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">${escaparHtml(avaliacao.categoria)}</p>
                </div>
                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Estado</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">${escaparHtml(avaliacao.estado)}</p>
                </div>
                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Resposta</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">${avaliacao.respondido_em ? formatarData(avaliacao.respondido_em) : 'Aguardando'}</p>
                </div>
            </div>

            <p class="mt-4 text-sm leading-6 text-slate-600">${escaparHtml(detalhes)}</p>

            <div class="mt-5 flex flex-wrap gap-3">
                ${pendente ? `
                    <button type="button" data-id="${avaliacao.id}" data-acao="aceitar" class="btnResponder inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                        <i data-lucide="check" class="h-4 w-4"></i>
                        Aceitar e liberar chat
                    </button>
                    <button type="button" data-id="${avaliacao.id}" data-acao="recusar" class="btnResponder inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        <i data-lucide="x" class="h-4 w-4"></i>
                        Recusar
                    </button>
                ` : ''}
                ${aceita ? `
                    <a href="./chat.html?avaliacao_id=${avaliacao.id}" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">
                        <i data-lucide="messages-square" class="h-4 w-4"></i>
                        Abrir chat
                    </a>
                ` : ''}
            </div>
        </article>
    `;
}

async function carregarAvaliacoes() {
    listaAvaliacoes.innerHTML = '<div class="rounded-2xl border border-slate-200 bg-white p-6 text-sm text-slate-500 shadow-sm">Carregando solicitações...</div>';

    const query = statusAtual ? `?status=${encodeURIComponent(statusAtual)}` : '';
    const resposta = await fetch(`${CAMINHO_API}/avaliacoes/listar.php${query}`, { credentials: 'include' });
    const json = await resposta.json();

    if (json.status !== 'ok') {
        listaAvaliacoes.innerHTML = `<div class="rounded-2xl border border-red-200 bg-red-50 p-6 text-sm text-red-700">${escaparHtml(json.mensagem || 'Não foi possível carregar solicitações.')}</div>`;
        return;
    }

    const avaliacoes = json.data || [];
    if (avaliacoes.length === 0) {
        listaAvaliacoes.innerHTML = '<div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-500 shadow-sm">Nenhuma solicitação encontrada.</div>';
        return;
    }

    listaAvaliacoes.innerHTML = avaliacoes.map(cardAvaliacao).join('');
    lucide.createIcons();
}

async function responderAvaliacao(botao) {
    const dados = new FormData();
    dados.append('avaliacao_id', botao.dataset.id);
    dados.append('acao', botao.dataset.acao);

    botao.disabled = true;

    try {
        const resposta = await fetch(`${CAMINHO_API}/avaliacoes/responder.php`, {
            method: 'POST',
            body: dados,
            credentials: 'include'
        });
        const json = await resposta.json();

        if (json.status !== 'ok') {
            mostrarFeedback(json.mensagem || 'Não foi possível responder a solicitação.', false);
            return;
        }

        mostrarFeedback(json.mensagem || 'Solicitação atualizada.');
        await carregarAvaliacoes();
    } catch (erro) {
        mostrarFeedback('Falha ao responder a solicitação.', false);
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
        carregarAvaliacoes();
    });
});

listaAvaliacoes.addEventListener('click', (evento) => {
    const botao = evento.target.closest('.btnResponder');
    if (botao) responderAvaliacao(botao);
});

carregarAvaliacoes();
