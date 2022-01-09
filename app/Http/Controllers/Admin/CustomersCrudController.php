<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CustomersRequest as StoreRequest;
use App\Http\Requests\UpdateCustomersRequest as UpdateRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\Customers;
use App\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use http\Env\Request;
use Illuminate\Support\Facades\App;

class CustomersCrudController extends CrudController
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

        CRUD::setModel(\App\Models\Customers::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/customers');
        CRUD::setEntityNameStrings(trans('admin.Customer'), trans('admin.Customer'));
    }

    protected function setupListOperation()
    {
        $this->crud->addColumn([ // Text
            'name'  => 'name',
            'label' => trans('admin.Name'),
            'type'      => 'text'
        ]);

        $this->crud->addColumn([ // Text
            'name'  => 'type',
            'label' =>trans('admin.type'),
            'entity'    => 'customertypes', // the method that defines the relationship in your Model
            'attribute' => 'name_en',
        ]);
        $this->crud->addColumn([ // Text
            'name'  => 'mobile',
            'label' => trans('admin.Mobile'),
            'type'      => 'text'
        ]);
        $this->crud->addColumn([ // Text
            'name'  => 'num_of_cars',
            'label' => trans('admin.# Cars'),
            'type'      => 'text'
        ]);

        $this->crud->addColumn([ // Text
            'name'  => 'num_of_orders',
            'label' => trans('admin.# Orders'),
            'type'      => 'text'
        ]);
        $this->crud->removeButton( 'delete' );

    }
    protected function setupUpdateOperation()
    {
        $this->addUserFields();
        $this->crud->setValidation(UpdateRequest::class);
    }
    protected function setupCreateOperation()
    {
        $this->addUserFields();

        CRUD::setValidation(StoreRequest::class);


    }

    protected function addUserFields()
    {
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


        CRUD::addField([  // Select2
            'label'     => trans('admin.type'),
            'type'      => 'relationship',
            'name'      => 'type', // the db column for the foreign key
            'entity'    => 'customertypes', // the method that defines the relationship in your Model
            'attribute' => 'name_en', // foreign key attribute that is shown to user
            'tab' => 'Texts',
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
    public static function fetch(\Illuminate\Http\Request  $request)
    {
        $customers = Customers::where('name','like','%'.$request->q.'%')->orWhere('mobile','like','%'.$request->q.'%')->get(['id','name','mobile']);
        $data = [] ;
        foreach ($customers as $customer)
        {
            if($customer->name)
            {
                $data[] = ['id'=>$customer->id , 'mobile'=>$customer->name.' - '.$customer->mobile];
            }
            else
            {
                $data[] = ['id'=>$customer->id , 'mobile'=>$customer->mobile];

            }
        }

        return $data;

    }
    public static function filter(\Illuminate\Http\Request  $request)
    {
        $customers = Customers::where('name','like','%'.$request->q.'%')->orWhere('mobile','like','%'.$request->q.'%')->get(['id','name','mobile']);
        $data = [] ;
        foreach ($customers as $customer)
        {
            if($customer->name)
            {
                $data[$customer->id] = $customer->name.' - '.$customer->mobile;
            }
            else
            {
                $data[$customer->id] =$customer->mobile;

            }
        }

        return $data;

    }
    public static function driver(\Illuminate\Http\Request  $request)
    {
         $customers = User::where('name','like','%'.$request->q.'%')->where('is_driver',1)->get(['id','name']);
        $data = [] ;
        foreach ($customers as $customer)
        { 
                $data[] = ['id'=>$customer->id , 'name'=>$customer->name];

        }
        return $data;
    }


}
