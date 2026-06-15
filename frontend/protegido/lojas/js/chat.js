const intervaloAtualizacaoMs = 4000;

let ultimoId = 0;
let carregando = false;
let pollingId = null;
let usuarioAtual = null;
let chatInicializado = false;
let chatAtual = null;

const parametros = new URLSearchParams(window.location.search);
const avaliacaoId = parametros.get('avaliacao_id') || '';
const chatIdInicial = parametros.get('chat_id') || '';

const listaMensagens = document.getElementById('listaMensagens');
const formChat = document.getElementById('formChat');
const campoMensagem = document.getElementById('campoMensagem');
const btnEnviar = document.getElementById('btnEnviar');
const mensagemErro = document.getElementById('mensagemErro');
const lojaNome = document.getElementById('lojaNome');
const lojaBanner = document.getElementById('lojaBanner');
const lojaIcone = document.getElementById('lojaIcone');
const btnFecharChat = document.getElementById('btnFecharChat');

lojaBanner.addEventListener('load', () => {
    lojaBanner.classList.remove('hidden');
    lojaIcone.classList.add('hidden');
});

lojaBanner.addEventListener('error', () => {
    lojaBanner.removeAttribute('src');
    lojaBanner.classList.add('hidden');
    lojaIcone.classList.remove('hidden');
});

function mostrarErro(texto) {
    mensagemErro.textContent = texto;
    mensagemErro.classList.remove('hidden');
}

function limparErro() {
    mensagemErro.textContent = '';
    mensagemErro.classList.add('hidden');
}

function escaparHtml(texto) {
    const div = document.createElement('div');
    div.textContent = texto;
    return div.innerHTML;
}

function formatarHora(data) {
    return new Intl.DateTimeFormat('pt-BR', {
        hour: '2-digit',
        minute: '2-digit'
    }).format(new Date(data.replace(' ', 'T')));
}

function renderizarMensagem(mensagem) {
    const enviadaPelaLoja = Number(mensagem.is_cliente) === 1;
    const souConsumidor = usuarioAtual?.tipo === 'consumidor';
    const minhaMensagem = souConsumidor ? !enviadaPelaLoja : enviadaPelaLoja;
    const lado = minhaMensagem ? 'mine' : 'other';
    const autor = minhaMensagem ? 'Você' : (enviadaPelaLoja ? 'Loja' : 'Consumidor');
    const textoMensagem = mensagem.mensagem
        ? `<p class="chat-message-text">${escaparHtml(mensagem.mensagem)}</p>`
        : '';

    return `
        <div class="chat-message-row chat-message-row--${lado}">
            <div class="chat-message-bubble chat-message-bubble--${lado}">
                <div class="chat-message-meta chat-message-meta--${lado}">
                    <span>${autor}</span>
                    <span>${formatarHora(mensagem.criado_em)}</span>
                </div>
                ${textoMensagem}
            </div>
        </div>
    `;
}

function adicionarMensagens(mensagens) {
    if (!mensagens.length) return;

    if (ultimoId === 0) {
        listaMensagens.innerHTML = '';
    }

    const estavaNoFim = listaMensagens.scrollHeight - listaMensagens.scrollTop - listaMensagens.clientHeight < 80;
    listaMensagens.insertAdjacentHTML('beforeend', mensagens.map(renderizarMensagem).join(''));
    ultimoId = Math.max(...mensagens.map((mensagem) => Number(mensagem.id)), ultimoId);

    if (estavaNoFim) {
        listaMensagens.scrollTop = listaMensagens.scrollHeight;
    }
}

function renderizarVazio() {
    if (ultimoId > 0) return;
    listaMensagens.innerHTML = `
        <div class="m-auto max-w-sm text-center">
            <i data-lucide="messages-square" class="mx-auto mb-3 h-10 w-10 text-slate-300"></i>
            <p class="text-sm font-medium text-slate-700">Comece a conversa com a loja.</p>
            <p class="mt-1 text-sm text-slate-500">A conversa foi liberada a partir da avaliação aceita.</p>
        </div>
    `;
    lucide.createIcons();
}

