<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class DailyAppointmentsReport extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Collection $appointments, public string $date)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reporte Diario de Citas Médicas - ' . date('d/m/Y', strtotime($this->date)),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-report',
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.daily-report', [
            'appointments' => $this->appointments,
            'date' => $this->date,
        ]);
        
        return [
            Attachment::fromData(fn () => $pdf->output(), 'Citas_'.$this->date.'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
