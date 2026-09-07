<?php

namespace App\Mail;

use App\Models\IkuPencapaian;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EwsWarningMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $pencapaian;

    //Menerima data pencapaian
    public function __construct(IkuPencapaian $pencapaian)
    {
        $this->pencapaian = $pencapaian;
    }

    //menentukan judul/subjek email EWS.
    public function envelope(): Envelope
    {
        $prodiName = $this->pencapaian->prodi ? $this->pencapaian->prodi->nama_prodi : 'Program Studi';
        $ikuName = $this->pencapaian->iku ? $this->pencapaian->iku->nama_iku : 'Indikator';
        return new Envelope(
            subject: "[EWS ALERT] Warning Ketercapaian IKU {$ikuName} - {$prodiName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ews_warning',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
