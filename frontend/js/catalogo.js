// catalogo.js — Lógica do catálogo de produtos

// Armazena todos os produtos para filtragem no cliente
let todosProdutos = [];

async function carregarCatalogo() {
    // Busca produtos e lojas (modelo simples: fetch -> json -> verificar status)
    const retornoProdutos = await fetch(CAMINHO_API + '/produtos/get.php', { credentials: 'include' });
    const respostaProdutos = await retornoProdutos.json();
    if (respostaProdutos.status === 'ok') {
        todosProdutos = respostaProdutos.data || [];
    } else {
        todosProdutos = [];
        console.warn('Erro ao carregar produtos:', respostaProdutos.mensagem || respostaProdutos);
    }

    const retornoLojas = await fetch(CAMINHO_API + '/lojas/get.php', { credentials: 'include' });
    const respostaLojas = await retornoLojas.json();
    const lojas = respostaLojas.status === 'ok' ? (respostaLojas.data || []) : ([]);

    // Renderiza o carrossel de lojas
    renderizarCarouselLojas(lojas);

    // Renderiza os cards de produtos
    renderizarProdutos(todosProdutos);

    // Ativa os filtros
    document.getElementById('filtraNome').addEventListener('input', aplicarFiltros);
    document.getElementById('filtraTipo').addEventListener('change', aplicarFiltros);
    document.getElementById('filtroPrecoMin').addEventListener('input', aplicarFiltros);
    document.getElementById('filtroPrecoMax').addEventListener('input', aplicarFiltros);

    lucide.createIcons();
}

// Renderiza os cards na grade, considerando quais devem aparecer
function renderizarProdutos(produtos) {
    const grid       = document.getElementById('catalogo_items_grid');
    const contadorEl = document.getElementById('catalogo_contador');

    contadorEl.textContent = `${produtos.length} produto(s) encontrado(s)`;

    if (produtos.length === 0) {
        grid.innerHTML = `
            <div class="col-span-full text-center py-12 border border-gray-300 rounded-2xl">
                <i data-lucide="inbox" class="w-16 h-16 text-gray-300 mx-auto mb-3"></i>
                <p class="text-lg">Nenhum produto encontrado</p>
            </div>
        `;
        lucide.createIcons();
        return;
    }

    grid.innerHTML = produtos.map(construirCardProduto).join('');
    lucide.createIcons();
}

// Aplica todos os filtros e re-renderiza a lista
function aplicarFiltros() {
    const nomeBuscado  = normalizar(document.getElementById('filtraNome').value);
    const tipoBuscado  = normalizar(document.getElementById('filtraTipo').value);
    const precoMin     = parseFloat(document.getElementById('filtroPrecoMin').value);
    const precoMax     = parseFloat(document.getElementById('filtroPrecoMax').value);

    const filtrados = todosProdutos.filter(produto => {
        const nome  = normalizar(produto.nome  || '');
        const tipo  = normalizar(produto.tipo  || '');
        const preco = parseFloat(produto.preco_final || produto.preco || 0);

        const correspondeNome  = nome.includes(nomeBuscado);
        const correspondeTipo  = !tipoBuscado || tipo === tipoBuscado;
        const correspondeMin   = isNaN(precoMin) || preco >= precoMin;
        const correspondeMax   = isNaN(precoMax) || preco <= precoMax;

        return correspondeNome && correspondeTipo && correspondeMin && correspondeMax;
    });

    renderizarProdutos(filtrados);
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
            <p class="text-sm tracking-[0.2em] text-slate-400"><s>${precoOriginal}</s></p>
            <div class="flex items-center gap-3">
                <p class="text-2xl font-bold tracking-tight text-slate-900">${preco}</p>
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

function construirCardLoja(loja) {
    const cidade = loja.cidade || 'Brasil';
    const estado = loja.estado || '';

    let bannerHtml = '<i data-lucide="store" class="h-12 w-12 text-slate-300"></i>';
    if (loja.banner_img) {
        bannerHtml = `<img src="${loja.banner_img}" alt="Banner da loja ${loja.nome_loja}" class="w-full aspect-[4/3] rounded-2xl object-cover" onerror="this.style.display='none'">`;
    }

    return `
        <article class="flex min-w-[84%] min-h-[340px] shrink-0 snap-start flex-col rounded-3xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-xl sm:min-w-[320px]">
            <div class="mb-4 flex aspect-[4/3] items-center justify-center rounded-2xl bg-slate-100">
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

// Renderiza o carrossel de lojas
function renderizarCarouselLojas(lojas) {
    const container = document.getElementById('carousel_lojas_container');
    if (!container || lojas.length === 0) return;

    const cards = lojas.map(loja => { return construirCardLoja(loja); });

    container.innerHTML = `
        <div class="flex items-center mb-3">
            <h2 class="text-3xl font-bold">Lojas parceiras</h2>
        </div>
        <div class="relative mb-8">
            <button type="button" id="carousel_lojas_prev"
                class="absolute -left-3 top-1/2 z-30 hidden -translate-y-1/2 rounded-full border border-gray-200 bg-white/90 p-2 text-gray-700 shadow-md backdrop-blur sm:flex">
                <i data-lucide="chevron-left" class="h-5 w-5"></i>
            </button>
            <div id="carousel_lojas"
                class="flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth pb-4 pt-2 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                ${cards.join('')}
            </div>
            <button type="button" id="carousel_lojas_next"
                class="absolute -right-3 top-1/2 z-30 hidden -translate-y-1/2 rounded-full border border-gray-200 bg-white/90 p-2 text-gray-700 shadow-md backdrop-blur sm:flex">
                <i data-lucide="chevron-right" class="h-5 w-5"></i>
            </button>
        </div>
    `;

    const carousel = document.getElementById('carousel_lojas');
    const prev     = document.getElementById('carousel_lojas_prev');
    const next     = document.getElementById('carousel_lojas_next');
    if (carousel && prev && next) {
        prev.addEventListener('click', () => carousel.scrollBy({ left: -(carousel.clientWidth * 0.85), behavior: 'smooth' }));
        next.addEventListener('click', () => carousel.scrollBy({ left:  (carousel.clientWidth * 0.85), behavior: 'smooth' }));
    }
}

// Remove acentos e deixa em minúsculo para comparação
function normalizar(texto) {
    return texto.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
}

// Formata um número como moeda brasileira
function formatarMoeda(valor) {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(valor);
}

// Inicia o carregamento
carregarCatalogo();
