<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateCarModelRequest as StoreRequest;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\UpdateCarModelRequest;
use App\Models\CarModel;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\App;

class CarModelCrudController extends CrudController
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

        CRUD::setModel(\App\Models\CarModel::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/carmodel');
        CRUD::setEntityNameStrings(trans('admin.Car Model'), trans('admin.Car Model'));
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
        CRUD::addField([  // Select2
            'label'     => trans('admin.Car Make'),
            'type'      => 'relationship',
            'name'      => 'car_make', // the db column for the foreign key
            'entity'    => 'carmakes', // the method that defines the relationship in your Model
            'attribute' => 'name_en', // foreign key attribute that is shown to use
            'tab' => 'Texts',
            'delay' => 500, // the minimum amount of time between ajax requests when searching in the field
            'data_source' => url("/admin/fetch/carmakes"), // url to controller search function (with /{id} should return model)

            'inline_create' => [ // specify the entity in singular
                'entity' => 'carmakes', // the entity in singular
                'force_select' => true, // should the inline-created entry be immediately selected?
                'modal_class' => 'modal-dialog modal-xl', // use modal-sm, modal-lg to change width
                'modal_route' => route('carmakes-inline-create'), // InlineCreate::getInlineCreateModal()
                'create_route' =>  route('carmakes-inline-create-save'), // InlineCreate::storeInlineCreate()
                'include_main_form_fields' => ['name_en', 'name_ar'], // pass certain fields from the main form to the modal
            ]
        ]);


        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }

    protected function setupUpdateOperation()

    {
        CRUD::setValidation(UpdateCarModelRequest::class);

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
        CRUD::addField([  // Select2
            'label'     => trans('admin.Car Make'),
            'type'      => 'relationship',
            'name'      => 'car_make', // the db column for the foreign key
            'entity'    => 'carmakes', // the method that defines the relationship in your Model
            'attribute' => 'name_en', // foreign key attribute that is shown to use
            'tab' => 'Texts',
            'delay' => 500, // the minimum amount of time between ajax requests when searching in the field
            'data_source' => url("/admin/fetch/carmakes"), // url to controller search function (with /{id} should return model)

            'inline_create' => [ // specify the entity in singular
                'entity' => 'carmakes', // the entity in singular
                'force_select' => true, // should the inline-created entry be immediately selected?
                'modal_class' => 'modal-dialog modal-xl', // use modal-sm, modal-lg to change width
                'modal_route' => route('carmakes-inline-create'), // InlineCreate::getInlineCreateModal()
                'create_route' =>  route('carmakes-inline-create-save'), // InlineCreate::storeInlineCreate()
                'include_main_form_fields' => ['name_en', 'name_ar'], // pass certain fields from the main form to the modal
            ]
        ]);


        $this->crud->setOperationSetting('contentClass', 'col-md-12');
    }
    public static function fetch(\Illuminate\Http\Request  $request)
    {
        $search_term = $request->input('q');
        $form = backpack_form_input();

        $options = CarModel::query();

        // if no category has been selected, show no options
        if (! $form['car_make']) {
            return [];
        }

        // if a category has been selected, only show articles in that category
        if ($form['car_make']) {
            $options = $options->where('car_make', $form['car_make']);
        }

        if ($search_term) {
            $results = $options->where('name_en', 'LIKE', '%'.$search_term.'%')->paginate(10);
        } else {
            $results = $options->paginate(10);
        }
        return $results;

    }

}
