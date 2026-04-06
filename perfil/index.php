<?php
require_once '../_php/crud.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    session_write_close();
    header('Location: ../login/');
    exit;
}

$usuario = $_SESSION['usuario'];
$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if ($nome === '' || $email === '') {
        $erro = 'Nome e email sao obrigatorios.';
    } else {
        $dadosAtualizacao = [
            'nome' => $nome,
            'email' => $email,
        ];

        if ($senha !== '') {
            $dadosAtualizacao['senha'] = $senha;
        }

        $atualizou = atualizar('conta', $usuario['id'], $dadosAtualizacao);

        if ($atualizou) {
            $resultadoUsuario = ler('conta', $usuario['id']);
            if ($resultadoUsuario && mysqli_num_rows($resultadoUsuario) > 0) {
                $_SESSION['usuario'] = mysqli_fetch_assoc($resultadoUsuario);
                $usuario = $_SESSION['usuario'];
            }
            $mensagem = 'Dados atualizados com sucesso.';
        } else {
            $erro = 'Nao foi possivel atualizar a conta.';
        }
    }
}
session_write_close();

$nome = htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8');
$tipo = htmlspecialchars($usuario['tipo'], ENT_QUOTES, 'UTF-8');

$feedbackHtml = '';
if ($mensagem !== '') {
    $feedbackHtml = <<<HTML
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        $mensagem
    </div>
HTML;
}

if ($erro !== '') {
    $feedbackHtml = <<<HTML
    <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        $erro
    </div>
HTML;
}

$content = <<<HTML
<section class="mx-auto max-w-3xl">
    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-blue-600">Perfil</p>
        <h1 class="mt-2 text-3xl font-bold text-slate-900">Atualize os dados da sua conta</h1>
        <p class="mt-3 text-sm leading-7 text-slate-600">
            Edite nome, email e senha de acesso. O tipo de conta atual e <span class="font-semibold text-slate-900">$tipo</span>.
        </p>
    </div>

    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="space-y-6">
            $feedbackHtml

            <form method="POST" action="" class="space-y-5">
                <div>
                    <label for="nome" class="mb-1.5 block text-sm font-medium text-slate-700">Nome</label>
                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        value="$nome"
                        required
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm transition-colors focus:border-blue-600 focus:outline-none focus:ring-1 focus:ring-blue-600"
                    >
                </div>

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="$email"
                        required
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm transition-colors focus:border-blue-600 focus:outline-none focus:ring-1 focus:ring-blue-600"
                    >
                </div>

                <div>
                    <label for="senha" class="mb-1.5 block text-sm font-medium text-slate-700">Nova senha</label>
                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        placeholder="Preencha somente se desejar alterar"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm transition-colors focus:border-blue-600 focus:outline-none focus:ring-1 focus:ring-blue-600"
                    >
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
<!--                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Conta ativa para acesso ao marketplace</p>-->
                    <button
                        type="submit"
                        class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2"
                    >
                        Salvar alteracoes
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
HTML;

require_once '../_componentes/layout.php';
