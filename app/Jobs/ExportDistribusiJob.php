<?php

namespace App\Jobs;

use App\Exports\ExelExporter;
use App\Exports\PdfExporter;
use App\Models\Distribusi;
use App\Models\HargaPangan;
use App\Models\User;
use App\Services\ExportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Testing\Concerns\InteractsWithExceptionHandling;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ExportDistribusiJob implements ShouldQueue
{
    use InteractsWithExceptionHandling, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $ids;
    protected User $user;
    protected string $pathFile;
    public $tries = 3;
    public $backoff = 30;
    public string $type;
    public function __construct(array $ids, User $user, string $pathFile, string $type)
    {
        $this->ids = $ids;
        $this->user = $user;
        $this->pathFile = $pathFile;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(ExportService $exportService): void
    {
        $model = 'distribusi ';

        $data = Distribusi::with(['pasarAsal', 'pasarTujuan', 'komoditas'])
            ->whereIn('id', $this->ids)
            ->get();

        $json = [
            'tanggal' => Carbon::now()->translatedFormat('l, d F Y'),
            'data' => $data,
            'start' => $data->min('tanggal'),
            'end' => $data->max('tanggal')
        ];

        if ($this->type == 'pdf') {
            $exporter = new PdfExporter($json, 'export.distribusi-pdf', $this->pathFile);
            $exporter->export();
        } else {
            Excel::store(new ExelExporter($json, 'export.distribusi-exel'), $this->pathFile, 'public');
        }

        $exportService->recordExport(
            $this->user,
            basename($this->pathFile),
            $this->pathFile,
            $model
        );

        $this->logActivity($data, $model);


        (new \App\Jobs\NotifyUserOfCompletedExport(
            $this->user,
            $this->pathFile,
            $model . $this->type,
            false
        ))->handle();
    }

    protected function logActivity(Collection $data, string $model)
    {
        activity()
            ->event('export')
            ->performedOn($data->first())
            ->causedBy($this->user)
            ->useLog(\Str::title("export data $model"))
            ->withProperties([
                'attributes' => [
                    'type' => $this->type,
                    'file_name' => str_replace("export/", '', $this->pathFile),
                    'total_data' => $data->count(),
                ]
            ])
            ->log("export " . strtoupper(($this->type)));
    }

    public function failed(?Throwable $exception): void
    {
        $model = 'distribusi ';
        \Log::error("Job failed Export $model : " . $exception->getMessage());
        (new \App\Jobs\NotifyUserOfCompletedExport(
            $this->user,
            $this->pathFile,
            $model . $this->type,
            true
        ))->handle();
    }
}
