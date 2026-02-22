<?php
    require_once("../../includes/padrao.inc.php");
    require_once("../../includes/dompdf/autoload.inc.php");
    require_once("../../includes/simplexlsxgen/src/SimpleXLSXGen.php");
    use Dompdf\Dompdf;

    $deFormatado   = date("d/m/Y", strtotime(str_replace('/', '-', $_GET["de"])));
    $ateFormatado  = date("d/m/Y", strtotime(str_replace('/', '-', $_GET["ate"])));
    $de   = date("Y-m-d", strtotime(str_replace('/', '-', $_GET["de"])));
    $ate  = date("Y-m-d", strtotime(str_replace('/', '-', $_GET["ate"])));
    $type = $_GET["type"];
    $emAndamentoTotais = 0;
    $atendidosTotais = 0;
    $transferidosTotais = 0;
    $totaisTotais = 0;
    $fileName = "relatorio_atendimentos_por_operador_".date("dmY_His").".".$type;
    $strPeriodo = "Período: ".$deFormatado." à ".$ateFormatado;
    $extraidoEm = "Relatório extraído em ".date("d/m/Y H:i:s");
    $tituloPrincipal = "RELATÓRIO DE ATENDIMENTOS POR OPERADOR";
    $tituloAbertos = "ATENDIMENTOS [EM ANDAMENTO] POR OPERADOR";
    $tituloFinalizados = "ATENDIMENTOS [FINALIZADOS] POR OPERADOR";
    $arrAtendimentosAbertos = array();
    $arrAtendimentosFinalizados = array();

    // Buscando os Atendimentos [EM ABERTO] //
        $sqlAbertos = "SELECT p.NOME as OPERADOR, count(1) AS ATENDIMENTOS
                            FROM tbatendimento c
                                INNER JOIN tbusuario p ON c.id_atend = p.id WHERE c.situacao = 'A' GROUP BY p.nome";
        $qryAbertos = mysqli_query($conexao, $sqlAbertos);

        while( $arrAbertos = mysqli_fetch_assoc($qryAbertos) ){
            $emAndamentoTotais += intval($arrAbertos['ATENDIMENTOS']);
            $arrAtendimentosAbertos[] = $arrAbertos;
        }

        $arrAtendimentosAbertos[] = array( "OPERADOR" => "", "ATENDIMENTOS" => $emAndamentoTotais);
    // FIM Buscando os Atendimentos [EM ABERTO] //

    // Buscando os Atendimentos [FINALIZADOS] //
        $sqlFinalizados = "SELECT p.NOME as OPERADOR , count(1) AS TOTAIS
                            , (SELECT count(1) FROM tbatendimento tba WHERE tba.id_atend = c.id_atend AND tba.finalizado_por = 'Transferencia') AS TRANSFERENCIAS 
                            FROM tbatendimento c
                                INNER JOIN tbusuario p ON c.id_atend = p.id 
                                    WHERE c.situacao = 'F' AND c.dt_atend BETWEEN '" . $de . "' AND '" . $ate . "'
                                        GROUP BY p.id";
        $qryFinalizados = mysqli_query($conexao, $sqlFinalizados);

        while( $arrFinalizados = mysqli_fetch_assoc($qryFinalizados)){
            $atendidosOperador = intval($arrFinalizados['TOTAIS'])-intval($arrFinalizados['TRANSFERENCIAS']);
            $arrFinalizados["ATENDIDOS"] = intval($atendidosOperador);
            $atendidosTotais += intval($atendidosOperador);
            $transferidosTotais += intval($arrFinalizados['TRANSFERENCIAS']);
            $totaisTotais += intval($arrFinalizados['TOTAIS']);
            $arrAtendimentosFinalizados[] = array( 
                "OPERADOR" => $arrFinalizados["OPERADOR"]
                , "ATENDIDOS" => $arrFinalizados["ATENDIDOS"]
                , "TRANSFERENCIAS" => $arrFinalizados["TRANSFERENCIAS"]
                , "TOTAIS" => $arrFinalizados["TOTAIS"]
            );
        }

        $arrAtendimentosFinalizados[] = array( "OPERADOR" => "", "ATENDIDOS" => $atendidosTotais, "TRANSFERENCIAS" => $transferidosTotais, "TOTAIS" => $totaisTotais);
    // FIM Buscando os Atendimentos [FINALIZADOS] //

    // Define o Tipo de arquivo para Exportação //
        if( $type === "pdf" ){
            $iTotalAtendimentosAbertos = count($arrAtendimentosAbertos);
            $iTotalAtendimentosFinalizados = count($arrAtendimentosFinalizados);

            // Montando os dados dos Atendimentos [EM ABERTO]//
                $html = '<table width="100%">
                            <tr>
                                <td colspan="2">'.$strPeriodo.'</td>
                                <td colspan="3" align="right"><i>'.$extraidoEm.'</i></td>
                            </tr>
                            <tr><td colspan="5">&nbsp;</td></tr>
                            <tr><td colspan="5" align="center">'.$tituloPrincipal.'</td></tr>
                            <tr><td colspan="5">&nbsp;</td></tr>
                            <tr><td colspan="5" style="border-bottom: 1px solid red">'.$tituloAbertos.'</td></tr>
                            <tr>
                                <td width="80%" colspan="4" style="border-bottom: 1px solid #000">OPERADOR</td>
                                <td width="20%" style="border-bottom: 1px solid #000" align="center">ATENDIMENTOS</td>
                            </tr>';

                for( $i = 0; $i < $iTotalAtendimentosAbertos; $i++ ){
                    if( $i === intval($iTotalAtendimentosAbertos-1) ){
                        $withPrimaryCss = '';
                        $withSecondCss = ' style="border-bottom: 1px solid red"';
                    }
                    else{
                        $withPrimaryCss = ' style="border-bottom: 1px solid #ccc"';
                        $withSecondCss = $withPrimaryCss;
                    }
    
                    $html .= '<tr>
                                <td'.$withPrimaryCss.' colspan="4">'.$arrAtendimentosAbertos[$i]['OPERADOR'].'</td>
                                <td'.$withSecondCss.' align="center">'.$arrAtendimentosAbertos[$i]['ATENDIMENTOS'].'</td>
                            </tr>';
                }
            // FIM Montando os dados dos Atendimentos [EM ABERTO]//

            // Montando os dados dos Atendimentos [FINALIZADOS]//
                $html .= '<table width="100%">
                            <tr><td colspan="4">&nbsp;</td></tr>
                            <tr><td colspan="4" style="border-bottom: 1px solid red">'.$tituloFinalizados.'</td></tr>
                            <tr>
                                <td width="40%" style="border-bottom: 1px solid #000">OPERADOR</td>
                                <td width="20%" style="border-bottom: 1px solid #000" align="center">ATENDIDOS</td>
                                <td width="20%" style="border-bottom: 1px solid #000" align="center">TRANSFERIDOS</td>
                                <td width="20%" style="border-bottom: 1px solid #000" align="center">TOTAIS</td>
                            </tr>';

                for( $i = 0; $i < $iTotalAtendimentosFinalizados; $i++ ){
                    if( $i === intval($iTotalAtendimentosFinalizados-1) ){
                        $withPrimaryCss = '';
                        $withSecondCss = ' style="border-bottom: 1px solid red"';
                    }
                    else{
                        $withPrimaryCss = ' style="border-bottom: 1px solid #ccc"';
                        $withSecondCss = $withPrimaryCss;
                    }

                    $html .= '<tr>
                            <td'.$withPrimaryCss.'>'.$arrAtendimentosFinalizados[$i]['OPERADOR'].'</td>
                            <td'.$withSecondCss.' align="center">'.$arrAtendimentosFinalizados[$i]['ATENDIDOS'].'</td>
                            <td'.$withSecondCss.' align="center">'.$arrAtendimentosFinalizados[$i]['TRANSFERENCIAS'].'</td>
                            <td'.$withSecondCss.' align="center">'.$arrAtendimentosFinalizados[$i]['TOTAIS'].'</td>
                        </tr>';
                }
            // FIM Montando os dados dos Atendimentos [FINALIZADOS]//

            // ***** Gerando o PDF ***** //
                // instantiate and use the dompdf class
                $dompdf = new Dompdf();
                $dompdf->loadHtml($html);

                // (Optional) Setup the paper size and orientation
                $dompdf->setPaper('A4', 'portrait');

                // Render the HTML as PDF
                $dompdf->render();

                // Output the generated PDF to Browser
                $dompdf->stream($fileName);
            // ***** FIM Gerando o PDF ***** //
        }
        else if( $type === "csv" ){
            $filePath = "relatorios/" . $fileName;
            $arrCabecalho = array(
                array()
                , array($strPeriodo,"","",$extraidoEm), array()
                , array("",$tituloPrincipal), array()
                , array($tituloAbertos)
            );
            $arrCabecalhoFinalizados = array(
                array()
                , array($tituloFinalizados)
            );

            ob_start();
            $df = fopen($filePath, 'w');

            // Imprimindo os Cabeçalhos //
                foreach ($arrCabecalho as $row) {
                    fputcsv($df, $row);
                }
            // FIM Imprimindo os Cabeçalhos //

            // Montando os dados dos Atendimentos [EM ABERTO]//
                fputcsv($df, array_keys(reset($arrAtendimentosAbertos)));

                foreach ($arrAtendimentosAbertos as $row) {
                    fputcsv($df, $row);
                }
            // FIM Montando os dados dos Atendimentos [EM ABERTO]//

            // Montando os dados dos Atendimentos [FINALIZADOS]//
                $arrCabecalhoFinalizados[] = array_keys(reset($arrAtendimentosFinalizados));
                
                foreach ($arrCabecalhoFinalizados as $row) {
                    fputcsv($df, $row);
                }

                foreach ($arrAtendimentosFinalizados as $row) {
                    fputcsv($df, $row);
                }
            // FIM Montando os dados dos Atendimentos [FINALIZADOS]//

            fclose($df);
            ob_get_clean();

            // desabilitar cache
            $now = gmdate("D, d M Y H:i:s");
            header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
            header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
            header("Last-Modified: {$now} GMT");

            // forçar download  
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");

            // disposição do texto / codificação
            header("Content-Disposition: attachment;filename=\"$fileName\"");
            header("Content-Transfer-Encoding: binary");
            header('Content-Length: ' . filesize($filePath)); 
            echo readfile($filePath);

            // Excluindo o Arquivo //
            unlink($filePath);
        }
        else if( $type === "xlsx" ){
            // Cabeçalhos //
                $arrDados = array(
                    "0" => array("")
                    , "1" => array($strPeriodo,"","",$extraidoEm)
                    , "2" => array("")
                    , "3" => array("",$tituloPrincipal)
                    , "4" => array("")
                    , "5" => array("",$tituloAbertos)
                    , "7" => array("OPERADOR","ATENDIMENTOS")
                );
            // FIM Cabeçalhos //

            // Atendimentos Abertos //
                foreach( $arrAtendimentosAbertos as $row){
                    $arrDados[] = array($row["OPERADOR"],$row["ATENDIMENTOS"]);
                }
            // FIM Atendimentos Abertos //

            // Atendimentos Finalizados //
                $arrDados[] = array("");
                $arrDados[] = array("",$tituloFinalizados);
                $arrDados[] = array("OPERADOR","ATENDIDOS","TRANSFERENCIAS","TOTAIS");

                foreach( $arrAtendimentosFinalizados as $row){
                    $arrDados[] = array($row["OPERADOR"],$row["ATENDIDOS"],$row["TRANSFERENCIAS"],$row["TOTAIS"]);
                }
            // FIM Atendimentos Finalizados //

            // Gerando o Arquivo e Download //
                $xlsx = SimpleXLSXGen::fromArray($arrDados);
                $xlsx->downloadAs($fileName);
            // FIM Gerando o Arquivo e Download //
        }
    // FIM Define o Tipo de arquivo para Exportação //