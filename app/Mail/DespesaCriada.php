<?php

namespace App\Mail;

use App\Models\Despesa;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use MailerSend\Helpers\Builder\Personalization;
use MailerSend\LaravelDriver\MailerSendTrait;

class DespesaCriada extends Mailable
{
    use Queueable, SerializesModels, MailerSendTrait;
    public Despesa $despesa;

    /**
     * Create a new message instance.
     */
    public function __construct(Despesa $despesa)
    {
        $this->despesa = $despesa;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new \Illuminate\Mail\Mailables\Envelope(
            subject: 'Nova despesa criada no seu cartão!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new \Illuminate\Mail\Mailables\Content(
            view: 'emails.despesa_criada',
            with: [
                'valor' => $this->despesa->valor,
                'descricao' => $this->despesa->descricao,
                'cartao'=> $this->despesa->cartao->number,
            ],
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
