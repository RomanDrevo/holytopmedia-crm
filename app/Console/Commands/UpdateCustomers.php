<?php

namespace App\Console\Commands;

use App\Liantech\Repositories\CustomersRepository;
use Illuminate\Console\Command;

class UpdateCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'liantech:update-customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all the customers from the Replica CRM';

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
        (new CustomersRepository)->updateFromDB();
    }
}
