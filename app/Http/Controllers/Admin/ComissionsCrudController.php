<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OrdersRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Orders;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

class ComissionsCrudController extends CrudController
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
        CRUD::setRoute(config('backpack.base.route_prefix').'/comissions');
        CRUD::setEntityNameStrings(trans('admin.Commission'), trans('admin.Commission'));
    }

    protected function setupListOperation()
    {
        if(isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $queries);

            if (isset($queries['userid']) && isset($queries['orderid'])) {

            foreach ($queries['orderid'] as $orderID)
            {
                Orders::where('id',$orderID)->update(['comission_paid'=>1,'commission_date_paid'=>Carbon::now()]);
            }

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
            'name'  => 'mobile',
            'label' => trans('admin.Mobile'),
        ]);
        $this->crud->addColumn([ // Text
            'name'  => 'comission',
            'label' => trans('admin.Paid Comission'),
        ]);
        $this->crud->addColumn([ // Text
            'name'  => 'pending_comission',
            'label' => trans('admin.Pending Comission'),
        ]);

        $this->crud->disableBulkActions();
        $this->crud->enableExportButtons();
        $this->crud->enableResponsiveTable();
        $this->crud->enablePersistentTable();
        $this->crud->enableDetailsRow();
        $this->crud->removeButton('create');
        $this->crud->removeAllButtons();

    }
    protected function showDetailsRow($id)
    {
        $notPaidInvoices = Orders::where('driver_id',$id)->where('comission_paid',0)->get();
        $invoices = Orders::where('driver_id',$id)->where('comission_paid',1)->get();
        $text='';
        $text.='<script>$("#selectorders'.$id.'").click(function (e) {$(this).closest("table").find("td input:checkbox").prop("checked", this.checked);});</script>';
        $text .= '<div class="row"><div class="col-md-6 col-sm-12"><h3>'.trans('admin.Commission not Paid').'</h3>';
        $text .= '<form acion="/admin/invoice/generate" method="get">';
        $text .= '<table class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2 dataTable dtr-inline">';
        $text .= '<tr role="row"><th data-orderable="false"><input type="checkbox" id="selectorders'.$id.'" /></th><th data-orderable="false">'.trans('admin.Order Id').'</th><th data-orderable="false">'.trans('admin.Order Date').'</th><th data-orderable="false">'.trans('admin.Amount').'</th></tr>';
        $text .='    <input type="hidden" name="userid" value="'.$id.'" />';
        $notPaidInvoicesTotal = 0 ;
        $PaidInvoicesTotal = 0 ;
        foreach ($notPaidInvoices as $order)
        {
            $notPaidInvoicesTotal+=$order->comission;
            $text.='<tr class="even">';
            $text.= '<td><input type="checkbox" name="orderid[]" class="orderchk'.$id.'" value="'.$order->id.'"/> </td>';
            $text.= '<td>'.@$order->invoice_unique_id.'</td>';
            $text.= '<td>'.@$order->date.'</td>';
            $text.= '<td>'.@$order->comission.'</td>';
            $text.='</tr>';
        }
        $text.='<tr><td colspan="3">'.trans('admin.Total').' : </td><td colspan="1">'.$notPaidInvoicesTotal.'</td></tr>';
        $text.='</table><input type="submit" value="'.trans('admin.Collect Commission').'"></fom>';
        $text.='</div><div class="col-md-6 col-sm-12"><h3>'.trans('admin.Commission Paid').'</h3>';
        $text .= '<table class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2 dataTable dtr-inline">';
        $text .= '<tr role="row"><th data-orderable="false"></th><th data-orderable="false">'.trans('admin.Order Id').'</th><th data-orderable="false">'.trans('admin.Commission Paid Date').'</th><th data-orderable="false">'.trans('admin.Amount').'</th></tr>';
        foreach ($invoices as $invoice)
        {
            $PaidInvoicesTotal+=$invoice->comission;
            $text.='<tr class="even">';
            $text.= '<td></td>';
            $text.= '<td>'.@$invoice->invoice_unique_id.'</td>';
            $text.= '<td>'.@$invoice->commission_date_paid.'</td>';
            $text.= '<td>'.@$invoice->comission.'</td>';
            $text.='</tr>';
        }
        $text.='<tr><td colspan="3">'.trans('admin.Total').' : </td><td colspan="1">'.$PaidInvoicesTotal.'</td></tr>';
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
