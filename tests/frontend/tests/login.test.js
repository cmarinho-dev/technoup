// @vitest-environment jsdom

import { afterEach, beforeEach, expect, test, vi } from 'vitest';

beforeEach(() => {
  vi.resetModules();
  document.body.innerHTML = `
    <input id="inputEmail">
    <input id="inputSenha">
    <button id="btnEntrar">Entrar</button>
    <p id="mensagemErro" class="hidden"></p>
  `;
  vi.stubGlobal('CAMINHO_API', '/api');
  vi.stubGlobal('fetch', vi.fn());
});

afterEach(() => {
  vi.unstubAllGlobals();
  document.body.innerHTML = '';
});

test('mostra erro quando email e senha estão vazios', async () => {
  await import('../../../frontend/js/login.js');

  document.getElementById('btnEntrar').click();

  const erro = document.getElementById('mensagemErro');
  expect(erro.textContent).toBe('Preencha o email e a senha.');
  expect(erro.classList.contains('hidden')).toBe(false);
  expect(fetch).not.toHaveBeenCalled();
});

test('mostra erro da API e libera o botão para nova tentativa', async () => {
  fetch.mockResolvedValue({
    json: async () => ({ status: 'nok', mensagem: 'Credenciais inválidas.' }),
  });
  await import('../../../frontend/js/login.js');

  document.getElementById('inputEmail').value = ' pessoa@exemplo.com ';
  document.getElementById('inputSenha').value = 'senha';
  document.getElementById('btnEntrar').click();

  await vi.waitFor(() => {
    expect(document.getElementById('mensagemErro').textContent).toBe('Credenciais inválidas.');
  });
  expect(document.getElementById('btnEntrar').disabled).toBe(false);
  expect(fetch).toHaveBeenCalledWith('/api/auth/login.php', expect.objectContaining({ method: 'POST' }));
  expect(fetch.mock.calls[0][1].body.get('email')).toBe('pessoa@exemplo.com');
});
