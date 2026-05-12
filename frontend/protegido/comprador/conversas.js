const listaConversas = document.getElementById('listaConversas');

function escaparHtml(texto) {
    const div = document.createElement('div');
    div.textContent = texto || '';
    return div.innerHTML;
}

function formatarData(data) {
    if (!data) return 'Sem mensagens';
    return new Intl.DateTimeFormat('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    }).format(new Date(data.replace(' ', 'T')));
}

function renderizarConversa(chat) {
    const titulo = TIPO_LISTA_CHAT === 'lojista' ? chat.consumidor_nome : chat.nome_loja;
    const subtitulo = `${chat.nome_peca} · ${chat.categoria}`;
    const link = TIPO_LISTA_CHAT === 'lojista'
        ? `../comprador/chat.html?chat_id=${chat.chat_id}`
        : `./chat.html?chat_id=${chat.chat_id}`;
    const lidoEm = TIPO_LISTA_CHAT === 'lojista' ? chat.lido_lojista_em : chat.lido_consumidor_em;
    const naoLida = chat.ultima_mensagem_em && (!lidoEm || new Date(chat.ultima_mensagem_em.replace(' ', 'T')) > new Date(lidoEm.replace(' ', 'T')));

    return `
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-bold text-slate-950">${escaparHtml(titulo)}</h2>
                        <span class="rounded-full ${chat.chat_status === 'aberto' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600'} px-3 py-1 text-xs font-bold uppercase tracking-[0.16em]">${chat.chat_status}</span>
                        ${naoLida ? '<span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold uppercase tracking-[0.16em] text-blue-700">Nova</span>' : ''}
                    </div>
                    <p class="mt-1 text-sm text-slate-500">${escaparHtml(subtitulo)}</p>
                </div>
                <p class="text-sm text-slate-400">${formatarData(chat.ultima_mensagem_em || chat.atualizado_em)}</p>
            </div>
            <p class="mt-4 line-clamp-2 text-sm leading-6 text-slate-600">${escaparHtml(chat.ultima_mensagem || 'Conversa liberada. Envie a primeira mensagem.')}</p>
            <div class="mt-5">
                <a href="${link}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                    <i data-lucide="messages-square" class="h-4 w-4"></i>
                    Abrir conversa
                </a>
            </div>
        </article>
    `;
}

async function carregarConversas() {
    const resposta = await fetch(`${CAMINHO_API}/chat/listar.php`, { credentials: 'include' });
    const json = await resposta.json();

    if (json.status !== 'ok') {
        listaConversas.innerHTML = `<div class="rounded-2xl border border-red-200 bg-red-50 p-6 text-sm text-red-700">${escaparHtml(json.mensagem || 'Não foi possível carregar conversas.')}</div>`;
        return;
    }

    const chats = json.data || [];
    if (chats.length === 0) {
        listaConversas.innerHTML = '<div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-500 shadow-sm">Nenhuma conversa liberada ainda.</div>';
        return;
    }

    listaConversas.innerHTML = chats.map(renderizarConversa).join('');
    lucide.createIcons();
}

carregarConversas();
