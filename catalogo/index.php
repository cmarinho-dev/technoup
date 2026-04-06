<?php
require_once '../_php/crud.php';
require_once '../_componentes/carrousel.php';

//ler produtos
$resultado_produtos = ler('produto');
$total_produtos = mysqli_num_rows($resultado_produtos);

//ler lojas
$resultado_loja = ler('loja');
$lojas = [];

if ($resultado_loja) {
    while ($loja = mysqli_fetch_assoc($resultado_loja)) {
        $lojas[] = $loja;
    }
}

//
// MOSTRAR LOJAS
//
$content = '';
$carrousel_content = '';
// Gera cards de lojas
if (count($lojas) > 0) {
    foreach ($lojas as $loja) {
        $img = '';
        if ($loja['banner_img']) {
            if (file_exists($loja['banner_img'])) {
                $descricao = 'imagem da loja ' . $loja['nome_loja'];
                $img = <<<IMG
                        <img src="{$loja['banner_img']}" alt="$descricao"
                            class="w-full h-full object-cover z-10 rounded-t-xl">
                    IMG;
            }
        }
        $carrousel_content .= <<<CARD
        <div data-nome-loja="{$loja['nome_loja']}" class="card min-w-[85%] snap-start rounded-xl border border-gray-300 bg-white transition-shadow hover:shadow-xl sm:min-w-[320px]">
            <div class="relative bg-gray-200 w-full rounded-t-xl aspect-5/3 mb-3 flex items-center justify-center">
                <i data-lucide="store" class="absolute w-12 h-12 text-gray-400"></i>
                $img
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-lg mb-2">{$loja['nome_loja']}</h3>
                <p class="text-gray-600 text-sm mb-3">{$loja['cidade']}</p>
            </div>
        </div>
        CARD;
    }
    $content .= getCarrousel($carrousel_content, 'Lojas parceiras', 'lojas_carousel');
} else {
    $content .= <<<HTML
    <div class="text-center py-12 border border-gray-300 rounded-2xl">
        <i data-lucide="store" class="w-16 h-16 text-gray-300 mx-auto mb-3"></i>
        <p class="text-lg">Nenhuma loja encontrada</p>
    </div>
HTML;
}


//
// MOSTRAR PRODUTOS
//    --primeiro vem umas opções de filtro e + um título
$content .= <<<HTML

