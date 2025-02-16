<?php

namespace App\Filament\Loggers;

use App\Models\Customer;
use App\Filament\Resources\CustomerResource;
use Illuminate\Contracts\Support\Htmlable;
use Noxo\FilamentActivityLog\Loggers\Logger;
use Noxo\FilamentActivityLog\ResourceLogger\Field;
use Noxo\FilamentActivityLog\ResourceLogger\RelationManager;
use Noxo\FilamentActivityLog\ResourceLogger\ResourceLogger;
use Spatie\Activitylog\Contracts\Activity;

class CustomerLogger extends Logger
{
    public static ?string $model = Customer::class;

    public static function getLabel(): string | Htmlable | null
    {
        return CustomerResource::getModelLabel();
    }

    public function getSubjectRoute(Activity $activity): ?string
    {
        return CustomerResource::getUrl('edit', ['record' => $activity->subject_id]);
    }

    public function getRelationManagerRoute(Activity $activity): ?string
    {
        return $this->getSubjectRoute($activity).'?activeRelationManager=0';
    }

    public static function resource(ResourceLogger $logger): ResourceLogger
    {
        return $logger
            ->fields([
                Field::make('name')
                    ->label(__('Name')),

                Field::make('email')
                    ->label(__('Email'))
                    ->badge(),

                Field::make('phone')
                    ->label(__('Phone')),

                Field::make('total_points')
                    ->label(__('Total Points'))
                    ,
            ]);
    }
}
