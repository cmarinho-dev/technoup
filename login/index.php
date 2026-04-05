<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    require_once '../_php/login.php';
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TechnoUp</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../_componentes/theme.css">
</head>

<body>
    <div class="flex min-h-screen w-full bg-white">
        <div class="flex-1 flex flex-col justify-center px-8 lg:flex-none lg:w-1/2 lg:px-16 xl:px-24">
            <div class="w-full max-w-sm mx-auto">
                <div class="mb-10 text-blue-600 inline-block">
                    <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-12 h-12">
                        <path d="M12 28C17.5 28 20 22 27 22C31.5 22 34.5 24.5 34.5 24.5C34.5 24.5 30.5 19 24 19C17 19 14.5 25 8 25C8 25 9.5 28 12 28Z" fill="currentColor" />
                        <path d="M19 20C24.5 20 27 14 34 14C38.5 14 41.5 16.5 41.5 16.5C41.5 16.5 37.5 11 31 11C24 11 21.5 17 15 17C15 17 16.5 20 19 20Z" fill="currentColor" />
                    </svg>
                </div>

                <h1 class="text-3xl font-bold tracking-tight text-slate-900 mb-2">Entre com sua conta</h1>
                <p class="text-sm text-slate-500 mb-8">Não possui uma conta? <a href="../registro/" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline transition-colors">Crie uma conta</a></p>

                <form action="" method="POST" class="space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                        <input type="email" id="email" name="email" required autocomplete="email"
                            class="w-full px-4 py-3 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-sm">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Senha</label>
                        <input type="password" id="password" name="senha" required autocomplete="current-password"
                            class="w-full px-4 py-3 rounded-lg bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-colors shadow-sm">
                    </div>
                    <div class="flex items-center justify-between pt-1 pb-1">
                        <div class="flex items-center gap-2 relative">
                            <input type="checkbox" id="remember" name="remember"
                                class="peer appearance-none w-4 h-4 border border-slate-300 rounded bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-1 cursor-pointer transition-all">
                            <svg class="absolute w-2.5 h-2.5 text-white left-[3px] pointer-events-none opacity-0 peer-checked:opacity-100 transition-opacity" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <label for="remember" class="text-sm text-slate-600 cursor-pointer select-none">Lembrar de mim</label>
                        </div>
                        <a href="#" class="text-sm font-semibold text-blue-600 hover:text-blue-700 hover:underline transition-colors">Esqueceu sua senha?</a>
                    </div>
                    <button type="submit"
                        class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition-all ease-in-out duration-200 active:scale-[0.98] outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">
                        Entrar
                    </button>
                </form>

            </div>
        </div>
        <div class="hidden lg:block lg:flex-1 relative">
            <img class="absolute inset-0 w-full h-full object-cover"
                src= "https://plus.unsplash.com/premium_photo-1764695604111-68bc34f59ff1?q=80&w=1332&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                alt="Imagem com um homem trabalhando em um laptop em um ambiente de escritório moderno">
            <div class="absolute inset-0 bg-slate-900/5"></div>
        </div>
    </div>
</body>

</html>