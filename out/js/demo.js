(() => {
    const root = new URL('../', document.currentScript.src);
    const pages = new Set(['', 'index.html', 'home.html', 'catalogo.html', 'sobre.html', 'formulario.html']);
    const message = 'Esta é só uma visualização de demonstração. Esta funcionalidade não está disponível nesta versão de portfólio.';
    const dialog = document.createElement('dialog');
    dialog.className = 'demo-dialog';
    dialog.setAttribute('aria-labelledby', 'demo-title');
    dialog.setAttribute('aria-describedby', 'demo-message');
    dialog.innerHTML = `
        <h2 id="demo-title">Visualização de demonstração</h2>
        <p id="demo-message"></p>
        <form method="dialog"><button type="submit">Entendi</button></form>
    `;
    dialog.querySelector('p').textContent = message;
    document.body.append(dialog);

    function showDemo() {
        if (!dialog.open) dialog.showModal();
    }

    dialog.addEventListener('click', (event) => {
        if (event.target !== dialog) return;
        const rect = dialog.getBoundingClientRect();
        if (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom) dialog.close();
    });

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a');
        if (!link) return;
        const url = new URL(link.href, document.baseURI);
        const relativePath = url.pathname.startsWith(root.pathname) ? url.pathname.slice(root.pathname.length) : null;
        if (link.hasAttribute('data-demo') || url.origin !== root.origin || !pages.has(relativePath)) {
            event.preventDefault();
            showDemo();
        }
    });

    document.addEventListener('submit', (event) => {
        if (dialog.contains(event.target)) return;
        event.preventDefault();
        showDemo();
    });

    const media = document.getElementById('midias');
    if (media) media.addEventListener('change', () => {
        const files = Array.from(media.files);
        document.getElementById('midiaSelecionada').textContent = files.length
            ? `${files.length} arquivo(s) selecionado(s): ${files.map(file => file.name).join(', ')}. Nenhum arquivo será enviado.`
            : 'Até 8 arquivos: JPG, PNG, WebP ou vídeos MP4, WebM, MOV e M4V.';
    });

    if (window.lucide) lucide.createIcons();
})();
