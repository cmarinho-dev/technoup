<?php
require_once '../_php/crud.php';
require_once '../_componentes/carrousel.php';

$resultado_produtos = ler('produto');
$resultado_lojas = ler('loja');

$produtos = [];
$lojas = [];

if ($resultado_produtos) {
    while ($produto = mysqli_fetch_assoc($resultado_produtos)) {
        $produtos[] = $produto;
    }
}

if ($resultado_lojas) {
    while ($loja = mysqli_fetch_assoc($resultado_lojas)) {
        $lojas[] = $loja;
    }
}

$departamentos = [
    ['titulo' => 'PC Gamer', 'descricao' => 'Maquinas montadas para jogar e criar.', 'icone' => 'monitor'],
    ['titulo' => 'Hardware', 'descricao' => 'Processadores, placas e upgrades.', 'icone' => 'cpu'],
    ['titulo' => 'Perifericos', 'descricao' => 'Teclados, mouses e audio.', 'icone' => 'mouse'],
    ['titulo' => 'Monitores', 'descricao' => 'Imagens fluidas para trabalho e jogo.', 'icone' => 'monitor-up'],
    ['titulo' => 'Cadeiras', 'descricao' => 'Conforto para setups longos.', 'icone' => 'armchair'],
    ['titulo' => 'Home Office', 'descricao' => 'Equipamentos para produtividade.', 'icone' => 'briefcase-business'],
];

function normalizarTexto($valor)
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function renderizarImagemProduto($produtoId)
{
    $resultadoImagem = ler('imagem_produto', $produtoId, 'produto_id');
    $imagem = $resultadoImagem ? mysqli_fetch_assoc($resultadoImagem) : null;

    if ($imagem && file_exists($imagem['caminho'] . $imagem['arquivo'])) {
        $imagemUrl = $imagem['caminho'] . $imagem['arquivo'];
        $descricao = normalizarTexto($imagem['descricao']);
        return <<<HTML
            <img src="$imagemUrl" alt="$descricao" class="h-full w-full rounded-2xl object-cover">
        HTML;
    }

    return '<i data-lucide="package" class="h-12 w-12 text-slate-300"></i>';
}

function renderizarCardProdutoHome($produto)
{
    $resultadoLoja = ler('loja', $produto['loja_id']);
    $loja = $resultadoLoja ? mysqli_fetch_assoc($resultadoLoja) : null;

    $nome = normalizarTexto($produto['nome']);
    $tipo = normalizarTexto($produto['tipo'] ?: 'Produto');
    $marca = normalizarTexto($produto['marca'] ?: 'TechnoUp');
    $lojaNome = normalizarTexto($loja['nome_loja'] ?? 'Loja parceira');
    $desconto = (int)($produto['desconto'] ?? 0);
    $precoOriginal = (float)$produto['preco'];
    $precoFinal = (float)($produto['preco_final'] ?? $produto['preco']);
    $preco = number_format($precoFinal, 2, ',', '.');
    $imagem = renderizarImagemProduto($produto['id']);
    $precoMarkup = '';

    if ($desconto > 0) {
        $precoOriginalFormatado = number_format($precoOriginal, 2, ',', '.');
        $precoMarkup = <<<HTML
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">De R$ $precoOriginalFormatado</p>
                        <div class="flex items-center gap-3">
                            <p class="text-2xl font-bold tracking-tight text-slate-900">R$ $preco</p>
                            <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-sm font-bold text-emerald-700">
                                -{$desconto}%
                            </span>
                        </div>
        HTML;
    } else {
        $precoMarkup = <<<HTML
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Preco atual</p>
                        <p class="text-2xl font-bold tracking-tight text-slate-900">R$ $preco</p>
        HTML;
    }

    return <<<HTML
        <article class="card flex min-w-[84%] shrink-0 snap-start flex-col rounded-3xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-xl sm:min-w-[320px]">
            <div class="mb-4 flex aspect-square items-center justify-center rounded-2xl bg-slate-100">
                $imagem
            </div>
            <div class="flex flex-1 flex-col space-y-3">
                <div class="flex items-center justify-between gap-3">
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-blue-700">$tipo</span>
                    <span class="text-xs font-medium text-slate-500">$marca</span>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">$nome</h3>
                    <p class="mt-1 text-sm text-slate-500">$lojaNome</p>
                </div>
                <div class="mt-auto flex items-end justify-between gap-3">
                    <div class="space-y-1">
                        $precoMarkup
                    </div>
                    <!--<a href="../catalogo" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">-->
                    <!--    Ver produto-->
                    <!--</a>-->
                </div>
            </div>
        </article>
    HTML;
}

function renderizarCardLojaHome($loja)
{
    $nome = normalizarTexto($loja['nome_loja']);
    $cidade = normalizarTexto($loja['cidade'] ?: 'Brasil');
    $estado = normalizarTexto($loja['estado'] ?: '');

    if (!empty($loja['banner_img']) && file_exists($loja['banner_img'])) {
        $bannerUrl = $loja['banner_img'];
        $banner = <<<HTML
            <img src="$bannerUrl" alt="Banner da loja $nome" class="w-full aspect-4/2 rounded-2xl object-cover">
        HTML;
    } else {
        $banner = '<i data-lucide="store" class="h-12 w-12 text-slate-300"></i>';
    }

    return <<<HTML
        <article class="card min-w-[84%] shrink-0 snap-start rounded-3xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-xl sm:min-w-[320px]">
            <div class="mb-4 flex aspect-[5/3] items-center justify-center rounded-2xl bg-slate-100">
                $banner
            </div>
            <div class="space-y-3">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">$nome</h3>
                    <p class="mt-1 text-sm text-slate-500">$cidade $estado</p>
                </div>
                <div class="flex items-center justify-between">
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">Marketplace</span>
                    <a href="../catalogo" class="text-sm font-semibold text-blue-600 transition hover:text-blue-700">Explorar</a>
                </div>
            </div>
        </article>
    HTML;
}

