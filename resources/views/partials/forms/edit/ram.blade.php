<!-- ram -->
<div class="form-group {{ $errors->has('ram') ? ' has-error' : '' }}">
    <label for="{{ $fieldname }}" class="col-md-3 control-label">{{ trans('admin/hardware/form.ram') }} </label>
    <div class="col-md-7 col-sm-12{{  (Helper::checkIfRequired($item, 'ram')) ? ' required' : '' }}">
        <input class="form-control" type="text" name="ram" id="ram" value="{{ old((isset($old_val_name) ? $old_val_name : $fieldname), $item->ram) }}" />
        {!! $errors->first('ram', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
    </div>
</div>
