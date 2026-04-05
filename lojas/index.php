<?php
require_once '../_php/crud.php';

$content = '';
session_start();
if (!empty($_SESSION['usuario']) && $_SESSION['usuario']['tipo'] === 'lojista') {
    $content = include 'gerenciar_produtos.php';
}
session_abort();

$resultado_loja = ler('loja');
$lojas = [];

if ($resultado_loja) {
    while ($loja = mysqli_fetch_assoc($resultado_loja)) {
        $lojas[] = $loja;
    }
}

$content .= <<<HTML
<div class="flex items-center gap-8">
    <div id="loja_header">
        <h1 class="text-3xl font-bold">Lojas disponiveis</h1>
    </div>
    <div id="pesquisa_loja" class="flex gap-3 flex-wrap">
        <div id="pesquisa_nome" class="relative flex items-center border-box max-w-64">   
            <input type="text" name="search" id="search" placeholder="Pesquisar lojas..." 
                class="box-border flex-1 px-3 py-2 border-zinc-200 border-2 rounded-lg 
                max-w-64 hover:border-blue-300 focus:outline-none focus:border-blue-500"> 
            <i data-lucide="search" class="absolute text-zinc-500 bg-white end-0 me-3 z-10 pointer-events-none"></i>
        </div>
    </div>
</div>
<div id="lojas_grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

HTML;


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
        $content .= <<<CARD
        <div class="bg-white rounded-xl border border-gray-300 hover:shadow-xl transition-shadow">
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
} else {
    $content .= <<<EMPTY
    <div class="col-span-full text-center py-12 border border-gray-300 rounded-2xl">
        <i data-lucide="store" class="w-16 h-16 text-gray-300 mx-auto mb-3"></i>
        <p class="text-lg">Nenhuma loja encontrada</p>
    </div>
EMPTY;
}

$content .= '</div>';

require_once '../_componentes/layout.php';