function renderizarCardDepartamento($departamento)
{
    $titulo = normalizarTexto($departamento['titulo']);
    $descricao = normalizarTexto($departamento['descricao']);
    $icone = normalizarTexto($departamento['icone']);

    return <<<HTML
        <article class="card min-w-[78%] shrink-0 snap-start rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950 p-5 text-white shadow-sm sm:min-w-[260px]">
            <div class="mb-10 flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10">
                <i data-lucide="$icone" class="h-6 w-6"></i>
            </div>
            <div class="space-y-2">
                <h3 class="text-xl font-semibold">$titulo</h3>
                <p class="text-sm leading-6 text-slate-300">$descricao</p>
            </div>
        </article>
    HTML;
}

$departamentosCarousel = '';
foreach ($departamentos as $departamento) {
    $departamentosCarousel .= renderizarCardDepartamento($departamento);
}

$produtosCarousel = '';
foreach (array_slice($produtos, 0, 6) as $produto) {
    $produtosCarousel .= renderizarCardProdutoHome($produto);
}

$lojasCarousel = '';
foreach (array_slice($lojas, 0, 6) as $loja) {
    $lojasCarousel .= renderizarCardLojaHome($loja);
}

$totalProdutos = count($produtos);
$totalLojas = count($lojas);
$carrosselDepartamentosHtml = getCarrousel($departamentosCarousel, 'Departamentos em destaque', 'home_departamentos');
$carrosselProdutosHtml = getCarrousel($produtosCarousel, 'Promoções imperdíveis', 'home_produtos');
$carrosselLojasHtml = getCarrousel($lojasCarousel, 'Lojas para acompanhar', 'home_lojas');

$content = <<<HTML
<section class="relative overflow-hidden rounded-[2rem] border border-slate-200 bg-gradient-to-br from-slate-950 via-slate-900 to-blue-900 px-6 py-8 text-white shadow-xl sm:px-10 sm:py-12">
    <div class="absolute inset-y-0 right-0 hidden w-1/2 bg-[radial-gradient(circle_at_top_right,_rgba(96,165,250,0.35),_transparent_55%)] lg:block"></div>
    <div class="relative grid gap-8 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
        <div class="space-y-6">
            <span class="inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.28em] text-blue-100">
                Setup, upgrade e marketplace em um so lugar
            </span>
            <div class="space-y-4">
                <h1 class="max-w-2xl text-4xl font-semibold tracking-tight sm:text-5xl">
                    Monte, compare e descubra os destaques da TechnoUp.
                </h1>
                <p class="max-w-xl text-base leading-7 text-slate-300 sm:text-lg">
                    A home foi organizada para destacar departamentos, produtos e lojas parceiras em uma navegacao horizontal mais direta, seguindo a ideia comercial da Terabyte sem perder o visual limpo do projeto.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="../catalogo" class="rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-100">
                    Ver catalogo
                </a>
                <a href="../catalogo" class="rounded-2xl border border-white/15 bg-white/5 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                    Conhecer lojas
                </a>
            </div>
<!--            <div class="flex flex-wrap gap-3 text-sm text-slate-200">-->
<!--                <span class="rounded-full bg-white/10 px-4 py-2">PC Gamer</span>-->
<!--                <span class="rounded-full bg-white/10 px-4 py-2">Hardware</span>-->
<!--                <span class="rounded-full bg-white/10 px-4 py-2">Monitores</span>-->
<!--                <span class="rounded-full bg-white/10 px-4 py-2">Perifericos</span>-->
<!--            </div>-->
        </div>
    </div>
</section>

<section class="mt-10">
    $carrosselDepartamentosHtml
</section>

<section class="mt-6">
    $carrosselProdutosHtml
</section>

<section class="mt-6">
    $carrosselLojasHtml
</section>

<section class="mt-10 grid gap-4 lg:grid-cols-3">
    <article class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
            <i data-lucide="shield-check" class="h-6 w-6"></i>
        </div>
        <h2 class="text-xl font-semibold text-slate-900">Compra mais segura</h2>
        <p class="mt-3 text-sm leading-7 text-slate-600">Loja, produto e navegacao em uma estrutura simples, com foco em comparacao e descoberta.</p>
    </article>
    <article class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
            <i data-lucide="truck" class="h-6 w-6"></i>
        </div>
        <h2 class="text-xl font-semibold text-slate-900">Vitrine de parceiros</h2>
        <p class="mt-3 text-sm leading-7 text-slate-600">Cada lojista ganha espaco proprio sem quebrar a experiencia central do marketplace.</p>
    </article>
    <article class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
            <i data-lucide="sparkles" class="h-6 w-6"></i>
        </div>
        <h2 class="text-xl font-semibold text-slate-900">Descoberta rapida</h2>
        <p class="mt-3 text-sm leading-7 text-slate-600">Os carrosseis deixam a home mais dinamica no mobile e ajudam a priorizar colecoes e campanhas.</p>
    </article>
</section>
HTML;

require_once '../_componentes/layout.php';
