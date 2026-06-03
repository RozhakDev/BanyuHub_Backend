<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\DateTimePicker::make('event_date')->required(),
                Forms\Components\TextInput::make('location')->required()->maxLength(255),
                Forms\Components\TextInput::make('quota')->required()->numeric()->minValue(1),
                Forms\Components\Textarea::make('description')->required()->columnSpanFull(),
            ]);
    }
}
