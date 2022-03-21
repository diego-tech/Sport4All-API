<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EventRequest;
use App\Models\Event;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class EventCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class EventCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Event::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/event');
        CRUD::setEntityNameStrings('event', 'events');
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
            $this->crud->addColumn(
            [
                'name' => 'club_id',
                'label' => 'Club'
            ],
            [
                'name' => 'final_time',
                'label' => 'Datetime final',
            ],
            );
        }else{
            $this->addColumns();
            $this->crud->addClause('where','club_id','=', backpack_user()->id);
        }

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    protected function setupShowOperation()
    {
        $this->setupListOperation();
        if(backpack_user()->email == 'admin@admin.com'){
            $this->crud->addColumn(
            [
                'name' => 'club_id',
                'label' => 'Club'
            ],
            [
                'name' => 'final_time',
                'label' => 'Datetime final',
            ],
            );
        }else{
            $this->crud->addClause('where','club_id','=', backpack_user()->id);
        }
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(EventRequest::class);

        $this->addFields();

        Event::creating(function($entry) {
            $entry->club_id = backpack_user()->id;
            $entry->final_time = $entry->day . " ". $entry->end_time;
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
        if(backpack_user()->email == 'admin@admin.com'){
        }elseif(backpack_user()->id == $this->crud->getRequest()->id){
            $this->crud->addClause('where','id','=', backpack_user()->id);
        }else{
            $this->crud->denyAccess('create');
            $this->crud->denyAccess('update');
            $this->crud->removeButton('create');
            $this->crud->removeButton('delete');
        }
        Event::updating(function($entry) {
            $entry->final_time = $entry->day . " ". $entry->end_time;
        });

    }

    private function addColumns(){
        $this->crud->addColumns([
            [
                'name' => 'name',
                'label' => 'Nombre'
            ],
            [
                'name' => 'visibility',
                'label' => 'Visibilidad',
            ],
            [
                'name' => 'people_left',
                'label' => 'Aforo'
            ],
            [
                'name' => 'type',
                'label' => 'Tipo',
            ],
            [
                'name' => 'description',
                'label' => 'Descripción',
            ],
            [
                'name' => 'price',
                'label' => 'Precio',
            ],
            [
                'name' => 'img',
                'label' => 'Imagen',
            ],
            [
                'name' => 'day',
                'label' => 'Día',
            ],
            [
                'name' => 'start_time',
                'label' => 'Hora de inicio evento',
            ],
            [
                'name' => 'end_time',
                'label' => 'Hora de cierre evento',
            ],
        ]);
    }

    private function addFields(){
        $this->crud->addFields([
            [
                'name' => 'name',
                'label' => 'Nombre'
            ],
            [
                'name' => 'visibility',
                'label' => 'Visibilidad',
                'type' => 'enum',
            ],
            [
                'name' => 'people_left',
                'label' => 'Aforo'
            ],
            [
                'name' => 'type',
                'label' => 'Tipo',
            ],
            [
                'name' => 'description',
                'label' => 'Descripción',
            ],
            [
                'name' => 'price',
                'label' => 'Precio',
            ],
            [
                'name' => 'img',
                'label' => 'Imagen',
                'type'      => 'upload',
                'upload'    => true,
            ],
            [
                'name' => 'day',
                'label' => 'Día',
                'type' => 'date',
            ],
            [
                'name' => 'start_time',
                'label' => 'Hora de inicio evento',
                'type' => 'time',
            ],
            [
                'name' => 'end_time',
                'label' => 'Hora de cierre evento',
                'type' => 'time',
            ],
        ]);
    }

}
