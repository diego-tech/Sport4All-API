<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReserveRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ReserveCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ReserveCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Reserve::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/reserve');
        CRUD::setEntityNameStrings('reserve', 'reserves');
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
            $this->crud->removeButton('create');
            $this->crud->removeButton('update');
            $this->crud->removeButton('delete');
            $this->crud->removeButton('search');
            
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
                'name' => 'QR',
                'label' => 'QR'
            ],
            [
                'name' => 'user_id',
                'label' => 'Usuario',
            ],
            [
                'name' => 'final_time',
                'label' => 'Datetime final',
            ],
            [
                'name' => 'start_time',
                'label' => 'Datetime inicio',
            ],
            );
        }else{
            $this->crud->addClause('where','club_id','=', backpack_user()->id);
            $this->crud->removeButton('update');
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
        CRUD::setValidation(ReserveRequest::class);

        if(backpack_user()->email == 'admin@admin.com'){
        }else{
            $this->crud->addClause('where','club_id','=', backpack_user()->id);
            $this->crud->denyAccess('create');
            $this->crud->removeButton('create');
            $this->crud->removeButton('delete');
            $this->crud->removeButton('edit');
        }

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
        }else{
            $this->crud->addClause('where','club_id','=', backpack_user()->id);
            $this->crud->denyAccess('create');
            $this->crud->denyAccess('update');
            $this->crud->removeButton('create');
            $this->crud->removeButton('delete');
        }
    }

    private function addColumns(){
        $this->crud->addColumns([
            [
                'name' => 'courts',
                'label' => 'Pista',
                'type'      => 'select',
                'entity'    => 'courts',
                'model'     => "App\Models\Court",
                'attribute' => 'name',
                'pivot'     => true,
            ],
            [
                'name' => 'lights',
                'label' => 'Luces',
            ],
            [
                'name' => 'day',
                'label' => 'Día'
            ],
            [
                'name' => 'price_people',
                'label' => 'Precio por persona',
            ],
            [
                'name' => 'start_time',
                'label' => 'Hora inicio',
            ],
            [
                'name' => 'end_time',
                'label' => 'Hora final',
            ],
        ]);
    }
}
