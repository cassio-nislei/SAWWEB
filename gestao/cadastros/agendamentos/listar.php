<?php require_once("../../../includes/padrao.inc.php"); ?>

<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover" id="grid1">
        <thead>
            <tr>
                <th>Titulo</th>
                <th>C</th>
                <th>Anexo</th>
                <th>Mensagem</th>
                <th>data</th>
                <th>hora</th>
                <th>Env.</th>
                <th>Ações</th> 
            </tr>
        </thead>
        <tbody>
            <?php
            $l = 1;

            $agendamentocsvs = mysqli_query(
                $conexao,
                "SELECT tr.id, tr.titulo, tr.msg , tr.canal , tr.data_agendada, tr.hora_agendada, tr.arquivocsv, tr.enviado
                    FROM tbmsgagendadasawcsv tr 
                    ORDER BY id"
            );

            while ($Listaagendamento = mysqli_fetch_array($agendamentocsvs)) {
                $Titulos = "Sem Titulo";
                if ($Listaagendamento["titulo"] != "") {
                    $Titulos = $Listaagendamento["titulo"];
                }

                $MSGs = "Olá uma novidade para você !!!";
                if ($Listaagendamento["msg"] != "") {
                    $MSGs = $Listaagendamento["msg"];
                }

                $UsaArquivo = "Sem anexo";
                if ($Listaagendamento["arquivocsv"] != "") {
                    $UsaArquivo = $Listaagendamento["arquivocsv"];
                }
                $confirma = "N";
                if ($Listaagendamento["enviado"] > 0) {
                    $confirma = "S";
                }

                $dataFormatada = date('d/m/y', strtotime($Listaagendamento["data_agendada"]));
                $horaFormatada = date('H:i', strtotime($Listaagendamento["hora_agendada"]));

                echo '<tr id="linha' . $l . '">';
                echo '<td><input type="hidden" name="IdAgendamentos" class="IdAgendamentos" value="' . $Listaagendamento["id"] . '" />' . $Listaagendamento["titulo"] . '</td>';
                echo '<td>' . $Listaagendamento["canal"] . '</td>';
                echo '<td>' . $UsaArquivo . '</td>';
                echo '<td>' . $MSGs . '</td>';
                
                
                echo '<td>' . $dataFormatada . '</td>';
                echo '<td>' . $horaFormatada . '</td>';
                echo '<td>' . $confirma . '</td>';
                echo '<td>
                        <button class="btn btn-danger ConfirmaExclusao" title="Excluir"><i class="fa fa-trash" aria-hidden="true"></i></button>
                        <button class="btn btn-success botaoAlterar" title="Editar"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                      </td></tr>';
                $l = $l + 1;
            }
            ?>
            <tr>

            </tr>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function () {
        $('.ConfirmaExclusao').on('click', function () {
            var id = $(this).closest("tr").find('.IdAgendamentos').val();

            // Aqui substituí a chamada para a função ConfirmarDados por um modal de confirmação simples
            var confirmacao = confirm('Deseja realmente remover este agendamento?');
            if (confirmacao) {
                $.post("cadastros/agendamentos/excluir.php", {
                    IdAgendamentos: id
                }, function (resultado) {
                    var mensagem = "<strong>Agendamento removido com sucesso!</strong>";
                    var mensagem2 = 'Falha ao remover agendamento!';

                    if (resultado == 1) {
                        mostraDialogo(mensagem2, "warning", 2500);
                    } else if (resultado == 2) {
                        mostraDialogo(mensagem, "success", 2500);
                        $("#btnCancelar").click();
                        $.ajax("cadastros/agendamentos/listar.php").done(function (data) {
                            $('#Listar').html(data);
                        });

                    } else {
                        mostraDialogo(mensagem2, "danger", 2500);
                    }
                });
            }
        });

        $('.botaoAlterar').on('click', function () {
            var id = $(this).closest("tr").find('.IdAgendamentos').val();

            $("#btnCancelar").css({
                "visibility": "visible"
            });

            // Alterando Displays //
            $("#gravaDepartamento").css("display", "block");

            $.getJSON('cadastros/agendamentos/carregardados.php?codigo=' + id, function (registro) {
                // Ajuste os campos abaixo conforme necessário
                $("#menu_resposta").val(registro.id);
                $("#menu_acao").val(registro.acao);
                $("#agendamento").val(registro.titulo);
                $("#agendamento").trigger('blur');

                // Ajuste do arquivo
                if (registro.datas != null && registro.datas != "") {
                    $("#arquivo_carregado").html("Arquivo: " + registro.datas);
                    $("#arquivo_carregado").css({
                        'color': 'red',
                        'font-size': '150%'
                    });
                } else {
                    $("#arquivo_carregado").html("Não existe um arquivo carregado");
                    $("#arquivo_carregado").css({
                        'color': 'black',
                        'font-size': '150%'
                    });
                }
                $("#foto").val('');

                // Mude a Ação para Alterar    
                $("#acao").val("2");
                $("#menu_resposta").focus();
            });
        });
    });
</script>
