<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OrdersRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Orders;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;
class OrdersCollectedCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkDeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkCloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;

    public function setup()
    {
        App::setLocale(session('locale'));

        CRUD::setModel(\App\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/orderscollected');
        CRUD::setEntityNameStrings(trans('admin.Cash With Driver'), trans('admin.Cash With Driver'));
    }

    protected function setupListOperation()
    {
        if(isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $queries);

            if (isset($queries['userid']) && isset($queries['orderid'])) {

                Orders::whereIn('id',$queries['orderid'])->update(['order_collected'=>1,'collected_date'=>Carbon::now()]);


            }
        }
         $this->crud->addClause('where','is_driver', '=', '1');
//        $this->crud->addClause('where', 'payment_type', '=', Orders::CASH_PAYMENT);
//        $this->crud->addClause('where', 'status', '=', 6);
        $this->crud->addColumn([ // Text
            'name'  => 'name',
            'label' => trans('admin.Name'),
        ]);
        $this->crud->addColumn([ // Text
            'name'  => 'cashwithdriver',
            'label' => trans('admin.Cash With Driver'),
        ]);   
        $this->crud->addColumn([ // Text
            'name'  => 'cashcollected',
            'label' => trans('admin.Cash Collected Driver'),
        ]);

        $this->crud->enableExportButtons();
        $this->crud->enableResponsiveTable();
        $this->crud->enablePersistentTable();
        $this->crud->enableDetailsRow();
        $this->crud->removeButton('create');
        $this->crud->removeButton('edit');
        $this->crud->removeAllButtons();
        $this->crud->disableBulkActions();
        $this->crud->removeButton('delete');


    }
    protected function showDetailsRow($id)
    {
        $notPaidInvoices = Orders::where('driver_id',$id)->where(function ($query) {
            $query->where('amount', '!=', 0)
                ->Where('amount', '!=', 'NULL');
        })->where('order_collected',0)->where('is_paid',1)->where('payment_type',Orders::CASH_PAYMENT)->get();
        $invoices = Orders::where('driver_id',$id)->where(function ($query) {
            $query->where('amount', '!=', 0)
                ->Where('amount', '!=', 'NULL');
        })->where('order_collected',1)->where('is_paid',1)->where('payment_type',Orders::CASH_PAYMENT)->get();
        $text='';
        $text.='<script>$("#selectorders'.$id.'").click(function (e) {$(this).closest("table").find("td input:checkbox").prop("checked", this.checked);});</script>';
        $text .= '<div class="row"><div class="col-md-6 col-sm-12"><h3>'.trans('admin.Orders  With Driver').'</h3>';
        $text .= '<form acion="/admin/invoice/generate" method="get">';
        $text .= '<table class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2 dataTable dtr-inline">';
        $text .= '<tr role="row"><th data-orderable="false"><input type="checkbox" id="selectorders'.$id.'" /></th><th data-orderable="false">'.trans('admin.Order Id').'</th><th data-orderable="false">'.trans('admin.Date').'</th><th data-orderable="false">'.trans('admin.Amount').'</th></tr>';
        $text .='    <input type="hidden" name="userid" value="'.$id.'" />';
        $notPaidInvoicesTotal = 0 ;
        $PaidInvoicesTotal = 0 ;
        foreach ($notPaidInvoices as $order)
        {
            $notPaidInvoicesTotal+=$order->amount?$order->amount:0;
            $text.='<tr class="even">';
            $text.= '<td><input type="checkbox" name="orderid[]" class="orderchk'.$id.'" value="'.$order->id.'"/> </td>';
            $text.= '<td>'.@$order->invoice_unique_id.'</td>';
            $text.= '<td>'.@$order->date.'</td>';
            $text.= '<td>'.@$order->amount.'</td>';
            $text.='</tr>';
        }
        $text.='<tr><td colspan="3">'.trans('admin.Total').' : </td><td colspan="1">'.$notPaidInvoicesTotal.'</td></tr>';
        $text.='</table><input type="submit" value="'.trans('admin.Collect Orders').'"></fom>';
        $text.='</div><div class="col-md-6 col-sm-12"><h3>'.trans('admin.Collected Orders').'</h3>';
        $text .= '<table class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2 dataTable dtr-inline">';
        $text .= '<tr role="row"><th data-orderable="false"></th><th data-orderable="false">'.trans('admin.Order Id').'</th><th data-orderable="false">'.trans('admin.Date').'</th><th data-orderable="false">'.trans('admin.collected_date').'</th><th data-orderable="false">'.trans('admin.Amount').'</th></tr>';
        foreach ($invoices as $order)
        {
            $PaidInvoicesTotal+=$order->amount;
            $text.='<tr class="even">';
            $text.= '<td> </td>';
            $text.= '<td>'.@$order->invoice_unique_id.'</td>';
            $text.= '<td>'.@$order->date.'</td>';
            $text.= '<td>'.@$order->collected_date.'</td>';
            $text.= '<td>'.@$order->amount.'</td>';
            $text.='</tr>';
        }
        $text.='<tr><td colspan="4">'.trans('admin.Total').' : </td><td colspan="1">'.$PaidInvoicesTotal.'</td></tr>';
        $text.='</table>';
        $text.='</div></div>';
        return $text;
    }
    protected function setupCreateOperation()
    {

        CRUD::addField([ // Text
            'name'  => 'order_collected',
            'label' => trans('admin.Collected from driver'),
            'type'  => 'radio',
            'tab'   => 'Texts',
            'options'     => [
                // the key will be stored in the db, the value will be shown as label;
                0 => "No",
                1 => "Yes"
            ],

        ]);






        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
