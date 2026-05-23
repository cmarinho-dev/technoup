// home.js — Lógica da página inicial

// Departamentos definidos localmente (dados fixos, sem precisar de CAMINHO_API)
const departamentos = [
    { titulo: 'PC Gamer',    descricao: 'Maquinas montadas para jogar e criar.',    icone: 'monitor' },
    { titulo: 'Hardware',    descricao: 'Processadores, placas e upgrades.',         icone: 'cpu' },
    { titulo: 'Perifericos', descricao: 'Teclados, mouses e audio.',                icone: 'mouse' },
    { titulo: 'Monitores',   descricao: 'Imagens fluidas para trabalho e jogo.',    icone: 'monitor-up' },
    { titulo: 'Cadeiras',    descricao: 'Conforto para setups longos.',              icone: 'armchair' },
    { titulo: 'Home Office', descricao: 'Equipamentos para produtividade.',         icone: 'briefcase-business' },
];

async function carregarHome() {
    // Busca produtos (modelo simples: fetch -> json -> verificar status)
    const retornoProdutos = await fetch(CAMINHO_API + '/produtos/get.php', { credentials: 'include' });
    const respostaProdutos = await retornoProdutos.json();
    const produtos = respostaProdutos.status === 'ok' ? (respostaProdutos.data || []) : ([]);
    if (respostaProdutos.status !== 'ok') console.warn('Erro ao carregar produtos:', respostaProdutos.mensagem || respostaProdutos);

    // Busca lojas
    const retornoLojas = await fetch(CAMINHO_API + '/lojas/get.php', { credentials: 'include' });
    const respostaLojas = await retornoLojas.json();
    const lojas = respostaLojas.status === 'ok' ? (respostaLojas.data || []) : ([]);
    if (respostaLojas.status !== 'ok') console.warn('Erro ao carregar lojas:', respostaLojas.mensagem || respostaLojas);

    // Renderiza os três carrosséis
    renderizarCarousel('carousel_departamentos', 'Departamentos em destaque', departamentos.map(construirCardDepartamento));
    renderizarCarousel('carousel_produtos',      'Promoções imperdíveis',     produtos.slice(0, 6).map(construirCardProduto));
    renderizarCarousel('carousel_lojas',         'Lojas para acompanhar',     lojas.slice(0, 6).map(construirCardLoja));

    // Atualiza os ícones do Lucide adicionados dinamicamente
    lucide.createIcons();
}

// Constrói o HTML do card de um produto
function construirCardProduto(produto) {
    const nomeLoja     = produto.nome_loja || 'Loja parceira';
    const desconto     = parseInt(produto.desconto || 0);
    const precoFinal   = parseFloat(produto.preco_final || produto.preco);

    // Formata os preços para exibição
    const preco        = formatarMoeda(precoFinal);
    const precoOriginal = formatarMoeda(parseFloat(produto.preco));

    let imagemHtml = `<i data-lucide="package" class="h-12 w-12 text-slate-300"></i>`;
    if (produto.imagem) {
        const src = produto.imagem.caminho + produto.imagem.arquivo;
        imagemHtml = `<img src="${src}" alt="${produto.nome}" class="h-48 w-full rounded-2xl object-contain sm:h-[230px]" onerror="this.style.display='none'">`;
    }

    let precoMarkup = '';
    if (desconto > 0) {
        precoMarkup = `
            <p class="text-sm tracking-[0.2em] text-slate-400">De <s>${precoOriginal}</s></p>
            <div class="flex items-center gap-3">
                <p class="text-2xl font-bold tracking-tight text-slate-900">Por ${preco}</p>
                <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-sm font-bold text-emerald-700">-${desconto}%</span>
            </div>
        `;
    } else {
        precoMarkup = `
            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Preço atual</p>
            <p class="text-2xl font-bold tracking-tight text-slate-900">${preco}</p>
        `;
    }

    return `
        <article class="flex w-full min-w-0 flex-col rounded-3xl border border-slate-200 bg-white p-4 shadow-sm transition sm:w-auto sm:min-h-[520px] sm:min-w-[320px] sm:shrink-0 sm:snap-start sm:hover:-translate-y-1 sm:hover:shadow-xl">
            <div class="mb-4 flex h-48 items-center justify-center rounded-2xl bg-white sm:h-[230px]">
                ${imagemHtml}
            </div>
            <div class="flex flex-1 flex-col space-y-3 overflow-hidden">
                <div class="flex items-center justify-between gap-3">
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-blue-700">${produto.tipo || 'Produto'}</span>
                    <span class="text-xs font-medium text-slate-500">${produto.marca || ''}</span>
                </div>
                <div>
                    <h3 class="min-h-[36px] text-lg font-semibold text-slate-900">${produto.nome}</h3>
                    <p class="mt-1 text-sm text-slate-500">${nomeLoja}</p>
                </div>
            </div>
        </article>
    `;
}

