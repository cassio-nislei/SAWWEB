<!-- Importação de Scripts -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.js"></script>
<script src="js/jquery.form.js"></script>

<!-- Importação de Estilos -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.13.18/jquery.timepicker.min.css">
<link href="css/estiloinputlabel.css" rel="stylesheet">
<script>
$(document).ready(function() {
    // Quando houver uma mudança no seletor 'canal'
    $("#canal").on("change", function() {
        // Atualiza o valor do campo 'acao' com o valor selecionado do 'canal'
        var canalSelecionado = $(this).val();
        $("#menu_canal").val(canalSelecionado);
    });
});
</script>

<script>
$( document ).ready(function() {	
	$('#Listar').load("cadastros/agendamentos/listar.php");


	$('.form_campos').on('focus blur',
function (e) {
  $(this).parents('.form-group').toggleClass('focused', (e.type==='focus' || this.value.length > 0));
}
).trigger('blur');
 $('.select').on('change blur',
function (e) {
  $(this).parents('.form-group-select').toggleClass('focused', (e.type==='focus' || this.value !==''));
}
).trigger('blur');
	
	});
 /* $(function() {
    // Inicializa o seletor de data usando jQuery UI Datepicker
    $("#seletorData").datepicker({
      dateFormat: "yy-mm-dd", // Formato da data
      onSelect: function(dateText) {
        // Quando uma data é selecionada, redireciona para a página PHP com a data como parâmetro na URL
     //   window.location.href = "processar_data.php?data=" + dateText;
      }
    });
  });
	/*$(function() {
    // Inicializa o seletor de hora usando jQuery UI Timepicker
    $("#seletorHora").timepicker({
      timeFormat: 'hh:mm', // Formato da hora
       interval: 15, // Intervalo entre as opções de hora
      scrollbar: true, // Adiciona uma barra de rolagem para seleção
    });
  });*/
  
  $(function atualizarAcao() {
    var canalSelecionado = $("#canal").val();
    $("#menu_canal").val(canalSelecionado);
     });  
 
</script>


<form method="post" action="cadastros/agendamentos/salvar.php" name="gravarAgendamentos" id="gravarAgendamentos" enctype="multipart/form-data">
<input type="hidden" value="0" name="acao" id="acao" />  
<input type="hidden" id="menu_canal" name="menu_canal" value="0">  
<input type="hidden" name="id" id="id" value="0">
<div class="container" id="FormAgendamento">
 <div class="panel panel-default">
	<div class="panel-heading"><b>Adicionar Novo Agendamentos de Mensagens</b></div>
  <div class="panel-body">

   <div class="row">
	  <div class="col-8">
		  <div class="form-group">
			<div class='form-group-select' style="width:100%">
         <input type="date" id="seletorData" name="seletorData" placeholder="Selecione uma data">
         <input type="time" id="seletorHora" name="seletorHora" placeholder="Selecione uma hora">
		  	 <input type="number" id="qtdemsg" name="qtdemsg" maxlength="4" pattern="\d{1,4}" title="Quantidade de msgs" placeholder="Qtdes Msgs" required>
				<div id="valida_categoria" style="display: none" class="msgValida">
					Por favor, informe o tipo do Produto .
				</div> 
			</div>  
		  </div> 
	    </div>
		<div class="col-4">
		  <div class="form-group">
			<div class='form-group-select' style="width:100%">
				<label class='control-label'>Canal</label>
				<select class="select form_campos" id="canal" name="canal" style="width:100%">
        <option value="0">Escolha um Canal</option>
				    <option value="1">1º Numero</option>
					<option value="2">2º Numero</option>
					<option value="3">3º Numero</option>
					<option value="4">4º Numero</option>
					<option value="5">5º Numero</option>
					<option value="6">6º Numero</option>
					<option value="7">7º Numero</option>
					<option value="8">8º Numero</option>
					<option value="9">9º Numero</option>
					<option value="10">10º Numero</option>
					<option value="11">11º Numero</option>					
				</select> 

      	<div id="valida_categoria" style="display: none" class="msgValida">
					Por favor, informe o tipo do Canal .
				</div> 
			</div>  
		  </div> 
	   </div>
	</div>


    <div class="form-group">
       <label class='control-label' for='inputNormal'>Descrição do Titulo*</label>
      <input type="text" id="titulo" name="titulo" class="form_campos">   
      <div id="valida_menu" style="display: none" class="msgValida">
        Por favor, informe um titulo.
      </div>  
    </div>  
		<div class="form-group">
       <label class='control-label' for='inputNormal'>Mensagem*</label>
      <input type="text" id="msgs" name="msgs" class="form_campos">   
      <div id="valida_menu" style="display: none" class="msgValida">
        Por favor, informe a mensagem.
      </div>  
    </div>  
	
	<div class="form-group">
            <h2 class="title" id="arquivo_carregado"></h2>						
	</div>
	<div class="form-group">
		<label for="exampleFormControlFile1">Arquivo de envio Automático</label>
		<input type="file" class="form-control-file" id="anexoa" name="anexoa">
   
   </div>
   
  
  
   <br />
    <div class="form-group">   
        <input type="reset" value="Cancelar" id="btnCancelar" class="btn btn-danger ml-auto" style="visibility:hidden;">&nbsp;&nbsp;
				<input type="submit" value="Gravar" id="btnGravar" class="btn btn-primary">
   
  	 </div>
	 </div>
	 </div>
	  </div>
