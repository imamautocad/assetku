<?php

namespace App\Presenters;

class WebsitePresenter
{
    protected $entity;

        public function __construct($entity)
        {
            $this->entity = $entity;
        }

        public function displayName()
        {
            return $this->entity->decs ?? 'Website #' . $this->entity->id;
        }

    public static function dataTableLayout() 
    { 
        return json_encode([
            // [
            //     'field' => 'id',
            //     'title' => 'ID',
            //     'sortable' => true
            // ],
            [
                'field' => 'manufacturers',
                'title' => 'Manufacturer',
                'sortable' => true
            ],
            [
                'field' => 'category',
                'title' => 'Category',
                'sortable' => true
            ],
            [
                'field' => 'company',
                'title' => 'Company',
                'sortable' => true
            ],         
            [
                'field' => 'name',
                'title' => 'Domain/Hosting',
                'sortable' => true
            ],  
            [
                'field' => 'id_subscribe',
                'title' => 'Id_Subscribe',
                'sortable' => true
            ],
            [
                'field' => 'decs',
                'title' => 'Description',
                'sortable' => true
            ],
            [
                'field' => 'pay_date',
                'title' => 'Pay Date',
                'sortable' => true
            ],
            [
                'field' => 'expired_date',
                'title' => 'Expired Date',
                'sortable' => true
            ],
            [
                'field' => 'status',
                'title' => 'Status',
                'sortable' => true
            ],
            [
                'field' => 'price',
                'title' => 'Price',
                'sortable' => true
            ],
            [
                'field' => 'actions',
                'title' => 'Actions',
                'switchable' => false,
                'sortable' => false,
                'escape' => false
            ],
        ]);
    }
}
