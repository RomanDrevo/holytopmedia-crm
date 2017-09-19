<?php

namespace App\Console\Commands;

use App\Liantech\Repositories\SpotRepository;
use Illuminate\Console\Command;

class UpdateDepositsAndWithdrawals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'liantech:update-records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all the deposits and withdrawals from the replica DB';

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
        (new SpotRepository)->update();
    }
}
