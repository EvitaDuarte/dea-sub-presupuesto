<?php
// _______________________________________________________________________________________________________
// function generarReportePDF(
//     string $archivoSalidaPdf,
//     array $titulosReporte,             // Ej: ["Reporte General", "Sucursal X"]
//     array $titulosEncabezados,         // Ej: ["ID", "Nombre", "Monto", "Fecha"]
//     array $detalleMovimientos,         // Arreglo de arreglos
//     string $orientacion = 'P',         // P = vertical (portrait), L = horizontal (landscape)
//     array $anchosColumnas = []         // Opcional, Ej: ["10%", "40%", "25%", "25%"]
// ) {
//     require_once __DIR__ . "/vendor/autoload.php";

//     // Inicializar mPDF con tamaño carta
//     $mpdf = new \Mpdf\Mpdf([
//         'format' => 'Letter',
//         'orientation' => $orientacion
//     ]);

//     // ==== Construir encabezado superior (header) ====
//     $titulosPrin = ["Dirección Ejecutiva de Administración","Subdirección de Presupuesto"];
//     //$titulosPrin = array_merge($titulosPrin, $titulosReporte);
//     $tituloCentral = "";
//     foreach ($titulosPrin as $t) {
//         $tituloCentral .= "<div style='text-align:center; font-size:14px; font-weight:bold;'>$t</div>";
//     }
//     foreach ($titulosReporte as $t) {
//         $tituloCentral .= "<div style='text-align:center; font-size:14px; font-weight:bold;'>$t</div>";
//     }

//     $fecha = date("d/m/Y");
//     $hora  = date("H:i:s");

//     $headerHTML = "
//         <table width='100%' style='border-bottom:1px solid #000; font-size:10px;'>
//             <tr>
//                 <td style='text-align:center;' rowspan='3'>
//                     $tituloCentral
//                 </td>
//                 <td style='text-align:right;'>Fecha : $fecha</td>
//             </tr>
//             <tr>
//                 <td style='text-align:right;'>Hora : $hora</td>
//             </tr>
//             <tr>
//                 <td style='text-align:right;'>Página: {PAGENO} de {nbpg}</td>
//             </tr>
//         </table>
//     ";

//     $mpdf->SetHTMLHeader($headerHTML);

//     // ==== Encabezado de tabla, se repetirá en cada página ====
//     $headerTabla = "<thead><tr>";

//     foreach ($titulosEncabezados as $i => $campo) {
//         $ancho = $anchosColumnas[$i] ?? ""; // si viene vacío mPDF distribuye
//         $headerTabla .= "<th style='border:1px solid #000; padding:4px;' width='$ancho'>$campo</th>";
//     }

//     $headerTabla .= "</tr></thead>";

//     // ==== Construcción del detalle ====
//     $bodyTabla = "<tbody>";

//     foreach ($detalleMovimientos as $fila) {
//         $bodyTabla .= "<tr>";
//         foreach ($fila as $col) {
//             $bodyTabla .= "<td style='border:1px solid #000; padding:4px;'>$col</td>";
//         }
//         $bodyTabla .= "</tr>";
//     }

//     $bodyTabla .= "</tbody>";

//     // ==== Ensamblar la tabla completa ====
//     $tabla = "
//         <table width='100%' style='border-collapse:collapse; font-size:10px;' autosize='1'>
//             $headerTabla
//             $bodyTabla
//         </table>
//     ";

//     $mpdf->WriteHTML($tabla);
//     $mpdf->Output($archivoSalidaPdf, \Mpdf\Output\Destination::FILE);
// }
// ___________________________________________________________________________________________________________

?>