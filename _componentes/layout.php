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
        h1, h2 ,h3 , h4 {
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }
        .text-muted {
            color: #da373d;
        }
    </style>
</head>

<body>
    <?php require_once 'header.php'; ?>

    <main class="flex flex-col gap-4 sm:mx-8 md:mx-16 lg:mx-24 px-8 py-8 my-8">
        <?php echo $content ?? 'ERROR: Content not found.'; ?>
    </main>

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>