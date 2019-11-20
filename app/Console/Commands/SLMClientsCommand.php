<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SLMClientsCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'slm:clients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all of the oauth clients';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $clients = \DB::table('oauth_clients')->get()->map(function ($client) {
            return [
                'id' => $client->id,
                'name' => $client->name,
                'secret' => $client->secret,
                'grant_type' => $client->personal_access_client ? 'personal_access' : 'password',
                'user_id' => $client->user_id ?: 'null',
                'status' => $client->revoked ? '<fg=red>revoked</fg=red>' : 'active',
            ];
        })->toArray();
        if (count($clients) > 0) {
            return $this->table(['id', 'name', 'secret', 'grant_type', 'user_id', 'status'], $clients);
        }
        $this->error('No clients found');
    }
}
