<?php

namespace App\Console\Commands;

use App\Actions\Billing\GenerateInvoice;
use Illuminate\Console\Command;

class DailyBillingProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bill:generate
                            {date? : The date in mm/dd/yyyy format to run billing for specific case}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs billing process to generate bills for customers whose subscription is ending today';

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
     * @return int
     */
    public function handle(GenerateInvoice $generateInvoice)
    {
        $billGenerationDate = $this->argument('date');
        GenerateInvoice::generateBill($this, $billGenerationDate);
        return Command::SUCCESS;

    }
}
