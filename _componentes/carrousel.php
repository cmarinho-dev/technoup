<?php
function getCarrousel($carrousel_content, $carrousel_title = 'Carrousel', $carrousel_id = ''): string
{
    $carrousel_id = trim($carrousel_id) !== '' ? $carrousel_id : 'carrousel_' . uniqid();
    $prev_id = $carrousel_id . '_prev';
    $next_id = $carrousel_id . '_next';

    return <<<HTML
<div class="flex items-center gap-8">
    <div id="loja_header">
        <h1 class="text-3xl font-bold">$carrousel_title</h1>
    </div>
</div>
<div class="relative mb-8">
    <button
        type="button"
        id="$prev_id"
        class="absolute -left-3 top-1/2 z-30 hidden -translate-y-1/2 rounded-full border border-gray-200 bg-white/90 p-2 text-gray-700 shadow-md backdrop-blur sm:flex"
        aria-label="Voltar $carrousel_title"
    >
        <i data-lucide="chevron-left" class="h-5 w-5"></i>
    </button>
    <div
        id="$carrousel_id"
        class="flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth pb-4 pt-2 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
    >
$carrousel_content
    </div>
    <button
        type="button"
        id="$next_id"
        class="absolute -right-3 top-1/2 z-30 hidden -translate-y-1/2 rounded-full border border-gray-200 bg-white/90 p-2 text-gray-700 shadow-md backdrop-blur sm:flex"
        aria-label="Avancar $carrousel_title"
    >
        <i data-lucide="chevron-right" class="h-5 w-5"></i>
    </button>
</div>
<script>
    (function () {
        const carousel = document.getElementById('$carrousel_id');
        const prev = document.getElementById('$prev_id');
        const next = document.getElementById('$next_id');

        if (!carousel || !prev || !next) {
            return;
        }

        function moverCarousel(direcao) {
            const distancia = Math.max(carousel.clientWidth * 0.85, 280);
            carousel.scrollBy({
                left: distancia * direcao,
                behavior: 'smooth'
            });
        }

        prev.addEventListener('click', () => moverCarousel(-1));
        next.addEventListener('click', () => moverCarousel(1));
    })();
</script>
HTML;
}
