<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Orders;
use App\Models\Invoices;

class xerocreateinvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xerocreateinvoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle()
    {

        (generatexerotoken());
        $orders = Orders::whereNull('xero_id')->get();
        foreach ($orders as $order)
        {
            xeroinvoice($order->id, 1);
        }

        $Invoices = Invoices::whereNull('xero_id')->get();
        foreach ($Invoices as $invoice)
        {
            xeroinvoice($invoice->id, 0);
        }

    }
}
