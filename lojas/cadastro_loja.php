<?php
require_once '../_php/crud.php';

$desejaAtualizar = false;

//verifica se é necessário criar um login do tipo lojista e então adiciona dados do login a inputs hidden
if (
    isset($_POST['nome']) && $_POST['nome'] != "" &&
    isset($_POST['email']) && $_POST['email'] != "" &&
    isset($_POST['senha']) && $_POST['senha'] != "" &&
    isset($_POST['tipo_conta']) && $_POST['tipo_conta'] != ""
) {
    $inputs_login = <<<LoginInputsHTML
<input type="hidden" name="registro_nome" value="{$_POST['nome']}"/>
<input type="hidden" name="registro_email" value="{$_POST['email']}"/>
<input type="hidden" name="registro_senha" value="{$_POST['senha']}"/>
<input type="hidden" name="registro_tipo" value="{$_POST['tipo_conta']}"/>
LoginInputsHTML;

} else {
    // verifica se o usuario lojista está cadastrado e logado - se sim então é porque deseja atualizar o seu cadastro
    session_start();
    if (isset($_SESSION['usuario'])) {
        session_write_close();
        require_once '../_php/valida_lojista.php';

        $desejaAtualizar = true;

        if (isset($_POST['conta_id']) && $_POST['conta_id'] != '' &&
            isset($_POST['nome_loja']) && $_POST['nome_loja'] != '' &&
            isset($_POST['cnpj']) && $_POST['cnpj'] != '') {

            $dados_loja = getDadosLojaDoForm();
            atualizar(
                'loja',
                $_POST['conta_id'],
                $dados_loja,
                'conta_id'
            );
            header('Location: ../lojas/');
            exit();
        }

        $id_usuario_loja = $_SESSION['usuario']['id'];
        $loja = [];

        $resultado_loja = ler('loja', $_SESSION['usuario']['id'], 'conta_id');
        if ($resultado_loja && mysqli_num_rows($resultado_loja) > 0) {
            $loja = mysqli_fetch_assoc($resultado_loja);
        }
    }
    //verifica se deve criar login de usuario lojista
    //- aqui é feito a criação tanto do login lojista quanto da loja
    elseif (isset($_POST['registro_tipo']) && $_POST['registro_tipo'] == 'lojista' &&
            isset($_POST['nome_loja']) && $_POST['nome_loja'] != '' &&
            isset($_POST['cnpj']) && $_POST['cnpj'] != '') {
        $desejaAtualizar = false; // aqui deseja criar
        session_write_close();

        $dados_conta_lojista = [
            'nome' => $_POST['registro_nome'],
            'email' => $_POST['registro_email'],
            'senha' => $_POST['registro_senha'],
            'tipo' => $_POST['registro_tipo']
        ];

        $login_lojista_criado = criar('conta', $dados_conta_lojista);

        $dados_loja = getDadosLojaDoForm();

        $dados_loja['conta_id'] = $login_lojista_criado['id'];

        criar('loja', $dados_loja);

        header('Location: ../login/');
        exit();
    }
}

function getDadosLojaDoForm()
{
    return [
        'nome_loja' => $_POST['nome_loja'],
        'telefone' => $_POST['telefone'],
        'cpf' => $_POST['cpf'],
        'cnpj' => $_POST['cnpj'],
        'cep' => $_POST['cep'],
        'estado' => $_POST['estado'],
        'cidade' => $_POST['cidade'],
        'bairro' => $_POST['bairro'],
        'logradouro' => $_POST['logradouro'],
        'numero' => $_POST['numero']
    ];
}

