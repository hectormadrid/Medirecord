<?php
require '../../vendor/autoload.php';

use Dompdf\Dompdf;

// Inicializar Dompdf
$dompdf = new Dompdf();

// Inicializar variables
$data = [];

session_start();
if (isset($_SESSION['PDFdata']) && !empty($_SESSION['PDFdata'])) {
    $jsonData = $_SESSION['PDFdata'];
    error_log('JSON recibido: ' . $jsonData); // Registrar el JSON recibido
    $decodedData = json_decode($jsonData, true);
    
    if (json_last_error() === JSON_ERROR_NONE) {
        $data = $decodedData;
    } else {
        error_log('Error al decodificar JSON: ' . json_last_error_msg());
    }
    
}

// Generar contenido HTML de la tabla con datos dinámicos
$html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Datos de la Tabla</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        thead { background-color: #007bff; color: #fff; }
        tbody { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>Codigo</th>
                <th>Número</th>
                <th>Nombre</th>
                <th>Rut</th>
                <th>Día</th>
                <th>Hora</th>
            </tr>
        </thead>
        <tbody>';

if (!empty($data)) {
    foreach ($data as $row) {
        $html .= '
            <tr>
                <td>' . htmlspecialchars($row[0]) . '</td>
                <td>' . htmlspecialchars($row[1]) . '</td>
                <td>' . htmlspecialchars($row[2]) . '</td>
                <td>' . htmlspecialchars($row[3]) . '</td>
                <td>' . htmlspecialchars($row[4]) . '</td>
                <td>' . htmlspecialchars($row[5]) . '</td>
            </tr>';
    }
} else {
    $html .= '
            <tr>
                <td colspan="6">No hay datos disponibles</td> 
            </tr>';
}

$html .= '
        </tbody>
    </table>
</body>
</html>
';

// Cargar contenido HTML
$dompdf->loadHtml($html);

// Configurar opciones de papel y orientación
$dompdf->setPaper('A4', 'landscape');

// Renderizar el HTML como PDF
$dompdf->render();

// Salida del PDF generado al navegador (descargar o ver en línea)
$dompdf->stream("tabla_datos.pdf", array("Attachment" => 0));
?>
