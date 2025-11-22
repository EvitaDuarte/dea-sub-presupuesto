<?php

<?php
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class ReportePDF{
    private Mpdf $mpdf;
    private array $titulosReporte;
    private array $titulosEncabezados;
    private array $detalleMovimientos;
    private array $anchosColumnas;
    private ?string $logo;

    public function __construct(
        array $titulosReporte,
        array $titulosEncabezados,
        array $detalleMovimientos,
        string $orientacion = 'P',   // P = vertical, L = horizontal
        array $anchosColumnas = [],
        ?string $logo = null
    ) {
        $this->titulosReporte = $titulosReporte;
        $this->titulosEncabezados = $titulosEncabezados;
        $this->detalleMovimientos = $detalleMovimientos;
        $this->anchosColumnas = $anchosColumnas;
        $this->logo = $logo;

        // Crear mPDF
        $this->mpdf = new Mpdf([
            'format' => 'Letter',
            'orientation' => $orientacion,
            'margin_top' => 40, // espacio para header
        ]);

        $this->configurarHeader();
        $this->cargarCSSInterno();
    }

    /**
     * Header repetido en cada página
     */
    private function configurarHeader(){
        $fecha = date("d/m/Y");
        $hora  = date("H:i:s");

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
            </table>
        ";

        $this->mpdf->SetHTMLHeader($header);
    }

    /**
     * CSS interno definido dentro de la clase
     */
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

            .titulo-reporte {
                font-size: 14px;
                font-weight: bold;
            }

            .header-datos {
                width: 30%;
                text-align: right;
                font-size: 10px;
                vertical-align: top;
            }

            /* TABLAS DEL REPORTE */
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

            /* Alternancia de filas */
            .fila-par {
                background-color: #f7f7f7;
            }
        ";

        $this->mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
    }

    private function construirTabla(): string{
        // Encabezados
        $thead = "<thead><tr>";
        foreach ($this->titulosEncabezados as $i => $campo) {
            $ancho = $this->anchosColumnas[$i] ?? "";
            $thead .= "<th width='$ancho'>$campo</th>";
        }
        $thead .= "</tr></thead>";

        // Cuerpo del detalle
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

    /**
     * Generar el PDF final en un archivo
     */
    public function generar(string $archivoSalidaPdf)
    {
        $tabla = $this->construirTabla();
        $this->mpdf->WriteHTML($tabla);
        $this->mpdf->Output($archivoSalidaPdf, Destination::FILE);
    }
}


/*
require_once "vendor/autoload.php";
require_once "ReportePDF.php";

$titulos = [
    "Reporte General de Ventas",
    "Sucursal Monterrey"
];

$encabezados = ["ID", "Producto", "Cantidad", "Precio", "Fecha"];

$detalle = [
    [1, "Mouse", 3, "$450", "2025-01-02"],
    [2, "Teclado", 1, "$890", "2025-01-03"],
    [3, "Monitor", 2, "$3,900", "2025-01-05"],
];

$anchos = ["10%", "40%", "10%", "20%", "20%"];

$reporte = new ReportePDF(
    $titulos,
    $encabezados,
    $detalle,
    "P",
    $anchos,
    "logo.png"
);

$reporte->generar("reporte.pdf");


*/

?>