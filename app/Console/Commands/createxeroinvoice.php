<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Models\Invoices;
use App\Models\Orders;


class createxeroinvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'createxeroinvoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'createxeroinvoice';

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
