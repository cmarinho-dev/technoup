*** Settings ***
Documentation     Suíte de testes de interface do TechnoUP baseada nos cenários do RA2.
...               Os testes acessam o site publicado localmente e não criam registros.
Library           SeleniumLibrary    timeout=10s    implicit_wait=0s
Suite Setup       Abrir navegador
Suite Teardown    Close All Browsers
Test Teardown     Go To    about:blank

*** Variables ***
${BASE_URL}       http://localhost/technoup/frontend
${BROWSER}        chrome

*** Test Cases ***
01 Home exibe chamada principal e atalhos
    Go To    ${BASE_URL}/home.html
    Wait Until Page Contains Element    css=main h1
    Page Should Contain    Monte, compare e descubra os destaques da TechnoUp.
    Page Should Contain Element    css=a[href="./catalogo.html"]

02 Home abre catálogo pelo botão de destaque
    Go To    ${BASE_URL}/home.html
    Click Element    xpath=//a[contains(., 'Ver catálogo')]
    Location Should Contain    /catalogo.html
    Page Should Contain    Catálogo de Produtos

03 Navegação principal abre a página Sobre
    Go To    ${BASE_URL}/home.html
    Click Element    xpath=//nav//a[contains(., 'Sobre')]
    Location Should Contain    /sobre.html
    Page Should Contain    Sobre a TechnoUp

04 Página Sobre apresenta perfis de usuário
    Go To    ${BASE_URL}/sobre.html
    Page Should Contain    Tipos de Usuários
    Page Should Contain    Comprador
    Page Should Contain    Administrador

05 Página Sobre apresenta os recursos da plataforma
    Go To    ${BASE_URL}/sobre.html
    Page Should Contain    Multi-Lojas
    Page Should Contain    Produtos Variados
    Page Should Contain    Seguro

06 Navegação principal abre avaliação de item
    Go To    ${BASE_URL}/home.html
    Click Element    xpath=//nav//a[contains(., 'Venda')]
    Location Should Contain    /formulario.html
    Page Should Contain    Avaliação de Item

07 Catálogo apresenta filtros e contador
    Go To    ${BASE_URL}/catalogo.html
    Wait Until Page Contains Element    id=catalogo_contador
    Page Should Contain Element    id=filtraNome
    Page Should Contain Element    id=filtraTipo
    Page Should Contain Element    id=filtroPrecoMin
    Page Should Contain Element    id=filtroPrecoMax
    Wait Until Element Does Not Contain    id=catalogo_contador    Carregando

08 Busca por nome inexistente mostra lista vazia
    Abrir catálogo
    Input Text    id=filtraNome    item-que-nao-existe-xyz
    Wait Until Page Contains    Nenhum produto encontrado
    Element Should Contain    id=catalogo_contador    0 produto(s) encontrado(s)

09 Preço mínimo acima dos produtos mostra lista vazia
    Abrir catálogo
    Input Text    id=filtroPrecoMin    999999999
    Wait Until Page Contains    Nenhum produto encontrado
    Element Should Contain    id=catalogo_contador    0 produto(s) encontrado(s)

10 Preço máximo zero mostra lista vazia
    Abrir catálogo
    Input Text    id=filtroPrecoMax    0
    Wait Until Page Contains    Nenhum produto encontrado
    Element Should Contain    id=catalogo_contador    0 produto(s) encontrado(s)

11 Limpar busca restaura a contagem original do catálogo
    Abrir catálogo
    ${contagem_inicial}=    Get Text    id=catalogo_contador
    Input Text    id=filtraNome    item-que-nao-existe-xyz
    Wait Until Page Contains    Nenhum produto encontrado
    Clear Element Text    id=filtraNome
    Wait Until Element Contains    id=catalogo_contador    produto(s) encontrado(s)
    ${contagem_restaurada}=    Get Text    id=catalogo_contador
    Should Be Equal    ${contagem_restaurada}    ${contagem_inicial}

12 Login vazio exibe validação sem enviar formulário
    Go To    ${BASE_URL}/login.html
    Click Element    id=btnEntrar
    Element Should Contain    id=mensagemErro    Preencha o email e a senha.

13 Login com senha ausente exibe validação
    Go To    ${BASE_URL}/login.html
    Input Text    id=inputEmail    pessoa@example.com
    Click Element    id=btnEntrar
    Element Should Contain    id=mensagemErro    Preencha o email e a senha.

14 Login com email sem arroba exibe validação
    Go To    ${BASE_URL}/login.html
    Input Text    id=inputEmail    email-invalido
    Input Text    id=inputSenha    senha-teste
    Click Element    id=btnEntrar
    Element Should Contain    id=mensagemErro    Digite um email válido.

15 Login oferece navegação para criar conta
    Go To    ${BASE_URL}/login.html
    Click Element    xpath=//a[contains(., 'Crie uma conta')]
    Location Should Contain    /registro.html
    Page Should Contain    Crie seu cadastro

16 Registro vazio apresenta mensagem de campos obrigatórios
    Go To    ${BASE_URL}/registro.html
    Click Element    id=btnCriarConta
    Element Should Contain    id=mensagemErro    Preencha todos os campos obrigatórios.

17 Registro rejeita nome abaixo do mínimo
    Go To    ${BASE_URL}/registro.html
    Input Text    id=inputNome    Al
    Input Text    id=inputCpf    39053344705
    Input Text    id=inputEmail    pessoa@example.com
    Input Text    id=inputSenha    senha123
    Input Text    id=inputConfirmarSenha    senha123
    Click Element    id=btnCriarConta
    Element Should Contain    id=mensagemErro    Informe seu nome completo.

18 Registro rejeita CPF inválido
    Go To    ${BASE_URL}/registro.html
    Input Text    id=inputNome    Ana Silva
    Input Text    id=inputCpf    11111111111
    Input Text    id=inputEmail    pessoa@example.com
    Input Text    id=inputSenha    senha123
    Input Text    id=inputConfirmarSenha    senha123
    Click Element    id=btnCriarConta
    Element Should Contain    id=mensagemErro    Digite um CPF válido.

19 Registro formata CPF durante a digitação
    Go To    ${BASE_URL}/registro.html
    Input Text    id=inputCpf    39053344705
    Textfield Value Should Be    id=inputCpf    390.533.447-05

20 Registro indica divergência entre senhas
    Go To    ${BASE_URL}/registro.html
    Input Text    id=inputNome    Ana Silva
    Input Text    id=inputCpf    39053344705
    Input Text    id=inputEmail    pessoa@example.com
    Input Text    id=inputSenha    senha123
    Input Text    id=inputConfirmarSenha    senha456
    Click Element    id=btnCriarConta
    Element Should Be Visible    id=erroSenha
    Element Should Contain    id=erroSenha    As senhas não coincidem.

*** Keywords ***
Abrir navegador
    Open Browser    about:blank    ${BROWSER}
    Set Window Size    1440    1000

Abrir catálogo
    Go To    ${BASE_URL}/catalogo.html
    Wait Until Page Contains Element    id=catalogo_contador
    Wait Until Element Does Not Contain    id=catalogo_contador    Carregando
