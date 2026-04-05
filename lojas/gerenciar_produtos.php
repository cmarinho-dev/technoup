<?php
require_once '../_php/crud.php';
require_once '../_componentes/modal.php';

$content = '';

if (!empty($_GET['id']) && !empty($_GET['request'])) {
  //deletar
  if ($_GET['request'] == 'delete') {
    deletarProduto($_GET['id']);
  }
  //exibir modal com formulario de atualizacao de produto
  elseif ($_GET['request'] == 'update') {
    $id = $_GET['id'];
    $modal_content = '<input hidden name="id" value="'. $id .'" />';
    $selecionado = ler('produto', $id)->fetch_assoc(); // retorna todas as colunas -> entao abaixo vamos tirar as colunas desnecessarias
    unset($selecionado['criado_em']);
    unset($selecionado['preco_final']);
    unset($selecionado['id']);
    foreach ($selecionado as $chave => $valor) {
      $label = ucwords(str_replace('_', ' ', $chave));
      $modal_content .= <<<HTML
      <div class="relative focus:font-bold">
          <input type="text" id="$chave" name="$chave" value="$valor"
              class="peer w-full px-6 py-3 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-sm" />
          <label for="$chave"
              class="absolute -top-[12px] left-2 text-slate-500 font-thin bg-white px-1 peer-focus:font-semibold peer-focus:text-blue-600 transition-colors transform">
              {$label}
            </label>
      </div>
      HTML;
    }
    $content .= getModal($modal_content, 'Atualizar produto');
  } 
} 
//atualizar
elseif (!empty($_POST['id'])) {
  atualizarProduto($_POST['id']);
}
//exibir modal com formulario de criacao de produto
$modal_content = '';
$campos = [
    'nome' => 'Nome',
    'preco' => 'Preço',
    'tipo' => 'Tipo',
    'modelo' => 'Modelo',
    'marca' => 'Marca',
    'descricao' => 'Descrição',
    'desconto' => 'Desconto (%)'
];
foreach ($campos as $chave => $label) {
    $modal_content .= <<<HTML
    <div class="relative focus:font-bold">
      <input type="text" id="$chave" name="$chave" value=""
          class="peer w-full px-6 py-3 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-sm" />
      <label for="$chave"
          class="absolute -top-[12px] left-2 text-slate-500 font-thin bg-white px-1 peer-focus:font-semibold peer-focus:text-blue-600 transition-colors transform">
          {$label}
        </label>
    </div>
    HTML;
}
$content .= getModal($modal_content, 'Novo produto', 'modal_criar', false);



$content .= <<<HTML
<div class="flex flex-col gap-8 mb-12">
  <div id="lojista_header" class="flex items-center justify-between">
    <h1 class="text-3xl font-bold">Sua loja</h1>
    <button onclick="document.querySelector('#modal_criar').style.display = 'grid'" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm">
      Adicionar produto </button>
  </div>
  <div class="max-h-58 overflow-x-auto">
    <table class="min-w-full divide-y-2 divide-gray-200">
      <thead class="sticky top-0 bg-white ltr:text-left rtl:text-right">
        <tr class="*:font-bold *:text-gray-900">
          <th class="px-3 py-2 whitespace-nowrap">#</th>
          <th class="px-3 py-2 whitespace-nowrap">Nome</th>
          <th class="px-3 py-2 whitespace-nowrap">Tipo</th>
          <th class="px-3 py-2 whitespace-nowrap">Marca</th>
          <th class="px-3 py-2 whitespace-nowrap">Modelo</th>
          <th class="px-3 py-2 whitespace-nowrap">Data criação</th>
          <th class="px-3 py-2 whitespace-nowrap">Descricao</th>
          <th class="px-3 py-2 whitespace-nowrap">Preço_Base</th>
          <th class="px-3 py-2 whitespace-nowrap">Desconto</th>
          <th class="px-3 py-2 whitespace-nowrap">Preço_Final</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
HTML;

$resposta_catalogo = ler('catalogo', $_SESSION['usuario']['id'], 'loja_id');

if ($resposta_catalogo) {
  $items_catalogo = [];
  while ($item_catalogo = mysqli_fetch_assoc($resposta_catalogo)) {
    $items_catalogo[] = $item_catalogo;
  }
}

$items_produtos = [];
foreach ($items_catalogo as $item_catalogo) {
  $items_produtos[] = ler('produto', $item_catalogo['produto_id'])->fetch_array();
}

foreach ($items_produtos as $item_produto) {
  $item_produto['desconto'] .= '%';
  if ($item_produto['desconto'] == '0%') {
    $item_produto['desconto'] = '';
  }

  $id = $item_produto['id'];
  $content .= <<<HTML
    <tr class="*:text-gray-900 *:first:font-medium">
      <td class="px-3 py-2 whitespace-nowrap">
        <div class="flex gap-2">
          <a href="./?request=update&id=$id"
            class="w-full p-2 bg-blue-600 hover:shadow-xl
              text-white rounded-xl shadow-sm transition-all ease-in-out duration-200 
              active:scale-[0.98] outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">
            <i data-lucide="pencil" class="size-4"></i>
          </a>
          <a href="./?request=delete&id=$id"
            class="w-full p-2 bg-red-500 hover:shadow-xl
              text-white rounded-xl shadow-sm transition-all ease-in-out duration-200 
              active:scale-[0.98] outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
            <i data-lucide="trash" class="size-4"></i>
          </a>
        </div>
      </td>
      <td class="px-3 py-2 whitespace-nowrap">{$item_produto['nome']}</td>
      <td class="px-3 py-2 whitespace-nowrap">{$item_produto['tipo']}</td>
      <td class="px-3 py-2 whitespace-nowrap">{$item_produto['marca']}</td>
      <td class="px-3 py-2 whitespace-nowrap">{$item_produto['modelo']}</td>
      <td class="px-3 py-2 whitespace-nowrap">{$item_produto['criado_em']}</td>
      <td class="px-3 py-2 whitespace-nowrap">{$item_produto['descricao']}</td>
      <td class="px-3 py-2 whitespace-nowrap">{$item_produto['preco']}</td>
      <td class="px-3 py-2 whitespace-nowrap">{$item_produto['desconto']}</td>
      <td class="px-3 py-2 whitespace-nowrap">{$item_produto['preco_final']}</td>
    </tr>
  HTML;
}

$content .= <<<HTML
      </tbody>
    </table>
  </div>
</div>
HTML;

return $content;

function deletarProduto($id) {
  deletar('catalogo', $id, 'produto_id');
  deletar('produto', $id);
}
function atualizarProduto($id) {
  $dados = [];
  $dados['nome'] = $_POST['nome'];
  $dados['preco'] = $_POST['preco'];
  $dados['tipo'] = $_POST['tipo'];
  $dados['descricao'] = $_POST['descricao'];
  $dados['modelo'] = $_POST['modelo'];
  $dados['marca'] = $_POST['marca'];
  $dados['desconto'] = $_POST['desconto'];
  atualizar('produto', $id, $dados);
}

function criarProduto() {
    $dados = [];
    $dados['nome'] = $_POST['nome'];
    $dados['preco'] = $_POST['preco'];
    $dados['tipo'] = $_POST['tipo'];
    $dados['descricao'] = $_POST['descricao'];
    $dados['modelo'] = $_POST['modelo'];
    $dados['marca'] = $_POST['marca'];
    $dados['desconto'] = $_POST['desconto'];
    criar('produto', $dados);
}