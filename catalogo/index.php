<?php
require_once '../_php/crud.php';

$resultado_catalogo = ler('catalogo');

$items_catalogo = [];
$total_produtos = 0;

if ($resultado_catalogo) {
    while ($item_catalogo = mysqli_fetch_assoc($resultado_catalogo)) {
        $items_catalogo[] = $item_catalogo;
        $total_produtos++;
    }
}

$content = <<<HTML

<div id="pesquisa_produto_catalogo" class="flex gap-3 mb-6 flex-wrap">
    <div id="pesquisa_tipo" class="relative flex items-center border-box max-w-64">
        <select name="tipo_produto" id="tipo_produto" class="box-border 
            flex-1 px-3 pe-10 py-2 border-zinc-200 border rounded-lg max-w-64 hover:border-blue-300
            focus:outline-none focus:border-blue-500 appearance-none">
            <option value="">Todos os tipos</option>
            <option value="computador">Computador</option>
            <option value="periferico">Periférico</option>
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
<div id="catalogo_header" class="mb-6">
    <h1 class="text-3xl font-bold mb-2">Catálogo de Produtos</h1>
</div>
<div id="catalogo_contador" class="text-gray-700 font-base">
    $total_produtos produtos encontrados
</div>
<div id="catalogo_items_grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">

HTML;

// Gera cards de produtos
if ($total_produtos > 0) {
    foreach ($items_catalogo as $item_catalogo) {
        $resultado_produto = ler('produto', $item_catalogo['produto_id']);
        $resultado_loja = ler('loja', $item_catalogo['loja_id']);
        $resultado_imagem = ler('imagem_produto', $item_catalogo['produto_id'], 'produto_id');

        $loja_nome = mysqli_fetch_assoc($resultado_loja)['nome_loja'];
        $produto = mysqli_fetch_assoc($resultado_produto);
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

        $preco_formatado = number_format($produto['preco'], 2, ',', '');
        $content .= <<<CARD
        <div class="bg-white rounded-xl border border-gray-300 hover:shadow-xl transition-shadow">
            <div class="relative bg-gray-200 w-full rounded-t-xl aspect-square mb-3 flex items-center justify-center">
                <i data-lucide="package" class="absolute w-12 h-12 text-gray-400"></i>
                $img
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-lg mb-2">{$produto['nome']}</h3>
                <p class="text-gray-600 text-sm mb-3">$loja_nome</p>
                <div class="flex justify-between items-center flex-wrap gap-2">
                    <span class="text-xl tracking-tighter font-bold text-blue-600 text-nowrap">R$ $preco_formatado</span>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm">
                        Adicionar
                    </button>
                </div>
            </div>
        </div>
        CARD;
    }
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
