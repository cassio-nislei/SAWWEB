<?php
// Buscar cor dos parâmetros
$corHeader = '#075e54'; // cor padrão
if (isset($conexao)) {
    $resultado = $conexao->query("SELECT color FROM tbparametros LIMIT 1");
    if ($resultado && $resultado->num_rows > 0) {
        $linha = $resultado->fetch_assoc();
        $corHeader = $linha['color'] ?? '#075e54';
    }
}
?>
<div tabindex="-1" id="panel-edit-profile" class="panel-left">
    <div class="_2fq0t copyable-area">
        <header class="_1FroB" style="position: relative; background-color: <?php echo $corHeader; ?>;">
            <div class="Ghmsz" data-animate-drawer-title="true" style="display: flex; align-items: flex-start; justify-content: center; gap: 15px;">
                <button class="_1aTxu btn-close" id="btn-close-panel-edit-profile" style="flex-shrink: 0; position: absolute; left: 15px;">
                    <span data-icon="back-light">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32">
                            <path fill="#FFF" d="M20 11H7.8l5.6-5.6L12 4l-8 8 8 8 1.4-1.4L7.8 13H20v-2z"></path>
                        </svg>
                    </span>
                </button>
                <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; margin-top: -40px;">
                    <div id="photo-container-header-profile" style="width: 60px; height: 60px; flex-shrink: 0; overflow: hidden; border-radius: 50%;">
                        <img src="#" class="photo" id="img-header-edit-profile" style="height: 100%; width: 100%; display:none">
                        <div style="display: block; width: 100%; height: 100%; overflow: hidden;">
                            <span data-icon="default-user" style="display: block; width: 100%; height: 100%; overflow: hidden;">
                                <img src="carregarFotoUsuario.php" class="rounded-circle user_img_msg" style="width: 100%; height: 100%; object-fit: cover; display: block; margin: 0; padding: 0; border: 0;" onerror="this.src='images/user-default.png'">
                            </span>
                        </div>
                    </div>
                    <div class="_1xGbt" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 14px;">Perfil</div>
                </div>
            </div>
        </header>
        <div class="_2sNbV">
            <div class="_12fSF">
                <div class="_1wCju">
                    <div class="_2UkYn" dir="ltr">
                        <div class="_1WNtc">
                            <div class="IuYNx">
                                <div id="photo-container-edit-profile" style="width: 200px; height: 200px; top: 0px; left: 0px; position: absolute;">
                                    <img src="#" class="photo" id="img-panel-edit-profile" style="height: 100%; width: 100%; display:none">
                                    <div class="_3ZW2E no-photo-edit" id="img-default-panel-edit-profile">
                                        <span data-icon="default-user" style="display: block; width: 100%; height: 100%; overflow: hidden;">
                                            <img src="carregarFotoUsuario.php" class="rounded-circle user_img_msg" style="width: 100%; height: 100%; object-fit: cover; display: block; margin: 0; padding: 0; border: 0;" onerror="this.src='images/user-default.png'">
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <input type="file" accept="image/gif,image/jpeg,image/jpg,image/png" style="display: none;" id="input-profile-photo">
                </div>
            </div>
            <div class="_1CRb5 _34vig _2phEY" style="height: auto; padding-bottom: 20px; margin-bottom: -30px;">
                <div class="_2VQzd">
                    <div>
                        <div class="LlZXr">
                            <div class="_3e7ko _1AUd-">
                                <span class="_1sYdX">Nome no Chat</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div tabindex="-1" class="ogWqZ  _2-h1L _31WRs" style="height: 25%; margin-top: -20px;">
                    <span class="_2rXhY CrwPM"></span>
                    <div class="_2OIDe"></div>
                    <div class="_1DTd4" style="height: 50%;">
                        <div tabindex="-1" class="_3F6QL bsmJe">
                            <div class="_39LWd" style="visibility: hidden;"></div>
                            <div id="input-name-panel-edit-profile" class="_2S1VP copyable-text selectable-text input-name" contenteditable="true" dir="ltr" style="text-align: left;">
                                <?php echo isset($_SESSION["usuariosaw"]["nome_chat"]) ? $_SESSION["usuariosaw"]["nome_chat"] : (isset($_SESSION["usuariosaw"]["nome"]) ? $_SESSION["usuariosaw"]["nome"] : "Usuário"); ?>
                            </div>
                        </div>
                        <div class="_2YmC2" style="margin-top: 5px;">
                            <span class="_3jFFV">
                                <div class="_3cyFx btn-save" id="btn-save-panel-edit-profile" title="Clique para salvar" style="transform: scaleX(1) scaleY(1); opacity: 1;">
                                    <span data-icon="checkmark">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                            <path opacity=".45" fill="#263238" d="M9 17.2l-4-4-1.4 1.3L9 19.9 20.4 8.5 19 7.1 9 17.2z"></path>
                                        </svg>
                                    </span>
                                </div>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="_idKB">
                <span class="Cpiae">Este nome será visível para seus contatos.</span>
            </div>
            <div class="_idKB">
                <span class="Cpiae"><input type="checkbox" name="horario_almoco" id="horario_almoco">Em horario de almoço.</span><br>
                <span id="spanHorarioAlmoco">
                    <textarea id="msgAlmocoAtendente" name="msgAlmocoAtendente" style="width: 100%; height: 100px;"></textarea>
                </span>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
