<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ClubsServicesRequest;
use App\Models\ClubsServices;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ClubsServicesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ClubsServicesCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\ClubsServices::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/clubs-services');
        CRUD::setEntityNameStrings('clubs services', 'clubs services');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
       $this->addColumns();

       if(backpack_user()->email == 'admin@admin.com'){
       }else{
           $this->crud->addClause('where','club_id','=', backpack_user()->id);
        }

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ClubsServicesRequest::class);

        $this->addFields();

        if(backpack_user()->email == 'admin@admin.com'){
        }else{
            $this->crud->addClause('where','club_id','=', backpack_user()->id);            
        }
        ClubsServices::creating(function($entry) {
            $service = ClubsServices::where('service_id' ,$this->crud->getRequest()->services)->first();
            if(!$service){
                $entry->service_id = $this->crud->getRequest()->services;
                $entry->club_id = backpack_user()->id;
            }else{
                return redirect('clubs-services');
            }
        });

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    private function addColumns()
    {
        $this->crud->addColumns([
            [
                'label'     => "Servicios",
                'type'      => 'select',
                'name'      => 'services', // the method that defines the relationship in your Model
                'entity'    => 'services', // the method that defines the relationship in your Model
                'model'     => "App\Models\Service", // foreign key model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'pivot'     => true, // on create&update, do you need to add/delete pivot table entries?
            ],
 
        ]);
    }

    private function addFields()
    {
        $this->crud->addFields([
            [
                'label'     => "Servicios",
                'type'      => 'select',
                'name'      => 'services', // the method that defines the relationship in your Model
                'entity'    => 'services', // the method that defines the relationship in your Model
                'model'     => "App\Models\Service", // foreign key model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'pivot'     => true, // on create&update, do you need to add/delete pivot table entries?
            ],
        ]);
    }
}
