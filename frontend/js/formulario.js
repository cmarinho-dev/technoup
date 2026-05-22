const formAvaliacaoItem = document.getElementById('formAvaliacaoItem');
const campoMidia = document.getElementById('midias');
const midiaSelecionada = document.getElementById('midiaSelecionada');
const mensagemFeedback = document.getElementById('mensagemFeedback');
const LIMITE_ARQUIVOS = 8;

function mostrarFeedback(texto, sucesso = true) {
    mensagemFeedback.textContent = texto;
    mensagemFeedback.className = sucesso
        ? 'rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700'
        : 'rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700';
}

function arquivoMidiaValido() {
    const arquivos = Array.from(campoMidia.files);
    if (arquivos.length === 0) return true;

    if (arquivos.length > LIMITE_ARQUIVOS) {
        mostrarFeedback(`Envie no máximo ${LIMITE_ARQUIVOS} arquivos.`, false);
        return false;
    }

    for (const arquivo of arquivos) {
        const extensao = arquivo.name.split('.').pop().toLowerCase();
        const imagem = ['image/jpeg', 'image/png', 'image/webp'].includes(arquivo.type)
            || ['jpg', 'jpeg', 'png', 'webp'].includes(extensao);
        const video = ['video/mp4', 'video/webm', 'video/quicktime', 'video/x-m4v'].includes(arquivo.type)
            || ['mp4', 'webm', 'mov', 'm4v'].includes(extensao);

        if (!imagem && !video) {
            mostrarFeedback('Envie fotos JPG, PNG, WebP ou vídeos MP4, WebM, MOV ou M4V.', false);
            return false;
        }

        const limiteMB = imagem ? 5 : 20;
        if (arquivo.size > limiteMB * 1024 * 1024) {
            mostrarFeedback(`${arquivo.name} deve ter no máximo ${limiteMB}MB.`, false);
            return false;
        }
    }

    return true;
}

campoMidia.addEventListener('change', () => {
    const arquivos = Array.from(campoMidia.files);
    if (arquivos.length === 0) {
        midiaSelecionada.textContent = 'Até 8 arquivos: JPG, PNG, WebP ou vídeos MP4, WebM, MOV e M4V.';
        return;
    }

    const nomes = arquivos.slice(0, 3).map((arquivo) => arquivo.name).join(', ');
    const restantes = arquivos.length > 3 ? ` e mais ${arquivos.length - 3}` : '';
    midiaSelecionada.textContent = `${arquivos.length} arquivo(s): ${nomes}${restantes}.`;
});

formAvaliacaoItem.addEventListener('submit', async (evento) => {
    evento.preventDefault();
    if (!arquivoMidiaValido()) return;

    const botao = formAvaliacaoItem.querySelector('button[type="submit"]');
    const textoOriginal = 'Enviar Avaliação';
    botao.disabled = true;
    botao.textContent = 'Enviando...';

    try {
        const resposta = await fetch(formAvaliacaoItem.action, {
            method: 'POST',
            body: new FormData(formAvaliacaoItem),
            credentials: 'include'
        });
        const json = await resposta.json();

        if (json.status !== 'ok') {
            mostrarFeedback(json.mensagem || 'Não foi possível enviar a avaliação.', false);
            return;
        }

        mostrarFeedback(json.mensagem || 'Solicitação enviada.');
        formAvaliacaoItem.reset();
        midiaSelecionada.textContent = 'Até 8 arquivos: JPG, PNG, WebP ou vídeos MP4, WebM, MOV e M4V.';
        setTimeout(() => {
            window.location.href = './protegido/consumidor/avaliacoes.html';
        }, 900);
    } catch (erro) {
        mostrarFeedback('Falha ao enviar a avaliação.', false);
    } finally {
        botao.disabled = false;
        botao.textContent = textoOriginal;
    }
});