/* Seção Nome no Chat - Estilo */
._1sYdX {
    color: #075e54;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: block;
    margin-bottom: 15px;
}

/* Container do input */
.ogWqZ._2-h1L._31WRs {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: -15px !important;
    position: relative;
}

/* Wrapper do input */
._3F6QL.bsmJe {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

/* O input contenteditable */
#input-name-panel-edit-profile {
    flex: 1;
    font-size: 18px;
    font-weight: 500;
    text-align: center !important;
    color: #1f2937;
    padding: 10px;
    border: none;
    outline: none;
    word-wrap: break-word;
    white-space: normal;
}

#input-name-panel-edit-profile:focus {
    background-color: #f3f4f6;
    border-radius: 4px;
}

/* Container do botão de salvar */
._2YmC2 {
    display: flex;
    align-items: center;
    justify-content: flex-end;
}

._3jFFV {
    display: flex;
    align-items: center;
}

/* Botão de salvar */
.btn-save {
    cursor: pointer;
    padding: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.btn-save:hover {
    background-color: #e5e7eb;
}

.btn-save svg {
    width: 24px !important;
    height: 24px !important;
}

/* Remover espaços desnecessários */
._39LWd {
    display: none !important;
}

._2OIDe {
    display: none !important;
}

._2rXhY.CrwPM {
    display: none !important;
}

._1DTd4 {
    display: flex;
    align-items: center;
    gap: 0;
}
</style>

<script>
$(document).ready(function() {

    
function listarAlmocoAtendente(){
        $.post("gravarHorarioAlmoco.php", function(retorno){
            var dadosalomoco = JSON.parse(retorno);    

              //Verifico se o Checkbox está marcado para mostrar ou oculta o campo da mensagem de almoço             
                 if( dadosalomoco.em_almoco==1 ){                    
                    $('#horario_almoco').prop('checked', true);                   
                    new bootstrap.Modal(document.getElementById('mdlAlmocando')).show();         
                      
                } else{ 

                    $('#horario_almoco').prop('checked', false);   
                    $('#mdlAlmocando').modal('hide');
                    $("#spanHorarioAlmoco").hide();
                }
            $('#msgAlmocoAtendente').val(dadosalomoco.msg_almoco);
        })
    }

     listarAlmocoAtendente();

 

     function gravaAlmocoAtendente(){
        var em_almoco = $("#horario_almoco").prop("checked");
        var msgAlmoco  = $("#msgAlmocoAtendente").val();
        console.log('Gravando: em_almoco=' + em_almoco + ', msgAlmoco=' + msgAlmoco);
       
        $.post("gravarHorarioAlmoco.php",{em_almoco:em_almoco, msgAlmoco:msgAlmoco}, function(retorno){
            console.log('Resposta do servidor:', retorno);
            listarAlmocoAtendente();
        }).fail(function(error) {
            console.error('Erro ao gravar:', error);
        })
     }
     $(document).on('change', '#horario_almoco', function(){
        gravaAlmocoAtendente();
     })

     $(document).on('change', '#msgAlmocoAtendente', function(){
        gravaAlmocoAtendente();
     })
    

     $(document).on('click', '#horario_almoco', function() {
        // Verifica se o campo está 'checado' //
        if( $("#horario_almoco").prop("checked") ){
            $("#spanHorarioAlmoco").attr("style", "display:block");  
            document.location.reload(true);                        
        }
        else{ $("#spanHorarioAlmoco").attr("style", "display:none"); }
    });

    $(document).on('click', '#btnVoltardoAlmoco', function() {
        console.log('Botão de retorno ao trabalho clicado');
        
        // Fechar o modal usando jQuery
        $('#mdlAlmocando').modal('hide');
        console.log('Modal fechado com jQuery');
        
        // Ocultar a mensagem de almoço imediatamente
        $("#spanHorarioAlmoco").hide();
        console.log('Mensagem de almoço ocultada');
        
        // Desmarcar checkbox
        $('#horario_almoco').prop('checked', false);
        console.log('Checkbox desmarcado');
        
        // Salvar os dados
        gravaAlmocoAtendente();
        
        // Recarregar estado após um delay
        setTimeout(function() {
            listarAlmocoAtendente();
            console.log('Funções executadas');
        }, 500);
    });

    $(document).on('click', '#btn-save-panel-edit-profile', function() {
        var nomeChat = $('#input-name-panel-edit-profile').text().trim();
        if(nomeChat !== '') {
            $.ajax({
                url: "salvarNomeChat.php",
                type: "POST",
                data: {nome_chat: nomeChat},
                xhrFields: {
                    withCredentials: true
                },
                success: function(retorno) {
                    retorno = retorno.trim();
                    if(retorno == 'sucesso') {
                        console.log('Nome do chat salvo com sucesso');
                    } else {
                        alert('Erro ao salvar: ' + retorno);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro na requisição:', error);
                    alert('Erro ao salvar nome do chat: ' + error);
                }
            });
        }
    });

    // Refresh user photo every 30 seconds
    setInterval(function() {
        const $img = $('.user_img_msg');
        const currentSrc = $img.attr('src');
        if (currentSrc && currentSrc.includes('carregarFotoUsuario.php')) {
            $img.attr('src', currentSrc.split('?')[0] + '?t=' + new Date().getTime());
        }
    }, 30000);

   


 });

</script>