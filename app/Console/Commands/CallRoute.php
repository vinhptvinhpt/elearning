<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

class CallRoute extends Command
{
    /**
     * The name and signature of the console command.
     * argument must be here if necessary, otherwise cause error: Too many arguments, expected arguments "command".
     * @var string
     */
    protected $signature = 'route:call {uri}';//php artisan route:call /cron/reset

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call route from CLI';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $request = Request::create($this->argument('uri'), 'GET'); //argument taken and use here
        $this->info(app()->make(Kernel::class)->handle($request));
    }
}
