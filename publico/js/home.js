// home.js — Lógica da página inicial

// Departamentos definidos localmente (dados fixos, sem precisar de API)
const departamentos = [
    { titulo: 'PC Gamer',    descricao: 'Maquinas montadas para jogar e criar.',    icone: 'monitor' },
    { titulo: 'Hardware',    descricao: 'Processadores, placas e upgrades.',         icone: 'cpu' },
    { titulo: 'Perifericos', descricao: 'Teclados, mouses e audio.',                icone: 'mouse' },
    { titulo: 'Monitores',   descricao: 'Imagens fluidas para trabalho e jogo.',    icone: 'monitor-up' },
    { titulo: 'Cadeiras',    descricao: 'Conforto para setups longos.',              icone: 'armchair' },
    { titulo: 'Home Office', descricao: 'Equipamentos para produtividade.',         icone: 'briefcase-business' },
];

async function carregarHome() {
    // Busca produtos e lojas ao mesmo tempo para economizar tempo
    const [respostaProdutos, respostaLojas] = await Promise.all([
        fetch(API + '/produtos/listar.php', { credentials: 'include' }),
        fetch(API + '/lojas/listar.php',    { credentials: 'include' })
    ]);

    const jsonProdutos = await respostaProdutos.json();
    const jsonLojas    = await respostaLojas.json();

    const produtos = jsonProdutos.data || [];
    const lojas    = jsonLojas.data    || [];

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

    let imagemHtml = '<i data-lucide="package" class="h-12 w-12 text-slate-300"></i>';
    if (produto.imagem) {
        const src = produto.imagem.caminho + produto.imagem.arquivo;
        imagemHtml = `<img src="${src}" alt="${produto.nome}" class="h-[230px] w-full rounded-2xl object-contain" onerror="this.style.display='none'">`;
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
        <article class="flex min-w-[84%] h-[460px] shrink-0 snap-start flex-col rounded-3xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-xl sm:min-w-[320px]">
            <div class="mb-4 flex h-[230px] items-center justify-center rounded-2xl bg-white">
                ${imagemHtml}
            </div>
            <div class="flex flex-1 flex-col space-y-3 overflow-hidden">
                <div class="flex items-center justify-between gap-3">
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-blue-700">${produto.tipo || 'Produto'}</span>
                    <span class="text-xs font-medium text-slate-500">${produto.marca || ''}</span>
                </div>
                <div>
                    <h3 class="min-h-[56px] text-lg font-semibold text-slate-900">${produto.nome}</h3>
                    <p class="mt-1 text-sm text-slate-500">${nomeLoja}</p>
                </div>
                <div class="mt-auto">
                    <div class="space-y-1">${precoMarkup}</div>
                </div>
            </div>
        </article>
    `;
}

// Constrói o HTML do card de uma loja
function construirCardLoja(loja) {
    const cidade = loja.cidade || 'Brasil';
    const estado = loja.estado || '';

    let bannerHtml = '<i data-lucide="store" class="h-12 w-12 text-slate-300"></i>';
    if (loja.banner_img) {
        bannerHtml = `<img src="${loja.banner_img}" alt="Banner da loja ${loja.nome_loja}" class="w-full aspect-[4/3] rounded-2xl object-cover" onerror="this.style.display='none'">`;
    }

    return `
        <article class="flex min-w-[84%] min-h-[340px] shrink-0 snap-start flex-col rounded-3xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-xl sm:min-w-[320px]">
            <div class="mb-4 flex aspect-[5/3] items-center justify-center rounded-2xl bg-slate-100">
                ${bannerHtml}
            </div>
            <div class="flex flex-1 flex-col space-y-3">
                <div>
                    <h3 class="min-h-[56px] text-lg font-semibold text-slate-900">${loja.nome_loja}</h3>
                    <p class="mt-1 text-sm text-slate-500">${cidade} ${estado}</p>
                </div>
                <div class="mt-auto flex items-center justify-between">
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">Marketplace</span>
                    <a href="./catalogo.html" class="text-sm font-semibold text-blue-600 transition hover:text-blue-700">Explorar</a>
                </div>
            </div>
        </article>
    `;
}

// Constrói o HTML do card de um departamento
function construirCardDepartamento(dep) {
    return `
        <article class="min-w-[78%] shrink-0 snap-start rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950 p-5 text-white shadow-sm sm:min-w-[260px]">
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
        <div class="relative mb-8 overflow-x-hidden">
            <button type="button" id="${prevId}"
                class="absolute -left-3 top-1/2 z-30 hidden -translate-y-1/2 rounded-full border border-gray-200 bg-white/90 p-2 text-gray-700 shadow-md backdrop-blur sm:flex"
                aria-label="Voltar">
                <i data-lucide="chevron-left" class="h-5 w-5"></i>
            </button>
            <div id="${id}"
                class="flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth pb-8 pt-2 px-2 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
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

// Formata um número como moeda brasileira
function formatarMoeda(valor) {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(valor);
}

// Inicia o carregamento quando o script é executado
carregarHome();
