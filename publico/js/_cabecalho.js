// _cabecalho.js — Gerencia o menu do usuário no cabeçalho
// Requer que a página defina as variáveis globais:
//   const API     = '../api';      (caminho para a pasta /api)
//   const PAGINAS = '.';           (caminho para a pasta /publico)

async function iniciarMenuUsuario() {
    const menuEl = document.getElementById('menuUsuario');
    if (!menuEl) return;

    try {
        const resposta = await fetch(API + '/auth/sessao.php', { credentials: 'include' });
        const json = await resposta.json();

        if (json.status === 'ok' && json.data && json.data.usuario) {
            menuEl.innerHTML = construirMenuLogado(json.data.usuario);
            iniciarDropdown();
        } else {
            menuEl.innerHTML = menuDeslogado();
        }
    } catch (e) {
        menuEl.innerHTML = menuDeslogado();
    }
}

function menuDeslogado() {
    return `
        <div class="flex items-center gap-3">
            <a class="hidden sm:block rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm border border-slate-300 transition-all hover:bg-slate-50"
               href="${PAGINAS}/registro.html">Registro</a>
            <a class="block rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-blue-700"
               href="${PAGINAS}/login.html">Login</a>
        </div>
    `;
}

function construirMenuLogado(usuario) {
    const label = `${usuario.nome} [${usuario.tipo}]`;

    // Monta as opções de acordo com o tipo de conta
    let opcoes = `
        <a href="${PAGINAS}/perfil.html"
           class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
            <div class="flex items-center gap-2">
                <i data-lucide="user" class="h-4 w-4 text-slate-400"></i>
                Meu perfil
            </div>
        </a>
    `;

    if (usuario.tipo === 'lojista') {
        opcoes += `
            <a href="${PAGINAS}/lojas/gerenciar.html"
               class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                <div class="flex items-center gap-2">
                    <i data-lucide="store" class="h-4 w-4 text-slate-400"></i>
                    Gerenciar produtos
                </div>
            </a>
            <a href="${PAGINAS}/lojas/cadastro.html"
               class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                <div class="flex items-center gap-2">
                    <i data-lucide="store" class="h-4 w-4 text-slate-400"></i>
                    Cadastro da loja
                </div>
            </a>
        `;
    } else if (usuario.tipo === 'administrador') {
        opcoes += `
            <a href="${PAGINAS}/admin/lojas.html"
               class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                <div class="flex items-center gap-2">
                    <i data-lucide="store" class="h-4 w-4 text-slate-400"></i>
                    Gerenciar lojas
                </div>
            </a>
        `;
    }

    return `
        <div class="relative inline-block text-left" id="profile-menu-container">
            <button type="button" onclick="toggleDropdown()"
                class="flex items-center gap-2 rounded-full border-none bg-white p-1 px-3 transition-all hover:bg-slate-50 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-offset-2"
                id="menu-button" aria-expanded="false" aria-haspopup="true">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-600 font-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-slate-700 hidden sm:block">${label}</span>
                <svg class="-mr-1 h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                </svg>
            </button>

            <div id="profile-dropdown"
                class="hidden absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-blue-300 ring-opacity-5 overflow-hidden"
                role="menu" aria-orientation="vertical" tabindex="-1">
                <div class="py-1">
                    ${opcoes}
                </div>
                <div class="border-t border-slate-100 py-1">
                    <button onclick="fazerLogoff()"
                        class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
                        <div class="flex items-center gap-2 font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Sair
                        </div>
                    </button>
                </div>
            </div>
        </div>
    `;
}

function toggleDropdown() {
    const dropdown = document.getElementById('profile-dropdown');
    if (dropdown) dropdown.classList.toggle('hidden');
}

function iniciarDropdown() {
    // Fecha o dropdown ao clicar fora
    window.addEventListener('click', function (e) {
        const dropdown = document.getElementById('profile-dropdown');
        const button   = document.getElementById('menu-button');
        if (dropdown && button && !button.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
    // Atualiza ícones Lucide adicionados dinamicamente
    if (window.lucide) lucide.createIcons();
}

async function fazerLogoff() {
    await fetch(API + '/auth/logoff.php', { method: 'POST', credentials: 'include' });
    window.location.href = PAGINAS + '/login.html';
}

// Inicia o menu assim que o script é carregado
iniciarMenuUsuario();
