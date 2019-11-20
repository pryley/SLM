<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SLMInstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'slm:install {--force : Overwrite encryption keys if they already exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Software License Manager';

    /**
     * Execute the console command.
     *
     * 1. install passport
     * 2. create admin user
     * 3. create normal user
     *
     * @return void
     */
    public function handle()
    {
        $this->call('passport:keys', ['--force' => $this->option('force')]);
        $this->call('passport:client', ['--personal' => true, '--name' => 'Personal Access Client']);
        $this->call('passport:client', ['--password' => true, '--name' => 'Password Grant Client']);
        $this->info('Software License Manager installed.');
    }
}
