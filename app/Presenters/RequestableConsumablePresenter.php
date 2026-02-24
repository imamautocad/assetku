<?php
namespace App\Presenters;

class RequestableConsumablePresenter
{
    public static function dataTableLayout()
    {
        return json_encode([
            ['field' => 'id', 'title' => 'ID', 'sortable' => true],
            ['field' => 'department', 'title' => 'Department', 'sortable' => true],
            ['field' => 'status', 'title' => 'Status', 'sortable' => true],
            ['field' => 'notes', 'title' => 'Notes'],
            ['field' => 'created_at', 'title' => 'Created At', 'sortable' => true],
            ['field' => 'actions', 'title' => 'Actions']
        ]);
    }
}
