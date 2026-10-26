# Testes web do TechnoUP com Robot Framework

A suíte contém 20 testes funcionais de interface baseados nos cenários de login e catálogo da planilha do RA2 e nos fluxos de navegação e registro existentes no frontend. Usa SeleniumLibrary para abrir páginas reais no Chrome. Os testes evitam criar contas ou enviar avaliações.

## Pré-requisitos

- Apache e MySQL ativos, banco TechnoUP criado e projeto publicado em `http://localhost/technoup`, conforme o README principal.
- Python 3.10+ e Google Chrome instalado.

Na pasta `tests/robot`, instale as dependências:

```powershell
py -m pip install -r requirements.txt
```

Execute a suíte a partir da pasta `tests/robot`:

```powershell
robot --outputdir resultados technoup_web.robot
```

Os artefatos `report.html`, `log.html` e `output.xml` são gerados na pasta `resultados`.

Se a instalação local usar outra URL, ajuste `${BASE_URL}` no arquivo `.robot` antes de executar. A página do catálogo consulta a API e, portanto, requer o servidor e o banco ativos.