// Constrói o HTML do card de uma loja
function construirCardLoja(loja) {
    const cidade = loja.cidade || 'Brasil';
    const estado = loja.estado || '';

    let bannerHtml = `<i data-lucide="store" class="h-12 w-12 text-slate-300"></i>`;
    if (loja.banner_img) {
        bannerHtml = `<img src="${loja.banner_img}" alt="Banner da loja ${loja.nome_loja}" class="w-full aspect-[4/3] rounded-2xl object-cover" onerror="this.style.display='none'">`;
    }

    return `
        <article class="flex w-full min-w-0 flex-col rounded-3xl border border-slate-200 bg-white p-4 shadow-sm transition sm:w-auto sm:min-h-[340px] sm:min-w-[320px] sm:shrink-0 sm:snap-start sm:hover:-translate-y-1 sm:hover:shadow-xl">
            <div class="mb-4 flex aspect-[4/3] items-center justify-center rounded-2xl bg-slate-100">
                ${bannerHtml}
            </div>
            <div class="flex flex-1 flex-col space-y-3">
                <div>
                    <h3 class="min-h-[36px] text-lg font-semibold text-slate-900">${loja.nome_loja}</h3>
                    <p class="mt-1 text-sm text-slate-500">${cidade} ${estado}</p>
                </div>
                <div class="mt-auto flex items-center justify-between">
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">Marketplace</span>
                    <div class="flex items-center gap-3">
                        <a href="./protegido/consumidor/denunciar.html?conta_id=${loja.conta_id}" class="text-xs font-semibold text-red-600 transition hover:text-red-700">Denunciar</a>
                        <a href="./catalogo.html" class="text-sm font-semibold text-blue-600 transition hover:text-blue-700">Explorar</a>
                    </div>
                </div>
            </div>
        </article>
    `;
}

// Constrói o HTML do card de um departamento
function construirCardDepartamento(dep) {
    return `
        <article class="w-full min-w-0 rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950 p-5 text-white shadow-sm sm:w-auto sm:min-w-[260px] sm:shrink-0 sm:snap-start">
            <div class="mb-10 flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10">
                <i data-lucide="${dep.icone}" class="h-6 w-6"></i>
            </div>
            <div class="space-y-2">
                <h3 class="text-xl font-semibold">${dep.titulo}</h3>
                <p class="text-sm leading-6 text-slate-300">${dep.descricao}</p>
            </div>
        </article>
    `;
}

// Injeta o carrossel no container e conecta os botões de scroll
function renderizarCarousel(id, titulo, cards) {
    const container = document.getElementById(id + '_container');
    if (!container) return;

    const prevId = id + '_prev';
    const nextId = id + '_next';

    container.innerHTML = `
        <div class="flex items-center gap-8 mb-3">
            <h2 class="text-3xl font-bold">${titulo}</h2>
        </div>
        <div class="relative mb-8 sm:overflow-x-hidden">
            <button type="button" id="${prevId}"
                class="absolute -left-3 top-1/2 z-30 hidden -translate-y-1/2 rounded-full border border-gray-200 bg-white/90 p-2 text-gray-700 shadow-md backdrop-blur sm:flex"
                aria-label="Voltar">
                <i data-lucide="chevron-left" class="h-5 w-5"></i>
            </button>
            <div id="${id}"
                class="grid grid-cols-1 gap-4 pb-6 pt-2 sm:flex sm:snap-x sm:snap-mandatory sm:overflow-x-auto sm:scroll-smooth sm:pb-8 sm:px-2 sm:[-ms-overflow-style:none] sm:[scrollbar-width:none] sm:[&::-webkit-scrollbar]:hidden">
                ${cards.join('')}
            </div>
            <button type="button" id="${nextId}"
                class="absolute -right-3 top-1/2 z-30 hidden -translate-y-1/2 rounded-full border border-gray-200 bg-white/90 p-2 text-gray-700 shadow-md backdrop-blur sm:flex"
                aria-label="Avançar">
                <i data-lucide="chevron-right" class="h-5 w-5"></i>
            </button>
        </div>
    `;

    // Conecta os botões de scroll
    const carousel = document.getElementById(id);
    const prev     = document.getElementById(prevId);
    const next     = document.getElementById(nextId);

    if (carousel && prev && next) {
        function moverCarousel(direcao) {
            const distancia = Math.max(carousel.clientWidth * 0.85, 280);
            carousel.scrollBy({ left: distancia * direcao, behavior: 'smooth' });
        }
        prev.addEventListener('click', () => moverCarousel(-1));
        next.addEventListener('click', () => moverCarousel(1));
    }
}

function formatarNotaLoja(item) {
    const total = Number(item.total_avaliacoes_atendimento || 0);
    if (total <= 0) {
        return '<p class="mt-2 inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500"><i data-lucide="star" class="h-3.5 w-3.5"></i> Sem notas</p>';
    }

    const media = Number(item.media_atendimento || 0).toFixed(1).replace('.', ',');
    const texto = total === 1 ? '1 avaliação' : `${total} avaliações`;
    return `<p class="mt-2 inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700"><i data-lucide="star" class="h-3.5 w-3.5 fill-amber-400 text-amber-400"></i> ${media} (${texto})</p>`;
}

function formatarMoeda(valor) {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(valor);
}

// Inicia o carregamento quando o script é executado
carregarHome();