function caminhoImagem(caminho) {
    if (!caminho) return '';
    if (/^(https?:)?\/\//.test(caminho) || caminho.startsWith('/')) return caminho;
    return `${CAMINHO_FRONTEND}/${caminho}`;
}

async function marcarComoLida() {
    if (!chatAtual?.id) return;
    const dados = new FormData();
    dados.append('chat_id', chatAtual.id);
    await fetch(`${CAMINHO_API}/chat/marcar_lida.php`, {
        method: 'POST',
        body: dados,
        credentials: 'include'
    });
}

function aplicarEstadoChat() {
    const fechado = chatAtual?.status === 'fechado';
    campoMensagem.disabled = fechado;
    btnEnviar.disabled = fechado;
    btnFecharChat.disabled = fechado;
    if (fechado) {
        campoMensagem.placeholder = 'Este chat foi fechado.';
        btnFecharChat.textContent = 'Chat fechado';
    }
}

async function carregarMensagens() {
    if (carregando || document.visibilityState !== 'visible') return;

    carregando = true;
    try {
        const params = new URLSearchParams({ ultimo_id: ultimoId });
        if (chatAtual?.id) {
            params.set('chat_id', chatAtual.id);
        } else if (chatIdInicial) {
            params.set('chat_id', chatIdInicial);
        } else if (avaliacaoId) {
            params.set('avaliacao_id', avaliacaoId);
        }

        const resposta = await fetch(`${CAMINHO_API}/chat/get.php?${params.toString()}`, {
            credentials: 'include'
        });
        const json = await resposta.json();

        if (json.status !== 'ok') {
            mostrarErro(json.mensagem || 'Não foi possível carregar o chat.');
            return;
        }

        limparErro();
        usuarioAtual = json.data.usuario;
        chatAtual = json.data.chat;
        lojaNome.textContent = json.data.loja?.nome_loja || 'Chat da loja';
        aplicarEstadoChat();

        if (json.data.loja?.banner_img) {
            lojaBanner.src = caminhoImagem(json.data.loja.banner_img);
            lojaBanner.alt = `Banner da loja ${lojaNome.textContent}`;
        } else {
            lojaBanner.removeAttribute('src');
            lojaBanner.classList.add('hidden');
            lojaIcone.classList.remove('hidden');
        }

        adicionarMensagens(json.data.mensagens || []);
        chatInicializado = true;
        marcarComoLida();
        if (ultimoId === 0) renderizarVazio();
    } catch (erro) {
        mostrarErro('Falha ao conectar com o chat.');
    } finally {
        carregando = false;
    }
}

async function enviarMensagem(evento) {
    evento.preventDefault();

    const mensagem = campoMensagem.value.trim();
    if (!mensagem) return;

    btnEnviar.disabled = true;
    limparErro();

    const dados = new FormData();
    dados.append('mensagem', mensagem);
    if (chatAtual?.id) {
        dados.append('chat_id', chatAtual.id);
    } else if (chatIdInicial) {
        dados.append('chat_id', chatIdInicial);
    } else if (avaliacaoId) {
        dados.append('avaliacao_id', avaliacaoId);
    }

    try {
        const resposta = await fetch(`${CAMINHO_API}/chat/enviar.php`, {
            method: 'POST',
            body: dados,
            credentials: 'include'
        });
        const json = await resposta.json();

        if (json.status !== 'ok') {
            mostrarErro(json.mensagem || 'Não foi possível enviar a mensagem.');
            return;
        }

        campoMensagem.value = '';
        adicionarMensagens([json.data]);
        listaMensagens.scrollTop = listaMensagens.scrollHeight;
    } catch (erro) {
        mostrarErro('Falha ao enviar mensagem.');
    } finally {
        const fechado = chatAtual?.status === 'fechado';
        btnEnviar.disabled = fechado;
        campoMensagem.focus();
    }
}

function iniciarPolling() {
    if (pollingId || document.visibilityState !== 'visible') return;
    if (!chatInicializado) carregarMensagens();
    pollingId = setInterval(carregarMensagens, intervaloAtualizacaoMs);
}

function pararPolling() {
    if (!pollingId) return;
    clearInterval(pollingId);
    pollingId = null;
}

document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
        iniciarPolling();
    } else {
        pararPolling();
    }
});

window.addEventListener('beforeunload', pararPolling);
formChat.addEventListener('submit', enviarMensagem);
campoMensagem.addEventListener('keydown', (evento) => {
    if (evento.key === 'Enter' && !evento.shiftKey) {
        evento.preventDefault();
        formChat.requestSubmit();
    }
});

btnFecharChat.addEventListener('click', async () => {
    if (!chatAtual?.id || !confirm('Deseja fechar este chat?')) return;
    const dados = new FormData();
    dados.append('chat_id', chatAtual.id);
    const resposta = await fetch(`${CAMINHO_API}/chat/fechar.php`, {
        method: 'POST',
        body: dados,
        credentials: 'include'
    });
    const json = await resposta.json();
    if (json.status !== 'ok') {
        mostrarErro(json.mensagem || 'Não foi possível fechar o chat.');
        return;
    }
    chatAtual.status = 'fechado';
    aplicarEstadoChat();
});

iniciarPolling();
