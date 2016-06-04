<?php
/**
*	cria_csv.php é parte do scriptDGP que é um software livre; você pode redistribui-lo e/ou 
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
//arquivo que delimita quais são as colunas existentes
$f = fopen("colunas.csv", "r");
//arquivo que vai estar já processado
$fd = fopen("dados_ok.csv", "w");
//lista de pesquisadores 
$fp = fopen("pesquisadores_ok.csv", "w");

	$valor_fp = "Tipo|Lider|Nome|Título|Data|Grupo|Situação\n";
	fwrite($fp,$valor_fp);
	// Lê cada uma das linhas do arquivo
	$valor_fd="";
	while(!feof($f)) { 
		
	    $linha= str_ireplace(")","\\)",
					str_ireplace("(","\\(",
						str_ireplace(".","\\.",
							str_ireplace("\r\n","",
										explode("|",fgets($f))
										)
									)
								)
							);
		

		//cria vetor com instrução de pesquisa para cada coluna 
		$vetorregex[$linha[0]]="((".$linha[1].").*?(".$linha[2]."))";
		
		//cria cabeçalho
		$valor_fd.=$linha[0]."$";
	}
	

	fwrite($fd, $valor_fd.="\n");
	fclose($f);
	
	//abre arquivo intermediário
	$f_g = fopen("grupos.csv", "r");
	
	// Lê cada uma das linhas do arquivo
	while(!feof($f_g)) { 
		$tipo_grupo = "";
		 $var_linha = fgets($f_g);
		if (trim($var_linha)!=""){
			//transforma em um vetor
			foreach($vetorregex as $regex_campo => $regex_valor){
				if ($vetorregex[$regex_campo]!=""){
					
					//busca cada valor de coluna de acordo com os dados do arquivo de colunas
					$achou = preg_match($vetorregex[$regex_campo],$var_linha,$matches, PREG_OFFSET_CAPTURE);
					$inicio_fim = str_ireplace("))",")",str_ireplace("((","(",explode(".*?",$vetorregex[$regex_campo])));

					//para debugar: mostra a expressão de pesquisa
					//echo "!!!!".$inicio_fim[0]."!!!";
					 
					if ( !$achou  and ($inicio_fim[0]=="(Unidade:)" or $inicio_fim[0]=="(Repercussões dos trabalhos do grupo)" or $inicio_fim[0]=="(Website/Blog)"  or $inicio_fim[0]=="(Linhas de pesquisa  Nome da linha de pesquisaQuantidade de EstudantesQuantidade de PesquisadoresAções)")){
						//para o caso de nao existir unidade, repercussão,Website ou Linhas de pesquisa
						$valor_fd="";
						
					}else if (!$achou and $inicio_fim[0]=="(Instituição do grupo:)"){
						//caso nao tenha unidade eh preciso mudar a expressão de busca para instituição
						$novo_regex = str_ireplace("Unidade:","Contato",$vetorregex[$regex_campo]);
						$achou = preg_match($novo_regex,$var_linha,$matches, PREG_OFFSET_CAPTURE);
						$inicio_fim = str_ireplace("))",")",str_ireplace("((","(",explode(".*?",$novo_regex)));
						$valor_fd=preg_replace($inicio_fim[1],"",preg_replace($inicio_fim[0],"",$matches[0][0]));
						
					}else if (!$achou and $inicio_fim[0]=="(Website:)"){
						//caso nao tenha repercussão eh preciso mudar a expressão de busca para website
						$novo_regex = str_ireplace("Repercuss","Linhas",$vetorregex[$regex_campo]);
						$achou = preg_match($novo_regex,$var_linha,$matches, PREG_OFFSET_CAPTURE);
						$inicio_fim = str_ireplace("))",")",str_ireplace("((","(",explode(".*?",$novo_regex)));
						$valor_fd=preg_replace($inicio_fim[1],"",preg_replace($inicio_fim[0],"",$matches[0][0]));
						
					}else if (!$achou and $inicio_fim[0]=="(Rede de pesquisa:)"){
						//caso nao tenha linha de pesquisa eh preciso mudar a expressão de busca para rede de pesquisa
						$novo_regex = str_ireplace("Linhas de pesquisa  Nome da linha de pesquisaQuantidade de EstudantesQuantidade de PesquisadoresAções","Recursos humanos",$vetorregex[$regex_campo]);
						$achou = preg_match($novo_regex,$var_linha,$matches, PREG_OFFSET_CAPTURE);
						$inicio_fim = str_ireplace("))",")",str_ireplace("((","(",explode(".*?",$novo_regex)));
						$valor_fd=preg_replace($inicio_fim[1],"",preg_replace($inicio_fim[0],"",$matches[0][0]));

					}else{
						// use para debugar em caso de erro: Notice: Undefined offset: 0 in C:\wamp\www\projeto\cria_csv.php on line xx, pode ser usado nas condicionais anteriores
						//echo $achou."--".$inicio_fim[0]."---".$matches[0][0]."----".$inicio_fim[1].":".$valor_fd."<br>";							
					
						$valor_fd=preg_replace($inicio_fim[1],"",preg_replace($inicio_fim[0],"",$matches[0][0]));		
					}

					switch ($regex_campo){
						case "Período de formação":
							$fim = substr(trim($valor_fd),0,3)."9";
							$valor_fd = substr(trim($valor_fd),0,3)."0-".$fim;
							if ($fim > date ("Y"))
							$valor_fd = str_ireplace($fim,date ("Y"),$valor_fd);
						break;
						case "Endereço":
							echo $end=$valor_fd;
						break;
						case "Situação do grupo":
							 $sit=$valor_fd;
						break;
						case "Líder\(es\) do grupo":
						
							$valor_fd=str_replace("Permite enviar email|","",str_ireplace("  ","|",$valor_fd));
							$vetor_lideres=explode("|",$valor_fd);
							//retira os valores vazios
							$remover = array("");
							$vetor_lideres = array_diff($vetor_lideres, $remover);

						break;
						case "Área predominante":
							//$valor_fd=str_ireplace("; ","|",$valor_fd);
							$area = explode("; ",$valor_fd);
							$valor_fd=$area[0];
							$valor_fd=trim($valor_fd);
						break;
						case "Sub-Area":
							//$valor_fd=str_ireplace("; ","|",$valor_fd);
							$area = explode("; ",$valor_fd);
							$valor_fd=$area[1];
							$valor_fd=trim($valor_fd);
						break;
						case "Pesquisadores":
						case "Estudantes":
						case "Técnicos":
							
							//formata delimitadores
							$valor_fd=str_ireplace("Não Informada","||Não Informada",$valor_fd);
							$valor_fd=preg_replace("(\d+\/\d+\/\d+)","||$0",$valor_fd);
							$valor_fd=str_ireplace("Graduação","|Graduação|",$valor_fd);
							$valor_fd=str_replace("MBA","|MBA|",$valor_fd);
							$valor_fd=str_ireplace("Mestrado","|Mestrado|",$valor_fd);
							$valor_fd=str_ireplace("|Mestrado| Profissional","|Mestrado Profissional|",$valor_fd);
							$valor_fd=str_ireplace("Doutorado","|Doutorado|",$valor_fd);
							$valor_fd=str_ireplace("Especialização","|Especialização|",$valor_fd);
							$valor_fd=str_ireplace("|Especialização| - Residência médica","|Especialização - Residência médica|",$valor_fd);
							$valor_fd=str_ireplace("Ensino Profissional de nível técnico","|Ensino Profissional de nível técnico|",$valor_fd);
							$valor_fd=str_ireplace("Extensão universitária","|Extensão universitária|",$valor_fd);
							$valor_fd=str_ireplace("Ensino Fundamental (1o grau)","|Ensino Fundamental (1o grau)|",$valor_fd);
							$valor_fd=str_ireplace("Ensino Médio (2o grau)","|Ensino Médio (2o grau)|",$valor_fd);
							$valor_fd=str_ireplace("|||","|",$valor_fd);
							$valor_fd=str_ireplace("Não Informada","Não Informada#",$valor_fd);
							$valor_fd=str_ireplace("Nenhum registro adicionado","Nenhum registro adicionado||#",$valor_fd);
							$valor_fd=preg_replace("/[0-9]\s/","$0#",$valor_fd);
							$valor_fd=str_ireplace("##","#",$valor_fd);
							$valor_fd=preg_replace("(\s+)"," ",$valor_fd);
							
							//valores para tabela de pesquisadores
							$valor_fp=$regex_campo."|".str_ireplace("#","#".$regex_campo."|",trim($valor_fd));
							$valor_fp=preg_replace("(((".$regex_campo.").{1})($))","",trim($valor_fp));
							//acrescenta tipo de lider
							$valor_fp=str_ireplace($regex_campo."|",$regex_campo."|N|",$valor_fp);
							
							$valor_fp=str_ireplace("| ","|",$valor_fp);
							//acrecenta url do grupo
							$valor_fp=str_ireplace("#","|".trim($end)."|".trim($sit)."\n",$valor_fp);
							
							//Marca dos lideres e vice-lideres do grupo
							if ($regex_campo=="Pesquisadores"){
								//print_r($vetor_lideres);
								foreach($vetor_lideres as  $key=> $lider){
										if ($key==1)
											$tipo = "L";
										else
											$tipo = "V";
										$valor_fp=str_ireplace("|N|".$lider,"|".$tipo."|".$lider,$valor_fp);
									
								}
							}
							//grava no arquivo de pesquisadores
							if (!strpos($valor_fp,"Nenhum registro adicionado"))
								fwrite($fp,$valor_fp);
						break;
						case "Linhas de Pesquisa":
						// verifica se nao tem linhas de pesquisa 
							if($achou){
								$valor_fd=preg_replace($inicio_fim[1],"",preg_replace($inicio_fim[0],"",$matches[0][0]));
								$valor_fd=preg_replace("/[0-9]\s/","$0#",$valor_fd);
								$valor_fd=preg_replace("/[0-9]/","|$0",$valor_fd);
								$valor_fd=str_ireplace(" # ","#",$valor_fd);
							}else
								$valor_fd="";
						break;
						case "Indicadores de recursos humanos":
							$valor_fd=preg_replace($inicio_fim[1],"",preg_replace($inicio_fim[0],"",$matches[0][0]));
							$valor_fd=preg_replace("/[0-9]/","|$0",$valor_fd);
							$valor_fd=preg_replace("/[MEG]/","#$0",$valor_fd);
							$valor_fd=str_ireplace("Ensino #Médio","Ensino Médio",$valor_fd);
						break;
						case "Rede de pesquisa":
							$vet = explode(" http",$valor_fd);
							$valor_fd= $vet[0];
						break;
						case "Pesquisadores Egressos":
						case "Estudantes Egressos":
							$valor_fd= str_ireplace("Nenhum registro adicionado","Nenhum registro adicionado||#",$valor_fd);
							$valor_fd= str_ireplace("De Não informada","||De Não informada",$valor_fd);
							//divide os campos após as datas
							$valor_fd=preg_replace("/[0-9]\s/","$0#",$valor_fd);
							//divide os campos antes das datas
							$valor_fd=preg_replace("/(De)\s[0-9][0-9]/","||$0",$valor_fd);
							$valor_fd=str_ireplace(" #a "," a ",$valor_fd);
							
							
							//Formata para arquivo de pesquisadores
							$valor_fp=$regex_campo."|".str_ireplace("#","#".$regex_campo."|",trim($valor_fd));
							$valor_fp=preg_replace("(((".$regex_campo.").{1})($))","",trim($valor_fp));
					
							//acrescenta nao lider
							$valor_fp=str_ireplace($regex_campo."|",$regex_campo."|N|",$valor_fp);
							$valor_fp=str_ireplace("| ","|",$valor_fp);
							//acrecenta url do grupo
							$valor_fp=str_ireplace("#","|".trim($end)."|".trim($sit)."\n",$valor_fp);
							
							//Marca dos lideres e vice-lideres do grupo
							if ($regex_campo=="Pesquisadores Egressos"){
								//print_r($vetor_lideres);
								foreach($vetor_lideres as  $key=> $lider){
										if ($key==1)
											$tipo = "L";
										else
											$tipo = "V";
										$valor_fp=str_ireplace("|N|".$lider,"|".$tipo."|".$lider,$valor_fp);
									
								}
							}
							
							//Bug: caso tenham preenchido o nome do pesquisador também como pesquisador egresso ele vai registrar a liderança duas vezes
							
							if (!strpos($valor_fp,"Nenhum registro adicionado"))
								fwrite($fp,$valor_fp);
						break;
						case "Colaboradores estrangeiros":
							$valor_fd= str_ireplace("Visualizar Currículo Lattes  Visualizar espelho do colaborador estrangeiro","#",$valor_fd);
							$valor_fd= str_ireplace("Nenhum registro adicionado","Nenhum registro adicionado||#",$valor_fd);
							
							$valor_fd=preg_replace("(\d+\/\d+\/\d+)","||$0",$valor_fd);
							
							$valor_fd=preg_replace("([A-Z][A-Z]+)","",$valor_fd);
							//Formata para arquivo de pesquisadores
							$valor_fp=$regex_campo."|".str_ireplace("#","#".$regex_campo."|",trim($valor_fd));
							$valor_fp=preg_replace("(((".$regex_campo.").{1})($))","",trim($valor_fp));
							//acrescenta nao lider
							$valor_fp=str_ireplace($regex_campo."|",$regex_campo."|N|",$valor_fp);
							$valor_fp=str_ireplace("  "," ",$valor_fp);
							$valor_fp=str_ireplace("| ","|",$valor_fp);
							//acrecenta url do grupo
							$valor_fp=str_ireplace("#","|".trim($end)."|".trim($sit)."\n",$valor_fp);
							
							if (!strpos($valor_fp,"Nenhum registro adicionado"))
								fwrite($fp,$valor_fp);
						break;
							$vet = explode(" http",$valor_fd);
							$valor_fd= $vet[0];
						break;
						default:
						break;
					}
					//echo $valor_fd;
					fwrite($fd, trim($valor_fd)."$");
					
					
				}
			}
			fwrite($fd, $tipo_grupo."\n");
		}
	}
	fclose($fd); 
	fclose($fp); 
	fclose($f_g);
?>