<?php
/**
*	Index.php é parte do scriptDGP que é um software livre; você pode redistribui-lo e/ou 
*	modifica-lo dentro dos termos da Licença Pública Geral GNU como 
*	publicada pela Fundação do Software Livre (FSF); na versão 2 da 
*	Licença, ou (na sua opinião) qualquer versão.
*	
*	Este programa é distribuído na esperança que possa ser util, 
*	mas SEM NENHUMA GARANTIA; sem uma garantia implicita de ADEQUAÇÂO a qualquer
*	MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
*	Licença Pública Geral GNU para maiores detalhes.
*	
*	Você deve ter recebido uma cópia da Licença Pública Geral GNU
*	junto com este programa, Se não, veja <http://www.gnu.org/licenses/>.
*
*	scriptDGP Copyright (C) 2016  Jussara Ribeiro de Oliveira
*	https://github.com/darksaj/scriptDGP/
**/
header('Content-Type: text/html; charset=UTF-8');

// links.csv possui a lista de links de grupos de pesquisa a serem trabalhados
$id_arquivo = fopen('links.csv','r');
// grupos.csv é o arquivo intermediário criado com a informação dos grupos sem formatação
$fp = fopen("grupos.csv", "a+");

//aumenta o tempo de execução
set_time_limit (1200);

$conteudo_grp="";
while(!feof($id_arquivo)) {
    
	// lê uma linha do arquivo
    $linha = fgets($id_arquivo, 4096);	

	//pega o conteudo do arquivo
	if ($linha!=""){
		$url_link = str_ireplace("\n","",$linha);
		//verificar se as quebras de linha do arquivo são todas tipo linux com apenas \n no final
		$url = file_get_contents($url_link );
		
		echo $url_link."<br>";
		
		//atualiza o endereço dos arquivos incluidos
		$url=str_ireplace("\"/dgp/","\"http://dgp.cnpq.br/dgp/",$url);
		
		//print $url;
	  		 
		
		//transforma quebras de linha e tabulações em espaços
		$conteudo = str_ireplace("\r\n"," ",
						str_ireplace("\r\n"," ",
							str_ireplace("\n"," ",
								str_ireplace("\r"," ",
									str_ireplace("\t"," ",
										/*preg_replace("/<.*?>/", ";", $url)*/
										strip_tags($url)
									)
								)
							)
						)
					);
		
		//tira codigo javascript 
		
		$conteudo = preg_replace("((PrimeFaces).*?(;))", "  ", $conteudo);
		$conteudo = preg_replace("((PrimeFaces).*?(;))", "  ", $conteudo);
		
	//verifica se o grupo existe
		if (stristr($conteudo,"Grupo de pesquisa não encontrado.")){
				echo "Grupo de pesquisa não encontrado.";
				break;
		}
		
		$conteudo = str_ireplace("\$(function(){","  ", $conteudo);
		$conteudo = str_ireplace("});","  ", $conteudo);
		$conteudo = str_ireplace("ui-button","  ", $conteudo);
		$conteudo = preg_replace("((HintsPaginacaoDatatables).*?(;))", "  ", $conteudo);
		$conteudo = str_ireplace("}","  ", $conteudo);
		$conteudo = str_ireplace("Endereço para acessar este espelho:","  ", $conteudo);
		$conteudo = str_ireplace("Visualizar espelho da linha de pesquisa","  ", $conteudo);
		$conteudo = preg_replace("((Imprimir).*?(correcao))", "  ", $conteudo);
		$conteudo = preg_replace("(((Equipamentos)( )(e)( )(Softwares)( )(Relevantes)).*?(correcao))", "  ", $conteudo);
		
		
		//tira espaços repetidos		
		while(substr_count($conteudo,"   ")>0){
			$conteudo = str_ireplace("   ","  ",$conteudo);
		}

		//tira informações de botões
		$conteudo = str_ireplace("Visualizar Currículo Lattes  Visualizar espelho do estudante","  ", $conteudo);
		$conteudo = str_ireplace("Visualizar Currículo Lattes  Visualizar espelho do pesquisador","  ", $conteudo);
		$conteudo = str_ireplace("Visualizar Currículo Lattes  Visualizar espelho do técnico","  ", $conteudo);
		$conteudo = str_ireplace("Visualizar espelho da instituição parceira","  ", $conteudo);
		$conteudo = str_ireplace("*Email do remetente  *Assunto  *Descrição  Enviar  Limpar","  ", $conteudo);
		//$conteudo = str_ireplace("Imprimir     function correcaoHintsPaginacaoDatatables(){  inseriHintPaginacaoDatatables(\"Primeira página\",  \"Última página\",  \"Página anterior\",  \"Próxima página\");","  ", $conteudo);
		
		$conteudo_grp.=$conteudo."\n";
	}
	
}

$escreve = fwrite($fp, $conteudo_grp);
 
// fecha o arquivo e libera recursos da memória
fclose($id_arquivo);

// Fecha o arquivo
fclose($fp); 

?>