// catalogo.js — Lógica do catálogo de produtos

// Armazena todos os produtos para filtragem no cliente
let todosProdutos = [];

async function carregarCatalogo() {
    // Busca produtos e lojas ao mesmo tempo
    const [respostaProdutos, respostaLojas] = await Promise.all([
        fetch(API + '/produtos/listar.php', { credentials: 'include' }),
        fetch(API + '/lojas/listar.php',    { credentials: 'include' })
    ]);

    const jsonProdutos = await respostaProdutos.json();
    const jsonLojas    = await respostaLojas.json();

    todosProdutos = jsonProdutos.data || [];
    const lojas   = jsonLojas.data   || [];

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
    const desconto     = parseInt(produto.desconto || 0);
    const precoFinal   = parseFloat(produto.preco_final || produto.preco);
    const preco        = formatarMoeda(precoFinal);
    const precoOriginal = formatarMoeda(parseFloat(produto.preco));
    const nomeLoja     = produto.nome_loja || '';

    let imgHtml = '<i data-lucide="package" class="absolute w-12 h-12 text-gray-400"></i>';
    if (produto.imagem) {
        const src = produto.imagem.caminho + produto.imagem.arquivo;
        imgHtml = `<img src="${src}" alt="${produto.nome}" class="w-full h-full object-contain z-10 rounded-t-xl" onerror="this.style.display='none'">`;
    }

    let precoMarkup = '';
    if (desconto > 0) {
        precoMarkup = `
            <span class="text-sm font-medium text-slate-400 line-through">De ${precoOriginal}</span>
            <span class="text-xl tracking-tighter font-bold text-blue-600 text-nowrap">Por ${preco}</span>
        `;
    } else {
        precoMarkup = `<span class="text-xl tracking-tighter font-bold text-blue-600 text-nowrap">${preco}</span>`;
    }

    return `
        <div class="bg-white rounded-xl border border-gray-300 hover:shadow-xl transition-shadow">
            <div class="relative bg-gray-200 w-full rounded-t-xl aspect-square mb-3 flex items-center justify-center">
                ${imgHtml}
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-lg mb-2">${produto.nome}</h3>
                <p class="text-gray-600 text-sm mb-3">${nomeLoja}</p>
                <div class="flex flex-col gap-1">${precoMarkup}</div>
            </div>
        </div>
    `;
}

// Renderiza o carrossel de lojas
function renderizarCarouselLojas(lojas) {
    const container = document.getElementById('carousel_lojas_container');
    if (!container || lojas.length === 0) return;

    const cards = lojas.map(loja => {
        let imgHtml = '<i data-lucide="store" class="absolute w-12 h-12 text-gray-400"></i>';
        if (loja.banner_img) {
            imgHtml = `<img src="${loja.banner_img}" alt="${loja.nome_loja}" class="w-full aspect-[4/3] rounded-2xl object-cover z-10" onerror="this.style.display='none'">`;
        }
        return `
            <div class="min-w-[85%] snap-start rounded-xl border border-gray-300 bg-white transition-shadow hover:shadow-xl sm:min-w-[320px]">
                <div class="relative bg-gray-200 w-full rounded-t-xl aspect-[4/3] mb-3 flex items-center justify-center">
                    ${imgHtml}
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-lg mb-1">${loja.nome_loja}</h3>
                    <p class="text-gray-600 text-sm">${loja.cidade || ''}</p>
                </div>
            </div>
        `;
    });

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
