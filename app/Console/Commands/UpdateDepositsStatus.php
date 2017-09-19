<?php

namespace App\Console\Commands;

use App\Liantech\Repositories\SpotRepository;
use Illuminate\Console\Command;

class UpdateDepositsStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'liantech:update-deposits-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all the deposits statuses from the SPOT CRM replica';

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
     * @return mixed
     */
    public function handle()
    {
        (new SpotRepository)->updatesDepositsStatus();
    }
}
