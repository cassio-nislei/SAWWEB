<script src="js/jquery.form.js"></script>
<script src="js/jquery.mask.js"></script>
<link href="css/estiloinputlabel.css" rel="stylesheet">
<script type='text/javascript' src="cadastros/horarios/acoes.js"></script>
<script>
$( document ).ready(function() {	
	
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
<?php
   require_once("../includes/padrao.inc.php");
 


  //Traz os dados dos Horarios Já preenchidos
$horasdomingo = mysqli_query($conexao,"select * from tbhorarios where dia_semana = 6");
$domingo = mysqli_fetch_array($horasdomingo);
$domingo["fechado"] ? $domingo_marcado = 'checked' : $domingo_marcado = '';

$horassegunda = mysqli_query($conexao,"select * from tbhorarios where dia_semana = 0");
$segunda = mysqli_fetch_array($horassegunda);
$segunda["fechado"] ? $segunda_marcado = 'checked' : $segunda_marcado = '';

$horasterca = mysqli_query($conexao,"select * from tbhorarios where dia_semana = 1");
$terca = mysqli_fetch_array($horasterca);
$terca["fechado"] ? $terca_marcado = 'checked' : $terca_marcado = '';

$horasquarta = mysqli_query($conexao,"select * from tbhorarios where dia_semana = 2");
$quarta = mysqli_fetch_array($horasquarta);
$quarta["fechado"] ? $quarta_marcado = 'checked' : $quarta_marcado = '';

$horasquinta = mysqli_query($conexao,"select * from tbhorarios where dia_semana = 3");
$quinta = mysqli_fetch_array($horasquinta);
$quinta["fechado"] ? $quinta_marcado = 'checked' : $quinta_marcado = '';

$horassexta = mysqli_query($conexao,"select * from tbhorarios where dia_semana = 4");
$sexta = mysqli_fetch_array($horassexta);
$sexta["fechado"] ? $sexta_marcado = 'checked' : $sexta_marcado = '';

$horassabado = mysqli_query($conexao,"select * from tbhorarios where dia_semana = 5");
$sabado = mysqli_fetch_array($horassabado);
$sabado["fechado"] ? $sabado_marcado = 'checked' : $sabado_marcado = '';

?>
 <form method="post" id="grava" name="grava" action="cadastros/horarios/salvar.php">
<div class="container" id="FormFormasPagamento" >
 <div class="panel panel-default">
	<div class="panel-heading"><b>Horários de Funcionamento</b></div>
  <div class="panel-body">

<div class="row">
     <div class="col-md-3">
         <div class='form-group-select' style="width:100%">
           <label class='control-label'>Dia da Semana</label>
          <select class="select form_campos" id="domingo" name="domingo" style="width:100%">
          <option value="1">Domingo</option>
       
    </select> 
         <div id="valida_tipo_entrega" style="display: none" class="msgValida">
            Por favor, informe o Dia da Semana .
        </div> 
      </div>  
     </div> 
	
	   <div class="col-md-3">
<div class="form-group">
 <label class='control-label' for='inputNormal'>Hora Inicio de Expediente*</label>
           <input type="text" value="<?php echo $domingo["hr_inicio"];?>" id="hora_inicio_expediente_domingo" name="hora_inicio_expediente_domingo" class="form_campos text-uppercase"> 
         <div id="valida_inicio_domingo" style="display: none" class="msgValida">
            Preencha o Inicio de Expediente no Domingo.
        </div> 
 </div>
	</div>
	
	   <div class="col-md-3">
<div class="form-group">
<label class='control-label' for='inputNormal'>Hora Final de Expediente*</label>
           <input type="text" value="<?php echo $domingo["hr_fim"];?>" id="hora_fim_expediente_domingo" name="hora_fim_expediente_domingo" class="form_campos text-uppercase"> 
         <div id="valida_fim_domingo" style="display: none" class="msgValida">
            Preencha o Final de Expediente no Domingo.
        </div> 
   </div>
	</div>
	
	 <div class="col-md-3">
<div class="form-group" style="text-align: center">

       	    
       <p> <input type="checkbox" <?php echo $domingo_marcado; ?> id="fechado_domingo" name="fechado_domingo" class="flat"> 
		   Fechado aos Domingos?</p>
   </div>
	</div>
	
	  </div>
	
	<div class="row">
     <div class="col-md-3">
         <div class='form-group-select' style="width:100%">
           <label class='control-label'>Dia da Semana</label>
          <select class="select form_campos" id="segunda" name="segunda" style="width:100%">
          <option value="2">Segunda Feira</option>
       
    </select> 
         <div id="valida_tipo_entrega" style="display: none" class="msgValida">
            Por favor, informe o Dia da Semana .
        </div> 
      </div>  
     </div> 
	
	   <div class="col-md-3">
<div class="form-group">
 <label class='control-label' for='inputNormal'>Hora Inicio de Expediente*</label>
           <input type="text" value="<?php echo $segunda["hr_inicio"];?>" id="hora_inicio_expediente_segunda" name="hora_inicio_expediente_segunda" class="form_campos text-uppercase"> 
         <div id="valida_inicio_segunda" style="display: none" class="msgValida">
            Preencha o Inicio de Expediente na Segunda Feira.
        </div> 
 </div>
	</div>
	
	   <div class="col-md-3">
<div class="form-group">
<label class='control-label' for='inputNormal'>Hora Final de Expediente*</label>
           <input type="text" value="<?php echo $segunda["hr_fim"];?>" id="hora_fim_expediente_segunda" name="hora_fim_expediente_segunda" class="form_campos text-uppercase"> 
         <div id="valida_fim_segunda" style="display: none" class="msgValida">
            Preencha o Final de Expediente na Segunda Feira.
        </div> 
   </div>
	</div>
	
	 <div class="col-md-3">
<div class="form-group" style="text-align: center">

        <p><input type="checkbox" <?php echo $segunda_marcado; ?> id="fechado_segunda" name="fechado_segunda" class="flat"> Fechado as Segundas?</p>
         
   </div>
	</div>
	
	  </div>
	  
	  <div class="row">
     <div class="col-md-3">
         <div class='form-group-select' style="width:100%">
           <label class='control-label'>Dia da Semana</label>
          <select class="select form_campos" id="segunda" name="segunda" style="width:100%">
          <option value="2">Terça Feira</option>
       
    </select> 
         <div id="valida_tipo_entrega" style="display: none" class="msgValida">
            Por favor, informe o Dia da Semana .
        </div> 
      </div>  
     </div> 
	
	   <div class="col-md-3">
<div class="form-group">
 <label class='control-label' for='inputNormal'>Hora Inicio de Expediente*</label>
           <input type="text" value="<?php echo $terca["hr_inicio"];?>" id="hora_inicio_expediente_terca" name="hora_inicio_expediente_terca" class="form_campos text-uppercase" required> 
         <div id="valida_inicio_terca" style="display: none" class="msgValida">
            Preencha o Inicio de Expediente na Terça Feira.
        </div> 
 </div>
	</div>
	
	   <div class="col-md-3">
<div class="form-group">
<label class='control-label' for='inputNormal'>Hora Final de Expediente*</label>
           <input type="text" value="<?php echo $terca["hr_fim"];?>" id="hora_fim_expediente_terca" name="hora_fim_expediente_terca" class="form_campos text-uppercase" required> 
         <div id="valida_fim_terca" style="display: none" class="msgValida">
            Preencha o Final de Expediente na Terça Feira.
        </div> 
   </div>
	</div>
	
	 <div class="col-md-3">
<div class="form-group" style="text-align: center">

       <p> <input type="checkbox" <?php echo $terca_marcado; ?> id="fechado_terca" name="fechado_terca" class="flat"> 
         Fechado as Terças?</p>
   </div>
	</div>
	
	  </div>
	  
	  <div class="row">
     <div class="col-md-3">
         <div class='form-group-select' style="width:100%">
           <label class='control-label'>Dia da Semana</label>
          <select class="select form_campos" id="segunda" name="segunda" style="width:100%">
          <option value="2">Quarta Feira</option>
       
    </select> 
         <div id="valida_tipo_entrega" style="display: none" class="msgValida">
            Por favor, informe o Dia da Semana .
        </div> 
      </div>  
     </div> 
	
	   <div class="col-md-3">
<div class="form-group">
 <label class='control-label' for='inputNormal'>Hora Inicio de Expediente*</label>
           <input type="text" value="<?php echo $quarta["hr_inicio"];?>" id="hora_inicio_expediente_quarta" name="hora_inicio_expediente_quarta" class="form_campos text-uppercase" required> 
         <div id="valida_inicio_quarta" style="display: none" class="msgValida">
            Preencha o Inicio de Expediente na Quarta Feira.
        </div> 
 </div>
	</div>
	
	   <div class="col-md-3">
<div class="form-group">
<label class='control-label' for='inputNormal'>Hora Final de Expediente*</label>
           <input type="text" value="<?php echo $quarta["hr_fim"];?>" id="hora_fim_expediente_quarta" name="hora_fim_expediente_quarta" class="form_campos text-uppercase" required> 
         <div id="valida_fim_quarta" style="display: none" class="msgValida">
            Preencha o Final de Expediente na Segunda Feira.
        </div> 
   </div>
	</div>
	
	 <div class="col-md-3">
<div class="form-group" style="text-align: center">

        <p><input type="checkbox" <?php echo $quarta_marcado; ?> id="fechado_quarta" name="fechado_quarta" class="flat"> 
        Fechado as Quartas? </p>
   </div>
	</div>
	
	  </div>
	  
	  <div class="row">
     <div class="col-md-3">
         <div class='form-group-select' style="width:100%">
           <label class='control-label'></label>
          <select class="select form_campos" id="segunda" name="segunda" style="width:100%">
          <option value="2">Quinta Feira</option>
       
    </select> 
         <div id="valida_tipo_entrega" style="display: none" class="msgValida">
            Por favor, informe o Dia da Semana .
        </div> 
      </div>  
     </div> 
	
	   <div class="col-md-3">
<div class="form-group">
 <label class='control-label' for='inputNormal'>Hora Inicio de Expediente*</label>
           <input type="text" value="<?php echo $quinta["hr_inicio"];?>" id="hora_inicio_expediente_quinta" name="hora_inicio_expediente_quinta" class="form_campos text-uppercase" required> 
         <div id="valida_inicio_quinta" style="display: none" class="msgValida">
            Preencha o Inicio de Expediente na Quinta Feira.
        </div> 
 </div>
	</div>
	
	   <div class="col-md-3">
<div class="form-group">
<label class='control-label' for='inputNormal'>Hora Final de Expediente*</label>
           <input type="text" value="<?php echo $quinta["hr_fim"];?>" id="hora_fim_expediente_quinta" name="hora_fim_expediente_quinta" class="form_campos text-uppercase" required> 
         <div id="valida_fim_quinta" style="display: none" class="msgValida">
            Preencha o Final de Expediente na Quinta Feira.
        </div> 
   </div>
	</div>
	
	 <div class="col-md-3">
<div class="form-group" style="text-align: center">
       <p> <input type="checkbox" <?php echo $quinta_marcado; ?> id="fechado_quinta" name="fechado_quinta" class="flat"> Fechado as Quintas?</p>
         
   </div>
	</div>
	
	  </div>
	  
	  <div class="row">
     <div class="col-md-3">
         <div class='form-group-select' style="width:100%">
           <label class='control-label'>Dia da Semana</label>
          <select class="select form_campos" id="segunda" name="segunda" style="width:100%">
          <option value="2">Sexta Feira</option>
       
    </select> 
         <div id="valida_tipo_entrega" style="display: none" class="msgValida">
            Por favor, informe o Dia da Semana .
        </div> 
      </div>  
     </div> 
	
	   <div class="col-md-3">
<div class="form-group">
 <label class='control-label' for='inputNormal'>Hora Inicio de Expediente*</label>
           <input type="text" value="<?php echo $sexta["hr_inicio"];?>" id="hora_inicio_expediente_sexta" name="hora_inicio_expediente_sexta" class="form_campos text-uppercase" required> 
         <div id="valida_inicio_sexta" style="display: none" class="msgValida">
            Preencha o Inicio de Expediente na Sexta Feira.
        </div> 
 </div>
	</div>
	
	   <div class="col-md-3">
<div class="form-group">
<label class='control-label' for='inputNormal'>Hora Final de Expediente*</label>
           <input type="text" value="<?php echo $sexta["hr_fim"];?>" id="hora_fim_expediente_sexta" name="hora_fim_expediente_sexta" class="form_campos text-uppercase" required> 
         <div id="valida_fim_sexta" style="display: none" class="msgValida">
            Preencha o Final de Expediente na Sexta Feira.
        </div> 
   </div>
	</div>
	
	 <div class="col-md-3">
<div class="form-group" style="text-align: center">
<p>
        <input type="checkbox" <?php echo $sexta_marcado; ?> id="fechado_sexta" name="fechado_sexta" class="flat"> Fechado as Sextas?</p>
         
   </div>
	</div>
	
	  </div>
	  
	  <div class="row">
     <div class="col-md-3">
         <div class='form-group-select' style="width:100%">
           <label class='control-label'>Dia da Semana</label>
          <select class="select form_campos" id="segunda" name="segunda" style="width:100%">
          <option value="2">Sábado</option>
       
    </select> 
         <div id="valida_tipo_entrega" style="display: none" class="msgValida">
            Por favor, informe o Dia da Semana .
        </div> 
      </div>  
     </div> 
	
	   <div class="col-md-3">
<div class="form-group">
 <label class='control-label' for='inputNormal'>Hora Inicio de Expediente*</label>
           <input type="text" value="<?php echo $sabado["hr_inicio"];?>" id="hora_inicio_expediente_sabado" name="hora_inicio_expediente_sabado" class="form_campos text-uppercase" required> 
         <div id="valida_inicio_sabado" style="display: none" class="msgValida">
            Preencha o Inicio de Expediente no Sábado.
        </div> 
 </div>
	</div>
	
	   <div class="col-md-3">
<div class="form-group">
<label class='control-label' for='inputNormal'>Hora Final de Expediente*</label>
           <input type="text" value="<?php echo $sabado["hr_fim"];?>" id="hora_fim_expediente_sabado" name="hora_fim_expediente_sabado" class="form_campos text-uppercase" required> 
         <div id="valida_fim_sabado" style="display: none" class="msgValida">
            Preencha o Final de Expediente no Sábado.
        </div> 
   </div>
	</div>
	
	 <div class="col-md-3">
<div class="form-group" style="text-align: center">

        <p><input type="checkbox" <?php echo $sabado_marcado; ?> id="fechado_sabado" name="fechado_sabado" class="flat"> Fechado aos Sábados?</p>
         
   </div>
	</div>
	
	  </div>
  
   <br />
    <div class="form-group">
    <input type="hidden" value="0" name="acao" id="acao">	
  <button class="btn btn-primary ml-auto" id="btnGravarFormaPagto" ><i class="fa fa-save"></i> Salvar Alterações</button> 
   
  	 </div>
	 </div>
	 </div>
	  </div>
</form> 
   


