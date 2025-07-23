<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

require '../vendor/autoload.php';
require '../db/conexion.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

$desde = $_GET['desde'] ?? null;
$hasta = $_GET['hasta'] ?? null;

if (!$desde || !$hasta) {
    echo "Debes seleccionar un rango de fechas.";
    exit;
}

// Consulta de pedidos
$stmt = $conexion->prepare("SELECT p.id, u.nombre AS cliente, p.total, p.metodo_pago, p.direccion, p.telefono, p.fecha 
                            FROM pedidos p 
                            INNER JOIN usuarios u ON u.id = p.usuario_id 
                            WHERE p.fecha BETWEEN ? AND ? 
                            ORDER BY p.fecha DESC");
$stmt->execute([$desde . " 00:00:00", $hasta . " 23:59:59"]);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Crear Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Pedidos");

// Logo
$logoPath = '../public/img/logo.png'; // Asegúrate que existe
if (file_exists($logoPath)) {
    $drawing = new Drawing();
    $drawing->setPath($logoPath);
    $drawing->setCoordinates('A1');
    $drawing->setHeight(80);
    $drawing->setWorksheet($sheet);
}

// Título
$sheet->mergeCells('A4:G4');
$sheet->setCellValue('A4', '📦 REPORTE DE PEDIDOS REALIZADOS');
$sheet->getStyle('A4')->getFont()->setSize(16)->setBold(true);
$sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Cabeceras
$cabeceras = ['ID Pedido', 'Cliente', 'Total (S/)', 'Método de Pago', 'Dirección', 'Teléfono', 'Fecha'];
$sheet->fromArray($cabeceras, null, 'A6');

// Estilo cabecera
$sheet->getStyle('A6:G6')->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E78']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
]);

// Datos
$fila = 7;
$totalGeneral = 0;
$fillGray = false;

foreach ($pedidos as $pedido) {
    $sheet->setCellValue("A$fila", $pedido['id']);
    $sheet->setCellValue("B$fila", $pedido['cliente']);
    $sheet->setCellValue("C$fila", $pedido['total']);
    $sheet->setCellValue("D$fila", $pedido['metodo_pago']);
    $sheet->setCellValue("E$fila", $pedido['direccion']);
    $sheet->setCellValue("F$fila", $pedido['telefono']);
    $sheet->setCellValue("G$fila", date('d/m/Y H:i', strtotime($pedido['fecha'])));

    // Estilo fila
    $bgColor = $fillGray ? 'F2F2F2' : 'FFFFFF';
    $sheet->getStyle("A$fila:G$fila")->applyFromArray([
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    ]);

    $totalGeneral += $pedido['total'];
    $fila++;
    $fillGray = !$fillGray;
}

// Total General
$sheet->mergeCells("A$fila:F$fila");
$sheet->setCellValue("A$fila", "TOTAL GENERAL:");
$sheet->getStyle("A$fila")->getFont()->setBold(true)->setSize(12);
$sheet->getStyle("A$fila")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

$sheet->setCellValue("G$fila", $totalGeneral);
$sheet->getStyle("G$fila")->getFont()->setBold(true)->setSize(12);
$sheet->getStyle("G$fila")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

// Autoajuste columnas
foreach (range('A', 'G') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Descargar
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte_pedidos_' . date("Ymd_His") . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
