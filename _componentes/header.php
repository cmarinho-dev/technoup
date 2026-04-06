<?php
session_start();
$opcoes_login = '';

if (isset($_SESSION['usuario'])) {
    $usuario_label = $_SESSION['usuario']['nome'] . ' [' . $_SESSION['usuario']['tipo'] . ']';
    $tipoUsuario = $_SESSION['usuario']['tipo'];
    session_write_close();

    $opcoes = [];
    switch ($tipoUsuario) {
        case 'lojista':
            $opcoes = [
                [
                    'label' => 'Meu perfil',
                    'url_destino' => '../perfil/',
                    'icone' => 'user',
                    'id' => 0
                ],
                [
                    'label' => 'Gerenciar produtos',
                    'url_destino' => '../lojas/',
                    'icone' => 'store',
                    'id' => 1
                ],
                [
                    'label' => 'Cadastro loja',
                    'url_destino' => '../lojas/cadastro_loja.php',
                    'icone' => 'store',
                    'id' => 2
                ]
            ];
            break;
        case 'administrador':
            $opcoes = [
                [
                    'label' => 'Meu perfil',
                    'url_destino' => '../perfil/',
                    'icone' => 'user',
                    'id' => 0
                ],
                [
                    'label' => 'Gerenciar lojas',
                    'url_destino' => '../admin+lojas/',
                    'icone' => 'store',
                    'id' => 1
                ],
                [
                    'label' => 'Configurações',
                    'url_destino' => '#',
                    'icone' => 'settings',
                    'id' => 2
                ]
            ];
            break;
        default:
            $opcoes = [
                [
                    'label' => 'Meu perfil',
                    'url_destino' => '../perfil/',
                    'icone' => 'user',
                    'id' => 0
                ],
            ];
            break;
    }
    $opcoes_login = retornaOpcoesUsuario($usuario_label, $opcoes);

} else {
    session_write_close();
    $opcoes_login = <<<HTML
  <div class="flex items-center gap-3">
    <a class="hidden sm:block rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm border border-slate-300 transition-all hover:bg-slate-150 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2" href="../registro">
      Registro
    </a>
    <a class="block rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2" href="../login">
      Login
    </a>
  </div>
  HTML;
}
?>

    <header class="bg-white shadow-sm border-b border-slate-200">
        <div class="mx-auto flex h-16 md:mx-12 lg:mx-28 items-center gap-8 px-4 sm:px-6">
            <a class="block text-blue-600" href="#">
                <span class="sr-only">Home</span>
                <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-10 h-10">
                    <path d="M12 28C17.5 28 20 22 27 22C31.5 22 34.5 24.5 34.5 24.5C34.5 24.5 30.5 19 24 19C17 19 14.5 25 8 25C8 25 9.5 28 12 28Z"
                          fill="currentColor"/>
                    <path d="M19 20C24.5 20 27 14 34 14C38.5 14 41.5 16.5 41.5 16.5C41.5 16.5 37.5 11 31 11C24 11 21.5 17 15 17C15 17 16.5 20 19 20Z"
                          fill="currentColor"/>
                </svg>
            </a>

            <div class="flex flex-1 items-center justify-end md:justify-between">
                <nav aria-label="Global" class="hidden md:block">
                    <ul class="flex items-center gap-6 text-sm font-medium text-slate-600">
                        <li>
                            <a class="transition hover:text-blue-600" href="../home"> Home </a>
                        </li>

                        <li>
                            <a class="transition hover:text-blue-600" href="../catalogo"> Catálogo </a>
                        </li>

                        <li>
                            <a class="transition hover:text-blue-600" href="../sobre"> Sobre </a>
                        </li>
                    </ul>
                </nav>

                <div class="flex items-center gap-4">
                    <?= $opcoes_login ?>

                    <button class="block rounded-lg bg-slate-100 p-2.5 text-slate-600 transition hover:text-slate-700 md:hidden">
                        <span class="sr-only">Toggle menu</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

<?php
function retornaOpcoesUsuario($usuario_label = 'unknow_user', $opcoes = [])
{
    $opcoesParteSuperior = getOpcoesParteSuperior($usuario_label);
    $opcoesParteMeio = getOpcoesParteMeio($opcoes);
    $opcoesParteInferior = getOpcoesParteInferior();
    return $opcoesParteSuperior . $opcoesParteMeio . $opcoesParteInferior;
}

function getOpcoesParteSuperior($usuario_label = '')
{
    return <<<HTMLsuperior
  <div class="relative inline-block text-left" id="profile-menu-container">
    <button type="button" onclick="toggleDropdown()" class="flex items-center gap-2 rounded-full border-none bg-white p-1 px-3 transition-all hover:bg-slate-50 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-offset-2" id="menu-button" aria-expanded="true" aria-haspopup="true">
      <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-600 font-bold">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
        </svg>
      </div>
      <span class="text-sm font-medium text-slate-700 hidden sm:block">$usuario_label</span>
      <svg class="-mr-1 h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
      </svg>
    </button>

    <div id="profile-dropdown" class="hidden absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-blue-300 ring-opacity-5 focus:outline-none overflow-hidden transition-all duration-200 ease-in-out" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
      <div class="py-1">
HTMLsuperior;
}

function getOpcoesParteMeio($opcoes = [])
{
    $opcoesParteMeio = '';
    foreach ($opcoes as $opcao) {
        $opcoesParteMeio .= <<<HTML
        <a href="{$opcao['url_destino']}" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors" role="menuitem" tabindex="-1" id="menu-item-{$opcao['id']}">
          <div class="flex items-center gap-2">
            <i data-lucide="{$opcao['icone']}"  class="h-4 w-4 text-slate-400"></i>
            {$opcao['label']}
          </div>
        </a>
HTML;
    }

    return $opcoesParteMeio;
}

function getOpcoesParteInferior()
{
    return <<<HTMLinferior
      </div>
      <div class="border-t border-slate-100 py-1 cursor-pointer">
        <form method="POST" action="../_php/logoff.php" class="block m-0">
          <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors cursor-pointer" role="menuitem" tabindex="-1" id="menu-item-3">
            <div class="flex items-center gap-2 font-medium">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
              Sair
            </div>
          </button>
        </form>
      </div>
    </div>
  </div>
  <script>
    function toggleDropdown() {
      const dropdown = document.getElementById('profile-dropdown');
      dropdown.classList.toggle('hidden');
    }
    // Fecha o dropdown se clicar fora dele
    window.addEventListener('click', function(e) {
      const dropdown = document.getElementById('profile-dropdown');
      const button = document.getElementById('menu-button');
      if (dropdown && button && !button.contains(e.target) && !dropdown.contains(e.target)) {
        if (!dropdown.classList.contains('hidden')) {
          dropdown.classList.add('hidden');
        }
      }
    });
  </script>
HTMLinferior;
}

?>
