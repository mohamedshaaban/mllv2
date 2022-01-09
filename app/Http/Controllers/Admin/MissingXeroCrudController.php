<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OrdersRequest as StoreRequest;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\CarModel;
use App\Models\Invoices;
use App\Models\RequestStatus;
use App\Models\Customers;
use App\Models\OrderInvoicess;
use App\Models\Orders;
use App\Models\Areas;
use App\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;
use http\Client\Request;
use http\Url;
use Illuminate\Support\Facades\App;

class MissingXeroCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
        update as traitUpdate;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    use \Backpack\CRUD\app\Http\Controllers\Operations\CloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkDeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkCloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;

    public function setup()
    {
        App::setLocale(session('locale'));

        CRUD::setModel(\App\Models\Orders::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/missingxero');
        CRUD::setEntityNameStrings(trans('admin.order'), trans('admin.orders'));

    }


    protected function setupListOperation()
    {
        $this->crud->addClause('whereNull', 'xero_id');


        $this->crud->addColumn([ // Text
            'name' => 'invoice_unique_id',
            'label' => trans('admin.Order Id'),
            'orderable' => true]);

        $this->crud->addColumn([ // Text
            'name' => 'customers',
            'label' => trans('admin.Customer'),
            'type' => 'relationship',
            'orderable' => true
        ]);

        $this->crud->addColumn([ // Text
            'name' => 'cars',
            'label' => trans('admin.Car Plate Id'),
            'type' => 'relationship',
            'attribute' => 'car_plate_id',
            'orderable' => true
        ]);
        $this->crud->addColumn([ // Text
            'name' => 'requeststatus',
            'label' => trans('admin.Status'),
            'type' => 'relationship',
//            'orderable' => true
        ]);

        $this->crud->addColumn([ // Text
            'name' => 'areafrom',
            'label' => trans('admin.From'),
            'type' => 'relationship',
//            'orderable' => true

        ]);

        $this->crud->addColumn([ // Text
            'name' => 'areato',
            'label' => trans('admin.To'),
            'type' => 'relationship',
//            'orderable' => true
        ]);
        $this->crud->addColumn([ // Text
            'name' => 'paid_status',
            'label' => trans('admin.Paid'),
            'type' => 'text',
//            'orderable' => true
        ]);

        $this->crud->addColumn([ // Text
            'name' => 'date',
            'label' => trans('admin.Date'),
            'type' => 'text',
            'orderable' => true

        ]);

         $this->crud->disableBulkActions();
        $this->crud->enableExportButtons();
        $this->crud->enableResponsiveTable();
        $this->crud->enablePersistentTable();
        $this->crud->removeAllButtons();

        $this->crud->addButtonFromModelFunction('line', 'xero', 'createXero', 'beginning');


    }

    public function generateInvoice(\Illuminate\Http\Request  $request)
    {
        $id = $request->id;
        $order = Orders::find($id);
        xeroinvoice($order->id, 1);
        $order = Orders::find($id);

        $pdf = getxeroinvoice($order->xero_id);

        return view('payment.pdf')->with(compact('pdf'));
        dd();
    }

}
