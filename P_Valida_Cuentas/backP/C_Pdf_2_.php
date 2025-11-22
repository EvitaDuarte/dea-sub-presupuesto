<?php
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class ReportePDFConCSS{

    private Mpdf $mpdf;
    private array $titulosReporte;
    private array $titulosEncabezados;
    private array $detalleMovimientos;
    private array $anchosColumnas;
    private ?string $logo;
    private string $filtro;
// _____________________________________________________________
    public function __construct(
        array $titulosReporte,
        array $titulosEncabezados,
        array $detalleMovimientos,
        string $orientacion = 'P',   // P = vertical, L = horizontal
        array $anchosColumnas = [],
        string $filtro = "",
        ?string $logo = null,
        
    ) {
        $this->titulosReporte       = $titulosReporte;
        $this->titulosEncabezados   = $titulosEncabezados;
        $this->detalleMovimientos   = $detalleMovimientos;
        $this->anchosColumnas       = $anchosColumnas;
        $this->logo                 = $logo;
        $this->filtro               = $filtro; // línea titulo secundario

        // Crear mPDF
        $this->mpdf = new Mpdf([
            'format' => 'Letter',
            'orientation' => $orientacion,
            'margin_top' => 40, // espacio para header
        ]);

        $this->configurarHeader();
        $this->cargarCSSInterno();
    }
// _____________________________________________________________
    private function configurarHeader(){
        $fecha      = date("d/m/Y");
        $hora       = date("H:i:s");
        $trFiltro   = "";

        // Títulos centrados
        $tituloCentral = "";
        foreach ($this->titulosReporte as $t) {
            $tituloCentral .= "<div class='titulo-reporte'>$t</div>";
        }

        // Logo fijo arriba-izquierda
        $logoHTML = "";
        if ($this->logo !== null && file_exists($this->logo)) {
            $logoHTML = "<img src='{$this->logo}' class='logo-header'>";
        }
        if ($this->filtro!=""){
            $this->filtro = str_ireplace( [" AND ", " OR ", " BETWEEN ", " LIKE "], [" y ", " o ", " entre ", " parecido "],  $this->filtro );
            $trFiltro = "<tr><td colspan='3' class='header-filtro'> " . $this->filtro . "</td></tr>";
        }
        $header = "
            <table class='tabla-header'>
                <tr>
                    <td class='header-logo'>$logoHTML</td>

                    <td class='header-titulos'>$tituloCentral</td>

                    <td class='header-datos'>
                        Fecha: $fecha<br>
                        Hora: $hora<br>
                        Página: {PAGENO} de {nbpg}
                    </td>
                </tr>
                $trFiltro
            </table>
        ";

        $this->mpdf->SetHTMLHeader($header);
    }
// _____________________________________________________________
    private function cargarCSSInterno(){
        $css = "
            /* HEADER */
            .tabla-header {
                width: 100%;
                border-bottom: 1px solid #000;
                font-size: 11px;
            }

            .header-logo {
                width: 15%;
                text-align: left;
                vertical-align: top;
            }

            .logo-header {
                width: 70px;
                height: auto;
            }

            .header-titulos {
                width: 55%;
                text-align: center;
                vertical-align: middle;
            }

            .header-filtro {
                text-align: center;
                font-weight: bold;
                padding-top: 5px;
                font-size: 9px;
            }

            .titulo-reporte {
                font-size: 13px;
                font-weight: bold;
            }

            .header-datos {
                width: 30%;
                text-align: right;
                font-size: 10px;
                vertical-align: top;
            }

            /* TABLAS */
            table.reporte {
                width: 100%;
                border-collapse: collapse;
                font-size: 10px;
                margin-top: 10px;
            }

            table.reporte th {
                border: 1px solid #000;
                background: #eaeaea;
                padding: 5px;
                font-weight: bold;
                text-align: center;
            }

            table.reporte td {
                border: 1px solid #000;
                padding: 5px;
            }

            .fila-par {
                background-color: #f7f7f7;
            }
        ";

        $this->mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
    }
// _____________________________________________________________
    private function construirTabla(): string{
        // Encabezados
        $thead = "<thead><tr>";
        foreach ($this->titulosEncabezados as $i => $campo) {
            $ancho = $this->anchosColumnas[$i] ?? "";
            $thead .= "<th width='$ancho'>$campo</th>";
        }
        $thead .= "</tr></thead>";

        // Detalle
        $tbody = "<tbody>";
        $par = false;

        foreach ($this->detalleMovimientos as $fila) {
            $clase = $par ? "fila-par" : "";
            $par = !$par;

            $tbody .= "<tr class='$clase'>";
            foreach ($fila as $col) {
                $tbody .= "<td>$col</td>";
            }
            $tbody .= "</tr>";
        }

        $tbody .= "</tbody>";

        return "
            <table class='reporte' autosize='1'>
                $thead
                $tbody
            </table>
        ";
    }
// _____________________________________________________________
    public function generar(string $archivoSalidaPdf){
        $tabla = $this->construirTabla();
        $this->mpdf->WriteHTML($tabla);
        $this->mpdf->Output( "../salidas/" .$archivoSalidaPdf, Destination::FILE);
    }
// _____________________________________________________________
}
/* Ejemplo de uso

require_once "vendor/autoload.php";
require_once "C_Pdf_2_.php";

$titulos = ["Reporte General", "Sucursal Centro"];

$encabezados = ["ID", "Descripción", "Monto", "Fecha"];

$detalle = [
    [1, "Venta A", "$150", "2025-01-10"],
    [2, "Venta B", "$300", "2025-01-11"],
    [3, "Venta C", "$450", "2025-01-11"],
];

$anchos = ["10%", "50%", "20%", "20%"];

$reporte = new ReportePDFConCSS(
    $titulos,
    $encabezados,
    $detalle,
    "P",
    $anchos,
    "logo.png"
);

$reporte->generar("reporte_css.pdf");


*/
?>