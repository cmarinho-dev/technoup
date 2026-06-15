const contaDenunciada = document.getElementById('contaDenunciada');
const motivoDenuncia = document.getElementById('motivoDenuncia');
const btnEnviarDenuncia = document.getElementById('btnEnviarDenuncia');
const mensagemFeedback = document.getElementById('mensagemFeedback');

function escaparHtml(texto) {
    const div = document.createElement('div');
    div.textContent = texto || '';
    return div.innerHTML;
}

function mostrarFeedback(mensagem, sucesso = true) {
    mensagemFeedback.textContent = mensagem;
    mensagemFeedback.className = sucesso
        ? 'mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700'
        : 'mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700';
}

function obterParametro(nome) {
    return new URLSearchParams(window.location.search).get(nome);
}

function rotuloLoja(conta) {
    if (conta.nome_loja) {
        return `${conta.nome_loja} (${conta.nome})`;
    }

    return conta.nome;
}

async function verificarSessao() {
    const resposta = await fetch(`${CAMINHO_API}/auth/sessao.php`, { credentials: 'include' });
    const json = await resposta.json();

    if (json.status !== 'ok' || !json.data.usuario) {
        window.location.href = `${CAMINHO_FRONTEND}/login.html`;
        return false;
    }

    if (json.data.usuario.tipo !== 'consumidor') {
        window.location.href = `${CAMINHO_FRONTEND}/catalogo.html`;
        return false;
    }

    return true;
}

async function carregarLojas() {
    const resposta = await fetch(`${CAMINHO_API}/denuncias/alvos.php`, { credentials: 'include' });
    const json = await resposta.json();

    if (json.status !== 'ok') {
        contaDenunciada.innerHTML = '<option value="">Não foi possível carregar lojas</option>';
        mostrarFeedback(json.mensagem || 'Não foi possível carregar lojas.', false);
        return;
    }

    const lojas = (json.data || []).filter((conta) => conta.tipo === 'lojista');

    if (lojas.length === 0) {
        contaDenunciada.innerHTML = '<option value="">Nenhuma loja disponível</option>';
        return;
    }

    const alvoId = obterParametro('conta_id');
    contaDenunciada.innerHTML = `
        <option value="">Selecione uma loja...</option>
        ${lojas.map((conta) => `
            <option value="${conta.id}" ${String(conta.id) === String(alvoId) ? 'selected' : ''}>
                ${escaparHtml(rotuloLoja(conta))}
            </option>
        `).join('')}
    `;
}

async function enviarDenuncia() {
    const denunciadoId = contaDenunciada.value;
    const motivo = motivoDenuncia.value.trim();

    if (!denunciadoId) {
        mostrarFeedback('Selecione a loja denunciada.', false);
        contaDenunciada.focus();
        return;
    }

    if (motivo.length < 10) {
        mostrarFeedback('Informe o motivo da denúncia com pelo menos 10 caracteres.', false);
        motivoDenuncia.focus();
        return;
    }

    const dados = new FormData();
    dados.append('denunciado_id', denunciadoId);
    dados.append('motivo', motivo);

    btnEnviarDenuncia.disabled = true;
    btnEnviarDenuncia.textContent = 'Enviando...';

    try {
        const resposta = await fetch(`${CAMINHO_API}/denuncias/salvar.php`, {
            method: 'POST',
            body: dados,
            credentials: 'include'
        });
        const json = await resposta.json();

        if (json.status !== 'ok') {
            mostrarFeedback(json.mensagem || 'Não foi possível enviar a denúncia.', false);
            return;
        }

        mostrarFeedback(json.mensagem || 'Denúncia registrada com sucesso.');
        motivoDenuncia.value = '';
        contaDenunciada.value = '';
    } catch (erro) {
        mostrarFeedback('Falha ao enviar a denúncia.', false);
    } finally {
        btnEnviarDenuncia.disabled = false;
        btnEnviarDenuncia.textContent = 'Enviar denúncia';
    }
}

btnEnviarDenuncia.addEventListener('click', enviarDenuncia);

verificarSessao().then((autenticado) => {
    if (autenticado) carregarLojas();
});
