<?php

namespace App\Livewire\Admin\Datatables;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class AppointmentTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return Appointment::query()->with(['patient.user', 'doctor.user']);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make('Paciente')
                ->label(fn ($row) => $row->patient?->user?->name ?? '-'),

            Column::make('Doctor')
                ->label(fn ($row) => $row->doctor?->user?->name ?? '-'),

            Column::make('Fecha', 'date')
                ->sortable(),

            Column::make('Hora inicio', 'start_time')
                ->sortable(),

            Column::make('Status', 'status')
                ->format(fn ($value, $row) => $row->status_label)
                ->sortable(),

            Column::make('Acciones')
                ->label(function ($row) {
                    return view('admin.appointments.actions', ['appointment' => $row]);
                }),
        ];
    }
}
