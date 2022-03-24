<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MatchsRequest;
use App\Models\Court;
use App\Models\Matchs;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MatchsCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MatchsCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Matchs::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/matchs');
        CRUD::setEntityNameStrings('matchs', 'matchs');
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
            $this->crud->addColumn([
                [
                    'name' => 'club_id',
                    'label' => 'Club',
                    'entity' => 'club',
                    'model' => "App\Models\Club",
                    'attribute' => 'name',
                    'pivot' => true,
                ],
                [
                    'name' => 'QR',
                    'label' => 'Codigo QR',
                ],
                [
                    'name' => 'final_time',
                    'label' => 'Datetima Finala',
                ],
                [
                    'name' => 'start_Datetime',
                    'label' => 'Datetime Inicio',
                ],
            ], 'update');
        }else{
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
                    'label' => 'Club',
                    'entity' => 'club',
                    'model' => "App\Models\Club",
                    'attribute' => 'name',
                    'pivot' => true,
                ],
                [
                    'name' => 'QR',
                    'label' => 'Codigo QR',
                ],
                [
                    'name' => 'final_time',
                    'label' => 'Datetima Finala',
                ],
                [
                    'name' => 'start_Datetime',
                    'label' => 'Datetime Inicio',
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
        CRUD::setValidation(MatchsRequest::class);
        dd($this->crud->getRequest());
        $this->addFields();
        Matchs::creating(function($entry) {
            $entry->club_id = backpack_user()->id;
            $entry->QR = mt_rand(1000, 9999);
            $entry->start_Datetime = $entry->day . " ". $entry->start_time;
            $entry->final_time = $entry->day . " ". $entry->end_time;
            $entry->court_id = $this->crud->getRequest()->court_id;
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
    }

    private function addColumns(){
        $this->crud->addColumns([
            [
                'label'     => "Pistas",
                'name'      => 'court_id',
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

    private function addFields(){
        $this->crud->addFields([
            [
                'label'     => "Pistas",
                'name'      => 'court_id',
                'type'      => 'select',
                'entity'    => 'courts',
                'model'     => "App\Models\Court",
                'attribute' => 'name',
                'pivot'     => true,
                'options'   => (function ($query) {
                    return $query->where('club_id', backpack_user()->id)->get();
                }), 
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
            ], 'update');
    }
}