?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Registro Loja - TechnoUp</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            font-optical-sizing: auto;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            color: #1a1a1a;
            line-height: 1.6;
        }

        h1, h2, h3, h4 {
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        /*.text-muted {*/
        /*    color: #da373d;*/
        /*}*/
    </style>
</head>

<body>
<div class="flex flex-row-reverse min-h-screen w-full bg-white">
    <div class="flex-1 flex flex-col justify-center px-8 lg:flex-none lg:w-1/2 lg:px-16 xl:px-24">
        <div class="w-full max-w-sm mx-auto">
            <div class="mb-5 text-blue-600 inline-block">
                <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-12 h-12">
                    <path
                            d="M12 28C17.5 28 20 22 27 22C31.5 22 34.5 24.5 34.5 24.5C34.5 24.5 30.5 19 24 19C17 19 14.5 25 8 25C8 25 9.5 28 12 28Z"
                            fill="currentColor"/>
                    <path
                            d="M19 20C24.5 20 27 14 34 14C38.5 14 41.5 16.5 41.5 16.5C41.5 16.5 37.5 11 31 11C24 11 21.5 17 15 17C15 17 16.5 20 19 20Z"
                            fill="currentColor"/>
                </svg>
            </div>

            <h1 class="mb-5 text-3xl font-bold tracking-tight text-slate-900 mb-2">
                Cadastro da loja
            </h1>

            <form action="" method="POST" class="space-y-3">
                <div class="space-y-6 py-3 px-2 pe-4 max-h-86 overflow-y-scroll">
                    <div class="relative focus:font-bold">
                        <input type="text" id="nome_loja" name="nome_loja" required
                               value="<?= $loja['nome_loja'] ?? ''; ?>"
                               class="peer w-full px-4 py-3 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-sm"/>
                        <label for="nome_loja"
                               class="absolute -top-[12px] left-2 text-slate-500 font-thin bg-white px-1 peer-focus:font-semibold peer-focus:text-blue-600 transition-colors transform">Nome
                            da loja</label>
                    </div>

                    <div class="relative focus:font-bold">
                        <input type="text" id="telefone" name="telefone" required
                               value="<?= $loja['telefone'] ?? ''; ?>"
                               class="peer w-full px-4 py-3 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-sm"/>
                        <label for="telefone"
                               class="absolute -top-[12px] left-2 text-slate-500 font-thin bg-white px-1 peer-focus:font-semibold peer-focus:text-blue-600 transition-colors transform">Telefone</label>
                    </div>

                    <div class="relative focus:font-bold">
                        <input type="text" id="cpf" name="cpf" required value="<?= $loja['cpf'] ?? ''; ?>"
                               class="peer w-full px-4 py-3 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-sm"/>
                        <label for="cpf"
                               class="absolute -top-[12px] left-2 text-slate-500 font-thin bg-white px-1 peer-focus:font-semibold peer-focus:text-blue-600 transition-colors transform">CPF</label>
                    </div>

                    <div class="relative focus:font-bold">
                        <input type="text" id="cnpj" name="cnpj" required value="<?= $loja['cnpj'] ?? ''; ?>"
                               class="peer w-full px-4 py-3 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-sm"/>
                        <label for="cnpj"
                               class="absolute -top-[12px] left-2 text-slate-500 font-thin bg-white px-1 peer-focus:font-semibold peer-focus:text-blue-600 transition-colors transform">CNPJ</label>
                    </div>

                    <div class="relative focus:font-bold">
                        <input type="text" id="cep" name="cep" required value="<?= $loja['cep'] ?? ''; ?>"
                               class="peer w-full px-4 py-3 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-sm"/>
                        <label for="cep"
                               class="absolute -top-[12px] left-2 text-slate-500 font-thin bg-white px-1 peer-focus:font-semibold peer-focus:text-blue-600 transition-colors transform">CEP</label>
                    </div>

                    <div class="relative focus:font-bold">
                        <input type="text" id="estado" name="estado" required value="<?= $loja['estado'] ?? ''; ?>"
                               class="peer w-full px-4 py-3 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-sm"/>
                        <label for="estado"
                               class="absolute -top-[12px] left-2 text-slate-500 font-thin bg-white px-1 peer-focus:font-semibold peer-focus:text-blue-600 transition-colors transform">Estado</label>
                    </div>

                    <div class="relative focus:font-bold">
                        <input type="text" id="cidade" name="cidade" required value="<?= $loja['cidade'] ?? ''; ?>"
                               class="peer w-full px-4 py-3 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-sm"/>
                        <label for="cidade"
                               class="absolute -top-[12px] left-2 text-slate-500 font-thin bg-white px-1 peer-focus:font-semibold peer-focus:text-blue-600 transition-colors transform">Cidade</label>
                    </div>

                    <div class="relative focus:font-bold">
                        <input type="text" id="bairro" name="bairro" required value="<?= $loja['bairro'] ?? ''; ?>"
                               class="peer w-full px-4 py-3 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-sm"/>
                        <label for="bairro"
                               class="absolute -top-[12px] left-2 text-slate-500 font-thin bg-white px-1 peer-focus:font-semibold peer-focus:text-blue-600 transition-colors transform">Bairro</label>
                    </div>

                    <div class="relative focus:font-bold">
                        <input type="text" id="logradouro" name="logradouro" required
                               value="<?= $loja['logradouro'] ?? ''; ?>"
                               class="peer w-full px-4 py-3 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-sm"/>
                        <label for="logradouro"
                               class="absolute -top-[12px] left-2 text-slate-500 font-thin bg-white px-1 peer-focus:font-semibold peer-focus:text-blue-600 transition-colors transform">Logradouro</label>
                    </div>

                    <div class="relative focus:font-bold">
                        <input type="text" id="numero" name="numero" required value="<?= $loja['numero'] ?? ''; ?>"
                               class="peer w-full px-4 py-3 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-sm"/>
                        <label for="numero"
                               class="absolute -top-[12px] left-2 text-slate-500 font-thin bg-white px-1 peer-focus:font-semibold peer-focus:text-blue-600 transition-colors transform">Número</label>
                    </div>
                </div>
                <button type="submit"
                        class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition-all -in-out duration-200 active:scale-[0.98] outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">
                    <?= $desejaAtualizar ? 'Atualizar' : 'Cadastrar'; ?>
                </button>

                <input type="hidden" name="conta_id" value="<?= $id_usuario_loja ?? null; ?>"/>
                <?= $inputs_login ?? '' ?>
            </form>
        </div>
    </div>
    <div class="hidden lg:block lg:flex-1 relative">
        <img class="absolute inset-0 w-full h-full object-cover"
             src="https://images.unsplash.com/photo-1563986768817-257bf91c5753?q=80&w=1510&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
             alt="Imagem com um homem trabalhando em um laptop em um ambiente de escritório moderno"/>
        <div class="absolute inset-0 bg-slate-900/5"></div>
    </div>
</div>
</body>

</html>