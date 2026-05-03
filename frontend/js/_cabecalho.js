// _cabecalho.js — Gerencia o menu do usuário no cabeçalho
// Requer que a página defina as variáveis globais:
//   const CAMINHO_API     = '../api';      (caminho para a pasta /api)
//   const CAMINHO_FRONTEND = '.';           (caminho para a pasta /frontend)

async function iniciarMenuUsuario() {
    const menuEl = document.getElementById('menuUsuario');
    if (!menuEl) return;

    try {
        const resposta = await fetch(CAMINHO_API + '/auth/sessao.php', { credentials: 'include' });
        const json = await resposta.json();

        if (json.status === 'ok' && json.data && json.data.usuario) {
            menuEl.innerHTML = construirMenuLogado(json.data.usuario);
            iniciarMenuDropdown();
        } else {
            menuEl.innerHTML = menuDeslogado();
        }
    } catch (e) {
        menuEl.innerHTML = menuDeslogado();
    }
}

function cls(name) {
    return window.TechnoUpStyle?.cls(name) || '';
}

function menuDeslogado() {
    return `
        <div class="${cls('menuGuestWrap')}">
            <a class="${cls('menuGuestRegister')}"
               href="${CAMINHO_FRONTEND}/registro.html">Registro</a>
            <a class="${cls('menuGuestLogin')}"
               href="${CAMINHO_FRONTEND}/login.html">Login</a>
        </div>
    `;
}

function construirMenuLogado(usuario) {
    const label = `${usuario.nome} [${usuario.tipo}]`;

    // Monta as opções de acordo com o tipo de conta
    let opcoes = `
        <a href="${CAMINHO_FRONTEND}/protegido/conta/perfil.html"
           class="${cls('menuItem')}">
            <div class="${cls('menuItemInner')}">
                <i data-lucide="user" class="${cls('menuItemIcon')}"></i>
                Meu perfil
            </div>
        </a>
    `;

    if (usuario.tipo === 'lojista') {
        opcoes += `
            <a href="${CAMINHO_FRONTEND}/protegido/lojas/gerenciar_produtos.html"
               class="${cls('menuItem')}">
                <div class="${cls('menuItemInner')}">
                    <i data-lucide="store" class="${cls('menuItemIcon')}"></i>
                    Gerenciar produtos
                </div>
            </a>
            <a href="${CAMINHO_FRONTEND}/protegido/lojas/novo.html"
               class="${cls('menuItem')}">
                <div class="${cls('menuItemInner')}">
                    <i data-lucide="store" class="${cls('menuItemIcon')}"></i>
                    Cadastro da loja
                </div>
            </a>
        `;
    } else if (usuario.tipo === 'administrador') {
        opcoes += `
            <a href="${CAMINHO_FRONTEND}/protegido/admin/lojas.html"
               class="${cls('menuItem')}">
                <div class="${cls('menuItemInner')}">
                    <i data-lucide="store" class="${cls('menuItemIcon')}"></i>
                    Gerenciar lojas
                </div>
            </a>
            <a href="${CAMINHO_FRONTEND}/protegido/admin/contas.html"
               class="${cls('menuItem')}">
                <div class="${cls('menuItemInner')}">
                    <i data-lucide="users" class="${cls('menuItemIcon')}"></i>
                    Gerenciar contas
                </div>
            </a>
        `;
    }

    return `
        <div class="${cls('menuWrap')}" id="profile-menu-container">
            <button type="button" onclick="abrirFecharMenuDropdown()"
                class="${cls('menuButton')}"
                id="menu-button" aria-expanded="false" aria-haspopup="true">
                <div class="${cls('menuAvatar')}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="${cls('menuAvatarIcon')}" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="${cls('menuLabel')}">${label}</span>
                <svg class="${cls('menuChevron')}" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                </svg>
            </button>

            <div id="profile-dropdown"
                class="${cls('menuDropdown')}"
                role="menu" aria-orientation="vertical" tabindex="-1">
                <div class="${cls('menuSection')}">
                    ${opcoes}
                </div>
                <div class="${cls('menuSectionBorder')}">
                    <button onclick="fazerLogoff()"
                        class="${cls('menuLogout')}">
                        <div class="${cls('menuLogoutInner')}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="${cls('menuLogoutIcon')}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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

function abrirFecharMenuDropdown() {
    const dropdown = document.getElementById('profile-dropdown');
    if (dropdown) dropdown.classList.toggle('hidden');
}

function iniciarMenuDropdown() {
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
    await fetch(CAMINHO_API + '/auth/logoff.php', { method: 'POST', credentials: 'include' });
    window.location.href = CAMINHO_FRONTEND + '/login.html';
}

// Inicia o menu assim que o script é carregado
iniciarMenuUsuario();