</form> 
  
   
   <div class="container">
			<div class="panel panel-default">
				<div class="panel-heading"><b>Agendamento Cadastrado</b></div>
				<div class="panel-body" id="Listar">
				
				<!-- Aqui Lista os departamentos Cadastrados -->
				
				
				</div>
	  </div>
	</div>
  <script type='text/javascript' src="js/funcoes.js"></script>
  <script>
$( document ).ready(function() {	
   //Botão Cancelar Ao CLicar deve ser oculto
		$("#btnCancelar").click(function(){
			$("#acao").val(0);
			$("#arquivo_carregado").html("");
			$("#btnCancelar").css({"visibility" : "hidden"});
		})
 // Função para atualizar o valor do campo 'acao' com o valor selecionado do 'canal'


  // Salvando um Registro //
	$('#btnGravar').click(function(e){
		e.preventDefault();	
   
		var mensagem  = "<strong>Agendamento Cadastrado com sucesso!</strong>";
        var mensagem2 = 'Falha ao Efetuar Cadastro!';
        var mensagem3 = 'Agendamento Já Cadastrado!';
        var mensagem4 = 'Agendamento Atualizada com Sucesso!';
        var mensagem5 = 'Já existe um canal vinculado ao Item Selecionado!';
        var mensagem6 = 'Existe um agendamento cadastrado com esse titulo e canal nessa data!';
  
        $("input:text").css({"border-color" : "#999"});
        $(".msgValida").css({"display" : "none"});
	    
        if ($.trim($("#titulo").val()) == ''){	
            $("#valida_titulo").css({"display" : "inline", "color" : "red"});			
            $("#titulo").css({"border-color" : "red"});
            $("#titulo").focus();
            return false;
        }	

        if ($.trim($("#msgs").val()) == ''){	
            $("#valida_msgs").css({"display" : "inline", "color" : "red"});
            $("#msgs").css({"border-color" : "red"});
            $("#msgs").focus();
            return false;
        }

        if ($.trim($("#seletorData").val()) == ''){	
            $("#valida_seletorData").css({"display" : "inline", "color" : "red"});
            $("#seletorData").css({"border-color" : "red"});
            $("#seletorData").focus();
            return false;
        }

        if ($.trim($("#seletorHora").val()) == ''){	
            $("#valida_seletorHora").css({"display" : "inline", "color" : "red"});
            $("#seletorHora").css({"border-color" : "red"});
            $("#seletorHora").focus();
            return false;
        }
 
	    $('#gravarAgendamentos').ajaxForm({
		    resetForm: true, 			  
            beforeSend:function() { 
                $("#btnGravarAgendamentos").attr('value', 'Salvando ...');
				$('#FormAgendamento').find('input, button').prop('disabled', true);
            },
            success: function( retorno ){
			//	alert(retorno);
                // Mensagem de Cadastro efetuado //
                if (retorno == 1) { 
					mostraDialogo(mensagem, "success", 2500);
					$("#btnCancelar").click(); }
                // Mensagem de Atualização Efetuada //
                else if (retorno == 2){ 
					mostraDialogo(mensagem4, "success", 2500); 
					$("#btnCancelar").click();
				}
                // Departamento já existe //
                else if (retorno == 3){ 
					mostraDialogo(mensagem3, "danger", 2500);			
				}
                // Já existe um departamento cadastrado para este menu //
                else if (retorno == 4){ 
					mostraDialogo(mensagem5, "danger", 2500);					
				 }
                // Já existe uma resposta automática Cadastrada para o Item de Menu selecionado //
                else if (retorno == 5){ 
					mostraDialogo(mensagem6, "danger", 2500); 				
				}
                // Mensagem de Falha no Cadastro //
                else{ mostraDialogo(mensagem2, "danger", 2500); }
				
                $.ajax("cadastros/agendamentos/listar.php").done(function(data) {
                    $('#Listar').html(data);
                });
            },		 
		    complete:function() {
                $("#btnGravarAgendamentos").attr('value', 'Salvar');
				$('#FormAgendamento').find('input, button').prop('disabled', false);
            },
            error: function (retorno) { mostraDialogo(mensagem5, "danger", 2500); }
	    }).submit();
	});
    // FIM Novo Registro //

});
  </script>