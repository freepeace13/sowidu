<?php

namespace Modules\Offer\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Modules\Offer\Models\Offer;

class OfferNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $recipientName;

    public $sender;

    public $note;

    public $url;

    public function __construct(public Offer $offer, public string $pdfPath)
    {
        $this->offer = $offer;
        $this->recipientName = $offer->recipient->name ?? 'There';
        $this->sender = $offer->company->name ?? config('app.name');
        $this->note = $offer->note;
        $this->url = route('offers.pdf.stream', $offer->uuid);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdf_path = $this->offer->pdf_path;
        $file_name = $this->offer->internal_id . '.pdf';
        $subject = $this->offer->subject ?? "New Offer from {$this->sender}";

        $mail = $this->subject($subject)
            ->markdown('offer::emails.sent');

        if ($pdf_path && Storage::exists($pdf_path)) {
            $mail->attachFromStorageDisk(
                config('filesystems.default'),
                $pdf_path,
                $file_name,
                [
                    'mime' => 'application/pdf',
                ],
            );
        }

        return $mail;
    }
}
