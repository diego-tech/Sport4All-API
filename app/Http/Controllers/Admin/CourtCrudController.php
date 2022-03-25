<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CourtRequest;
use App\Models\Court;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CourtCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CourtCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Court::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/court');
        CRUD::setEntityNameStrings('court', 'courts');
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

        if (backpack_user()->email == 'admin@admin.com') {
            $this->crud->addColumn([
                'name' => 'club_id',
                'label' => 'Club',
                'entity'    => 'club',
                'model'     => "App\Models\Club",
                'attribute' => 'name',
                'pivot'     => true,
            ], 'update');
        } else {
            $this->crud->addClause('where', 'club_id', '=', backpack_user()->id);
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
        CRUD::setValidation(CourtRequest::class);

        $this->addFields();

        Court::creating(function ($entry) {
            $entry->club_id = backpack_user()->id;
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
        $court = Court::where('id', $this->crud->getRequest()->id)->value('club_id');

        $this->setupCreateOperation();
        if (backpack_user()->email == 'admin@admin.com') {
        } elseif (backpack_user()->id == $court) {
        } else {
            $this->crud->denyAccess('create');
            $this->crud->denyAccess('update');
            $this->crud->removeButton('create');
            $this->crud->removeButton('delete');
        }
    }

    private function addColumns()
    {
        $this->crud->addColumns([
            [
                'name' => 'name',
                'label' => 'Nombre'
            ],
            [
                'name' => 'type',
                'label' => 'Tipo',
            ],
            [
                'name' => 'sport',
                'label' => 'Deporte'
            ],
            [
                'name' => 'surfaces',
                'label' => 'Tipo de pista',
            ],
        ]);
    }

    private function addFields()
    {
        $this->crud->addFields([
            [
                'name' => 'name',
                'label' => 'Nombre',
            ],
            [
                'name' => 'type',
                'label' => 'Tipo',
                'type' => 'enum',
            ],
            [
                'name' => 'sport',
                'label' => 'Deporte',
                'type' => 'enum',
            ],
            [
                'name' => 'surfaces',
                'label' => 'Tipo de pista',
                'type' => 'enum',
            ],
        ]);
    }
}
