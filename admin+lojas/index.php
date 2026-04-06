<?php
require_once '../_php/valida_admin.php';
require_once '../_php/crud.php';

if (!empty($_GET['request']) && !empty($_GET['conta_id'])) {
    $contaId = (int)$_GET['conta_id'];

    if ($_GET['request'] === 'toggle') {
        alternarStatusConta($contaId);
    }

    if ($_GET['request'] === 'delete') {
        deletarContaLojista($contaId);
    }
}

$resultadoLojistas = listarLojistasComLoja();
$content = <<<HTML
<div class="flex flex-col gap-8 mb-12">
  <div id="admin_lojas_header" class="flex items-center justify-between gap-4 flex-wrap">
    <div>
      <h1 class="text-3xl font-bold">Administracao de lojas</h1>
      <p class="text-sm text-slate-500 mt-2">Gerencie logins de lojistas, status da conta e dados da loja vinculada.</p>
    </div>
    <a href="../lojas/cadastro_loja.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">
      Nova loja
    </a>
  </div>
  <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-slate-200">
      <thead class="bg-slate-50 text-left">
        <tr class="*:px-4 *:py-3 *:text-xs *:font-semibold *:uppercase *:tracking-[0.16em] *:text-slate-500">
          <th>Acoes</th>
          <th>Status</th>
          <th>Responsavel</th>
          <th>Email</th>
          <th>Loja</th>
          <th>Telefone</th>
          <th>CNPJ</th>
          <th>Cidade</th>
          <th>Endereco</th>
          <th>Criado em</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-200">
HTML;

if ($resultadoLojistas && mysqli_num_rows($resultadoLojistas) > 0) {
    while ($lojista = mysqli_fetch_assoc($resultadoLojistas)) {
        $contaId = (int)$lojista['conta_id'];
        $ativo = (int)$lojista['ativo'] === 1;
        $statusLabel = $ativo ? 'Ativa' : 'Inativa';
        $statusClasses = $ativo
            ? 'bg-emerald-50 text-emerald-700'
            : 'bg-slate-100 text-slate-600';
        $toggleLabel = $ativo ? 'Desativar' : 'Ativar';
        $toggleClasses = $ativo
            ? 'bg-amber-500 hover:bg-amber-600 focus:ring-amber-500'
            : 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-600';
        $endereco = trim(($lojista['logradouro'] ?? '') . ', ' . ($lojista['numero'] ?? ''));
        $cidade = trim(($lojista['cidade'] ?? '') . ' / ' . ($lojista['estado'] ?? ''));
        $nomeLoja = htmlspecialchars($lojista['nome_loja'] ?? 'Sem loja cadastrada', ENT_QUOTES, 'UTF-8');
        $nomeConta = htmlspecialchars($lojista['nome_conta'], ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars($lojista['email'], ENT_QUOTES, 'UTF-8');
        $telefone = htmlspecialchars($lojista['telefone'] ?? '-', ENT_QUOTES, 'UTF-8');
        $cnpj = htmlspecialchars($lojista['cnpj'] ?? '-', ENT_QUOTES, 'UTF-8');
        $cidade = htmlspecialchars($cidade !== '/' ? $cidade : '-', ENT_QUOTES, 'UTF-8');
        $endereco = htmlspecialchars(trim($endereco, ' ,') ?: '-', ENT_QUOTES, 'UTF-8');
        $criadoEm = htmlspecialchars($lojista['criado_em'], ENT_QUOTES, 'UTF-8');

        $content .= <<<HTML
        <tr class="align-top *:px-4 *:py-4 *:text-sm *:text-slate-700">
          <td class="whitespace-nowrap">
            <div class="flex gap-2">
              <a href="./?request=toggle&conta_id=$contaId"
                class="rounded-xl px-3 py-2 text-xs font-semibold text-white shadow-sm transition-all duration-200 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 $toggleClasses">
                $toggleLabel
              </a>
              <a href="./?request=delete&conta_id=$contaId"
                class="rounded-xl bg-red-500 px-3 py-2 text-xs font-semibold text-white shadow-sm transition-all duration-200 hover:bg-red-600 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                onclick="return confirm('Deseja deletar esta conta de lojista? Isso removera tambem a loja e os produtos vinculados.');">
                Deletar
              </a>
            </div>
          </td>
          <td>
            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold $statusClasses">$statusLabel</span>
          </td>
          <td class="font-medium text-slate-900">$nomeConta</td>
          <td>$email</td>
          <td>$nomeLoja</td>
          <td>$telefone</td>
          <td>$cnpj</td>
          <td>$cidade</td>
          <td>$endereco</td>
          <td class="whitespace-nowrap">$criadoEm</td>
        </tr>
HTML;
    }
} else {
    $content .= <<<HTML
        <tr>
          <td colspan="10" class="px-4 py-12 text-center text-sm text-slate-500">
            Nenhuma conta de lojista encontrada.
          </td>
        </tr>
HTML;
}

$content .= <<<HTML
      </tbody>
    </table>
  </div>
</div>
HTML;

require_once '../_componentes/layout.php';

function listarLojistasComLoja()
{
    global $conn;

    $sql = "
        SELECT
            conta.id AS conta_id,
            conta.nome AS nome_conta,
            conta.email,
            conta.ativo,
            conta.criado_em,
            loja.nome_loja,
            loja.telefone,
            loja.cnpj,
            loja.cidade,
            loja.estado,
            loja.logradouro,
            loja.numero
        FROM conta
        LEFT JOIN loja ON loja.conta_id = conta.id
        WHERE conta.tipo = 'lojista'
        ORDER BY conta.criado_em DESC
    ";

    return mysqli_query($conn, $sql);
}

function alternarStatusConta($contaId)
{
    $resultadoConta = ler('conta', $contaId);
    if (!$resultadoConta || mysqli_num_rows($resultadoConta) === 0) {
        header('Location: .');
        exit;
    }

    $conta = mysqli_fetch_assoc($resultadoConta);
    $novoStatus = ((int)$conta['ativo'] === 1) ? 0 : 1;

    atualizar('conta', $contaId, ['ativo' => $novoStatus]);

    header('Location: .');
    exit;
}

function deletarContaLojista($contaId)
{
    deletar('conta', $contaId);

    header('Location: .');
    exit;
}
