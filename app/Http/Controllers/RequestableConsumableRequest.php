<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestableConsumableRequest extends FormRequest
{
    public function authorize()
    {
        // Kita kontrol permission via Policy (nanti). Untuk sementara true.
        return true;
    }

    public function rules()
    {
        return [
            'department_id' => 'required|exists:departments,id',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,submitted',
            'items' => 'required|array|min:1',
            'items.*.consumable_id' => 'required|exists:consumables,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string'
        ];
    }

    public function messages()
    {
        return [
            'items.required' => 'Please add at least one item.',
            'items.*.consumable_id.required' => 'Consumable is required.',
            'items.*.quantity.required' => 'Quantity is required and must be numeric.'
        ];
    }
}
