// JavaScript Document
$( document ).ready(function() {
  function mostraDialogo(mensagem, tipo, tempo){ 
	  
       
		
    
    // se houver outro alert desse sendo exibido, cancela essa requisição
    if($("#message").is(":visible")){
        return false;
    }

    // se não setar o tempo, o padrão é 3 segundos
    if(!tempo){
        var tempo = 3000;
    }

    // se não setar o tipo, o padrão é alert-info
    if(!tipo){
        var tipo = "info";
    }

    // monta o css da mensagem para que fique flutuando na frente de todos elementos da página
    var cssMessage = "display: block; position: fixed; top: 0; left: 20%; right: 20%; width: 60%; padding-top: 10px; z-index: 9999";
    var cssInner = "margin: 0 auto; box-shadow: 1px 1px 5px black;";

    // monta o html da mensagem com Bootstrap
    var dialogo = "";
    dialogo += '<div id="message" style="'+cssMessage+'">';
    dialogo += '    <div class="alert alert-'+tipo+' alert-dismissable" style="'+cssInner+'">';
    dialogo += '    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>';
    dialogo +=          mensagem;
    dialogo += '    </div>';
    dialogo += '</div>';

    // adiciona ao body a mensagem com o efeito de fade
    $("body").append(dialogo);
    $("#message").hide();
    $("#message").fadeIn(200);

    // contador de tempo para a mensagem sumir
    setTimeout(function() {
        $('#message').fadeOut(300, function(){
            $(this).remove();
        });
    }, tempo); // milliseconds

}

     //Novo Registro
	 $('#btnGravar').click(function(e){
	   e.preventDefault();
	  var mensagem = "<strong>Perfil atualizado com sucesso!</strong>";
	  var mensagem2 = 'Falha ao Efetuar Cadastro!';
	  
	  $("input:text").css({"border-color" : "#999", "padding": "2px"});
	  
	    if ($.trim($("#nome_usuario").val()) == -1){
				$("#nome_usuario").css({"border-color" : "#F00", "padding": "2px"});
				alert("Favor Informar o Nome");
				$("#nome_usuario").focus();
				return false;  
			  }
	  
	      $('#grava').ajaxForm({
		resetForm: false, 
		 beforeSend:function() { 
           $('#btnGravar').html('<i class="fa fa-spinner fa-spin"></i> Salvando...');
           $('#btnGravar').attr('disabled', true);	
		   $('#grava').find('input').prop('disabled', true);
        },
		success: function( retorno )
				{
			//		alert(retorno);
					if (retorno == 1) {
					  $("#imgPerfil1").attr("src",$("#foto2").val());	
					  $("#imgPerfil2").attr("src",$("#foto2").val());
					  mostraDialogo(mensagem, "success", 2500);	
					}else{
					  mostraDialogo(mensagem2, "danger", 2500);	
					}
				},			  	 
		complete:function() {
			$('#btnGravar').html('<i class="fa fa-save"></i> Salvar Alterações');
			$('#btnGravar').attr('disabled', false);
			$('#grava').find('input, button').prop('disabled', false); 
			
		 },
		 error: function (retorno) {
                //  alert(retorno);
			    mostraDialogo(retorno, "danger", 2500);	
                }
			  

	}).submit(); 	  

	 });
  
  
	//Carrego a Imagem antes mesmo de Envia-la ao servidor
//	 $('#foto').change(function(){
	 //  const file = $(this)[0].files[0]
	 //  const fileReader = new FileReader()
	 //  fileReader.onloadend = function(){
//		   $("#imgPerfil").attr("src",fileReader.result)
//	   }
	  // fileReader.readAsDataURL(file)
	 //})
	
	
	
	 //Ações para Efetuar o corte da imagem
	$image_crop = $('#image_demo').croppie({
    enableExif: true,
    viewport: {
      width:300,
      height:120,
      type:'canvas' //circle square
    },
    boundary:{
      width:500,
      height:300
    }
  });

  $('#foto').on('change', function(){
    var reader = new FileReader();
    reader.onload = function (event) {
      $image_crop.croppie('bind', {
        url: event.target.result
      }).then(function(){
        console.log('jQuery bind complete');
      });
    }
    reader.readAsDataURL(this.files[0]);
    $('#uploadimageModal').modal('show');
  });

  $('.crop_image').click(function(event){
    $image_crop.croppie('result', {
      type: 'canvas',
      size: 'viewport'
    }).then(function(response){
      $.ajax({
        url:"cadastros/perfil/upload.php",
        type: "POST",
        data:{"image": response},
        success:function(data)
        {
          $('#uploadimageModal').modal('hide');
		  $("#foto_carregada").attr("src",data);
		  $("#foto2").val(data);
         // $('#visualizar').html(data);
        }
      });
    })
  });
	
	
	//Carrega as Cidades
		  $("select[name=empresa_estado]").change(function(){
            $("select[name=empresa_cidade]").html('<option value="0">Carregando...</option>');
            
            $.post("includes/cidades.php", 
                  {estado:$(this).val()},
                  function(valor){
                     $("select[name=empresa_cidade]").html(valor);
				     $("select[name=empresa_cidade]").trigger('blur');
                  }
                  )
            
         })
	
  
		  
				  
});