// catalogo.js — Lógica do catálogo de produtos

// Armazena todos os produtos para filtragem no cliente
let todosProdutos = [];

function cls(name) {
    return window.TechnoUpStyle?.cls(name) || '';
}

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
            <div class="${cls('catalogEmpty')}">
                <i data-lucide="inbox" class="${cls('catalogEmptyIcon')}"></i>
                <p class="${cls('catalogEmptyText')}">Nenhum produto encontrado</p>
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

    let imagemHtml = `<i data-lucide="package" class="${cls('productIcon')}"></i>`;
    if (produto.imagem) {
        const src = produto.imagem.caminho + produto.imagem.arquivo;
        imagemHtml = `<img src="${src}" alt="${produto.nome}" class="${cls('productImage')}" onerror="this.style.display='none'">`;
    }

    let precoMarkup = '';
    if (desconto > 0) {
        precoMarkup = `
            <p class="${cls('productPriceOld')}"><s>${precoOriginal}</s></p>
            <div class="${cls('productPriceWrap')}">
                <p class="${cls('productPriceNew')}">${preco}</p>
                <span class="${cls('productDiscount')}">-${desconto}%</span>
            </div>
        `;
    } else {
        precoMarkup = `
            <p class="${cls('productPriceLabel')}">Preço atual</p>
            <p class="${cls('productPriceNew')}">${preco}</p>
        `;
    }

    return `
        <article class="${cls('productCard')}">
            <div class="${cls('productMedia')}">
                ${imagemHtml}
            </div>
            <div class="${cls('productBody')}">
                <div class="${cls('productMeta')}">
                    <span class="${cls('productType')}">${produto.tipo || 'Produto'}</span>
                    <span class="${cls('productBrand')}">${produto.marca || ''}</span>
                </div>
                <div>
                    <h3 class="${cls('productTitle')}">${produto.nome}</h3>
                    <p class="${cls('productStore')}">${nomeLoja}</p>
                </div>
                <div class="${cls('productBottom')}">
                    <div class="${cls('productBottomInner')}">${precoMarkup}</div>
                </div>
            </div>
        </article>
    `;
}

function construirCardLoja(loja) {
    const cidade = loja.cidade || 'Brasil';
    const estado = loja.estado || '';

    let bannerHtml = `<i data-lucide="store" class="${cls('storeIcon')}"></i>`;
    if (loja.banner_img) {
        bannerHtml = `<img src="${loja.banner_img}" alt="Banner da loja ${loja.nome_loja}" class="${cls('storeImage')}" onerror="this.style.display='none'">`;
    }

    return `
        <article class="${cls('storeCard')}">
            <div class="${cls('storeMedia')}">
                ${bannerHtml}
            </div>
            <div class="${cls('storeBody')}">
                <div>
                    <h3 class="${cls('storeTitle')}">${loja.nome_loja}</h3>
                    <p class="${cls('storeLocation')}">${cidade} ${estado}</p>
                </div>
                <div class="${cls('storeBottom')}">
                    <span class="${cls('storeBadge')}">Marketplace</span>
                    <a href="./catalogo.html" class="${cls('storeLink')}">Explorar</a>
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
        <div class="${cls('carouselStoreHead')}">
            <h2 class="${cls('carouselTitle')}">Lojas parceiras</h2>
        </div>
        <div class="${cls('carouselStoreWrap')}">
            <button type="button" id="carousel_lojas_prev"
                class="${cls('carouselButtonPrev')}">
                <i data-lucide="chevron-left" class="${cls('carouselButtonIcon')}"></i>
            </button>
            <div id="carousel_lojas"
                class="${cls('carouselStoreTrack')}">
                ${cards.join('')}
            </div>
            <button type="button" id="carousel_lojas_next"
                class="${cls('carouselButtonNext')}">
                <i data-lucide="chevron-right" class="${cls('carouselButtonIcon')}"></i>
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
