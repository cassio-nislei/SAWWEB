<script src="js/jquery.form.js"></script>
<link href="css/estiloinputlabel.css" rel="stylesheet">
<script>
$( document ).ready(function() {	
	$('#Listar').load("cadastros/menu/listar.php");

  $.ajax("cadastros/menu/combomenu.php").done(function(data) {
			$('#id_menu').html(data);
      $('#id_menu').trigger('blur');
	});
	
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
</script>

<form method="post" action="cadastros/menu/salvar.php" name="gravaMenu" id="gravaMenu">	
<input type="hidden" value="0" name="acao" id="acao" />
<input type="hidden" name="id" id="id" value="0">
<div class="container" id="FormCategorias">
 <div class="panel panel-default">
	<div class="panel-heading"><b>Menus</b></div>
  <div class="panel-body">

    <div class="form-group">
      <div class='form-group-select' style="width:100%">
           <label class='control-label'>Menu Superior</label>
          <select class="select form_campos" id="id_menu" name="id_menu" style="width:100%">
         
         </select> 
           <div id="valida_categoria" style="display: none" class="msgValida">
              Por favor, informe o tipo do Produto .
          </div> 
       </div>  
    </div>   

    <div class="form-group">
       <label class='control-label' for='inputNormal'>Descrição do Item do Menu*</label>
      <input type="text" id="txtmenu" name="txtmenu" class="form_campos">   
      <div id="valida_menu" style="display: none" class="msgValida">
        Por favor, informe a descrição do Menu.
      </div>  
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
				<div class="panel-heading"><b>Menus Cadastrados</b></div>
				<div class="panel-body" id="Listar">
				
				<!-- Aqui Lista as Empresas Cadastradas -->
				
				
				</div>
	  </div>
	</div>
  <script type='text/javascript' src="js/funcoes.js"></script>
  <script>
$( document ).ready(function() {	
   //Botão Cancelar Ao CLicar deve ser oculto
		$("#btnCancelar").click(function(){
			$("#acao").val(0);
			$("#btnCancelar").css({"visibility" : "hidden"});
		})


  // Salvando um Registro //
	$('#btnGravar').click(function(e){
		e.preventDefault();	

		var mensagem  = "<strong>Menu Cadastrado com sucesso!</strong>";
		var mensagem2 = 'Falha ao Efetuar Cadastro!';
		var mensagem3 = 'Menu Já Cadastrado!';
		var mensagem4 = 'Menu Atualizado com Sucesso!';
		

		$("input:text").css({"border-color" : "#999"});
		$(".msgValida").css({"display" : "none"});
	    
		if ($.trim($("#txtmenu").val()) == ''){
			$("#valida_menu").css({"display" : "inline", "color" : "red"});
			$("#txtmenu").css({"border-color" : "red"});
			$("#txtmenu").focus();
			return false;
		}
 
	    $('#gravaMenu').ajaxForm({
			resetForm: true, 			  
			beforeSend:function() { 
				$("#btnGravar").attr('value', 'Salvando ...');
				$('#btnGravar').attr('disabled', true);
				$('#gravaMenu').find('input').prop('disabled', true);
			},
			success: function( retorno ){
				//alert(retorno);
     
				if (retorno == 1) { mostraDialogo(mensagem, "success", 2500); }
				else if (retorno == 2){ mostraDialogo(mensagem4, "success", 2500); }
				else if (retorno == 3){ mostraDialogo(mensagem3, "danger", 2500); }
				else if (retorno == 4){ mostraDialogo(mensagem4, "success", 2500); }
				else if (retorno == 5){ mostraDialogo(mensagem5, "danger", 2500); }
				else{ 
				//	alert(retorno);
					
				//	mostraDialogo(mensagem2+retorno, "danger", 2500); 
				}
                $("#btnCancelar").css({"visibility" : "hidden"});
				$.ajax("cadastros/menu/listar.php").done(function(data) {
					$('#Listar').html(data);
				});
				//Atualizo a lista
				$.ajax("cadastros/menu/combomenu.php").done(function(data) {
            $('#id_menu').html(data);
            $('#id_menu').trigger('blur');
        });
			},		 
			complete:function(retorno) {
		//		alert("Completo"+retorno);
				$("#btnGravar").attr('value', 'Salvar');
				$('#btnGravar').attr('disabled', false);
				$('#gravaMenu').find('input, button').prop('disabled', false);	
				
			},
			error: function (retorno) {
			//	alert("Erro"+retorno);
				 mostraDialogo(mensagem5, "danger", 2500);
			 
		}
		}).submit();
	});
	// FIM Salvando um Registro //  

});
  </script>