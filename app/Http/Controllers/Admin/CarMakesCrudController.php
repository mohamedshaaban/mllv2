<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateCarMakesRequest as StoreRequest;
use App\Http\Requests\UpdateCarMakesRequest as UpdateRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Models\CarMakes;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\App;

class CarMakesCrudController extends CrudController
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

        CRUD::setModel(\App\Models\CarMakes::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/carmakes');
        CRUD::setEntityNameStrings(trans('admin.Car Make'), trans('admin.Car Make'));
    }

    protected function setupListOperation()
    {
        $this->crud->addColumn(
            [
                'name'=>'name_en',
                'label' => trans('admin.Name en')
            ]
        );
        $this->crud->addColumn(
            [
                'name'=>'name_ar',
                'label' => trans('admin.Name ar')
            ]
        );

    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addField([ // Text
            'name'  => 'name_en',
            'label' => trans('admin.Name en'),
            'type'  => 'text',
            'tab'   => 'Texts',

        ]);
        CRUD::addField([ // Text
            'name'  => 'name_ar',
            'label' => trans('admin.Name ar'),
            'type'  => 'text',
            'tab'   => 'Texts',

        ]);



        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }

    protected function setupUpdateOperation()
    {
        CRUD::setValidation(UpdateRequest::class);

        CRUD::addField([ // Text
            'name'  => 'name_en',
            'label' => trans('admin.Name en'),
            'type'  => 'text',
            'tab'   => 'Texts',

        ]);
        CRUD::addField([ // Text
            'name'  => 'name_ar',
            'label' => trans('admin.Name ar'),
            'type'  => 'text',
            'tab'   => 'Texts',

        ]);



        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }
    public static function fetch(\Illuminate\Http\Request  $request)
    {
        $areas = CarMakes::where('name_en','like','%'.$request->q.'%')->get(['id','name_en']);
        $data = [] ;
        foreach ($areas as $area)
        {
            $data[] = ['id'=>$area->id , 'name_en'=>$area->name_en];
        }

        return $data;

    }

}
