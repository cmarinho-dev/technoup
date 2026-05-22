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

function rotuloCliente(conta) {
    return conta.nome;
}

async function verificarSessao() {
    const resposta = await fetch(`${CAMINHO_API}/auth/sessao.php`, { credentials: 'include' });
    const json = await resposta.json();

    if (json.status !== 'ok' || !json.data.usuario) {
        window.location.href = `${CAMINHO_FRONTEND}/login.html`;
        return false;
    }

    if (json.data.usuario.tipo !== 'lojista') {
        window.location.href = `${CAMINHO_FRONTEND}/catalogo.html`;
        return false;
    }

    return true;
}

async function carregarClientes() {
    const resposta = await fetch(`${CAMINHO_API}/denuncias/alvos.php`, { credentials: 'include' });
    const json = await resposta.json();

    if (json.status !== 'ok') {
        contaDenunciada.innerHTML = '<option value="">Não foi possível carregar clientes</option>';
        mostrarFeedback(json.mensagem || 'Não foi possível carregar clientes.', false);
        return;
    }

    const clientes = (json.data || []).filter((conta) => conta.tipo === 'consumidor');

    if (clientes.length === 0) {
        contaDenunciada.innerHTML = '<option value="">Nenhum cliente disponível</option>';
        return;
    }

    const alvoId = obterParametro('conta_id');
    contaDenunciada.innerHTML = `
        <option value="">Selecione um cliente...</option>
        ${clientes.map((conta) => `
            <option value="${conta.id}" ${String(conta.id) === String(alvoId) ? 'selected' : ''}>
                ${escaparHtml(rotuloCliente(conta))}
            </option>
        `).join('')}
    `;
}

async function enviarDenuncia() {
    const denunciadoId = contaDenunciada.value;
    const motivo = motivoDenuncia.value.trim();

    if (!denunciadoId) {
        mostrarFeedback('Selecione o cliente denunciado.', false);
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
    if (autenticado) carregarClientes();
});
