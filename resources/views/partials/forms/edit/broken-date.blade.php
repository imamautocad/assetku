<!-- Broken -->
<div class="form-group {{ $errors->has('broken_date') ? ' has-error' : '' }}">
    <label for="broken_date" class="col-md-3 control-label">{{ trans('admin/hardware/form.broken_date') }}</label>
    <div class="col-md-9">
        <div class="input-group col-md-4 col-sm-6" style="padding-left: 0px;">
            <input class="form-control" type="text" name="broken_date" id="broken_date" value="{{ old('broken_date', $item->broken_date) }}" maxlength="3" />
            <span class="input-group-addon">{{ trans('admin/hardware/form.months') }}</span>
        </div>
        <div class="col-md-9" style="padding-left: 0px;">
            {!! $errors->first('broken_date', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
        </div>
    </div>
</div>
