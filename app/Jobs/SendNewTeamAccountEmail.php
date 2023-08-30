<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewTeamAccountEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $recipientEmail;
    protected $subject;
    protected $content;

    public function __construct($recipientEmail, $subject, $content)
    {
        $this->recipientEmail = $recipientEmail;
        $this->subject = $subject;
        $this->content = $content;
    }

    public function handle()
    {
        Mail::raw($this->content, function ($message) {
            $message->to($this->recipientEmail);
            $message->subject($this->subject);
        });
    }
}
