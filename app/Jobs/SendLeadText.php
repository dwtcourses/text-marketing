<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Libraries\Api;
use App\Dialog;
use App\User;

class SendLeadText implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dialog;
    protected $clients;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Dialog $dialog, $clients, User $user)
    {
        $this->dialog = $dialog;
        $this->clients = $clients;
        $this->user = $user;
        //$response = Api::dialog($this->dialog->id, $this->clients, $this->dialog->text, $this->user->company_name, $this->user->offset);
        //dd($response);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = Api::dialog($this->dialog->id, $this->clients, $this->dialog->text, $this->user->company_name, $this->user->offset);
        if ($response['code'] == 200) {
            $this->dialog->update(['status' => 1]);
        }
    }
}
