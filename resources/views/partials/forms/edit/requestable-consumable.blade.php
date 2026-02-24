<!-- Requestable -->
<div class="form-group">
    <div class="col-sm-offset-3 col-md-9">
        <label class="form-control" for="requestable">
            <input type="checkbox" value="1" name="requestable" id="requestable"
              {{ old('requestable', $item->requestable ?? false) ? 'checked' : '' }}> {{ trans('general.request_consumable') }}
    </label>
    </div>
</div>
