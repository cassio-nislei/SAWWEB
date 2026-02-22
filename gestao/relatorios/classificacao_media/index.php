<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
<link rel="stylesheet" href="css/jquery-ui.min.css" />
 <link href="css/estiloinputlabel.css" rel="stylesheet">


    <div class="container-fluid">  
	<div class="row">    
		   <!-- Donut Chart -->
		   <div class="col-12">         

<!-- Card Body -->
 <div class="card shadow mb-4">
				 <div class="card-header py-3">
					 <h6 class="m-0 font-weight-bold text-primary">Classificação média por atendente</h6>
				 </div>
				 <div class="card-body" id="graficoAtendimentosPorAtendente">
				 <?php
  require_once(__DIR__ . "/../../../includes/padrao.inc.php");

                       mysqli_next_result($conexao);

                      $usuarios = mysqli_query(
                          $conexao
                          , "		SELECT tu.nome, ROUND(AVG(voto),1) AS media, COUNT(tc.id_atendimento) AS total
						  FROM tbclassificaatendimento tc
						  INNER JOIN tbusuario tu ON tc.id_usuario = tu.id
						  GROUP BY tu.id;"
                                    
                      );

                      
       

                           $totalAtendimentos = 0;
                          while($ln = mysqli_fetch_assoc($usuarios)){                   
							
                             //Altero o estilo da cor de acordo com a quantidade de atendimentos
                            $percentual = ($ln["media"] /5) * 100;
                             if ($ln["media"]>3){
                               $estilo = 'bg-success'; 
                             }else if ($percentual>2){
                                $estilo = 'bg-info';  
                             }else{
                                $estilo = 'bg-danger'; 
                             }
                             
                         
                                echo '
                                <h4 class="small font-weight-bold">'.$ln["nome"].' <span class="float-right">Total de votos: '.$ln["total"].' Nota Média:'.$ln["media"].'</span></h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar '.$estilo.'" role="progressbar" style="width: '.$percentual.'%" aria-valuenow="'.$ln["media"].'" aria-valuemin="0" aria-valuemax="5"></div>
                                    </div>
                                 ';
                            
                          }
               

                    ?>   
				
				 </div>
			 </div>         
			 </div>	
</div>	
	</div>
	<script src="js/jquery-ui.min.js" type="text/javascript"></script>
	<script src="js/jquery.mask2.js"></script>
    <script src="js/jquery.maskMoney.js"></script>

	<script>

// JavaScript Document
$( document ).ready(function() {
   	 	//Adiciona estilo nos campos tipo do Android para gerar um caption animado
	$('.form_campos').on('focus blur',
	function (e) {
	  $(this).parents('.form-group').toggleClass('focused', (e.type==='focus' || this.value !==''));
	}
	).trigger('blur');
	$('.select').on('change blur',
function (e) {
  $(this).parents('.form-group-select').toggleClass('focused', (e.type==='focus' || this.value !==''));
}
).trigger('blur');

	

	
	//Adiciona as Mascaras nos campos
	$("#emissao, #saida, #de, #ate").mask("99/99/9999");
		
	function updateAb(value){     
       $("#emissao, #saida, #de, #ate").trigger('blur');    
    }
					  
   $( "#de, #ate" ).datepicker({
        showOn: "button",
	    dateFormat: 'dd-mm-yy',
        buttonImage: "imgs/calendario.png",
        buttonImageOnly: true,
	    dateFormat: 'dd/mm/yy',
        dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'],
        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
	    onSelect: function(selected,evnt) {
         updateAb(selected);
       }
	})
   
	//Adiciono a data atual no filtro 
	//$("#de, #ate").datepicker( "setDate" , dataAtualFormatada() );

	$('.pesqEtiquetas').select2({
    placeholder: 'TAGS',
    maximumSelectionLength: 10,
    "language": "pt-BR"
  });

	
		$("#btnFiltrar").click(function() {
			var de = $("#de").val(),
			ate = $("#ate").val(),
			id_etiqueta = $("#id_etiqueta").val(),
			situacao = $("#situacao").val(),
			celular = $("#celular").val(),
			protocolo = $("#protocolo").val()  ;
		
        if (de.length != 10){
            alert("Favor Informe uma Data inicial válida");
			$("#de").focus();
			return false;
         }
		 if (ate.length != 10){
            alert("Favor Informe uma Data final válida");
			$("#de").focus();
			return false;
         }

			$("#Listar").html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');
			$.post("relatorios/atendimentos/dados.php", {de:de, ate:ate, situacao:situacao,celular:celular, protocolo:protocolo, id_etiqueta:id_etiqueta },function(retorno){
				$("#Listar").html(retorno);
			})
			//$("#Listar").load("relatorios/vendasperiodo/dados.php");
		})


		function dataAtualFormatada(){
    var data = new Date();
    var dia = data.getDate();
    if (dia.toString().length == 1)
      dia = "0"+dia;
    var mes = data.getMonth()+1;
    if (mes.toString().length == 1)
      mes = "0"+mes;
    var ano = data.getFullYear();  
    return dia+"/"+mes+"/"+ano;
}
	});		
	</script>
