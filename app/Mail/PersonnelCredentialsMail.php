<?php

namespace App\Mail;

use App\Models\PersonnelSecurite;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PersonnelCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $personnel;
    public $password;
    public $isReset;

    /**
     * Create a new message instance.
     */
    public function __construct(PersonnelSecurite $personnel, string $password, bool $isReset = false)
    {
        $this->personnel = $personnel;
        $this->password = $password;
        $this->isReset = $isReset;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->isReset 
            ? 'Vos nouveaux identifiants de connexion - Personnel de Sécurité'
            : 'Vos identifiants de connexion - Personnel de Sécurité';

        return new Envelope(
            subject: $subject,
            from: config('mail.from.address', 'noreply@app.com'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.personnel-credentials',
            with: [
                'personnel' => $this->personnel,
                'password' => $this->password,
                'isReset' => $this->isReset,
            ]
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