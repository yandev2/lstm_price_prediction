<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExelExporter implements FromView, ShouldAutoSize
{
    protected array $data;
    protected string $view;

    public function __construct(array $data, string $view)
    {
        $this->data = $data;
        $this->view = $view;
    }
    public function view(): View
    {
        return view($this->view, $this->data);
    }
}
