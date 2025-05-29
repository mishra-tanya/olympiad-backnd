<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class WelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome Mail',
        );
    }

    // No need for the content() method if you're using build()
    public function build()
    {
        return $this->view('emails.welcome')
                ->subject('Welcome to Our Platform!')
                ->with([
                        'user' => $this->user,
                    ]);
    }

    // If you don't need any attachments, you can leave this empty
    public function attachments(): array
    {
        return [];
    }
}
