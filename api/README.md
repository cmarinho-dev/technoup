# API Java

O Spring Boot serve a API, o frontend e os arquivos de mídia no mesmo host. As URLs da API são em português, sem extensão `.php`; formulários continuam usando os campos existentes e as respostas mantêm o envelope `{ "status", "mensagem", "data" }` com `status` igual a `ok` ou `nok`.

## Executar

1. Instale Java 17 ou superior e MySQL. Em uma instalação nova, crie o banco `technoup` com o `script.sql` da raiz. **Não execute esse script em um banco existente:** ele começa com `DROP DATABASE`.
2. Na pasta `api-v2`, configure `DB_URL`, `DB_USER` e `DB_PASSWORD` conforme o MySQL. Os padrões são `jdbc:mysql://localhost:3306/technoup`, `root` e senha vazia.
3. Execute `./gradlew bootRun` (ou `gradlew.bat bootRun` no Windows) e abra `http://localhost:8080/frontend/home.html`. A porta pode ser alterada com `SERVER_PORT`.

Execute o Gradle a partir de `api-v2`. Se iniciar o JAR de outro diretório, defina `TECHNOUP_WORKSPACE` com o caminho absoluto da raiz do repositório, que contém `frontend/`, `imagens/` e `videos/`.

## Estrutura

- `controller/` lê as requisições e define rotas e métodos HTTP.
- `dto/request/` e `dto/response/` preservam os campos de formulário e o envelope JSON.
- `service/implementation/` contém regras de negócio, permissões e transações.
- `repository/` contém as consultas JDBC e o acesso ao MySQL.
- `mapper/` monta os dados retornados pelas telas; `entity/` contém os dados tipados de loja e produto.
- `config/`, `exception/` e `util/` cuidam de arquivos estáticos, erros e validações compartilhadas.

Os grupos de URL são `/api/autenticacao`, `/api/contas`, `/api/lojas`, `/api/produtos`, `/api/avaliacoes`, `/api/chats` e `/api/denuncias`. Os métodos e caminhos completos estão em `src/test/java/br/com/pucpr/technoup/RouteContractTests.java`.

## Sessão e mídia

As sessões usam o cookie `JSESSIONID`. Após a migração do servidor PHP, cada usuário precisa entrar novamente. Contas antigas com senha em texto simples são convertidas para BCrypt no primeiro login; novas senhas já são gravadas com BCrypt.

`MediaStorage` usa Apache Tika para identificar o conteúdo dos uploads, limita imagens a 5 MB, vídeos a 20 MB e cada solicitação a oito arquivos. Arquivos ficam em `imagens/` ou `videos/`; o banco continua guardando nome, caminho e tipo, sem mudança de esquema.

Execute `./gradlew test` para verificar rotas, validações, sessão, upload e publicação dos arquivos estáticos. O fluxo completo com persistência exige um MySQL com o esquema do projeto.
