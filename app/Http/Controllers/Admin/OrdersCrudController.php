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
use http\Url;
use Illuminate\Support\Facades\App;
use Redirect;
class OrdersCrudController extends CrudController
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
        CRUD::setRoute(config('backpack.base.route_prefix') . '/orders');
        CRUD::setEntityNameStrings(trans('admin.order'), trans('admin.orders'));

    }

    public function store()
    {
        $lastOrder = Orders::OrderBy('id', 'DESC')->first();
        $lastOrderId = 0 ;
        if($lastOrder)
        {
            $lastOrderId = $lastOrder->id;
        }
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->unsetValidation(); // validation has already been run

//        $chkXeroToken = checkxerotoken();
//        if(!$chkXeroToken)
//        {
//            \Alert::error(trans('admin.Somethings went wrong please try again'))->flash();
//
//            return  ;
//        }

        $response = $this->traitStore();

        $order = Orders::find($this->crud->entry->id);
//            $order->invoice_unique_id = 'WbMll-'.generateRandomString(5);
        if ($order->payment_type == Orders::KNET_PAYMENT) {
//               $paymentLink =  tappayment($order);


        }
        $url = generateRandomString(10);
        $order->url = $url;
        $order->payment_link = url('/') . '/payorder/' . $url;
        if (!$order->paid_by) {
            $order->paid_by = $order->customer_id;
        }
//        if (!$order->is_paid) {
//            $order->is_paid = 0;
//            $order->save();
//
//        }
        $driver = User::where('id', $order->driver_id)->first();
        if (!$order->comission) {
            if ($driver) {
                if ($driver->commission_type == 1) {
                    $order->comission = $driver->commission;
                } else {
                    $order->comission = ($order->amount * $driver->commission / 100);
                }
            }
        }
        if($order->car_model_text && $order->car_model_text!='')
        {
            $carmodel = CarModel::create(['name_en' => $order->car_model_text, 'name_ar' => $order->car_model_text,
                'car_make' => 4]);
            $order->car_model = $carmodel->id;
            $order->car_make = 4;

        }
        $order->invoice_unique_id = 'WbMLL-ORD-' . ($lastOrderId + 1);
        $order->save();
        if ($order->amount == 0 || $order->amount == NULL) {
            try {
                xeroquotes($order->id, 1);
            } catch (\Exception $exception) {
            }
        } else {
            try {
                xeroinvoice($order->id, 1);
            } catch (\Exception $exception) {
            }
        }
        if ($order->is_paid) {
            if($order->payment_type == Orders::CASH_PAYMENT)
            {
                $order->status = Orders::COMPLETED_ORDER;
                $order->save();
            }
            $account = isset($driver)? $driver->xero_account : null ;
            if(!$account)
            {
                $account  = ($order->payment_type == Orders::KNET_PAYMENT) ? config('app.XEROKNET'): config('app.XEROCASH');
            }
            try {
                $order->refresh();
                (addpaymentxero($order->id, 1, $order->amount, $account));
            } catch (\Exception $exception) {
            }
        }


        return $response;
    }


    protected function setupListOperation()
    {


        $this->crud->addColumn([ // Text
            'name' => 'invoice_unique_id',
            'label' => trans('admin.Order Id'),
            'orderable' => true]);

        $this->crud->addColumn([ // Text
            'name' => 'customers',
            'label' => trans('admin.Customer'),
            'type' => 'relationship',
            'attribute' => 'mobile', // foreign key attribute that is shown to use

            'orderable' => true,
            'orderLogic' => function ($query, $column, $columnDirection) {
                return $query->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
                    ->orderBy('customers.mobile', $columnDirection)->select('orders.*');
            }
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
            'label' => trans('admin.status'),
            'type' => 'relationship',
            'attribute' => 'name',
            'orderable' => true,
            'orderLogic' => function ($query, $column, $columnDirection) {
                return $query->leftJoin('request_status', 'orders.status', '=', 'request_status.id')
                    ->orderBy('request_status.name_en', $columnDirection)->select('orders.*');
            }
        ]);

        $this->crud->addColumn([ // Text
            'name' => 'areafrom',
            'label' => trans('admin.From'),
            'type' => 'relationship',
            'attribute' => 'name',
            'orderable' => true,
            'orderLogic' => function ($query, $column, $columnDirection) {
                return $query->leftJoin('areas', 'orders.area_from', '=', 'areas.id')
                    ->orderBy('areas.name_en', $columnDirection)->select('orders.*');
            }

        ]);

        $this->crud->addColumn([ // Text
            'name' => 'areato',
            'label' => trans('admin.To'),
            'type' => 'relationship',
            'attribute' => 'name',
            'orderable' => true,
            'orderLogic' => function ($query, $column, $columnDirection) {
                return $query->leftJoin('areas', 'orders.area_to', '=', 'areas.id')
                    ->orderBy('areas.name_en', $columnDirection)->select('orders.*');
            }
        ]);

        $this->crud->addColumn([ // Text
            'name' => 'paymenttext',
            'label' => trans('admin.Payment Type'),
            'attribute' => 'paymenttext',
            'orderable' => true,
            'orderLogic' => function ($query, $column, $columnDirection) {
                return $query->orderBy('orders.payment_type', $columnDirection)->select('orders.*');
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
//            'orderable' => true
        ]);

        $this->crud->addColumn([ // Text
            'name' => 'date',
            'label' => trans('admin.Date'),
            'type' => 'text',
            'orderable' => true

        ]);
        $this->crud->addColumn([ // Text
            'name' => 'collected_date',
            'label' => trans('admin.collected_date'),
            'type' => 'text',
            'orderable' => true

        ]);
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
                'placeholder' => 'Pick a customer'

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
                'placeholder' => 'Pick a driver'

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
            'placeholder' => 'Pick a car'
        ],
            url('admin/filterorder/car'), // the ajax route
            function($value) { // if the filter is active\
                $this->crud->addClause('where', 'car_id', $value);
            });

        $dataStatus = [];
        $statuss = RequestStatus::all();
        foreach ($statuss as $status)
        {
            $dataStatus[$status->id] = $status->name;
        }
        $this->crud->addFilter([
                'name'        => 'status',
                'type'        => 'select2_multiple',
                'attribute' => 'name',
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
            $dataArea[$area->id] = $area->name;
        }
        $this->crud->addFilter([
                'name'        => 'area_from',
                'type'        => 'select2_multiple',
                'attribute' => 'name',
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
                'attribute' => 'name',
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
                    [1=>trans('admin.Paid'),0=>trans('admin.Not Paid')]
                    ;
            }
            , // the ajax route
            function($value) { // if the filter is active\
                $this->crud->addClause('whereIn', 'is_paid', json_decode($value));
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
//        $this->addCustomCrudFilters();
        $this->crud->addButtonFromModelFunction('line', 'share ', 'openGoogle', 'beginning');
        $this->crud->disableBulkActions();
        $this->crud->removeButton('delete');
        $this->crud->enableExportButtons();
        $this->crud->enableResponsiveTable();
        $this->crud->disablePersistentTable();
//        $this->crud
    }

    protected function setupUpdateOperation()
    {
        $readonly = false;

        if ((request()->route('id'))) {
            $order = Orders::find(request()->route('id'));
            $orderCount = OrderInvoicess::where('orders_id',request()->route('id'))->first();
            if($order->is_paid || $order->partially_paid || $orderCount)

            {
                $readonly = true;
            }
            if ($order->payment_type == Orders::KNET_PAYMENT) {
                $readonly_ispaid = 'readonly';
            }
        }
        if($readonly)
        {
            $this->crud->removeSaveActions(['save_and_edit','save_and_back','save_and_new']);
        }
        else
        {
            $this->crud->removeSaveActions(['save_and_back','save_and_new']);
        }


    }
    protected function setupCreateOperation()
    {

        $readonly_ispaid = '';
        $readonly = false;

        $order = null;
        if ((request()->route('id'))) {
            $order = Orders::find(request()->route('id'));
            $orderCount = OrderInvoicess::where('orders_id',request()->route('id'))->first();
            if($order->is_paid || $order->partially_paid || $orderCount)
            {
                $readonly = true;
            }
            if ($order->payment_type == Orders::KNET_PAYMENT) {
                $readonly_ispaid = 'readonly';
            }
        }
        $lastOrder = Orders::OrderBy('id', 'DESC')->first();
        $lastOrderId = 0 ;
        if($lastOrder)
        {
            $lastOrderId = $lastOrder->id;
        }
        CRUD::setValidation(StoreRequest::class);
        if($readonly)
        {
            $orderCount = OrderInvoicess::where('orders_id',request()->route('id'))->first();
            if($orderCount) {
                $invoice = Invoices::find($orderCount->invoices_id);
                if($invoice) {
                    CRUD::addField([   // CustomHTML
                        'name' => 'separator',
                        'type' => 'custom_html',
                        'tab' => 'Texts',
                        'value' => '<p class="help-block">The Order Has been linked to invoice : <a href="' . route('payInvoice', $invoice->magic_link) . '" target="_blank"> ' . $invoice->invoice_unique_id . '</a></p>'
                    ]);
                }
            }
        }
        CRUD::addField([ // Text
            'name' => 'invoice_unique_id',
            'label' => trans('admin.Order Id'),
            'type' => 'text',
            'tab' => 'Texts',
            'default' => 'WbMLL-ORD-' . ($lastOrderId + 1),
            'attributes' => [
                'readonly' => 'readonly',
            ],
        ]);


//        $this->crud->addButton('share');
        $this->crud->addButtonFromModelFunction('line', 'share ', 'openGoogle', 'beginning');

        if(!$readonly) {
            CRUD::addField([  // Select2
                'label' => trans('admin.Customer'),
                'type' => 'relationship',
                'name' => 'customer_id', // the db column for the foreign key
                'entity' => 'customers', // the method that defines the relationship in your Model
                'attribute' => 'mobile', // foreign key attribute that is shown to use
                'tab' => 'Texts',
                'data_source' => url("/admin/fetch/customer"), // url to controller search function (with /{id} should return model)

                'inline_create' => [ // specify the entity in singular
                    'include_all_form_fields' => false,
                    'entity' => 'customers', // the entity in singular
                    'force_select' => true, // should the inline-created entry be immediately selected?
                    'include_main_form_fields' => ['car_plate_id'], // pass certain fields from the main form to the modal

                ]
            ]);
            CRUD::addField([  // Select2
                'label' => trans('admin.Paid By'),
                'type' => 'relationship',
                'name' => 'paidby', // the db column for the foreign key
                'entity' => 'paidby', // the method that defines the relationship in your Model
                'attribute' => 'mobile', // foreign key attribute that is shown to user
                'data_source' => url("/admin/fetch/customer"), // url to controller search function (with /{id} should return model)

                'inline_create' => [ // specify the entity in singular
                    'include_all_form_fields' => false,
                    'entity' => 'customers', // the entity in singular
                    'force_select' => true, // should the inline-created entry be immediately selected?
                    'include_main_form_fields' => ['car_plate_id'], // pass certain fields from the main form to the modal

                ],
                'tab' => 'Texts',
            ]);
            CRUD::addField([  // Select2
                'label' => trans('admin.Car Manufacturer'),
                'type' => 'select2',
                'name' => 'car_make', // the db column for the foreign key
                'entity' => 'carmakes', // the method that defines the relationship in your Model
                'attribute' => 'name_en', // foreign key attribute that is shown to use
                'tab' => 'Texts',
                'delay' => 500, // the minimum amount of time between ajax requests when searching in the field
                'data_source' => url("/admin/fetch/carmakes"), // url to controller search function (with /{id} should return model)


            ]);
            CRUD::addField([
                'tab' => 'Texts',

                'name' => 'crmnotexits',
                'label' => trans('admin.Car Make not exists'),
                'type' => 'boolean',
                // optionally override the Yes/No texts
                'options' => [1 => trans('admin.Yes')],
                'attributes' => [
                    'class' => ' not_exits_make-class',
                ]
            ]);
//            $this->crud->addField([ // select2_from_ajax: 1-n relationship
//                'tab' => 'Texts',
//                'type' => 'text',
//
//                'name' => 'car_model_text', // the column that contains the ID of that connected entity;
//                'label' => trans('admin.Car model'), // placeholder for the select
//                'placeholder' => trans('admin.Enter model'), // placeholder for the select
//                'attributes' => [
//                    'class' => 'form-control modeltext-class',
//                ],
//                // 'method'                    => 'GET', // optional - HTTP method to use for the AJAX call (GET, POST)
//            ]);
//            $this->crud->addField([ // select2_from_ajax: 1-n relationship
//                'tab' => 'Texts',
//                'include_all_form_fields' => true, //sends the other form fields along with the request so it can be filtered.
//                'minimum_input_length' => 0, 'label' => trans('admin.Car Model'), // Table column heading
//                'type' => 'select2_from_ajax',
//                'name' => 'car_model', // the column that contains the ID of that connected entity;
//                'entity' => 'carmodel', // the method that defines the relationship in your Model
//                'attribute' => 'name_en', // foreign key attribute that is shown to user
//                'data_source' => url("/admin/fetch/carmodel"), // url to controller search function (with /{id} should return model)
//                'placeholder' => 'Select model', // placeholder for the select
//                'dependencies' => ['car_make'], // when a dependency changes, this select2 is reset to null
//                'attributes' => [
//                    'class' => 'form-control modelselect-class',
//                ],
//                // 'method'                    => 'GET', // optional - HTTP method to use for the AJAX call (GET, POST)
//            ]);
            CRUD::addField([  // Select2
                'label' => trans('admin.Car Plate ID'),
                'type' => 'relationship',
                'name' => 'car_id', // the db column for the foreign key
                'entity' => 'cars', // the method that defines the relationship in your Model
                'attribute' => 'car_plate_id', // foreign key attribute that is shown to use
                'tab' => 'Texts',
                'data_source' => url("/admin/fetch/car"), // url to controller search function (with /{id} should return model)

                'inline_create' => [ // specify the entity in singular
                    'include_all_form_fields' => false,
                    'entity' => 'carsorders', // the entity in singular
                    'force_select' => true, // should the inline-created entry be immediately selected?
                    'include_main_form_fields' => ['car_plate_id'], // pass certain fields from the main form to the modal

                ]
            ]);
            CRUD::addField([  // Select2
                'label' => trans('admin.Area From'),
                'type' => 'relationship',
                'name' => 'area_from', // the db column for the foreign key
                'entity' => 'areafrom', // the method that defines the relationship in your Model
                'attribute' => 'name_en', // foreign key attribute that is shown to use
                'tab' => 'Texts',
                'delay' => 500, // the minimum amount of time between ajax requests when searching in the field
                'data_source' => url("/admin/fetch/areas"), // url to controller search function (with /{id} should return model)

                'inline_create' => [ // specify the entity in singular
                    'entity' => 'areas', // the entity in singular
                    'force_select' => true, // should the inline-created entry be immediately selected?
                    'modal_class' => 'modal-dialog modal-xl', // use modal-sm, modal-lg to change width
                    'modal_route' => route('areas-inline-create'), // InlineCreate::getInlineCreateModal()
                    'create_route' => route('areas-inline-create-save'), // InlineCreate::storeInlineCreate()

                    'include_main_form_fields' => ['name_en', 'name_ar'], // pass certain fields from the main form to the modal
                ]
            ]);
            CRUD::addField([  // Select2
                'label' => trans('admin.Area To'),
                'type' => 'relationship',
                'name' => 'area_to', // the db column for the foreign key
                'entity' => 'areato', // the method that defines the relationship in your Model
                'attribute' => 'name_en', // foreign key attribute that is shown to use
                'tab' => 'Texts',
                'delay' => 500, // the minimum amount of time between ajax requests when searching in the field
                'data_source' => url("/admin/fetch/areas"), // url to controller search function (with /{id} should return model)

                'inline_create' => [ // specify the entity in singular
                    'entity' => 'areas', // the entity in singular
                    'force_select' => true, // should the inline-created entry be immediately selected?
                    'modal_class' => 'modal-dialog modal-xl', // use modal-sm, modal-lg to change width
                    'modal_route' => route('areas-inline-create'), // InlineCreate::getInlineCreateModal()
                    'create_route' => route('areas-inline-create-save'), // InlineCreate::storeInlineCreate()
                    'include_main_form_fields' => ['name_en', 'name_ar'], // pass certain fields from the main form to the modal
                ]
            ]);

            $drivs = User::where('is_driver',1)->pluck('name','id')->toArray();
//dd($drivs);
            CRUD::addField([  // Select2
                'label' => trans('admin.Driver'),
                'type' => 'select_from_array',
                'name' => 'driver_id', // the db column for the foreign key
                'entity' => 'driver', // the method that defines the relationship in your Model
//                'attribute' => 'name', // foreign key attribute that is shown to use
                'tab' => 'Texts',
                'options'     =>
                    $drivs,
//                'data_source' => url("/admin/fetch/driver"), // url to controller search function (with /{id} should return model)
                'attributes' => [
                    'class' => 'form-control comissiondriverlist-class',

                ],
            ]);
            CRUD::addField([  // Select2
                'label' => trans('admin.Status'),
                'type' => 'select2',
                'name' => 'status', // the db column for the foreign key
                'entity' => 'requeststatus', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to use
                'tab' => 'Texts',
                'default' => '2',
                'allows_null' => true,

            ]);
            CRUD::addField([ // Text
                'name' => 'address',
                'label' => trans('admin.Address'),
                'type' => 'text',
                'tab' => 'Texts',

            ]);
            CRUD::addField([ // Text
                'name' => 'remarks',
                'label' => trans('admin.remarks'),
                'type' => 'textarea',
                'tab' => 'Texts',

            ]);
            $this->crud->addField([   // DateTime
                'tab' => 'Texts',

                'name' => 'date',
                'label' => trans('admin.Datetime'),
                'type' => 'datetime_picker',
                'allows_null' => true,
                'default' => Carbon::now()->format('Y-m-d H:i:s'),
                // optional:
                'datetime_picker_options' => [
                    'format' => 'DD/MM/YYYY HH:mm',
                    'language' => 'en',
                ],
            ]);
            CRUD::addField([ // Text
                'name' => 'amount',
                'label' => trans('admin.Amount'),
                'type' => 'text',
                'tab' => 'Texts',
                'default' => 0


            ]);
            CRUD::addField([ // Text
                'name' => 'comission',
                'label' => trans('admin.Comission'),
                'type' => 'text',
                'tab' => 'Texts',
                'default' => 0,
                'attributes' => [
//                'readonly' => 'readonly',
                    'class' => 'form-control comissiondriver-class',

                ],

            ]);
            CRUD::addField([ // Text
                'name' => 'payment_type',
                'label' => trans('admin.Payment Type'),
                'type' => 'select_from_array',
                'tab' => 'Texts',
                'allows_null' => false,

                'attributes' => [
                    'class' => 'form-control paymenttype-class',

                ],
                'options' => [
                    // the key will be stored in the db, the value will be shown as label;
                    Orders::CASH_PAYMENT => trans("admin.Cash"),
                    Orders::KNET_PAYMENT => trans("admin.Knet"),
//                Orders::LATE_PAYMENT => "Late"
                ],
                'default' => Orders::KNET_PAYMENT,

            ]);
            if ($readonly_ispaid) {
                CRUD::addField([ // Text
                    'name' => 'is_paid',
                    'label' => trans('admin.Is Paid'),
                    'type' => 'select_from_array',
                    'tab' => 'Texts',
                    'allows_null' => false,

                    'attributes' => [
                        'class' => 'form-control paidpayment-class',
                        'readonly' => 'readonly',
                    ],
                    'options' => [
                        // the key will be stored in the db, the value will be shown as label;
                        '0' => trans('admin.No'),
                        '1' => trans('admin.Yes')
                    ],


                ]);
            } else {
                CRUD::addField([ // Text
                    'name' => 'is_paid',
                    'label' => trans('admin.Is Paid'),
                    'type' => 'select_from_array',
                    'allows_null' => false,

                    'tab' => 'Texts',
                    'attributes' => [
                        'class' => 'form-control paidpayment-class',

                    ],
                    'options' => [
                        // the key will be stored in the db, the value will be shown as label;
                        0 => trans('admin.No'),
                        1 => trans('admin.Yes')
                    ],
                    'default' => 0,

                ]);
            }
            CRUD::addField([ // Text
                'name' => 'link_generated',
                'label' => trans('admin.Payment Invoice Generted'),
                'type' => 'hidden',
                'value' => 0,

            ]);
            if($order)
            {
                CRUD::addField([   // CustomHTML
                    'name' => 'separator',
                    'type' => 'custom_html',
                    'tab' => 'Texts',
                    'hint'=>'Please save Order Infor before Copy',
                    'value' => '<er style="display:none"  id="texttoshare" ></er><input type="text" style="display:none" value="ToTest"  class="myInput" id="myInput"/><button class="btn btn-success" onclick="shareOrder('.$order->id.')" type="button" value="share" class="form-control"><i class="lab la-whatsapp"></i> '.trans('admin.share').'</button><button class="btn btn-success" style="margin: 10px;" onclick="copyOrder('.$order->id.')" type="button" value="share" class="form-control"><i class="la la-copy"></i> '.trans('admin.Copy To Clipboard').'</button>
                        <p class="help-block">'.trans('admin.Please save Order Info before Copy').'</p>'                ]);
            }

            CRUD::addField([ // Text
                'name' => 'payment_link',
                'label' => trans('admin.Payment Link'),
                'type' => 'text',
                'tab' => 'Texts',
                'attributes' => [
                    'class' => 'form-control some-class',
                    'readonly' => 'readonly',
                ],

            ]);
        }
        else
        {
            CRUD::addField([  // Select2
                'label' => trans('admin.Customer'),
                'type' => 'relationship',
                'name' => 'customer_id', // the db column for the foreign key
                'entity' => 'customers', // the method that defines the relationship in your Model
                'attribute' => 'mobile', // foreign key attribute that is shown to use
                'tab' => 'Texts',
                'data_source' => url("/admin/fetch/customer"), // url to controller search function (with /{id} should return model)
                'attributes' => [
                    'disabled' => 'disabled',
                ],
                'inline_create' => [ // specify the entity in singular
                    'include_all_form_fields' => false,
                    'entity' => 'customers', // the entity in singular
                    'force_select' => true, // should the inline-created entry be immediately selected?
                    'include_main_form_fields' => ['car_plate_id'], // pass certain fields from the main form to the modal

                ]
            ]);
            CRUD::addField([  // Select2
                'label' => trans('admin.Paid By'),
                'type' => 'relationship',
                'name' => 'paidby', // the db column for the foreign key
                'entity' => 'paidby', // the method that defines the relationship in your Model
                'attribute' => 'mobile', // foreign key attribute that is shown to user
                'data_source' => url("/admin/fetch/customer"), // url to controller search function (with /{id} should return model)
                'attributes' => [
                    'disabled' => 'disabled',
                ],
                'inline_create' => [ // specify the entity in singular
                    'include_all_form_fields' => false,
                    'entity' => 'customers', // the entity in singular
                    'force_select' => true, // should the inline-created entry be immediately selected?
                    'include_main_form_fields' => ['car_plate_id'], // pass certain fields from the main form to the modal

                ],
                'tab' => 'Texts',
            ]);
            CRUD::addField([  // Select2
                'label' => trans('admin.Car Manufacturer'),
                'type' => 'select2',
                'name' => 'car_make', // the db column for the foreign key
                'entity' => 'carmakes', // the method that defines the relationship in your Model
                'attribute' => 'name_en', // foreign key attribute that is shown to use
                'tab' => 'Texts',
                'delay' => 500, // the minimum amount of time between ajax requests when searching in the field
                'data_source' => url("/admin/fetch/carmakes"), // url to controller search function (with /{id} should return model)

                'attributes' => [
                    'disabled' => 'disabled',
                ],
            ]);
            CRUD::addField([
                'tab' => 'Texts',

                'name' => 'crmnotexits',
                'label' => trans('admin.Car Make not exists'),
                'type' => 'boolean',
                // optionally override the Yes/No texts
                'options' => [1 => trans('admin.Yes')],
                'attributes' => [
                    'class' => ' not_exits_make-class',
                    'disabled' => 'disabled',

                ]
            ]);
//            $this->crud->addField([ // select2_from_ajax: 1-n relationship
//                'tab' => 'Texts',
//                'type' => 'text',
//
//                'name' => 'car_model_text', // the column that contains the ID of that connected entity;
//                'label' => trans('admin.Car model'), // placeholder for the select
//                'placeholder' => trans('admin.Enter model'), // placeholder for the select
//                'attributes' => [
//                    'class' => 'form-control modeltext-class',
//                    'disabled' => 'disabled',
//                ],
//                // 'method'                    => 'GET', // optional - HTTP method to use for the AJAX call (GET, POST)
//            ]);
//            $this->crud->addField([ // select2_from_ajax: 1-n relationship
//                'tab' => 'Texts',
//                'include_all_form_fields' => true, //sends the other form fields along with the request so it can be filtered.
//                'minimum_input_length' => 0, 'label' => trans('admin.Car Model'), // Table column heading
//                'type' => 'select2_from_ajax',
//                'name' => 'car_model', // the column that contains the ID of that connected entity;
//                'entity' => 'carmodel', // the method that defines the relationship in your Model
//                'attribute' => 'name_en', // foreign key attribute that is shown to user
//                'data_source' => url("/admin/fetch/carmodel"), // url to controller search function (with /{id} should return model)
//                'placeholder' => 'Select model', // placeholder for the select
//                'dependencies' => ['car_make'], // when a dependency changes, this select2 is reset to null
//                'attributes' => [
//                    'class' => 'form-control modelselect-class',
//                    'disabled' => 'disabled',
//                ],
//                // 'method'                    => 'GET', // optional - HTTP method to use for the AJAX call (GET, POST)
//            ]);
            CRUD::addField([  // Select2
                'label' => trans('admin.Car Plate ID'),
                'type' => 'relationship',
                'name' => 'car_id', // the db column for the foreign key
                'entity' => 'cars', // the method that defines the relationship in your Model
                'attribute' => 'car_plate_id', // foreign key attribute that is shown to use
                'tab' => 'Texts',
                'data_source' => url("/admin/fetch/car"), // url to controller search function (with /{id} should return model)

                'inline_create' => [ // specify the entity in singular
                    'include_all_form_fields' => false,
                    'entity' => 'carsorders', // the entity in singular
                    'force_select' => true, // should the inline-created entry be immediately selected?
                    'include_main_form_fields' => ['car_plate_id'], // pass certain fields from the main form to the modal
                ],
                'attributes' => [
                    'disabled' => 'disabled',
                ],
            ]);
            CRUD::addField([  // Select2
                'label' => trans('admin.Area From'),
                'type' => 'relationship',
                'name' => 'area_from', // the db column for the foreign key
                'entity' => 'areafrom', // the method that defines the relationship in your Model
                'attribute' => 'name_en', // foreign key attribute that is shown to use
                'tab' => 'Texts',
                'delay' => 500, // the minimum amount of time between ajax requests when searching in the field
                'data_source' => url("/admin/fetch/areas"), // url to controller search function (with /{id} should return model)

                'inline_create' => [ // specify the entity in singular
                    'entity' => 'areas', // the entity in singular
                    'force_select' => true, // should the inline-created entry be immediately selected?
                    'modal_class' => 'modal-dialog modal-xl', // use modal-sm, modal-lg to change width
                    'modal_route' => route('areas-inline-create'), // InlineCreate::getInlineCreateModal()
                    'create_route' => route('areas-inline-create-save'), // InlineCreate::storeInlineCreate()

                    'include_main_form_fields' => ['name_en', 'name_ar'], // pass certain fields from the main form to the modal
                ],
                'attributes' => [
                    'disabled' => 'disabled',
                ],
            ]);
            CRUD::addField([  // Select2
                'label' => trans('admin.Area To'),
                'type' => 'relationship',
                'name' => 'area_to', // the db column for the foreign key
                'entity' => 'areato', // the method that defines the relationship in your Model
                'attribute' => 'name_en', // foreign key attribute that is shown to use
                'tab' => 'Texts',
                'delay' => 500, // the minimum amount of time between ajax requests when searching in the field
                'data_source' => url("/admin/fetch/areas"), // url to controller search function (with /{id} should return model)

                'inline_create' => [ // specify the entity in singular
                    'entity' => 'areas', // the entity in singular
                    'force_select' => true, // should the inline-created entry be immediately selected?
                    'modal_class' => 'modal-dialog modal-xl', // use modal-sm, modal-lg to change width
                    'modal_route' => route('areas-inline-create'), // InlineCreate::getInlineCreateModal()
                    'create_route' => route('areas-inline-create-save'), // InlineCreate::storeInlineCreate()
                    'include_main_form_fields' => ['name_en', 'name_ar'], // pass certain fields from the main form to the modal
                ],
                'attributes' => [
                    'disabled' => 'disabled',
                ],
            ]);
            $customers = User::where('is_driver',1)->get(['id','name']);
            $data = [] ;
            foreach ($customers as $customer)
            {
                $data[$customer->id] = [$customer->name];
            }
            $drivs = User::where('is_driver',1)->pluck('name','id')->toArray();

            CRUD::addField([  // Select2
                'label' => trans('admin.Driver'),
                'type' => 'select_from_array',
                'name' => 'driver_id', // the db column for the foreign key
//                'entity' => 'driver', // the method that defines the relationship in your Model
//                'attribute' => 'name', // foreign key attribute that is shown to use
                'tab' => 'Texts',
                'options'     =>
                    $drivs,
//                'data_source' => url("/admin/fetch/driver"), // url to controller search function (with /{id} should return model)
                'attributes' => [
                    'class' => 'form-control comissiondriverlist-class',
                    'disabled' => 'disabled',
                ],
            ]);
            CRUD::addField([  // Select2
                'label' => trans('admin.Status'),
                'type' => 'select2',
                'name' => 'status', // the db column for the foreign key
                'entity' => 'requeststatus', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to use
                'tab' => 'Texts',
                'default' => '2',
                'allows_null' => true,
                'attributes' => [
                    'disabled' => 'disabled',
                ],
            ]);
            CRUD::addField([ // Text
                'name' => 'address',
                'label' => trans('admin.Address'),
                'type' => 'text',
                'tab' => 'Texts',
                'attributes' => [
                    'disabled' => 'disabled',
                ],
            ]);
            CRUD::addField([ // Text
                'name' => 'remarks',
                'label' => trans('admin.remarks'),
                'type' => 'textarea',
                'tab' => 'Texts',
                'attributes' => [
                    'disabled' => 'disabled',
                ],
            ]);
            $this->crud->addField([   // DateTime
                'tab' => 'Texts',

                'name' => 'date',
                'label' => trans('admin.Datetime'),
                'type' => 'datetime_picker',
                'allows_null' => true,
                'default' => Carbon::now()->format('Y-m-d H:i:s'),
                // optional:
                'datetime_picker_options' => [
                    'format' => 'DD/MM/YYYY HH:mm',
                    'language' => 'en',
                ],
                'attributes' => [
                    'disabled' => 'disabled',
                ],
            ]);
            CRUD::addField([ // Text
                'name' => 'amount',
                'label' => trans('admin.Amount'),
                'type' => 'text',
                'tab' => 'Texts',
                'default' => 0,
                'attributes' => [
                    'disabled' => 'disabled',
                ],

            ]);
            CRUD::addField([ // Text
                'name' => 'comission',
                'label' => trans('admin.Comission'),
                'type' => 'text',
                'tab' => 'Texts',
                'default' => 0,
                'attributes' => [
//                'readonly' => 'readonly',
                    'class' => 'form-control comissiondriver-class',
                    'disabled' => 'disabled',
                ],

            ]);
            CRUD::addField([ // Text
                'name' => 'payment_type',
                'label' => trans('admin.Payment Type'),
                'type' => 'select_from_array',
                'tab' => 'Texts',
                'allows_null' => true,
                'attributes' => [
                    'class' => 'form-control paymenttype-class',
                    'disabled' => 'disabled',
                ],
                'options' => [
                    // the key will be stored in the db, the value will be shown as label;
                    Orders::CASH_PAYMENT => trans("admin.Cash"),
                    Orders::KNET_PAYMENT => trans("admin.Knet"),
//                Orders::LATE_PAYMENT => "Late"
                ],
                'default' => Orders::CASH_PAYMENT,

            ]);
            if ($readonly_ispaid) {
                CRUD::addField([ // Text
                    'name' => 'is_paid',
                    'label' => trans('admin.Is Paid'),
                    'type' => 'select_from_array',
                    'tab' => 'Texts',
                    'allows_null' => true,

                    'attributes' => [
                        'class' => 'form-control paidpayment-class',
                        'readonly' => 'readonly',
                    ],
                    'options' => [
                        // the key will be stored in the db, the value will be shown as label;
                        '0' => trans('admin.No'),
                        '1' => trans('admin.Yes')
                    ],


                ]);
            } else {
                CRUD::addField([ // Text
                    'name' => 'is_paid',
                    'label' => trans('admin.Is Paid'),
                    'type' => 'select_from_array',
                    'allows_null' => true,

                    'tab' => 'Texts',
                    'attributes' => [
                        'class' => 'form-control paidpayment-class',
                        'disabled' => 'disabled',

                    ],
                    'options' => [
                        // the key will be stored in the db, the value will be shown as label;
                        0 => trans('admin.No'),
                        1 => trans('admin.Yes')
                    ],
                    'default' => 0,

                ]);
            }
            CRUD::addField([ // Text
                'name' => 'link_generated',
                'label' => trans('admin.Payment Invoice Generted'),
                'type' => 'hidden',
                'value' => 0,
                'attributes' => [
                    'disabled' => 'disabled',
                ],
            ]);
            if($order)
            {
                CRUD::addField([   // CustomHTML
                    'name' => 'separator',
                    'type' => 'custom_html',
                    'tab' => 'Texts',
                    'value' => '<er style="display:none" id="texttoshare" ></er><input type="text"  style="display:none" class="myInput" id="myInput"/><button class="btn btn-success" onclick="shareOrder('.$order->id.')" type="button" value="share" class="form-control"><i class="lab la-whatsapp"></i> '.trans('admin.share').'</button><button class="btn btn-success" style="margin: 10px;" onclick="copyOrder('.$order->id.')" type="button" value="share" class="form-control"><i class="la la-copy"></i> '.trans('admin.Copy To Clipboard').'</button>
                        <p class="help-block">'.trans('admin.Please save Order Info before Copy').'</p>'
                ]);
            }
            CRUD::addField([ // Text
                'name' => 'payment_link',
                'label' => trans('admin.Payment Link'),
                'type' => 'text',
                'tab' => 'Texts',
                'attributes' => [
                    'class' => 'form-control some-class',
                    'disabled' => 'disabled',
                ],

            ]);
        }

        if($readonly)
        {

            $this->crud->removeSaveActions(['save_and_new','save_and_back','save_and_edit']);
        }
        else{

            $this->crud->removeSaveActions(['save_and_new','save_and_back']);
        }
        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }

    public function update()
    {

        $queries = array();

        $previousOrder = Orders::find($_REQUEST['id']);
        if($previousOrder->is_paid)
        {
            return Redirect::back();
        }
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->unsetValidation(); // validation has already been run
        $response = $this->traitUpdate();

        $order = Orders::find($this->crud->entry->id);
        $orderInvoices = OrderInvoicess::where('orders_id', $order->id)->first();
        if(($previousOrder->payment_type==''||!$previousOrder->payment_type)&& $previousOrder->payment_type != $order->payment_type && $order->payment_type == Orders::KNET_PAYMENT)
        {
            $url = generateRandomString(10);
            $order->url = $url;
            $order->payment_link = url('/') . '/payorder/' . $url;
            $order->save();
        }
//        if ((($previousOrder->amount != $order->amount && $order->payment_type == Orders::KNET_PAYMENT) ||
//                (!$order->is_paid && $order->payment_type == Orders::KNET_PAYMENT))&&!$orderInvoices) {
////            $paymentLink =  tappayment($order);
//            $url = generateRandomString(10);
//            $order->url = $url;
//            $order->payment_link = url('/') . '/payorder/' . $url;
//
//            $order->save();
//        }
//        else
//        {
//            $order->payment_link = '';
//            $order->save();
//        }
        if (!$order->paid_by) {
            $order->paid_by = $order->customer_id;
            $order->save();
        }
//        if (!$order->is_paid) {
//            $order->is_paid = $previousOrder->is_paid;
//            $order->save();
//        }
        $driver = User::where('id', $order->driver_id)->first();

        if ($order->comission==0||!$order->comission) {
            if ($driver) {
                if ($driver->commission_type == 1) {
                    $order->comission = $driver->commission;
                } else {
                    $order->comission = ($order->amount * $driver->commission / 100);
                }
                $order->save();
            }
        }



        if ($order->is_paid) {
            if($order->payment_type == Orders::CASH_PAYMENT)
            {
                $order->status = Orders::COMPLETED_ORDER;
                $order->save();
            }
            $account = isset($driver)? $driver->xero_account : null ;
            if(!$account)
            {
                $account  = ($order->payment_type == Orders::KNET_PAYMENT) ? config('app.XEROKNET'): config('app.XEROCASH');
            }
            try {
                (addpaymentxero($order->id, 1, $order->amount, $account));
            } catch (\Exception $ex) {
            }
        }
        else
        {

            xeroquotestoinvoice($order->id, 1);
        }
        return $response;
    }

}
