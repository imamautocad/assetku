@extends('layouts.default')
@section('content')
<h2>{{ isset($requestable) ? 'Edit Requestable Consumable' : 'Create Requestable Consumable' }}</h2>

<form action="{{ isset($requestable) ? route('requestable.update', $requestable->id) : route('requestable.store') }}" method="POST">
    @csrf
    @if(isset($requestable))
        @method('PUT')
    @endif

    <div>
        <label>Department:</label>
        <select name="department_id" required>
            @foreach(\App\Models\Department::all() as $dept)
                <option value="{{ $dept->id }}" {{ isset($requestable) && $requestable->department_id == $dept->id ? 'selected' : '' }}>
                    {{ $dept->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Notes:</label>
        <textarea name="notes">{{ $requestable->notes ?? '' }}</textarea>
    </div>

    <div>
        <label>Status:</label>
        <select name="status">
            <option value="draft" {{ isset($requestable) && $requestable->status=='draft' ? 'selected' : '' }}>Draft</option>
            <option value="submitted" {{ isset($requestable) && $requestable->status=='submitted' ? 'selected' : '' }}>Submit</option>
        </select>
    </div>

    <h3>Items</h3>
    <table border="1" cellpadding="5" cellspacing="0" id="items-table">
        <thead>
            <tr>
                <th>Consumable</th>
                <th>Quantity</th>
                <th>Notes</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($requestable))
                @foreach($requestable->items as $item)
                <tr>
                    <td>
                        <select name="items[{{ $loop->index }}][consumable_id]">
                            @foreach($consumables as $consumable)
                                <option value="{{ $consumable->id }}" {{ $consumable->id == $item->consumable_id ? 'selected' : '' }}>
                                    {{ $consumable->name }} (Stock: {{ $consumable->qty }})
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="items[{{ $loop->index }}][quantity]" value="{{ $item->quantity }}" min="1"></td>
                    <td><input type="text" name="items[{{ $loop->index }}][notes]" value="{{ $item->notes }}"></td>
                    <td><button type="button" class="remove-row">Remove</button></td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    <button type="button" id="add-item">Add Item</button>
    <br><br>
    <button type="submit">Save</button>
</form>

@endsection

@section('scripts')
<script>
let index = {{ isset($requestable) ? $requestable->items->count() : 0 }};
document.getElementById('add-item').addEventListener('click', function() {
    const table = document.getElementById('items-table').getElementsByTagName('tbody')[0];
    let row = document.createElement('tr');
    let consumableOptions = `@foreach($consumables as $consumable)<option value="{{ $consumable->id }}">{{ $consumable->name }} (Stock: {{ $consumable->qty }})</option>@endforeach`;
    row.innerHTML = `
        <td><select name="items[${index}][consumable_id]">${consumableOptions}</select></td>
        <td><input type="number" name="items[${index}][quantity]" value="1" min="1"></td>
        <td><input type="text" name="items[${index}][notes]"></td>
        <td><button type="button" class="remove-row">Remove</button></td>
    `;
    table.appendChild(row);
    index++;
});

document.addEventListener('click', function(e){
    if(e.target && e.target.classList.contains('remove-row')){
        e.target.closest('tr').remove();
    }
});
</script>
@endsection
