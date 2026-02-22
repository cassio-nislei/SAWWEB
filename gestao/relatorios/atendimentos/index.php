<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
<link rel="stylesheet" href="css/jquery-ui.min.css" />
 <link href="css/estiloinputlabel.css" rel="stylesheet">


    <div class="container-fluid">  
			<div class="panel panel-default">
				<div class="panel-heading"><b>Opções de Filtro</b><br />
				  <div class="row">
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12">
						<div class='form-group'>
						   <label class='control-label' for='inputNormal'>De</label>
						   <input type="text" name="de" id="de" class="form_campos" value="<?php echo date("d/m/Y");?>"> 
						 </div> 
					</div> 
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12">
						 <div class='form-group'>
						   <label class='control-label' for='inputNormal'>Até</label>
						   <input type="text" name="ate" id="ate" class="form_campos" value="<?php echo date("d/m/Y");?>"> 
						 </div> 
					</div>
                   </div> <!-- Fim da primeira Linha -->

				   <div class="row">

				   <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12">
						 <div class='form-group-select' style="width:100%">
						 <label class='control-label'>Situação</label>
                             <select class="select form_campos" id="situacao" name="situacao" style="width:100%">
							     <option value="0">TODAS</option>
							     <option value="A">EM ATENDIMENTO</option>
							     <option value="F">FINALIZADOS</option>
						   </select> 
						 </div> 
					</div> 

					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12">
						 <div class='form-group'>
						 <select class="js-example-basic-multiple pesqEtiquetas" name="id_etiqueta[]" multiple="multiple" id="id_etiqueta" style="width:90%">
							<?php
						//Crio a lista de etiquetas e defino as cores a exibir
							$query = mysqli_query($conexao, "SELECT * FROM tbetiquetas");                       
							while ($ListarEtiquetas = mysqli_fetch_array($query)){       
							echo  '<option value="'.$ListarEtiquetas["id"].'" data-color="'.$ListarEtiquetas["cor"].'" >'.$ListarEtiquetas["descricao"].'</otpion>';                     
							}
							?>
						</select>
						 </div> 
					</div>
					</div> <!-- Fim da primeira Linha -->


					<div class="row">
						<div class="col-md-6">
							<div class='form-group'>
							<label class='control-label' for='inputNormal' id="nome_razao">Nº de Protocolo</label>
							<input type="text" name="protocolo" id="protocolo" size="46" class="form_campos" />
							</div>         
						</div>  		  
						<div class="col-md-6">
							<div class='form-group'>
							<label class='control-label' for='inputNormal' id="fantasia_apelido">Celular</label>
							<input type="text" name="celular" id="celular" size="46" class="form_campos"/>				   	
							</div>         
						</div>
		
					</div>	  


					<div class="row">
					<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 col-12" >
				      <div class='form-group'>
						<button class="btn btn-primary btn-icon-split" style="outline: none; align-self: flex-end !important;" id="btnFiltrar">
					   <span class="icon text-white-50">
						  <i class="fas fa-search fa-sm"></i>
					   </span>
					   <span class="text">Filtrar</span>
				   </button>
					  </div> 
				  </div> 
				</div>



				<div class="panel-body" id="Listar">
				
				   <!-- Aqui Lista os dados do Relatorio -->
				
				
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