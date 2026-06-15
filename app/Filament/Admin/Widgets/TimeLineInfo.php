<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;

class TimeLineInfo extends Widget
{
    protected string $view = 'filament.admin.widgets.time-line-info';

     protected int | string | array $columnSpan = 'full';
}
