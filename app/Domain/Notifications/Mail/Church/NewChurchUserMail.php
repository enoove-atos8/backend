<?php

namespace App\Domain\Notifications\Mail\Church;

use App\Domain\Accounts\Users\DataTransferObjects\UserData;
use App\Domain\Accounts\Users\DataTransferObjects\UserDetailData;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewChurchUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public UserData $userData;
    public UserDetailData $userDetailData;
    public string $tenant;
    private const SUBJECT_EMAIL = "Seja bem vindo ao ambiente eclesiástico da";


    /**
     * Create a new message instance.
     *
     * @param UserData $userData
     * @param UserDetailData $userDetailData
     * @param string $tenant
     */
    public function __construct(UserData $userData, UserDetailData $userDetailData, string $tenant)
    {
        $this->userData = $userData;
        $this->userDetailData = $userDetailData;
        $this->tenant = $tenant;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        $fromEmail = config('mail.from.address', 'noreply@atos8.com');
        $fromName =  strtoupper($this->tenant) . ' - Atos 8';

        return new Envelope(
            from: new Address($fromEmail, $fromName),
            subject: self::SUBJECT_EMAIL . ' ' . strtoupper($this->tenant),
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            html: 'notifications.mail.churches.church_created_mail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments(): array
    {
        return [];
    }
}
