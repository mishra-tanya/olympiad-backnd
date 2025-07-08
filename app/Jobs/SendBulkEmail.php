<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\BulkEmail;

class SendBulkEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $message;
    protected $subject;

    public function __construct($email, $message, $subject)
    {
        $this->email = $email;
        $this->message = $message;
        $this->subject = $subject;
    }

    public function handle(): void
    {
        Mail::to($this->email)->send(new BulkEmail($this->message, $this->subject));
    }
}
