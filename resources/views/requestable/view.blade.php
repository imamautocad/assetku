@extends('layouts.app')
@section('content')
<h2>Request Detail</h2>
<p><strong>Department:</strong> {{ $requestable->department->name }}</p>
<p><strong>Status:</strong> {{ ucfirst($requestable->status) }}</p>
<p><strong>Notes:</strong> {{ $requestable->notes }}</p>
<p><strong>Created At:</strong> {{ $requestable->created_at->format('d-m-Y H:i') }}</p>

<h3>Items</h3>
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>No</th>
            <th>Consumable</th>
            <th>Quantity</th>
            <th>Notes</th>
        </tr>
    </thead>
    <tbody>
        @foreach($requestable->items as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item->consumable->name }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ $item->notes }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<br>
<a href="{{ route('requestable.index') }}">Back to List</a>
@if($requestable->status == 'draft')
    | <a href="{{ route('requestable.edit', $requestable->id) }}">Edit Draft</a>
@endif
@endsection
