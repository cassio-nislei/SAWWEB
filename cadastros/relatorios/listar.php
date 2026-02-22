<?php 
    require_once("../../includes/padrao.inc.php");

	$dtIni = isset($_GET["dtIni"]) ? reverseDate($_GET["dtIni"]) : null;
    $dtFim = isset($_GET["dtFim"]) ? reverseDate($_GET["dtFim"]) : null;
    $atendente = isset($_GET["atendente"]) ? $_GET["atendente"] : "0";
    $numero = (isset($_GET["numero"]) && !empty($_GET['numero'])) ? SomenteNumero($_GET['numero']) : null;
    $ddi = $numero !== null ? substr($numero, 0, 2) : "";
    $ddd = $numero !== null ? substr($numero, 2, 2) : "";
    $situacao = (isset($_GET["situacao"]) && !empty($_GET["situacao"])) ? $_GET["situacao"] : null;
    $ordenacao = isset($_GET["ordenacao"]) ? $_GET["ordenacao"] : "ASC";
    $fotoPerfil = fotoPerfil;
    
    // Validação do DDD //
    /* Essa validação é necessária pois o Whats não reconhece os 9 dígitos para os DDD maiores de 30 */
    if( intval($ddd) > 30 ){
        $numero = $ddi . $ddd . substr($numero, 5, 8);
    }

    // Montando a Query de Pesquisa //
        $sql = "SELECT tba.numero, tba.dt_atend, tba.hr_atend, tba.nome, tbu.nome AS atendente, tbd.departamento, tfp.foto AS foto_perfil 
                    FROM tbatendimento tba
                        LEFT JOIN tbusuario tbu ON(tba.id_atend=tbu.id)
                        LEFT JOIN tbdepartamentos tbd ON(tba.setor=tbd.id)
                        LEFT JOIN tbfotoperfil tfp ON tfp.numero = tba.numero
                            WHERE dt_atend BETWEEN '".$dtIni."' AND '".$dtFim."'";

        // Atendente //
        if( $atendente !== "0" ){ $sql .= " AND tbu.id = " . $atendente; }

        // Número //
        if( $numero !== null ){ $sql .= " AND tba.numero = '" . $numero . "'"; }

        // Situação //
        if( $situacao !== null ){ $sql .= " AND tba.situacao = '" . $situacao . "'"; }

        // Agrupamento //
        $sql .= " GROUP BY tba.numero, tba.dt_atend, tba.hr_atend, tba.nome, tbu.nome, tbd.departamento, tfp.foto";

        // Ordenação //
        $sql .= " ORDER BY dt_atend " . $ordenacao . ", hr_atend " . $ordenacao;
    // Montando a Query de Pesquisa //

    $dados = mysqli_query($conexao, $sql)
        or die(mysqli_error($conexao));
?>

<?php
    if( mysqli_num_rows($dados) ){
        while( $registros = mysqli_fetch_object($dados) ){
            $nomeCliente = limpaNome($registros->nome);

            // Pego a foto de perfil //
            if( $_SESSION["parametros"]["exibe_foto_perfil"] ){
                $fotoPerfil = getFotoPerfil($conexao, $registros->numero);
            }
            else{ $fotoPerfil = fotoPerfil; }
?>''
    <tr data-numero="<?php echo $registros->numero; ?>" data-nome="<?php echo $nomeCliente; ?>">
        <td><?php echo Mask($registros->numero); ?></td>
        <td>
            <div class="_1WliW" style="width: 35px; float: left; margin: -10px 15px -10px 0px;">
                <img src="<?php echo $fotoPerfil; ?>" class="rounded-circle user_img">
            </div>
            <?php echo $nomeCliente; ?>
        </td>
        <td><?php echo reverseDate($registros->dt_atend,"-"); ?> <?php echo $registros->hr_atend; ?></td>
        <td><?php echo $registros->atendente; ?></td>
        <td><button type="button" title="Editar" class="abrir-historico"><i class="fas fa-eye"></i></button></td>
    </tr>
<?php
        }
    }
    else{
?>
    <tr>
        <td colspan="5">Nenhum registro encontrado!</td>
    </tr>
<?php   
    }
?>

<script>
    $(document).ready(function() {
        // Valida se Habilita a opção de Histórico de Conversas para os Operadores //
        $(".abrir-historico").on("click", function() {
            var numero = $(this).parent().parent().data('numero');
            var nome = $(this).parent().parent().data('nome');

            if( $("#perfilUsuario").val() == 1
                && $("#historicoConversas").val() == 0 ){
                mostraDialogo("Atenção: Apenas Administradores pode visualizar o Histórico de Conversas!", "danger", 3000);
            }
            else{
                // Fechando a Modal //
		        $('#modalRelatorio').attr('style', 'display: none');

                abrirModal('#modalHistorico');

                $.ajax("atendimento/historico.php?numero="+numero+"&nome="+nome).done(function(data) {
                    $('#HistoricoAberto').html(data);
                });
            }
        });
    });
</script>