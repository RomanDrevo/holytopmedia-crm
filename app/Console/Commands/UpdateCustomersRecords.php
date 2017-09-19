<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Liantech\Repositories\CustomersRepository;
class UpdateCustomersRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'liantech:update-existing-customers-records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update customers data';

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
        (new CustomersRepository)->updateExistingRecords();
    }
}
