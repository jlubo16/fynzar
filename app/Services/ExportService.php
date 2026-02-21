<?php
// app/Services/ExportService.php

namespace App\Services;

use App\Services\Exporters\CSVExporter;
use App\Services\Exporters\PDFExporter;
use App\Services\Exporters\JSONExporter;

class ExportService
{
    protected $csvExporter;
    protected $pdfExporter;
    protected $jsonExporter;

    public function __construct(
        CSVExporter $csvExporter,
        PDFExporter $pdfExporter,
        JSONExporter $jsonExporter
    ) {
        $this->csvExporter = $csvExporter;
        $this->pdfExporter = $pdfExporter;
        $this->jsonExporter = $jsonExporter;
    }

    public function export(string $format, array $data, string $module)
    {
        switch ($format) {
            case 'csv':
                return $this->csvExporter->export($data, $module);
                
            case 'pdf':
                return $this->pdfExporter->export($data, $module);
                
            case 'json':
                return $this->jsonExporter->export($data, $module);
                
            default:
                throw new \InvalidArgumentException("Formato no soportado: {$format}");
        }
    }
}