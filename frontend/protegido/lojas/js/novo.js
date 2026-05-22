// novo.js — formulário de criação de loja (apenas criação)

async function iniciarNovoLoja() {
    const resposta = await fetch(CAMINHO_API + '/auth/sessao.php', { credentials: 'include' });
    const json = await resposta.json();

    if (json.status !== 'ok' || !json.data.usuario) {
        window.location.href = CAMINHO_FRONTEND + '/login.html';
        return;
    }

    if (json.data.usuario.tipo !== 'lojista') {
        window.location.href = CAMINHO_FRONTEND + '/catalogo.html';
        return;
    }

    // Se já tem loja, vai para a página de alterar
    if (json.data.loja) {
        window.location.href = CAMINHO_FRONTEND + '/protegido/lojas/alterar.html';
        return;
    }

    document.getElementById('inputCnpj').addEventListener('input', (evento) => {
        evento.target.value = formatarCnpj(evento.target.value);
    });
    document.getElementById('inputCep').addEventListener('input', (evento) => {
        evento.target.value = somenteDigitos(evento.target.value).slice(0, 8);
    });
    document.getElementById('inputTelefone').addEventListener('input', (evento) => {
        evento.target.value = formatarTelefone(evento.target.value);
    });
    document.getElementById('inputEstado').addEventListener('input', (evento) => {
        evento.target.value = evento.target.value.replace(/[^a-zA-Z]/g, '').slice(0, 2).toUpperCase();
    });
    document.getElementById('btnSalvarLoja').addEventListener('click', salvarLoja);
}

function somenteDigitos(valor) {
    return String(valor || '').replace(/\D+/g, '');
}

function formatarCnpj(valor) {
    const digitos = somenteDigitos(valor).slice(0, 14);
    return digitos
        .replace(/^(\d{2})(\d)/, '$1.$2')
        .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
        .replace(/\.(\d{3})(\d)/, '.$1/$2')
        .replace(/(\d{4})(\d)/, '$1-$2');
}

function formatarTelefone(valor) {
    const digitos = somenteDigitos(valor).slice(0, 11);
    if (digitos.length <= 10) {
        return digitos.replace(/^(\d{2})(\d)/, '($1) $2').replace(/(\d{4})(\d)/, '$1-$2');
    }

    return digitos.replace(/^(\d{2})(\d)/, '($1) $2').replace(/(\d{5})(\d)/, '$1-$2');
}

function cnpjValido(valor) {
    const cnpj = somenteDigitos(valor);
    if (cnpj.length !== 14 || /^(\d)\1+$/.test(cnpj)) return false;

    const calcular = (base, pesos) => {
        const soma = base.split('').reduce((total, numero, indice) => total + Number(numero) * pesos[indice], 0);
        const resto = soma % 11;
        return resto < 2 ? 0 : 11 - resto;
    };

    const primeiro = calcular(cnpj.slice(0, 12), [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);
    const segundo = calcular(cnpj.slice(0, 13), [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);

    return Number(cnpj[12]) === primeiro && Number(cnpj[13]) === segundo;
}

async function salvarLoja() {
    const nomeLoja   = document.getElementById('inputNomeLoja').value.trim();
    const cnpj       = document.getElementById('inputCnpj').value.trim();
    const telefone   = document.getElementById('inputTelefone').value.trim();
    const cep        = document.getElementById('inputCep').value.trim();
    const estado     = document.getElementById('inputEstado').value.trim();
    const cidade     = document.getElementById('inputCidade').value.trim();
    const bairro     = document.getElementById('inputBairro').value.trim();
    const logradouro = document.getElementById('inputLogradouro').value.trim();
    const numero     = document.getElementById('inputNumero').value.trim();

    esconderFeedback();

    if (!nomeLoja || !cnpj) {
        mostrarFeedback('Nome da loja e CNPJ são obrigatórios.', false);
        return;
    }

    if (nomeLoja.length < 3) {
        mostrarFeedback('Informe um nome de loja com pelo menos 3 caracteres.', false);
        return;
    }

    if (!cnpjValido(cnpj)) {
        mostrarFeedback('Digite um CNPJ válido.', false);
        return;
    }

    if (telefone && ![10, 11].includes(somenteDigitos(telefone).length)) {
        mostrarFeedback('Telefone deve ter DDD e 10 ou 11 dígitos.', false);
        return;
    }

    if (cep && somenteDigitos(cep).length !== 8) {
        mostrarFeedback('CEP deve ter 8 dígitos.', false);
        return;
    }

    if (estado && !/^[A-Z]{2}$/.test(estado)) {
        mostrarFeedback('Estado deve ser uma sigla com 2 letras.', false);
        return;
    }

    const btn = document.getElementById('btnSalvarLoja');
    btn.disabled = true;
    btn.textContent = 'Salvando...';

    const fd = new FormData();
    fd.append('nome_loja', nomeLoja);
    fd.append('telefone', somenteDigitos(telefone));
    fd.append('cnpj', somenteDigitos(cnpj));
    fd.append('cep', somenteDigitos(cep));
    fd.append('estado', estado.toUpperCase());
    fd.append('cidade', cidade);
    fd.append('bairro', bairro);
    fd.append('logradouro', logradouro);
    fd.append('numero', numero);

    const resposta = await fetch(CAMINHO_API + '/lojas/novo.php', {
        method: 'POST',
        body: fd,
        credentials: 'include'
    });
    const json = await resposta.json();

    if (json.status === 'ok') {
        mostrarFeedback('Loja cadastrada!', true);
        setTimeout(() => { window.location.href = CAMINHO_FRONTEND + '/protegido/lojas/gerenciar_produtos.html'; }, 1200);
    } else {
        mostrarFeedback(json.mensagem || 'Erro ao salvar.', false);
        btn.disabled = false;
        btn.textContent = 'Cadastrar';
    }
}

function mostrarFeedback(mensagem, sucesso) {
    const el = document.getElementById('mensagemFeedback');
    el.textContent = mensagem;
    el.className = sucesso ? 'rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700' : 'rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700';
    el.classList.remove('hidden');
}

function esconderFeedback() { document.getElementById('mensagemFeedback').classList.add('hidden'); }

// Inicia a página
iniciarNovoLoja();