<div id="catalogo_header" class=" flex flex-row flex-wrap justify-between items-center mb-3">
    <h1 class="text-3xl font-bold mb-2">Catálogo de Produtos</h1>
    <div id="pesquisa_produto_catalogo" class="flex gap-3 mb-6 flex-wrap">
        <div id="pesquisa_tipo" class="relative flex items-center border-box max-w-64">
            <select name="tipo_produto" id="tipo_produto" class="box-border 
                flex-1 px-3 pe-10 py-2 border-zinc-200 border rounded-lg max-w-64 hover:border-blue-300
                focus:outline-none focus:border-blue-500 appearance-none">
                <option value="">Todos os tipos</option>
                <option value="computador">Computador</option>
                <option value="Periférico">Periférico</option>
                <option value="eletronico">Eletrônico</option>
            </select>
            <i data-lucide="chevron-down" class="absolute text-zinc-500 bg-white end-0 me-3 z-10 pointer-events-none"></i>
        </div>
        <input id="pesquisa_preco_min" type="number" name="preco_min" placeholder="Min R$" 
                class="box-border flex-1 px-3 py-2 border-zinc-200 border rounded-lg 
                max-w-24 hover:border-blue-300 focus:outline-none focus:border-blue-500
                [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none 
                [&::-webkit-inner-spin-button]:appearance-none"> 
        <input id="pesquisa_preco_max" type="number" name="preco_max" placeholder="Max R$" 
                class="box-border flex-1 px-3 py-2 border-zinc-200 border rounded-lg 
                max-w-24 hover:border-blue-300 focus:outline-none focus:border-blue-500
                [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none
                [&::-webkit-inner-spin-button]:appearance-none"> 
        <div id="pesquisa_nome" class="relative flex items-center border-box max-w-64">   
            <input type="text" name="search" id="search" placeholder="Pesquisar produtos..." 
                class="box-border flex-1 px-3 py-2 border-zinc-200 border-2 rounded-lg 
                max-w-64 hover:border-blue-300 focus:outline-none focus:border-blue-500"> 
            <i data-lucide="search" class="absolute text-zinc-500 bg-white end-0 me-3 z-10 pointer-events-none"></i>
        </div>
    </div>
</div>
<div id="catalogo_contador" class="text-gray-700 font-base">
    $total_produtos produtos encontrados
</div>
<div id="catalogo_items_grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">

HTML;

// Gera cards de produtos
if ($total_produtos > 0) {
    while ($produto = $resultado_produtos->fetch_assoc()) {
        $resultado_loja = ler('loja', $produto['loja_id']);
        $resultado_imagem = ler('imagem_produto', $produto['id'], 'produto_id');

        $loja_nome = mysqli_fetch_assoc($resultado_loja)['nome_loja'];
        $imagem = mysqli_fetch_assoc($resultado_imagem);

        $img = '';
        if ($imagem) {
            if (file_exists($imagem['caminho'] . $imagem['arquivo'])) {
                $imagem_url = $imagem['caminho'] . $imagem['arquivo'];
                $img = <<<IMG
                        <img src="$imagem_url" alt="{$imagem['descricao']}"
                            class="w-full h-full object-cover z-10 rounded-t-xl">
                    IMG;
            }
        }

        $desconto = (int)($produto['desconto'] ?? 0);
        $preco_original_formatado = number_format((float)$produto['preco'], 2, ',', '.');
        $preco_final_formatado = number_format((float)($produto['preco_final'] ?? $produto['preco']), 2, ',', '.');
        $preco_markup = '';

        if ($desconto > 0) {
            $preco_markup = <<<HTMLPRECO
                <div class="relative inline-flex items-start">
                    <span class="text-sm font-medium text-slate-400 line-through">De R$ $preco_original_formatado</span>
                </div>
                <span class="text-xl tracking-tighter font-bold text-blue-600 text-nowrap">Por R$ $preco_final_formatado</span>
            HTMLPRECO;
        } else {
            $preco_markup = <<<HTMLPRECO
                <span class="text-xl tracking-tighter font-bold text-blue-600 text-nowrap">R$ $preco_final_formatado</span>
            HTMLPRECO;
        }

        $content .= <<<CARD
        <div  data-nome-produto="{$produto['nome']}" data-preco-produto="{$produto['preco_final']}" data-tipo-produto="{$produto['tipo']}"
        class="card bg-white rounded-xl border border-gray-300 hover:shadow-xl transition-shadow">
            <div class="relative bg-gray-200 w-full rounded-t-xl aspect-square mb-3 flex items-center justify-center">
                <i data-lucide="package" class="absolute w-12 h-12 text-gray-400"></i>
                $img
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-lg mb-2">{$produto['nome']}</h3>
                <p class="text-gray-600 text-sm mb-3">$loja_nome</p>
                <div class="flex justify-between items-end flex-wrap gap-2">
                    <div class="flex flex-col gap-1">
                        $preco_markup
                    </div>
                    <!--<button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm">-->
                    <!--    Adicionar-->
                    <!--</button>-->
                </div>
            </div>
        </div>
        CARD;
    }
    $content .= <<<HTMLjs
<script>
    const buscarInput = document.getElementById('search');
    const tipoInput = document.getElementById('tipo_produto');
    const precoMinInput = document.getElementById('pesquisa_preco_min');
    const precoMaxInput = document.getElementById('pesquisa_preco_max');

    function filtrarProdutos() {
        const nomeBuscado = (buscarInput.value || '').trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, "");
        const tipoBuscado = (tipoInput.value || '').trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, "");
        const precoMin = parseFloat(precoMinInput.value);
        const precoMax = parseFloat(precoMaxInput.value);

        document.querySelectorAll('.card').forEach(card => {
            const nomeProduto = (card.dataset.nomeProduto || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, "");
            const tipoProduto = (card.dataset.tipoProduto || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, "");
            const precoProduto = parseFloat(card.dataset.precoProduto || 0);

            const correspondeNome = nomeProduto.includes(nomeBuscado);
            const correspondeTipo = !tipoBuscado || tipoProduto === tipoBuscado;
            const correspondePrecoMin = Number.isNaN(precoMin) || precoProduto >= precoMin;
            const correspondePrecoMax = Number.isNaN(precoMax) || precoProduto <= precoMax;

            if (correspondeNome && correspondeTipo && correspondePrecoMin && correspondePrecoMax) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    buscarInput.addEventListener('input', filtrarProdutos);
    tipoInput.addEventListener('change', filtrarProdutos);
    precoMinInput.addEventListener('input', filtrarProdutos);
    precoMaxInput.addEventListener('input', filtrarProdutos);
</script>
HTMLjs;

} else {
    $content .= <<<EMPTY
    <div class="col-span-full text-center py-12 border border-gray-300 rounded-2xl">
        <i data-lucide="inbox" class="w-16 h-16 text-gray-300 mx-auto mb-3"></i>
        <p class="text-lg">Nenhum produto encontrado</p>
    </div>
EMPTY;
}

$content .= '</div>';

require_once '../_componentes/layout.php';
