<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
// ____________________________________________________________________________
function generarXLS(string $archivoSalida, array $headers, array $rows): void{
    // Crear objeto hoja de cálculo
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // --- 1) Escribir encabezados ---
    $colLetter = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($colLetter . '1', $header);
        $colLetter++;
    }

    // --- 2) Escribir datos ---
    $rowNumber = 2; // Comienza debajo de los encabezados
    foreach ($rows as $row) {
        $colLetter = 'A';
        foreach ($row as $value) {
            $sheet->setCellValue($colLetter . $rowNumber, $value);
            $colLetter++;
        }
        $rowNumber++;
    }

    // --- 3) Guardar archivo ---
    $writer = new Xls($spreadsheet);
    $writer->save("../salidas/".$archivoSalida);
}

// ____________________________________________________________________________
// function descargarXLS(string $filename, array $headers, array $rows): void { // Lo descarga en el navagador pero con caracteres raros
//     $spreadsheet = new Spreadsheet();
//     $sheet = $spreadsheet->getActiveSheet();

//     // Encabezados
//     $colLetter = 'A';
//     foreach ($headers as $header) {
//         $sheet->setCellValue($colLetter . '1', $header);
//         $colLetter++;
//     }

//     // Datos
//     $rowNum = 2;
//     foreach ($rows as $row) {
//         $colLetter = 'A';
//         foreach ($row as $value) {
//             $sheet->setCellValue($colLetter . $rowNum, $value);
//             $colLetter++;
//         }
//         $rowNum++;
//     }

//     // Limpiar buffer antes de enviar
//     if (ob_get_length()) {
//         ob_end_clean();
//     }

//     // Encabezados HTTP
//     header("Content-Type: application/vnd.ms-excel");
//     header("Content-Disposition: attachment; filename=\"{$filename}\"");
//     header("Cache-Control: max-age=0");

//     $writer = new Xls($spreadsheet);
//     $writer->save("php://output");
//     exit;
// }
?>