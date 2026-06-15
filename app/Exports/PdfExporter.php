<?php

namespace App\Exports;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfExporter 
{
    protected array $data;
    protected string $view;
    protected string $pathFile;

    public function __construct(array $data, string $view,string $pathFile)
    {
        $this->data = $data;
        $this->view = $view;
        $this->pathFile = $pathFile;
    }
     public function export(): void
    {
        $pdf = Pdf::loadView($this->view, $this->data)
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ])
            ->setPaper('A4', 'landscape');

        Storage::disk('public')->put($this->pathFile, $pdf->output());
    }
}