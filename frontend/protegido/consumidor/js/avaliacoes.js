const listaAvaliacoes = document.getElementById('listaAvaliacoes');

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

function textoStatusSolicitacao(status) {
    if (status === 'aceita') return 'Aceita';
    if (status === 'recusada') return 'Recusada';
    return 'Pendente';
}

function classeStatusSolicitacao(status) {
    if (status === 'aceita') return 'bg-emerald-50 text-emerald-700';
    if (status === 'recusada') return 'bg-red-50 text-red-700';
    return 'bg-amber-50 text-amber-700';
}

function textoStatusAvaliacao(status) {
    const mapa = {
        aguardando_envio: 'Aguardando envio',
        recebido: 'Recebido',
        em_avaliacao: 'Em avaliação',
        avaliado: 'Avaliado',
        proposta_enviada: 'Proposta enviada',
        finalizado: 'Finalizado'
    };
    return mapa[status] || 'Aguardando envio';
}

function progressoStatus(status) {
    const ordem = ['aguardando_envio', 'recebido', 'em_avaliacao', 'avaliado', 'proposta_enviada', 'finalizado'];
    const indice = Math.max(ordem.indexOf(status || 'aguardando_envio'), 0);
    return Math.round((indice / (ordem.length - 1)) * 100);
}

function cardAvaliacao(avaliacao) {
    const statusAvaliacao = avaliacao.status_avaliacao || 'aguardando_envio';
    const progresso = progressoStatus(statusAvaliacao);
    const podeAbrirChat = avaliacao.status === 'aceita' && avaliacao.chat_id;

    return `
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-xl font-bold text-slate-950">${escaparHtml(avaliacao.nome_item)}</h2>
                        <span class="rounded-full px-3 py-1 text-xs font-bold uppercase tracking-[0.16em] ${classeStatusSolicitacao(avaliacao.status)}">${textoStatusSolicitacao(avaliacao.status)}</span>
                    </div>
                    <p class="mt-1 text-sm text-slate-500">${escaparHtml(avaliacao.nome_loja)} · ${escaparHtml(avaliacao.categoria)}</p>
                </div>
                <p class="text-sm text-slate-400">${formatarData(avaliacao.criado_em)}</p>
            </div>

            <div class="mt-5 rounded-xl bg-slate-50 p-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Status do item</p>
                    <p class="text-sm font-semibold text-slate-800">${textoStatusAvaliacao(statusAvaliacao)}</p>
                </div>
                <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-200">
                    <div class="h-full rounded-full bg-blue-600" style="width: ${progresso}%"></div>
                </div>
            </div>

            <div class="mt-5 grid gap-3 md:grid-cols-2">
                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Estado informado</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">${escaparHtml(avaliacao.estado)}</p>
                </div>
                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Resposta da loja</p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">${avaliacao.respondido_em ? formatarData(avaliacao.respondido_em) : 'Aguardando'}</p>
                </div>
            </div>

            <p class="mt-4 text-sm leading-6 text-slate-600">${escaparHtml(avaliacao.detalhes || 'Sem detalhes adicionais.')}</p>

            <div class="mt-5 flex flex-wrap gap-3">
                ${podeAbrirChat ? `
                    <a href="./chat.html?chat_id=${avaliacao.chat_id}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                        <i data-lucide="messages-square" class="h-4 w-4"></i>
                        Abrir conversa
                    </a>
                ` : ''}
            </div>
        </article>
    `;
}

async function carregarAvaliacoes() {
    const resposta = await fetch(`${CAMINHO_API}/avaliacoes/minhas.php`, { credentials: 'include' });
    const json = await resposta.json();

    if (json.status !== 'ok') {
        listaAvaliacoes.innerHTML = `<div class="rounded-2xl border border-red-200 bg-red-50 p-6 text-sm text-red-700">${escaparHtml(json.mensagem || 'Não foi possível carregar avaliações.')}</div>`;
        return;
    }

    const avaliacoes = json.data || [];
    if (avaliacoes.length === 0) {
        listaAvaliacoes.innerHTML = '<div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-500 shadow-sm">Nenhuma avaliação enviada ainda.</div>';
        return;
    }

    listaAvaliacoes.innerHTML = avaliacoes.map(cardAvaliacao).join('');
    lucide.createIcons();
}

carregarAvaliacoes();
