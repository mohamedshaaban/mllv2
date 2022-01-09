<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OrdersRequest as StoreRequest;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Orders;
use App\Models\OrderInvoicess;
use App\User;
use Redirect;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;use App\Models\CarModel;
use Illuminate\Support\Facades\App;


class OrdersDriverCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\CloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkDeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkCloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;

    public function setup()
    {
        App::setLocale(session('locale'));

        CRUD::setModel(\App\Models\Orders::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/driversorders');
        CRUD::setEntityNameStrings(trans('admin.order'), trans('admin.orders'));
    }

    protected function setupListOperation()
    {
        $this->crud->addColumn([ // Text
            'name' => 'invoice_unique_id',
            'label' => trans('admin.Order Id')]);

        $this->crud->addClause('where', 'driver_id', '=', backpack_user()->id);

        $this->crud->addColumn([ // Text
            'name' => 'customers',
            'label' => trans('admin.Customer'),
            'type' => 'relationship'
        ]);

        $this->crud->addColumn([ // Text
            'name' => 'cars',
            'label' => trans('admin.car'),
            'type' => 'relationship'
        ]);
        $this->crud->addColumn([ // Text
            'name' => 'requeststatus',
            'label' => trans('admin.Status'),
            'attribute' => 'name',
            'type' => 'relationship'
        ]);

        $this->crud->addColumn([ // Text
            'name' => 'areafrom',
            'label' => trans('admin.From'),
            'attribute' => 'name',
            'type' => 'relationship'
        ]);

        $this->crud->addColumn([ // Text
            'name' => 'areato',
            'label' => trans('admin.To'),
            'attribute' => 'name',
            'type' => 'relationship'
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
        $this->crud->addButtonFromModelFunction('line', 'share ', 'openGoogle', 'beginning');
        $this->crud->disableBulkActions();
        $this->crud->removeButton('delete');


    }

    protected function setupCreateOperation()
    {

        $readonly_ispaid = '';
        $readonly = false;

        $order = null;
        if ((request()->route('id'))) {
            $order = Orders::find(request()->route('id'));
            $orderCount = OrderInvoicess::where('orders_id',request()->route('id'))->first();
            if($order->is_paid || $order->status == 6  || $order->partially_paid || $orderCount)
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
                'default' => 'WbMLL-ORD-' . ($lastOrder->id + 1),
                'attributes' => [
                    'readonly' => 'readonly',
                ],
            ]);

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
                'options' => [1 => 'Yes'],
                'attributes' => [
                    'class' => ' not_exits_make-class',
                ]
            ]);

            $this->crud->addField([ // select2_from_ajax: 1-n relationship
                'tab' => 'Texts',
                'type' => 'text',

                'name' => 'car_model_text', // the column that contains the ID of that connected entity;
                'label' => trans('admin.Car model'), // placeholder for the select
                'placeholder' => 'Enter model', // placeholder for the select
                'attributes' => [
                    'class' => 'form-control modeltext-class',
                ],
                // 'method'                    => 'GET', // optional - HTTP method to use for the AJAX call (GET, POST)
            ]);


            $this->crud->addField([ // select2_from_ajax: 1-n relationship
                'tab' => 'Texts',
                'include_all_form_fields' => true, //sends the other form fields along with the request so it can be filtered.
                'minimum_input_length' => 0, 'label' => trans('admin.Car Model'), // Table column heading
                'type' => 'select2_from_ajax',
                'name' => 'car_model', // the column that contains the ID of that connected entity;
                'entity' => 'carmodel', // the method that defines the relationship in your Model
                'attribute' => 'name_en', // foreign key attribute that is shown to user
                'data_source' => url("/admin/fetch/carmodel"), // url to controller search function (with /{id} should return model)
                'placeholder' => 'Select model', // placeholder for the select
                'dependencies' => ['car_make'], // when a dependency changes, this select2 is reset to null
                'attributes' => [
                    'class' => 'form-control modelselect-class',
                ],
                // 'method'                    => 'GET', // optional - HTTP method to use for the AJAX call (GET, POST)
            ]);


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
            CRUD::addField([  // Select2
                'label' => trans('admin.Driver'),
                'type' => 'hidden',
                'name' => 'driver_id', // the db column for the foreign key
                'entity' => 'driver', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to use
                'tab' => 'Texts',
                'value' => backpack_user()->id
            ]);

            CRUD::addField([  // Select2
                'label' => trans('admin.Status'),
                'type' => 'select2',
                'name' => 'status', // the db column for the foreign key
                'entity' => 'requeststatus', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to use
                'tab' => 'Texts',
                'default' => '2',
                'allows_null' => false,

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
                'type' => 'number',
                'tab' => 'Texts',
                'default' => 0

            ]);

            if($order)
            {
                CRUD::addField([   // CustomHTML
                    'name' => 'separator',
                    'type' => 'custom_html',
                    'tab' => 'Texts',
                    'hint'=>'Please save Order Infor before Copy',
                    'value' => '<er style="display:none" id="texttoshare" ></er><button class="btn btn-success" onclick="shareOrder('.$order->id.')" type="button" value="share" class="form-control"><i class="lab la-whatsapp"></i> '.trans('admin.share').'</button><button class="btn btn-success" style="margin: 10px;" onclick="copyOrder('.$order->id.')" type="button" value="share" class="form-control"><i class="la la-copy"></i> '.trans('admin.Copy To Clipboard').'</button>

                        <p class="help-block">'.trans('admin.Please save Order Info before Copy').'</p>'                ]);
            }

            CRUD::addField([ // Text
                'name' => 'payment_link',
                'label' => trans('admin.Payment Link'),
                'type' => 'text',
                'tab' => 'Texts',

            ]);


            CRUD::addField([ // Text
                'name' => 'payment_type',
                'label' => trans('admin.Payment Type'),
                'type' => 'select_from_array',
                'tab' => 'Texts',
                'attributes' => [
                    'class' => 'form-control paymenttype-class',
                ],
                'options' => [
                    // the key will be stored in the db, the value will be shown as label;
                    Orders::CASH_PAYMENT => trans("admin.Cash"),
                    Orders::KNET_PAYMENT => trans("admin.Knet"),
//                Orders::LATE_PAYMENT => "Late"
                ],

            ]);
            if ($readonly_ispaid) {
                CRUD::addField([ // Text
                    'name' => 'is_paid',
                    'label' => trans('admin.Is Paid'),
                    'type' => 'select_from_array',
                    'tab' => 'Texts',
                    'attributes' => [
                        'class' => 'form-control paidpayment-class',
                        'readonly' => 'readonly',

                    ],
                    'options' => [
                        // the key will be stored in the db, the value will be shown as label;
                        0 => trans('admin.No'),
                        1 => trans('admin.Yes')
                    ],

                ]);
            } else {
                CRUD::addField([ // Text
                    'name' => 'is_paid',
                    'label' => trans('admin.Is Paid'),
                    'type' => 'select_from_array',
                    'tab' => 'Texts',
                    'attributes' => [
                        'class' => 'form-control paidpayment-class',
                    ],
                    'options' => [
                        // the key will be stored in the db, the value will be shown as label;
                        0 => trans('admin.No'),
                        1 => trans('admin.Yes')
                    ],

                ]);
            }

            CRUD::addField([ // Text
                'name' => 'link_generated',
                'label' => trans('admin.Payment Invoice Generted'),
                'type' => 'hidden',
                'value' => 1,

            ]);
            if($order)
            {
                CRUD::addField([   // CustomHTML
                    'name' => 'separator',
                    'type' => 'custom_html',
                    'tab' => 'Texts',
                    'hint'=>'Please save Order Infor before Copy',
                    'value' => '<er style="display:none" id="texttoshare" ></er><button class="btn btn-success" onclick="shareOrder('.$order->id.')" type="button" value="share" class="form-control"><i class="lab la-whatsapp"></i> '.trans('admin.share').'</button><button class="btn btn-success" style="margin: 10px;" onclick="copyOrder('.$order->id.')" type="button" value="share" class="form-control"><i class="la la-copy"></i> '.trans('admin.Copy To Clipboard').'</button>
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
            CRUD::setValidation(StoreRequest::class);

            CRUD::addField([ // Text
                'name' => 'invoice_unique_id',
                'label' => trans('admin.Order Id'),
                'type' => 'text',
                'tab' => 'Texts',
                'default' => 'WbMLL-ORD-' . ($lastOrder->id + 1),
                'attributes' => [
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ],
            ]);


            CRUD::addField([  // Select2
                'label' => trans('admin.Customer'),
                'type' => 'relationship',
                'name' => 'customer_id', // the db column for the foreign key
                'entity' => 'customers', // the method that defines the relationship in your Model
                'attribute' => 'mobile', // foreign key attribute that is shown to use
                'tab' => 'Texts',
                'data_source' => url("/admin/fetch/customer"), // url to controller search function (with /{id} should return model)
                'attributes' => [
                    'readonly' => 'readonly',
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
                    'readonly' => 'readonly',
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
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ],

            ]);

            CRUD::addField([
                'tab' => 'Texts',

                'name' => 'crmnotexits',
                'label' => trans('admin.Car Make not exists'),
                'type' => 'boolean',
                // optionally override the Yes/No texts
                'options' => [1 => 'Yes'],
                'attributes' => [
                    'class' => ' not_exits_make-class',
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',

                ]
            ]);

            $this->crud->addField([ // select2_from_ajax: 1-n relationship
                'tab' => 'Texts',
                'type' => 'text',

                'name' => 'car_model_text', // the column that contains the ID of that connected entity;
                'label' => trans('admin.Car model'), // placeholder for the select
                'placeholder' => 'Enter model', // placeholder for the select
                'attributes' => [
                    'class' => 'form-control modeltext-class',
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',

                ],
                // 'method'                    => 'GET', // optional - HTTP method to use for the AJAX call (GET, POST)
            ]);


            $this->crud->addField([ // select2_from_ajax: 1-n relationship
                'tab' => 'Texts',
                'include_all_form_fields' => true, //sends the other form fields along with the request so it can be filtered.
                'minimum_input_length' => 0, 'label' => trans('admin.Car Model'), // Table column heading
                'type' => 'select2_from_ajax',
                'name' => 'car_model', // the column that contains the ID of that connected entity;
                'entity' => 'carmodel', // the method that defines the relationship in your Model
                'attribute' => 'name_en', // foreign key attribute that is shown to user
                'data_source' => url("/admin/fetch/carmodel"), // url to controller search function (with /{id} should return model)
                'placeholder' => 'Select model', // placeholder for the select
                'dependencies' => ['car_make'], // when a dependency changes, this select2 is reset to null
                'attributes' => [
                    'class' => 'form-control modelselect-class',
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',

                ],
                // 'method'                    => 'GET', // optional - HTTP method to use for the AJAX call (GET, POST)
            ]);


            CRUD::addField([  // Select2
                'label' => trans('admin.Car Plate ID'),
                'type' => 'relationship',
                'name' => 'car_id', // the db column for the foreign key
                'entity' => 'cars', // the method that defines the relationship in your Model
                'attribute' => 'car_plate_id', // foreign key attribute that is shown to use
                'tab' => 'Texts',
                'data_source' => url("/admin/fetch/car"), // url to controller search function (with /{id} should return model)
                'attributes' => [
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ],
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
                'attributes' => [
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ],
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
                'attributes' => [
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ],
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
                'label' => trans('admin.Driver'),
                'type' => 'hidden',
                'name' => 'driver_id', // the db column for the foreign key
                'entity' => 'driver', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to use
                'tab' => 'Texts',
                'attributes' => [
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ],
                'value' => backpack_user()->id
            ]);

            CRUD::addField([  // Select2
                'label' => trans('admin.Status'),
                'type' => 'hidden',
                'name' => 'status', // the db column for the foreign key
                'entity' => 'requeststatus', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to use
                'tab' => 'Texts',
                'value' => 8,
                'attributes' => [
                    'readonly' => 'readonly',
                    'disabled' => 'disabled',
                ],

            ]);

            CRUD::addField([ // Text
                'name' => 'address',
                'label' => trans('admin.Address'),
                'type' => 'text',
                'tab' => 'Texts',
                'attributes' => [
                    'readonly' => 'readonly',
                ],

            ]);
            CRUD::addField([ // Text
                'name' => 'remarks',
                'label' => trans('admin.remarks'),
                'type' => 'textarea',
                'tab' => 'Texts',
                'attributes' => [
                    'readonly' => 'readonly',
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
                'attributes' => [
                    'readonly' => 'readonly',
                ],
                'datetime_picker_options' => [
                    'format' => 'DD/MM/YYYY HH:mm',
                    'language' => 'en',
                ],
            ]);


            CRUD::addField([ // Text
                'name' => 'amount',
                'label' => trans('admin.Amount'),
                'type' => 'number',
                'tab' => 'Texts',
                'attributes' => [
                    'readonly' => 'readonly',
                ],
                'default' => 0

            ]);


            CRUD::addField([ // Text
                'name' => 'payment_link',
                'label' => trans('admin.Payment Link'),
                'type' => 'text',
                'attributes' => [
                    'readonly' => 'readonly',
                ],
                'tab' => 'Texts',

            ]);


            CRUD::addField([ // Text
                'name' => 'payment_type',
                'label' => trans('admin.Payment Type'),
                'type' => 'select_from_array',
                'tab' => 'Texts',
                'attributes' => [
                    'class' => 'form-control paymenttype-class',
                    'readonly' => 'readonly',

                ],
                'options' => [
                    // the key will be stored in the db, the value will be shown as label;
                    Orders::CASH_PAYMENT => trans("admin.Cash"),
                    Orders::KNET_PAYMENT => trans("admin.Knet"),
//                Orders::LATE_PAYMENT => "Late"
                ],

            ]);
            if ($readonly_ispaid) {
                CRUD::addField([ // Text
                    'name' => 'is_paid',
                    'label' => trans('admin.Is Paid'),
                    'type' => 'select_from_array',
                    'tab' => 'Texts',
                    'attributes' => [
                        'class' => 'form-control paidpayment-class',
                        'readonly' => 'readonly',

                    ],
                    'options' => [
                        // the key will be stored in the db, the value will be shown as label;
                        0 => trans('admin.No'),
                        1 => trans('admin.Yes')
                    ],

                ]);
            } else {
                CRUD::addField([ // Text
                    'name' => 'is_paid',
                    'label' => trans('admin.Is Paid'),
                    'type' => 'select_from_array',
                    'tab' => 'Texts',
                    'attributes' => [
                        'class' => 'form-control paidpayment-class',
                        'readonly' => 'readonly',

                    ],
                    'options' => [
                        // the key will be stored in the db, the value will be shown as label;
                        0 => trans('admin.No'),
                        1 => trans('admin.Yes')
                    ],

                ]);
            }

            CRUD::addField([ // Text
                'name' => 'link_generated',
                'label' => trans('admin.Payment Invoice Generted'),
                'type' => 'hidden',
                'value' => 1,
                'attributes' => [
                    'readonly' => 'readonly',
                ],

            ]);
            if($order)
            {
                CRUD::addField([   // CustomHTML
                    'name' => 'separator',
                    'type' => 'custom_html',
                    'tab' => 'Texts',
                    'hint'=>'Please save Order Infor before Copy',
                    'value' => '<er style="display:none" id="texttoshare" ></er><button class="btn btn-success" onclick="shareOrder('.$order->id.')" type="button" value="share" class="form-control"><i class="lab la-whatsapp"></i> '.trans('admin.share').'</button><button class="btn btn-success" style="margin: 10px;" onclick="copyOrder('.$order->id.')" type="button" value="share" class="form-control"><i class="la la-copy"></i> '.trans('admin.Copy To Clipboard').'</button>
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
        $this->crud->setOperationSetting('contentClass', 'col-md-12');
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

    public function store()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->unsetValidation(); // validation has already been run
        $response = $this->traitStore();

        $order = Orders::find($this->crud->entry->id);
        $driver = User::where('id',$order->driver_id)->first();

//            $order->invoice_unique_id = 'WbMll-'.generateRandomString(5);
        if($order->payment_type == Orders::KNET_PAYMENT)
        {
            $url = generateRandomString(10);
            $order->url = $url;
            $order->payment_link = url('/').'/payorder/'.$url;

        }
        if(!$order->comission)
        {
            if($driver) {
                if ($driver->commission_type == 1) {
                    $order->comission = $driver->commission;
                } else {
                    $order->comission = ($order->amount * $driver->commission / 100);
                }
            }
        }
        if($order->car_model_text && $order->car_model_text!='') {
            $carmodel = CarModel::create(['name_en' => $order->car_model_text, 'name_ar' => $order->car_model_text,
                'car_make' => 4]);
            $order->car_model = $carmodel->id;
            $order->car_make = 4;
        }
        $order->save();
        if($order->amount == 0 || $order->amount == NULL)
        {
            try {
                xeroquotes($order->id,1);
            }
            catch (\Exception $exception){}
        }
        else
        {
            try {
                xeroinvoice($order->id,1);
            }
            catch (\Exception $exception){}
        }
        if($order->is_paid)
        {
            $account = isset($driver)? $driver->xero_account : null ;
            if(!$account)
            {
                $account  = ($order->payment_type == Orders::KNET_PAYMENT) ? config('app.XEROKNET'): config('app.XEROCASH');
            }
            try
            {
                (addpaymentxero( $order->id ,1 , $order->amount , $account));
            }
            catch ( \Exception $exception){}
        }


        return $response;
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
        $driver = User::where('id', $order->driver_id)->first();
        if(($previousOrder->payment_type==''||!$previousOrder->payment_type)&& $previousOrder->payment_type != $order->payment_type && $order->payment_type == Orders::KNET_PAYMENT)
        {
            $url = generateRandomString(10);
            $order->url = $url;
            $order->payment_link = url('/') . '/payorder/' . $url;
            $order->save();
        }
        if($previousOrder->amount != $order->amount && $order->payment_type == Orders::KNET_PAYMENT)
        {
//            $url = generateRandomString(10);
//            $order->url = $url;
//            $order->payment_link = url('/').'/payorder/'.$url;
        }
        if(!$previousOrder->comission) {
            if ($driver) {
                if ($driver->commission_type == 1) {
                    $order->comission = $driver->commission;
                } else {
                    $order->comission = ($order->amount * $driver->commission / 100);
                }
            }
        }
        $order->save();


        try
        {
            (xeroquotestoinvoice($order->id,1));

        }
        catch (\Exception $exception){}
        if($order->is_paid)
        {
            $account = isset($driver)? $driver->xero_account : null ;
            if(!$account)
            {
                $account  = ($order->payment_type == Orders::KNET_PAYMENT) ? config('app.XEROKNET'): config('app.XEROCASH');
            }
            try
            {
                (addpaymentxero( $order->id ,1 , $order->amount , $account));
            }
            catch (\Exception $exception){}
        }
        return $response;
    }

}
