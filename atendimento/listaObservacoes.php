<?php
	// Requires //
	require_once("../includes/padrao.inc.php");

	echo '<div class="container">'; 

	echo "<br><center<h1>Observações Relacionadas a atendimentos anteriores</h1></center><br><br>";


		$numero = isset($_POST["numero"]) ? $_POST["numero"] : "";


			$strSQL = "SELECT data, obs
						FROM tbatendimentoobservacoes							
								WHERE numero = '".$numero."'
									ORDER BY id desc";
	

	// Lista as conversas //
	$qryConversa = mysqli_query($conexao, $strSQL) 
		or die("Erro ao listar as Conversas: " . $strSQL . "<br/>" . mysqli_error($conexao));

		if (mysqli_num_rows($qryConversa)==0){
			echo "<br><center<h1>Nenhuma Observação Relacionada a este número</h1></center><br><br>";
		}

	while( $objConversa = mysqli_fetch_object($qryConversa) ){
		    $mensagem = $objConversa->obs;
			$datamensagem = date("d/m/Y",strtotime($objConversa->data));
			$horamensagem = date("H:i",strtotime($objConversa->data));
	  
			echo '<div class="message">					
			<div class="font-style _3DFk6 message-in tail">
				<span class="tail-container"></span>
				<span class="tail-container highlight"></span>														
				<div class="Tkt2p">';	      
 
	echo'<div class="_3zb-j ZhF0n">
						<span dir="ltr" class="selectable-text invisible-space message-text">'. str_replace("\\n","<br/>",$mensagem) .'</span>
					</div>
					<div class="_2f-RV" style="width:100%;">    
										
						<div class="_1DZAH" style="float:right;">																								    
							  <span class="msg-time">Gravada '.$datamensagem. ' as '. $horamensagem.'</span>																			
						</div>
					</div>
				</div>
				<span class="tail-container" style="margin-top:10px;margin-left:96%;width:25px;height:25px;							
				justify-content: center;color:#000;">
			

					  </span>

			</div>	
		</div>					
		';

		}

		echo '</div>';
	
	
			
		