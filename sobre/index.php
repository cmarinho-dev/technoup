<?php
$content = <<<HTML
<h1 class="text-2xl font-semibold">Sobre a TechnoUp</h1>
<p class="text-gray-600 text-lg">
  TechnoUp é uma plataforma de marketplace multi-lojas onde você pode encontrar
  produtos de diversas lojas em um único lugar, ou criar sua própria loja
  virtual.
</p>
<div class="my-px"></div>
<div class="flex items-center flex-wrap gap-4">
  <div class="flex items-stretch flex-wrap gap-4">
    <div
      class="flex flex-col h-full gap-2 border-2 shadow border-gray-200 rounded-lg p-6 max-w-sm"
    >
      <i data-lucide="store" class="text-blue-500 size-10"></i>
      <h2 class="text-xl font-semibold">Multi-Lojas</h2>
      <p class="text-gray-600">
        Plataforma que conecta várias lojas virtuais em um só lugar
      </p>
    </div>
    <div
      class="flex flex-col h-full gap-2 border-2 shadow border-gray-200 rounded-lg p-6 max-w-sm"
    >
      <i data-lucide="user" class="text-blue-500 size-10"></i>
      <h2 class="text-xl font-semibold">Para Todos</h2>
      <p class="text-gray-600">
        Compradores, lojas e administradores em uma plataforma unificada
      </p>
    </div>
  </div>
  <div class="flex items-stretch flex-wrap gap-4">
    <div
      class="flex flex-col h-full gap-2 border-2 shadow border-gray-200 rounded-lg p-6 max-w-sm"
    >
      <i data-lucide="package" class="text-blue-500 size-10"></i>
      <h2 class="text-xl font-semibold">Produtos Variados</h2>
      <p class="text-gray-600">
        Encontre tudo o que precisa de diversos vendedores
      </p>
    </div>
    <div
      class="flex flex-col h-full gap-2 border-2 shadow border-gray-200 rounded-lg p-6 max-w-sm"
    >
      <i data-lucide="shield" class="text-blue-500 size-10"></i>
      <h2 class="text-xl font-semibold">Seguro</h2>
      <p class="text-gray-600">
        Realize suas desejadas compras com proteção e segurança
    </p>
    </div>
  </div>
</div>

<div class="my-3"></div>

<h1 class="text-xl font-semibold">Tipos de Usuários</h1>
<div class="my-px"></div>
<div class="flex items-center flex-wrap gap-4">
  <div class="flex items-center flex-wrap gap-4">
    <div
      class="flex flex-col gap-2 border-2 shadow border-gray-200 rounded-lg p-6 max-w-sm"
    >
      <h2 class="text-xl font-semibold">Comprador</h2>
      <p class="text-gray-600">
        Pode navegar pelo catálogo, adicionar produtos ao carrinho e fazer pedidos
      </p>
    </div>
     <div
      class="flex flex-col gap-2 border-2 shadow border-gray-200 rounded-lg p-6 max-w-sm"
    >
      <h2 class="text-xl font-semibold">Loja</h2>
      <p class="text-gray-600">
        Pode criar uma loja virtual, adicionar produtos e gerenciar suas vendas
      </p>
    </div>
     <div
      class="flex flex-col gap-2 border-2 shadow border-gray-200 rounded-lg p-6 max-w-sm"
    >
      <h2 class="text-xl font-semibold">Administrador</h2>
      <p class="text-gray-600">
        Pode gerenciar lojas,aprovar lojas e supervisionar a plataforma
      </p>
    </div>
  </div>
</div>
HTML;
require_once '../_componentes/layout.php';
