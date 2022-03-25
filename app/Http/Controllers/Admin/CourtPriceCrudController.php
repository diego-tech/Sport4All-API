<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CourtPriceRequest;
use App\Models\CourtPrice;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CourtPriceCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CourtPriceCrudController extends CrudController
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
        CRUD::setModel(\App\Models\CourtPrice::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/court-price');
        CRUD::setEntityNameStrings('court price', 'court prices');
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
        CRUD::setValidation(CourtPriceRequest::class);

        $this->addFields();
        CourtPrice::creating(function($entry) {
            $entry->court_id = $this->crud->getRequest()->courts;
            $entry->price_id = $this->crud->getRequest()->prices;
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
                'label'     => "Court",
                'type'      => 'select', 
                'name'      => 'court_id', // the method that defines the relationship in your Model
                'entity'    => 'courts', // the method that defines the relationship in your Model
                'model'     => "App\Models\Court", // foreign key model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'pivot'     => true, // on create&update, do you need to add/delete pivot table entries?
            ],
            [
                'label'     => "Precio",
                'type'      => 'select',
                'name'      => 'price_id', // the method that defines the relationship in your Model
                'entity'    => 'prices', // the method that defines the relationship in your Model
                'model'     => "App\Models\Price", // foreign key model
                'attribute' => 'price', // foreign key attribute that is shown to user
                'pivot'     => true, // on create&update, do you need to add/delete pivot table entries?
            ]
        ]);
    }

    private function addFields()
    {
        $this->crud->addFields([
            [
                'label'     => "Court",
                'type'      => 'select',
                'name'      => 'courts', // the method that defines the relationship in your Model
                'entity'    => 'courts', // the method that defines the relationship in your Model
                'model'     => "App\Models\Court", // foreign key model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'pivot'     => true, // on create&update, do you need to add/delete pivot table entries?
                'options'   => (function ($query) {
                    return $query->where('club_id', backpack_user()->id)->get();
                }),
            ],
            [
                'label'     => "Precio",
                'type'      => 'select',
                'name'      => 'prices', // the method that defines the relationship in your Model
                'entity'    => 'prices', // the method that defines the relationship in your Model
                'model'     => "App\Models\Price", // foreign key model
                'attribute' => 'price', // foreign key attribute that is shown to user
                'pivot'     => true, // on create&update, do you need to add/delete pivot table entries?
                'options'   => (function ($query) {
                    return $query->where('club_id', backpack_user()->id)->get();
                }),
            ]
        ]);
    }
}
