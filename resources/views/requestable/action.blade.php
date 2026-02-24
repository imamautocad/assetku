@php
    // $r adalah RequestableConsumable model passed from controller mapping
@endphp

<a href="{{ route('requestable.show', $r->id) }}" class="btn btn-xs btn-default">View</a>

@if($r->status == 'draft' && (Auth::id() == $r->user_id || Auth::user()->hasRole('admin')))
    <a href="{{ route('requestable.edit', $r->id) }}" class="btn btn-xs btn-primary">Edit</a>
    <form action="{{ route('requestable.destroy', $r->id ?? 0) }}" method="POST" style="display:inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Delete draft?')">Delete</button>
    </form>
@endif