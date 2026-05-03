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

function cls(name) {
    return window.TechnoUpStyle?.cls(name) || '';
}

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

    let imagemHtml = `<i data-lucide="package" class="${cls('productIcon')}"></i>`;
    if (produto.imagem) {
        const src = produto.imagem.caminho + produto.imagem.arquivo;
        imagemHtml = `<img src="${src}" alt="${produto.nome}" class="${cls('productImage')}" onerror="this.style.display='none'">`;
    }

    let precoMarkup = '';
    if (desconto > 0) {
        precoMarkup = `
            <p class="${cls('productPriceOld')}">De <s>${precoOriginal}</s></p>
            <div class="${cls('productPriceWrap')}">
                <p class="${cls('productPriceNew')}">Por ${preco}</p>
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

// Constrói o HTML do card de uma loja
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

// Constrói o HTML do card de um departamento
function construirCardDepartamento(dep) {
    return `
        <article class="${cls('departmentCard')}">
            <div class="${cls('departmentIconWrap')}">
                <i data-lucide="${dep.icone}" class="${cls('departmentIcon')}"></i>
            </div>
            <div class="${cls('departmentBody')}">
                <h3 class="${cls('departmentTitle')}">${dep.titulo}</h3>
                <p class="${cls('departmentText')}">${dep.descricao}</p>
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
        <div class="${cls('carouselHead')}">
            <h2 class="${cls('carouselTitle')}">${titulo}</h2>
        </div>
        <div class="${cls('carouselWrap')}">
            <button type="button" id="${prevId}"
                class="${cls('carouselButtonPrev')}"
                aria-label="Voltar">
                <i data-lucide="chevron-left" class="${cls('carouselButtonIcon')}"></i>
            </button>
            <div id="${id}"
                class="${cls('carouselTrack')}">
                ${cards.join('')}
            </div>
            <button type="button" id="${nextId}"
                class="${cls('carouselButtonNext')}"
                aria-label="Avançar">
                <i data-lucide="chevron-right" class="${cls('carouselButtonIcon')}"></i>
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
