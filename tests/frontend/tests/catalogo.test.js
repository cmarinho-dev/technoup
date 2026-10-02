// @vitest-environment jsdom

import { afterEach, beforeEach, expect, test, vi } from 'vitest';

beforeEach(() => {
  vi.resetModules();
  document.body.innerHTML = `
    <input id="filtraNome">
    <select id="filtraTipo"><option value="">Todos</option></select>
    <input id="filtroPrecoMin">
    <input id="filtroPrecoMax">
    <div id="carousel_lojas_container"></div>
    <p id="catalogo_contador"></p>
    <div id="catalogo_items_grid"></div>
  `;
  vi.stubGlobal('CAMINHO_API', '/api');
  vi.stubGlobal('lucide', { createIcons: vi.fn() });
});

afterEach(() => {
  vi.unstubAllGlobals();
  document.body.innerHTML = '';
});

test('carrega produtos e filtra por nome e preço', async () => {
  const produtos = [
    { id: 1, nome: 'Memória RAM', tipo: 'Hardware', preco: '250.00', preco_final: '250.00' },
    { id: 2, nome: 'Mouse', tipo: 'Periférico', preco: '80.00', preco_final: '80.00' },
  ];
  vi.stubGlobal('fetch', vi.fn()
    .mockResolvedValueOnce({ json: async () => ({ status: 'ok', data: produtos }) })
    .mockResolvedValueOnce({ json: async () => ({ status: 'ok', data: [] }) }));

  await import('../../../frontend/js/catalogo.js');
  await vi.waitFor(() => {
    expect(document.querySelectorAll('#catalogo_items_grid article')).toHaveLength(2);
  });
  expect(document.getElementById('catalogo_contador').textContent).toBe('2 produto(s) encontrado(s)');

  const nome = document.getElementById('filtraNome');
  nome.value = 'memoria';
  nome.dispatchEvent(new Event('input'));
  expect(document.querySelectorAll('#catalogo_items_grid article')).toHaveLength(1);
  expect(document.getElementById('catalogo_items_grid').textContent).toContain('Memória RAM');

  const precoMinimo = document.getElementById('filtroPrecoMin');
  precoMinimo.value = '300';
  precoMinimo.dispatchEvent(new Event('input'));
  expect(document.getElementById('catalogo_contador').textContent).toBe('0 produto(s) encontrado(s)');
  expect(document.getElementById('catalogo_items_grid').textContent).toContain('Nenhum produto encontrado');
});
