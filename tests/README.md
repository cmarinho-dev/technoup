# Testes da API

Os testes de unidade verificam as funções de `api/validacoes.php` e a normalização de uploads em `api/midias.php` sem Apache ou MySQL.
Os testes de integração fazem requisições GET públicas ao Apache e precisam do MySQL ligado.
Nenhum teste precisa de login, senha ou cookie.

Na raiz do projeto, execute:

```powershell
php tests/api/phpunit.phar --do-not-cache-result tests/api
```

Para usar outra URL da API:

```powershell
$env:TEST_API_BASE_URL = 'http://localhost/technoPhp/api'
php tests/api/phpunit.phar --do-not-cache-result tests/api
```

Para executar somente os testes de unidade:

```powershell
php tests/api/phpunit.phar --do-not-cache-result tests/api/ValidacoesTest.php
php tests/api/phpunit.phar --do-not-cache-result tests/api/MidiasTest.php
```

# Testes do frontend

Os testes usam Vitest e jsdom para verificar o login e os filtros do catálogo com respostas simuladas da API. Não precisam de Apache, MySQL ou login real.

Na pasta `tests/frontend`, instale as dependências `npm install` e execute:

```powershell
cd tests/frontend
$env:NODE_OPTIONS = '--use-system-ca'
npm.cmd install --offline=false
npm.cmd test
```
