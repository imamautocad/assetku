<!-- cpu -->
<div class="form-group {{ $errors->has('cpu') ? ' has-error' : '' }}">
    <label for="{{ $fieldname }}" class="col-md-3 control-label">{{ trans('admin/hardware/form.cpu') }} </label>
    <div class="col-md-7 col-sm-12{{  (Helper::checkIfRequired($item, 'cpu')) ? ' required' : '' }}">
        <input class="form-control" type="text" name="cpu" id="cpu" value="{{ old((isset($old_val_name) ? $old_val_name : $fieldname), $item->cpu) }}" />
        {!! $errors->first('cpu', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
    </div>
</div>
 
