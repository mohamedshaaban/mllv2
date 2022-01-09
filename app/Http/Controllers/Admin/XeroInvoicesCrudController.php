<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CustomersRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\Request;
use App\Models\Customers;
use App\Models\Invoices;
use App\Models\Orders;
use App\Models\RequestStatus;
use App\Models\OrderInvoicess;
use App\Models\Areas;
use App\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\App;

class XeroInvoicesCrudController extends CrudController
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

        CRUD::setModel(\App\Models\Orders::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/xeroinvoices');
        CRUD::setEntityNameStrings(trans('admin.Invoices'), trans('admin.Invoices'));
    }

    protected function setupListOperation()
    {
        $this->crud->addClause('where', 'xero_id', '!=', 'null');
//        $this->crud->addClause('whereHas', 'invoices');

        $this->crud->addFilter([
            'name'        => 'invoice_unique_id',
            'type'        => 'text',
            'label'       => trans('admin.Order ID')
        ],
            false,
            function ($value) { // if the filter is active, apply these constraints
                $this->crud->addClause('where', 'invoice_unique_id', 'like', '%'.$value.'%');
            });

        $dataCustomers = [];
        $customers = Customers::all();
        foreach ($customers as $customer)
        {
            $dataCustomers[$customer->id] = $customer->name.' - '.$customer->mobile;
        }
        $this->crud->addFilter([
                'name'        => 'customer_id',
                'type'        => 'select2_multiple',
                'label'       => trans('admin.Customer'),
                'placeholder' => trans('admin.Pick a customer')

            ]
            , function()use($dataCustomers) {
                return
                    $dataCustomers
                    ;
            }
            , // the ajax route
            function($value) { // if the filter is active\
                $this->crud->addClause('whereIn', 'customer_id', json_decode($value));
            });
        $dataDrivers = [];
        $drivers = User::where('is_driver',1)->get();
        foreach ($drivers as $driver)
        {
            $dataDrivers[$driver->id] = $driver->name;
        }
        $this->crud->addFilter([
                'name'        => 'driver_id',
                'type'        => 'select2_multiple',
                'label'       => trans('admin.Driver'),
                'placeholder' => trans('admin.Pick a driver')

            ]
            , function()use($dataDrivers) {
                return
                    $dataDrivers
                    ;
            }
            , // the ajax route
            function($value) { // if the filter is active\
                $this->crud->addClause('whereIn', 'driver_id', json_decode($value));
            });

        $this->crud->addFilter([
            'name'        => 'car_id',
            'type'        => 'select2_ajax',
            'label'       => trans('admin.Car Plate'),
            'placeholder' => trans('admin.Pick a car')
        ],
            url('admin/filterorder/car'), // the ajax route
            function($value) { // if the filter is active\
                $this->crud->addClause('where', 'car_id', $value);
            });

        $dataStatus = [];
        $statuss = RequestStatus::all();
        foreach ($statuss as $status)
        {
            $dataStatus[$status->id] = $status->name_en;
        }
        $this->crud->addFilter([
                'name'        => 'status',
                'type'        => 'select2_multiple',
                'label'       => trans('admin.Status'),
                'placeholder' => trans('admin.Pick a status')

            ]
            , function()use($dataStatus) {
                return
                    $dataStatus
                    ;
            }
            , // the ajax route
            function($value) { // if the filter is active\
                $this->crud->addClause('whereIn', 'status', json_decode($value));
            });
        //Area Filter
        $dataArea = [];
        $areas = Areas::all();
        foreach ($areas as $area)
        {
            $dataArea[$area->id] = $area->name_en;
        }
        $this->crud->addFilter([
                'name'        => 'area_from',
                'type'        => 'select2_multiple',
                'label'       => trans('admin.Area From'),
                'placeholder' => trans('admin.Pick an area')

            ]
            , function()use($dataArea) {
                return
                    $dataArea
                    ;
            }
            , // the ajax route
            function($value) { // if the filter is active\
                $this->crud->addClause('whereIn', 'area_from', json_decode($value));
            });
        $this->crud->addFilter([
                'name'        => 'area_to',
                'type'        => 'select2_multiple',
                'label'       => trans('admin.Area To'),
                'placeholder' => trans('admin.Pick an area')

            ]
            , function()use($dataArea) {
                return
                    $dataArea
                    ;
            }
            , // the ajax route
            function($value) { // if the filter is active\
                $this->crud->addClause('whereIn', 'area_to', json_decode($value));
            });
        $this->crud->addFilter([
                'name'        => 'is_paid',
                'type'        => 'select2_multiple',
                'label'       => trans('admin.Paid'),
                'placeholder' => trans('admin.Pick Payment status')

            ]
            , function(){
                return
                    [1=>'Paid',0=>'Not Paid']
                    ;
            }
            , // the ajax route
            function($value) { // if the filter is active\
                $this->crud->addClause('whereIn', 'area_to', json_decode($value));
            });

        $this->crud->addFilter([
            'name'        => 'date',
            'type'        => 'date',
            'label'       => trans('admin.Date'),
            'placeholder' => trans('admin.Pick a date')
        ],
            false,
            function ($value) { // if the filter is active, apply these constraints
                $this->crud->addClause('where', 'date', 'like', '%'.$value.'%');
            });
        $this->crud->addColumn([ // Text
            'name' => 'customers',
            'label' => trans('admin.Customer'),
            'type' => 'relationship',
            'attribute' => 'mobile', // foreign key attribute that is shown to use

            'orderable' => true,
            'orderLogic' => function ($query, $column, $columnDirection) {
                return $query->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
                    ->orderBy('customers.name', $columnDirection)->select('orders.*');
            }

        ]);
        $this->crud->addColumn([ // Text
            'name' => 'invoice_unique_id',
            'label' => trans('admin.Order Id'),
            'type' => 'text',
            'orderable' => true,
            'orderLogic' => function ($query, $column, $columnDirection) {
                return $query->orderBy('invoice_unique_id', $columnDirection)->select('orders.*');
            }

        ]);
        $this->crud->addColumn([ // Text
            'name' => 'invoice_id',
            'label' => trans('admin.Invoice'),
            'type' => 'text',
            'orderable' => true,
            'orderLogic' => function ($query, $column, $columnDirection) {
                return $query->orderBy('invoice_id', $columnDirection)->select('orders.*');
            }

        ]);
        $this->crud->addColumn([ // Text
            'name' => 'driver',
            'label' => trans('admin.Driver'),
            'type' => 'relationship',
            'attribute'=>'name',
            'orderable' => true,
            'orderLogic' => function ($query, $column, $columnDirection) {
                return $query->orderBy('driver_id', $columnDirection)->select('orders.*');
            }

        ]);
        $this->crud->addColumn([ // Text
            'name' => 'paymenttext',
            'label' => trans('admin.Payment'),
            'type' => 'text',
            'orderable' => true,
            'orderLogic' => function ($query, $column, $columnDirection) {
                return $query->orderBy('payment_type', $columnDirection)->select('orders.*');
            }

        ]);
        $this->crud->addColumn([ // Text
            'name' => 'paid_status',
            'label' => trans('admin.Paid'),
            'type' => 'text',
            'orderable' => true,
            'orderLogic' => function ($query, $column, $columnDirection) {
                return $query->orderBy('is_paid', $columnDirection)->select('orders.*');
            }

        ]);
        $this->crud->addColumn([ // Text
            'name' => 'created_at',
            'label' => trans('admin.Date'),
            'type' => 'text',
            'orderable' => true,

        ]);
        $this->crud->removeAllButtons();

        $this->crud->addButtonFromModelFunction('line', 'Xero Invoice', 'openGoogle', 'beginning');
        $this->crud->disableBulkActions();
//        $this->crud->
//        $this->crud->enableExportButtons();
//        $this->crud->enableResponsiveTable();
//        $this->crud->enablePersistentTable();
//        $this->addCustomCrudFilters();

    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addField([ // Text
            'name'  => 'name',
            'label' => trans('admin.Name'),
            'type'  => 'text',
            'tab'   => 'Texts',


        ]);
        CRUD::addField([ // Text
            'name'  => 'mobile',
            'label' => trans('admin.Mobile'),
            'type'  => 'text',
            'tab'   => 'Texts',


        ]);

        CRUD::addField([ // Text
            'name'  => 'type',
            'label' => trans('admin.Type'),
            'type' => 'select_from_array',
            'options' => [Customers::CUSTOMER=>'customer',Customers::GARAGE=>'garage'],
            'allows_null' => false,
            'tab'   => 'Texts',
        ]);
        CRUD::addField([ // Text
            'name'  => 'status',
            'label' => trans('admin.Status'),
            'type' => 'select_from_array',
            'options' => [Customers::ACTIVE=>'Active',Customers::BLOCK=>'Block'],
            'allows_null' => false,
            'tab'   => 'Texts',
        ]);



        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
    public static function fetch(\Illuminate\Http\Request  $request)
    {
        $areas = Customers::where('name','like','%'.$request->q.'%')->get(['id','name']);
        $data = [] ;
        foreach ($areas as $area)
        {
            $data[] = ['id'=>$area->id , 'name'=>$area->name];
        }

        return $data;

    }

}
