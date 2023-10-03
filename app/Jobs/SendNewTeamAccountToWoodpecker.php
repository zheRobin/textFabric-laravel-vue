<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class SendNewTeamAccountToWoodpecker implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $firstname;
    protected $lastname;

    public function __construct($email, $firstname, $lastname)
    {
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }

    public function handle()
    {

        $woodpeckerAuth = config('app.woodpecker_auth', '');
        $woodpeckerCampaignId = config('app.woodpecker_campaign_id', '');

        $response = Http::withHeaders([
            'Authorization' => $woodpeckerAuth
        ])->post('https://api.woodpecker.co/rest/v1/add_prospects_campaign', [
            "campaign" => [
            "campaign_id" => $woodpeckerCampaignId
            ],
            "update" => "true",
            "prospects" => [
            [
               "email" => $this->email,
               "first_name" => $this->firstname,
               "last_name" => $this->lastname,
               "status" => "ACTIVE"
            ]
         ]
        ]);

    }
}
