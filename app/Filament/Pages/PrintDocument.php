<?php

namespace App\Filament\Pages;



use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;

class PrintDocument extends Page
{
    protected static string $layout = 'filament.layout';
    protected static string $view = 'filament.print';

    public ?Model $record=null;
    public ?string $type = null;

    public function mount(): void
    {
        if(request()->has('record') && !empty(request()->record)) {
            $this->type = request()->type;
            $this->record =  match (request()->type){
                'document' => \TomatoPHP\FilamentDocs\Models\Document::find(request()->record),
                'order' => \App\Models\Order::find(request()->record),
                default => null,
            };
        }
    }
}
