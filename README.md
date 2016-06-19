# scriptDGP v 1.0

README
Script para baixar dados dos grupos do Diretório de Grupos de Pesquisa do CNPQ <http://lattes.cnpq.br/web/dgp>
Este script foi escrito e testado em php v5.5.12 e todos arquivos utilizam a codificação UTF-8. E atualmente conta com os seguintes arquivos:

<index.php>
Arquivo php responsável por fazer o download dos dados dos grupos de pesquisa, tem como requisito o aquivo <links.csv> preenchido. Ao ser executado cria o arquivo <grupos.csv> que contém o conteúdo de cada link sem tags html e javascript

<cria_csv.php>
Arquivo php responsável por formatar os dados presentes no arquivo <grupos.csv>, este usa como base o arquivo <colunas.csv> que traz referências de como encontrar os dados dos grupos que foram gravados. Através de expressões regulares e buscas de string ele encontra e formata os dados de cada um dos campos presentes nos links fornecidos. Criando assim os arquivos: <dados_ok.csv> com os dados de cada diretório que utiliza $ como delimitador (pois o | pode ser usado nos dados dos grupos) e tem como colunas aquelas especificadas no arquivo <colunas.csv> e <pesquisadores_ok.csv> com os dados de participantes do grupo que utiliza | como delimitador que possui os dados referentes aos participantes do grupo e replicam algumas informações do grupo também, as colunas atualmente estão cravadas no código

<colunas.csv>
Arquivo CSV que possui as referências que serão usadas pelas expressões regulares para encontrar o valor de cada campo dos links. Este se faz necessário pois os dados gravados em <grupos.csv> não trazem nenhuma delimitação clara dos campos, devido ao uso do comando strip_tags.
Atualmente ele possui o seguinte formato:
Nome do campo|Referência inicial|Referência Final
Além dos campos existentes nos links dos diretórios foram acrescentados os seguintes campos: Período de formação (com a década referente a criação dos grupos) e Sub-Area (que traz separadamente qual a sub-área de cada área)

<links-exemplo.csv>
Possui um exemplo de lista de links de grupos de pesquisa

INSTALL
Basta descompactar a pasta do projeto em um diretório www do PHP, nenhuma biblioteca adicional foi instalada

CONFIG
Criar o arquivo <links.csv> (atualmente ele precisa ter quebra de linha tipo windows e possuir uma linha vazia no final do arquivo), pode ser usado como base o arquivo <links-exemplo.csv> com a lista de links a serem baixados, conforme exemplo:
http://dgp.cnpq.br/dgp/espelhogrupo/0030069073641405
http://dgp.cnpq.br/dgp/espelhogrupo/0031120984394992
http://dgp.cnpq.br/dgp/espelhogrupo/0049772352586301

RUN
1- Executar o arquivo index.php no navegador - o arquivo <grupos.csv> será criado
2- Caso não ocorra nenhum problema, executar o arquivo  <cria_csv.php> no navegador - os arquivos <dados_ok.csv> e <pesquisadores_ok.csv> serão criados

LICENSE
Utiliza GNU GPL 3.0

AUTHORS
Jussara Ribeiro de Oliveira <darksaj@gmail.com>

BUGS
v 1.0
- Ao identificar um Lider ou Vice Lider se esse estiver cadastrado duas vezes na lista de participantes (em pesquisadores e pesquisadores egressos por exemplo) ele identifica as duas ocorrências com a liderança, podendo ocasionar problemas depois na geração de gráficos que necessitem desses dados
- O arquivo links.csv precisa estar com quebra de linha do windows (\r\n) e uma linha vazia no final
- Alguns dados não são processados como os "indicadores de recursos humanos" e outros mais ao final dos links
- Não é exatamente um bug mas o código precisa ser revisado pois tem muitas verificações redundantes, talvez as expressões regulares possam ser otimizadas e alguns condicionais do arquivo <cria_csv.php> precisam ser enxugadas. Além de melhorar o tratamento de erros.

ChangeLog
04/06/2016 Criação da versão 1.0 do projeto no Github
