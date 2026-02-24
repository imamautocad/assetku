<div id="{{ (isset($id_divname)) ? $id_divname : 'assetsBulkEditToolbar' }}" style="min-width:340px">
    <form
    method="POST"
    action="{{ route('consumables.bulk.labels') }}"
    accept-charset="UTF-8"
    class="form-inline"
    id="{{ (isset($id_formname)) ? $id_formname : 'consumablessBulkForm' }}"
>
    @csrf

    <input name="sort" type="hidden" value="consumables.id">
    <input name="order" type="hidden" value="asc">  
    <label for="bulk_actions">
        <span class="sr-only">
            {{ trans('button.bulk_actions') }}
        </span>
    </label>
    <select name="bulk_actions" class="form-control select2" aria-label="bulk_actions" style="min-width: 340px;">
        

            <option value="labels" {{$snipeSettings->shortcuts_enabled == 1 ? "accesskey=l" : ''}}>{{ trans_choice('button.generate_labels', 2) }}</option>
        {{-- @endif --}}
    </select>

    <button class="btn btn-primary" id="{{ (isset($id_button)) ? $id_button : 'bulkConsumableEditButton' }}" disabled>{{ trans('button.go') }}</button>
    </form>
</div>

 
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('consumablessBulkForm');
    const select = form.querySelector('select[name="bulk_actions"]');

    form.addEventListener('submit', function () {
        if (select.value === 'labels') {
            form.setAttribute('target', '_blank');
        } else {
            form.removeAttribute('target');
        }
    });
});
</script>