<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ClubRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ClubCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ClubCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Club::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/club');
        CRUD::setEntityNameStrings('club', 'clubs');
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
            $this->crud->addClause('where','id','=', backpack_user()->id);
            $this->crud->removeButton('create');
            $this->crud->removeButton('delete');
            $this->crud->removeButton('search');
            
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
        CRUD::setValidation(ClubRequest::class);

        $this->addFields();
        
        if(backpack_user()->email == 'admin@admin.com'){
        }else{
            $this->crud->addClause('where','id','=', backpack_user()->id);
            $this->crud->denyAccess('create');
            $this->crud->removeButton('create');
            $this->crud->removeButton('delete');
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
            $this->crud->addClause('where','id','=', backpack_user()->id);
            $this->crud->denyAccess('create');
            $this->crud->removeButton('create');
            $this->crud->removeButton('delete');
        }
    }

    private function addColumns(){
        $this->crud->addColumns([
            [
                'name' => 'name',
                'label' => 'Nombre'
            ],
            [
                'name' => 'email',
                'label' => 'Email',
            ],
            [
                'name' => 'description',
                'label' => 'Descripción'
            ],
            [
                'name' => 'web',
                'label' => 'Página web',
            ],
            [
                'name' => 'tlf',
                'label' => 'Teléfono'
            ],
            [
                'name' => 'club_img',
                'label' => 'Logo'
            ],
            [
                'name' => 'club_banner',
                'label' => 'Banner'
            ],
            [
                'name' => 'first_hour',
                'label' => 'Horario apertura',
            ],
            [
                'name' => 'last_hour',
                'label' => 'Horario cierre',
            ]
        ]);
    }

    private function addFields(){
        $this->crud->addFields([
            [
                'name' => 'name',
                'label' => 'Nombre',
            ],
            [
                'name' => 'email',
                'label' => 'Email',
            ],
            [
                'name' => 'password',
                'label' => 'Confirmar contraseña o cambiar a una nueva',
            ],
            [
                'name' => 'description',
                'label' => 'Descripción'
            ],
            [
                'name' => 'web',
                'label' => 'Página web',
            ],
            [
                'name' => 'tlf',
                'label' => 'Teléfono'
            ],
            [
                'name'      => 'club_img',
                'label'     => 'Logo',
                'type'      => 'upload',
                'upload'    => true,
            ],
            [
                'name' => 'club_banner',
                'label' => 'Banner',
                'type'      => 'upload',
                'upload'    => true,
            ],
            [
                'name' => 'first_hour',
                'label' => 'Horario apertura',
                'type' => 'time',
            ],
            [
                'name' => 'last_hour',
                'label' => 'Horario cierre',
                'type' => 'time',
            ]
            ]);
    }
}
