<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechnoUp</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
<?php require_once 'header.php'; ?>

<main class="flex flex-col gap-4 sm:mx-8 md:mx-16 lg:mx-24 px-8 py-8 my-8">
    <?php echo $content ?? 'ERROR: Content not found.'; ?>
</main>

<footer class="border-t border-slate-200 bg-white">
    <div class="mx-auto grid gap-10 px-8 py-10 sm:mx-8 md:mx-16 lg:mx-24 lg:grid-cols-[1.3fr_0.7fr_0.7fr_0.9fr]">
        <div>
            <div class="flex items-center gap-3 text-blue-600">
                <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-10 w-10">
                    <path d="M12 28C17.5 28 20 22 27 22C31.5 22 34.5 24.5 34.5 24.5C34.5 24.5 30.5 19 24 19C17 19 14.5 25 8 25C8 25 9.5 28 12 28Z" fill="currentColor"/>
                    <path d="M19 20C24.5 20 27 14 34 14C38.5 14 41.5 16.5 41.5 16.5C41.5 16.5 37.5 11 31 11C24 11 21.5 17 15 17C15 17 16.5 20 19 20Z" fill="currentColor"/>
                </svg>
                <div>
                    <p class="text-lg font-semibold text-slate-900">TechnoUp</p>
                    <p class="text-sm text-slate-500">Marketplace de tecnologia e lojas parceiras.</p>
                </div>
            </div>
            <p class="mt-4 max-w-md text-sm leading-7 text-slate-600">
                Descubra produtos, acompanhe vitrines de lojistas e encontre componentes, perifericos e setups completos em uma experiencia centralizada.
            </p>
        </div>
        <div>
            <h2 class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">Navegacao</h2>
            <div class="mt-4 flex flex-col gap-3 text-sm text-slate-600">
                <a class="transition hover:text-blue-600" href="../home">Home</a>
                <a class="transition hover:text-blue-600" href="../catalogo">Catalogo</a>
                <a class="transition hover:text-blue-600" href="../lojas">Lojas</a>
                <a class="transition hover:text-blue-600" href="../sobre">Sobre</a>
            </div>
        </div>
        <div>
            <h2 class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">Categorias</h2>
            <div class="mt-4 flex flex-col gap-3 text-sm text-slate-600">
                <span>Hardware</span>
                <span>Perifericos</span>
                <span>Monitores</span>
                <span>PC Gamer</span>
            </div>
        </div>
        <div>
            <h2 class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-400">Contato</h2>
            <div class="mt-4 flex flex-col gap-3 text-sm text-slate-600">
                <span>atendimento@technoup.local</span>
                <span>Seg a Sex, 9h as 18h</span>
                <span>Marketplace para lojistas e consumidores</span>
            </div>
        </div>
    </div>
    <div class="border-t border-slate-200 px-8 py-4 text-center text-sm text-slate-500">
        TechnoUp &copy; 2026. Todos os direitos reservados.
    </div>
</footer>

<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<script>
    lucide.createIcons();
</script>
</body>

</html>
