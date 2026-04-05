<?php
require_once '../_php/crud.php';
require_once '../_componentes/modal.php';

$content = '';

// ifs para verificar se desejar DELETAR, ATUALIZAR ou CRIAR
if (!empty($_GET['id']) && !empty($_GET['request'])) {
    //deletar
    if ($_GET['request'] == 'delete') {
        deletarProduto($_GET['id']);
    } //atualizar
    elseif ($_GET['request'] == 'update'
        && !empty($_POST['nome'])
        && !empty($_POST['preco'])) {
        atualizarProduto($_GET['id']);
    } //exibir modal com formulario de atualizacao de produto
    elseif ($_GET['request'] == 'update') {
        $id = $_GET['id'];

        // a seguir é retornado todas as colunas, ate as que nao presisam ser mudadas
        //    -> entao abaixo vamos tirar as colunas desnecessarias
        $selecionado = ler('produto', $id)->fetch_assoc();

        unset($selecionado['criado_em']);
        unset($selecionado['preco_final']);
        unset($selecionado['id']);
        unset($selecionado['loja_id']);

        $modal_content = '';
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
} //criar
elseif (!empty($_POST['nome'])
    && !empty($_POST['preco'])) {
    criarProduto();
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


//mostrar tabela com produtos da loja
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
          <th class="px-3 py-2 whitespace-nowrap">Descrição</th>
          <th class="px-3 py-2 whitespace-nowrap">Preço_Base</th>
          <th class="px-3 py-2 whitespace-nowrap">Desconto</th>
          <th class="px-3 py-2 whitespace-nowrap">Preço_Final</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
HTML;

$resultado_produtos = ler('produto', $_SESSION['loja']['id'], 'loja_id');
while ($produto = $resultado_produtos->fetch_assoc()) {

    $produto['desconto'] .= '%';
    if ($produto['desconto'] == '0%') {
        $produto['desconto'] = '';
    }

    $id = $produto['id'];
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
      <td class="px-3 py-2 whitespace-nowrap">{$produto['nome']}</td>
      <td class="px-3 py-2 whitespace-nowrap">{$produto['tipo']}</td>
      <td class="px-3 py-2 whitespace-nowrap">{$produto['marca']}</td>
      <td class="px-3 py-2 whitespace-nowrap">{$produto['modelo']}</td>
      <td class="px-3 py-2 whitespace-nowrap">{$produto['criado_em']}</td>
      <td class="px-3 py-2 whitespace-nowrap">{$produto['descricao']}</td>
      <td class="px-3 py-2 whitespace-nowrap">{$produto['preco']}</td>
      <td class="px-3 py-2 whitespace-nowrap">{$produto['desconto']}</td>
      <td class="px-3 py-2 whitespace-nowrap">{$produto['preco_final']}</td>
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

function deletarProduto($id)
{
    deletar('produto', $id);

    header("Location: .");
    exit;
}

function atualizarProduto($id)
{
    $dados = [];
    $dados['nome'] = $_POST['nome'];
    $dados['preco'] = $_POST['preco'];
    $dados['tipo'] = $_POST['tipo'];
    $dados['descricao'] = $_POST['descricao'];
    $dados['modelo'] = $_POST['modelo'];
    $dados['marca'] = $_POST['marca'];
    $dados['desconto'] = $_POST['desconto'] ?? 0;
    atualizar('produto', $id, $dados);

    header("Location: .");
    exit;
}

function criarProduto()
{
    $dados = [];
    $dados['loja_id'] = $_SESSION['loja']['id'];
    $dados['nome'] = $_POST['nome'];
    $dados['preco'] = $_POST['preco'];
    $dados['tipo'] = $_POST['tipo'];
    $dados['descricao'] = $_POST['descricao'];
    $dados['modelo'] = $_POST['modelo'];
    $dados['marca'] = $_POST['marca'];
    $dados['desconto'] = $_POST['desconto'] ?? 0;

    criar('produto', $dados);

    header("Location: .");
    exit;
}