<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class SLMAccessTokenCommand extends Command
{
    /**
     * @var array
     */
    protected $clients;

    /**
     * @var array
     */
    protected $users;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'slm:access-token
		{--id= : The id of the client}
		{--email : The user\'s email}
		{--password : The user\'s password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an access token for an oauth client';

    /**
     * Execute the console command.
     *
     * @return void|null
     * @todo Restrict clients by scope: check User role or provide a default scope for
     *       personal_access clients (i.e. "basic" scope). See: App\Providers\AuthServiceProvider
     */
    public function handle()
    {
        $this->clients = $this->getClients();
        $this->users = $this->getUsers();
        if (empty($this->clients)) {
            return $this->error('No clients have been created');
        }
        if (empty($client = $this->getClient())) {
            return $this->error('Client not found');
        }
        if (empty($input = $this->getInput($client))) {
            return $this->error('No users have been created');
        }
        $request = Request::create('/v1/oauth/token', 'POST', $input);
        $response = json_decode(app()->dispatch($request)->getContent());
        if (!empty($response->message)) {
            return $this->error($response->message);
        }
        $this->line('<comment>Access Token:</comment> '.$response->access_token);
        if (isset($response->refresh_token)) {
            $this->line('<comment>Refresh Token:</comment> '.$response->refresh_token);
        }
    }

    /**
     * @return \stdClass|null
     */
    protected function getClient()
    {
        if (empty($id = $this->option('id'))) {
            $this->showClientsTable();
            $id = $this->ask('What is the id of the client?');
        }
        return \DB::table('oauth_clients')->where('id', $id)->first();
    }

    /**
     * @return array
     */
    protected function getClients()
    {
        return \DB::table('oauth_clients')->get()->map(function ($client) {
            return [
                'id' => $client->id,
                'name' => $client->name,
                'secret' => $client->secret,
                'grant_type' => $client->personal_access_client ? 'personal_access' : 'password',
                'user_id' => $client->user_id ?: 'null',
                'status' => $client->revoked ? '<fg=red>revoked</fg=red>' : 'active',
            ];
        })->toArray();
    }

    /**
     * @param \stdClass $client
     * @return array|null
     */
    protected function getInput($client)
    {
        $input = [
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'grant_type' => $client->personal_access_client ? 'personal_access' : 'password',
        ];
        if ($client->password_client) {
            if (empty($this->users)) {
                return;
            }
            $this->showUsersTable();
            $input['username'] = $this->option('email') ?: $this->ask('What is the email of the user?');
            $input['password'] = $this->option('password') ?: $this->secret('What is the password of the user?');
        }
        return $input;
    }

    /**
     * @return array
     */
    protected function getUsers()
    {
        return app(User::class)->get(['id', 'email', 'role'])->toArray();
    }

    /**
     * @return void
     */
    protected function showClientsTable()
    {
        $this->table(['id', 'name', 'secret', 'grant_type', 'user_id', 'status'], $this->clients);
    }

    /**
     * @return void
     */
    protected function showUsersTable()
    {
        $this->table(['id', 'email', 'role'], $this->users);
    }
}
