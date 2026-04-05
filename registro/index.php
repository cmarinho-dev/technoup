<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_conta = $_POST['tipo_conta'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    require_once '../_php/crud.php';
    $dados_usuario = [
        'nome' => $nome,
        'email' => $email,
        'senha' => $senha,
        'tipo' => $tipo_conta,
    ];

    criar('conta', $dados_usuario);
    header('Location: ../login/');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - TechnoUp</title>
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
    </style>
</head>

<body>
<div class="flex flex-row-reverse min-h-screen w-full bg-white">
    <div class="flex-1 flex flex-col justify-center px-8 lg:flex-none lg:w-1/2 lg:px-16 xl:px-24">
        <div class="w-full max-w-sm mx-auto">
            <div class="mb-10 text-blue-600 inline-block">
                <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-12 h-12">
                    <path
                            d="M12 28C17.5 28 20 22 27 22C31.5 22 34.5 24.5 34.5 24.5C34.5 24.5 30.5 19 24 19C17 19 14.5 25 8 25C8 25 9.5 28 12 28Z"
                            fill="currentColor"/>
                    <path
                            d="M19 20C24.5 20 27 14 34 14C38.5 14 41.5 16.5 41.5 16.5C41.5 16.5 37.5 11 31 11C24 11 21.5 17 15 17C15 17 16.5 20 19 20Z"
                            fill="currentColor"/>
                </svg>
            </div>

            <h1 class="text-3xl font-bold tracking-tight text-slate-900 mb-2">Crie seu cadastro</h1>
            <p class="text-sm text-slate-500 mb-8">Já possui uma conta? <a href="../login/"
                                                                           class="font-semibold text-blue-600 hover:text-blue-700 hover:underline transition-colors">Entre
                    aqui</a></p>

            <form action="" method="POST" class="space-y-5">
                <div class="text-md text-slate-600 font-medium -mb-1">Tipo de conta</div>
                <div class="flex items-center gap-2 mt-2 ms-[4px]">
                    <input type="radio" name="tipo_conta" id="tipo_conta_consumidor" value="consumidor"
                           class="box-border
                            size-3 peer appearance-none rounded-full ring-1 ring-offset-2 ring-slate-300 checked:ring-blue-600 checked:bg-blue-600"
                           checked>
                    <label for="tipo_conta_consumidor" class="text-sm text-slate-600">Consumidor</label>
                    <div class="w-2"></div>
                    <input type="radio" name="tipo_conta" id="tipo_conta_loja" value="lojista"
                           class="box-border
                            size-3 peer appearance-none rounded-full ring-1 ring-offset-2 ring-slate-300 checked:ring-blue-600 checked:bg-blue-600">
                    <label for="tipo_conta_loja" class="text-sm text-slate-600">Lojista</label>
                </div>
                <input type="text" id="name" name="nome" required autocomplete="name" placeholder="Nome completo"
                       class="w-full px-4 py-3 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-sm">
                <input type="email" id="email" name="email" required autocomplete="email" placeholder="Email"
                       class="w-full px-4 py-3 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-sm">
                <input type="password" id="password" name="senha" required placeholder="Senha"
                       class="w-full px-4 py-3 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-sm">
                <span id="confirm_password_error" class="text-red-600 text-sm"></span>
                <input type="password" id="confirm_password" name="confirm_password" required
                       placeholder="Confirmar Senha"
                       class="w-full px-4 py-3 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-sm">
                <button type="submit"
                        class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition-all ease-in-out duration-200 active:scale-[0.98] outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">
                    Criar conta
                </button>
            </form>

        </div>
    </div>
    <div class="hidden lg:block lg:flex-1 relative">
        <img class="absolute inset-0 w-full h-full object-cover"
             src="https://images.unsplash.com/photo-1563986768817-257bf91c5753?q=80&w=1510&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
             alt="Imagem com um homem trabalhando em um laptop em um ambiente de escritório moderno">
        <div class="absolute inset-0 bg-slate-900/5"></div>
    </div>
</div>
<script>
    const form = document.querySelector('form');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const confirmPasswordError = document.getElementById('confirm_password_error');

    form.addEventListener('submit', (e) => {
        if (passwordInput.value !== confirmPasswordInput.value) {
            e.preventDefault();
            confirmPasswordError.textContent = 'As senhas não coincidem!';
        } else {
            confirmPasswordError.textContent = '';

            //verifica se é lojista e direciona a requisição para o cadastro de loja
            const tipoContaOption = document.getElementsByName('tipo_conta');
            tipoContaOption.forEach(e => {
                if (e.id === 'tipo_conta_loja' && e.checked) {
                    form.action = '../lojas/cadastro_loja.php';
                }
            })
        }
    });
</script>
</body>

</html>