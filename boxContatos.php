<?php
// Buscar cor e imagem dos parâmetros
$corParametros = '#075e54'; // cor padrão
$imagemPerfil = 'img/ico-addcontato.svg'; // imagem padrão
if (isset($conexao)) {
    $resultado = $conexao->query("SELECT color, imagem_perfil FROM tbparametros LIMIT 1");
    if ($resultado && $resultado->num_rows > 0) {
        $linha = $resultado->fetch_assoc();
        $corParametros = $linha['color'] ?? '#075e54';
        // Se a imagem estiver em base64, monta o data URI
        if (!empty($linha['imagem_perfil'])) {
            $base64 = $linha['imagem_perfil'];
            // Detecta o tipo da imagem (assume PNG se não conseguir detectar)
            if (strpos($base64, 'data:image') === 0) {
                // Já é um data URI válido
                $imagemPerfil = $base64;
            } else {
                // É apenas o base64, precisa montar o data URI
                $imagemPerfil = 'data:image/png;base64,' . $base64;
            }
        }
    }
}
?>
<div id="box-contatos">
	<div class="box-azul" style="background-color: <?php echo $corParametros; ?> !important;">
		<p class="suport">
			<img src="<?php echo $imagemPerfil; ?>" class="ico-image" style="width:30px;height:30px;display:inline-block;" onerror="this.src='img/ico-addcontato.svg'"> <span class="title" style="font-size:1rem">Lista de contatos (<span id="qtdeContatos"></span>)</span>
			<span class="voltar"></span>
		</p>
		<a id="aModalEtiquetas" href="javascript:;" onclick="abrirModal('#modalEtiqueta');" class="uk-bottom uk-bottom-line" style="position:relative;top:-10px;color:<?php echo $corParametros; ?> !important;">Etiquetas</a>
		<a id="aModalContatosBoxContatos" href="javascript:;" onclick="abrirModal('#modalContato')" class="uk-bottom uk-bottom-line" style="position:relative;top:-10px;color:<?php echo $corParametros; ?> !important;">Adicionar Contato</a>
	</div>
	<div class="card-geral">
		<div tabindex="-1" class="_3CPl4">
			<div class="gQzdc" style="display:flex;height:85px;">
				<div style="margin-left: 12px;">
					
					<label style="float:left">
				   	   <select class="uk-select" id="etiqueta" style="width:100px">		
					      <option value="0" style="background-color:#FFFFFF">TODAS</option>				   
						  <?php
							$menus = mysqli_query($conexao, "SELECT * FROM tbetiquetas");

							while ($ListarDepartamentos = mysqli_fetch_array($menus)){
								echo '<option value="'.$ListarDepartamentos["cor"].'" style="background-color:'.$ListarDepartamentos["cor"].'">   </option>';
							}
							?>     
							
					</select> 

					</label>
					<label style="float:left">
				   	   <select class="uk-select" id="tipo_pesquisa" style="width:200px">		
					      <option value="0">NOME</option>				   
						  <option value="1">TELEFONE</option>
						  <option value="2">CPF/CNPJ</option>
						  <option value="3">RAZÃO SOCIAL</option>
					</select> 
					</label><br>
					<label style="float:left">					
						<input type="text"  style="width:300px" id="pesquisaContato" name="pesquisaContato" class="uk-input" data-tab="2" placeholder="Pesquisar por contato">
				
					</label>
					<label style="float:left;margin-top:5px;margin-left:-25px">					
					    <button>
						<div class="_1M3wR _3M2St">
							<span data-icon="search">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
									<path fill="#263238" fill-opacity=".3" d="M15.009 13.805h-.636l-.22-.219a5.184 5.184 0 0 0 1.256-3.386 5.207 5.207 0 1 0-5.207 5.208 5.183 5.183 0 0 0 3.385-1.255l.221.22v.635l4.004 3.999 1.194-1.195-3.997-4.007zm-4.808 0a3.605 3.605 0 1 1 0-7.21 3.605 3.605 0 0 1 0 7.21z"></path>
								</svg>
							</span>
						</div>
					</button>
					
					</label>
					
				</div>
			</div>
		</div>
		
		<div id="ListaViewContatos" style="height:77vh; overflow: auto; background-color:#FFF;">
			<!-- Lista de Contatos -->
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		// Carregamento das Modais //
			// Modal Contato //
			$("#aModalContatosBoxContatos").on("click", function() {
				$.ajax("cadastros/contatos/index.php").done(function(data) {
					$('#modalContato').html(data);
				});
			});
		// FIM Carregamento das Modais //
		$("#etiqueta").change(function(){
			$("#etiqueta").css("background-color",$(this).val());

		})

		$("#etiqueta").change(function(){
			$("#etiqueta").css("background-color",$(this).val());

		})

		$("#etiqueta, #tipo_pesquisa").change(function(){
			var tipo_pesquisa = $("#tipo_pesquisa option:selected").val();
					if (tipo_pesquisa==0 || tipo_pesquisa==3 ){
						if (typeof $.fn.unmask === 'function') {
							$('#pesquisaContato').unmask();
						}
					}else{
						// Restaurar plugins e aplicar mask com segurança
						if (typeof window.restaurarPlugins === 'function') {
							window.restaurarPlugins();
						}
						if (typeof $.fn.mask === 'function') {
							$("#pesquisaContato").mask("99999999999999");
						} else {
							console.warn("jQuery Mask não está disponível em boxContatos");
						}
					}					
					$("#pesquisaContato").focus();
                });
		
	});
</script>