const parametros = new URLSearchParams(window.location.search);
const lojaPreSelecionada = parametros.get('loja_id') || '';

const campoLoja = document.getElementById('campoLoja');
const campoNome = document.getElementById('campoNome');
const campoTipo = document.getElementById('campoTipo');
const campoDetalhes = document.getElementById('campoDetalhes');
const formAvaliacao = document.getElementById('formAvaliacao');
const btnEnviarAvaliacao = document.getElementById('btnEnviarAvaliacao');
const mensagemFeedback = document.getElementById('mensagemFeedback');

function mostrarFeedback(texto, sucesso = true) {
    mensagemFeedback.textContent = texto;
    mensagemFeedback.className = sucesso
        ? 'mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700'
        : 'mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700';
}

async function carregarLojas() {
    const resposta = await fetch(`${CAMINHO_API}/lojas/get.php`, { credentials: 'include' });
    const json = await resposta.json();
    const lojas = json.status === 'ok' ? json.data : [];

    campoLoja.innerHTML = lojas.map((loja) => {
        const selected = String(loja.id) === lojaPreSelecionada ? 'selected' : '';
        return `<option value="${loja.id}" ${selected}>${loja.nome_loja}</option>`;
    }).join('');
}

async function enviarAvaliacao(evento) {
    evento.preventDefault();

    const estado = document.querySelector('input[name="estado"]:checked')?.value || '';
    const dados = new FormData();
    dados.append('loja_id', campoLoja.value);
    dados.append('nome', campoNome.value.trim());
    dados.append('tipopeca', campoTipo.value);
    dados.append('estado', estado);
    dados.append('detalhes', campoDetalhes.value.trim());

    btnEnviarAvaliacao.disabled = true;

    try {
        const resposta = await fetch(`${CAMINHO_API}/avaliacoes/criar.php`, {
            method: 'POST',
            body: dados,
            credentials: 'include'
        });
        const json = await resposta.json();

        if (json.status !== 'ok') {
            mostrarFeedback(json.mensagem || 'Não foi possível enviar a avaliação.', false);
            return;
        }

        mostrarFeedback('Avaliação enviada. A loja vai analisar e liberar o chat se aceitar.');
        formAvaliacao.reset();
        if (lojaPreSelecionada) campoLoja.value = lojaPreSelecionada;
    } catch (erro) {
        mostrarFeedback('Falha ao enviar avaliação.', false);
    } finally {
        btnEnviarAvaliacao.disabled = false;
    }
}

formAvaliacao.addEventListener('submit', enviarAvaliacao);
carregarLojas();
