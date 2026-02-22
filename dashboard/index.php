<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
	<meta name="generator" content="Jekyll v3.8.6">
	<title>Dashboard</title>
	<link rel="stylesheet" href="../css/bootstrap.min.css">
	<link rel="stylesheet" href="../css/jquery-ui.min.css">
	<link href="css/style.css" rel="stylesheet">
	<script src="../js/jquery-3.6.0.min.js"></script>
	<script src="../js/jquery-ui.min.js"></script>
</head>

<body>
	<nav class="navbar navBarBackground justify-content-center">
		<a class="navbar-brand navoptions" href="#">Informações sobre os atendimentos</a>
	</nav>

	<div class="container-fluid" id="dashBoard">
		<div class="d-none d-sm-block d-flex justify-content-center">
			Opções de Filtro
			<div class="form-group row">
				<div class="col">
					<span for="example-date-input" class="col-2 col-form-label">Data inicial</span>
					<div class="col-10">
						<input class="form-control" type="text" id="de">
					</div>
				</div>
				<div class="col">
					<span for="example-date-input" class="col-2 col-form-label">Data Final</span>
					<div class="col-10">
						<input class="form-control" type="text" value="<?php echo date('d/m/Y'); ?>" id="ate">
					</div>
				</div>
				<div class="col">

					<div class="col-10">
						<input type="checkbox" id="cbAtendimentos" name="cbAtendimentos"> Mostrar Detalhes
					</div>
				</div>
			</div>
		</div>

		<div class="row" id="dashBoard">
			<!-- dashboard Mobile  -->
			<main role="main" class="col-md-12 ml-sm-auto col-lg-12 px-4" id="dados"></main>
		</div>
	</div>

	<script>
		$(document).ready(function() {
			$("#de, #ate").datepicker({
				dateFormat: 'dd/mm/yy',
				closeText: "Fechar",
				prevText: "&#x3C;Anterior",
				nextText: "Próximo&#x3E;",
				currentText: "Hoje",
				monthNames: ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"],
				monthNamesShort: ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"],
				dayNames: ["Domingo", "Segunda-feira", "Terça-feira", "Quarta-feira", "Quinta-feira", "Sexta-feira", "Sábado"],
				dayNamesShort: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb"],
				dayNamesMin: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb"],
				weekHeader: "Sm",
				firstDay: 1
			});

			var now = new Date;
			var mes = now.getMonth() + 1;
			var dtAtual = now.getDate() + '/' + mes + '/' + now.getFullYear();

			// Carrega os Campos com a Data Atual //
			$("#de").datepicker("setDate", dtAtual);
			$("#ate").datepicker("setDate", dtAtual);

			// Atualiza os Dados //
			function atualizar() {
				var de = $("#de").val(),
					ate = $("#ate").val(),
					mostrarListas = $("#cbAtendimentos").is(':checked');
				$.post("telas/painelr.php", {
					de: de,
					ate: ate,
					mostrar: mostrarListas
				}, function(retorno) { $("#dados").html(retorno); })
			}
			// Fim Atualiza os Dados //

			// Se mudar uma informação destes campos, chama a function atualizar() //
				$("#de, #ate").change(function() { atualizar(); })
				$("input[name=cbAtendimentos]").change(function() { atualizar(); });
			// FIM Se mudar uma informação destes campos, chama a function atualizar() //

			// Carrega os Dados pela Primeira vez //
			atualizar();

			// Carrega os Dados à cada 60 Segundos //
			var intervalAtualizar = setInterval(function() { atualizar(); }, 60000);
		});
	</script>
</body>
</html>