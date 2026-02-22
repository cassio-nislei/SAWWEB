// JavaScript Document
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

   function getMoney( str )
{
        return parseInt( str.toString().replace(/[\D]+/g,'') );
}
	  
	  function formatReal( int )
{
        var tmp = int+'';
        var neg = false;
        if(tmp.indexOf("-") == 0)
        {
            neg = true;
            tmp = tmp.toString().replace("-","");
        }
        
        if(tmp.length == 1) tmp = "0"+tmp
    
        tmp = tmp.toString().replace(/([0-9]{2})$/g, ",$1");
        if( tmp.length > 6)
            tmp = tmp.toString().replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");
        
        if( tmp.length > 9)
            tmp = tmp.toString().replace(/([0-9]{3}).([0-9]{3}),([0-9]{2}$)/g,".$1.$2,$3");
    
        if( tmp.length > 12)
            tmp = tmp.toString().replace(/([0-9]{3}).([0-9]{3}).([0-9]{3}),([0-9]{2}$)/g,".$1.$2.$3,$4");
        
        if(tmp.indexOf(".") == 0) tmp = tmp.toString().replace(".","");
        if(tmp.indexOf(",") == 0) tmp = tmp.toString().replace(",","0,");
    
    return (neg ? '-'+tmp : tmp);
}


function ConfirmarDados(titulo, mensagem, callback) {
    // Criar elementos do modal
    var modal = document.createElement('div');
    modal.className = 'modal';
    modal.tabIndex = -1;
    modal.role = 'dialog';
  
    var modalDialog = document.createElement('div');
    modalDialog.className = 'modal-dialog';
    modalDialog.role = 'document';
  
    var modalContent = document.createElement('div');
    modalContent.className = 'modal-content';
  
    var modalHeader = document.createElement('div');
    modalHeader.className = 'modal-header';
  
    var modalTitle = document.createElement('h5');
    modalTitle.className = 'modal-title';
    modalTitle.innerText = titulo;
  
    var closeButton = document.createElement('button');
    closeButton.type = 'button';
    closeButton.className = 'close';
    closeButton.setAttribute('data-dismiss', 'modal');
    closeButton.setAttribute('aria-label', 'Close');
  
    var closeIcon = document.createElement('span');
    closeIcon.setAttribute('aria-hidden', 'true');
    closeIcon.innerHTML = '&times;';
  
    closeButton.appendChild(closeIcon);
    modalHeader.appendChild(modalTitle);
    modalHeader.appendChild(closeButton);
  
    var modalBody = document.createElement('div');
    modalBody.className = 'modal-body';
    modalBody.innerHTML = '<p>' + mensagem + '</p>';
  
    var modalFooter = document.createElement('div');
    modalFooter.className = 'modal-footer';
  
    var confirmButton = document.createElement('button');
    confirmButton.type = 'button';
    confirmButton.className = 'btn btn-primary';
    confirmButton.innerText = 'Confirmar';
  
    var cancelButton = document.createElement('button');
    cancelButton.type = 'button';
    cancelButton.className = 'btn btn-secondary';
    cancelButton.setAttribute('data-dismiss', 'modal');
    cancelButton.innerText = 'Cancelar';
  
    modalFooter.appendChild(confirmButton);
    modalFooter.appendChild(cancelButton);
  
    modalContent.appendChild(modalHeader);
    modalContent.appendChild(modalBody);
    modalContent.appendChild(modalFooter);
  
    modalDialog.appendChild(modalContent);
    modal.appendChild(modalDialog);
  
    // Adicionar evento de clique ao botão "Confirmar"
    confirmButton.onclick = function () {
      document.body.removeChild(modal);
      removerBackdrop();
      callback(true);
    };
  
    // Adicionar evento de clique ao botão "Cancelar"
    cancelButton.onclick = function () {
      document.body.removeChild(modal);
      removerBackdrop();
      callback(false);
    };
  
    // Adicionar o modal ao corpo da página
    document.body.appendChild(modal);
  
    // Exibir o modal
    $(modal).modal('show');
  
    // Remover o backdrop quando o modal for fechado
    $(modal).on('hidden.bs.modal', function () {
      removerBackdrop();
    });
  
    // Função para remover o backdrop
    function removerBackdrop() {
      var backdrop = document.querySelector('.modal-backdrop');
      if (backdrop) {
        document.body.removeChild(backdrop);
      }
    }
  }
  
