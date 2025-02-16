<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Noxo\FilamentActivityLog\Extensions\LogCreateRecord;

class CreateCustomer extends CreateRecord
{
    use LogCreateRecord; // <--- here

    protected static string $resource = CustomerResource::class;
}
