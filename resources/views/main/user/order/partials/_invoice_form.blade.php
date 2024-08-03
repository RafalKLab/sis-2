<label for="{{ $data['field_id'] }}">Sąskaitos faktūros numeris</label>
<input name="field_{{$data['field_id'] }}" type="text" class="form-control {{ $errors->has('field_' . $data['field_id']) ? 'is-invalid' : '' }}" id="{{ $data['field_id'] }}" value="{{ old('field_' . $data['field_id'], $data['value']) }}">
@if ($errors->has('field_' . $data['field_id']))
    <div class="invalid-feedback">
        {{ $errors->first('field_' . $data['field_id']) }}
    </div>
@endif
<label for="invoice_issue_date" class="col-sm-3 col-form-label">Išrašymo data</label>
<input name="invoice_issue_date" type="date" class="form-control {{ $errors->has('invoice_issue_date') ? 'is-invalid' : '' }}" id="invoice_issue_date" value="{{ old('invoice_issue_date', $data['additional_data']['issue_date']) }}">
@if ($errors->has('invoice_issue_date'))
    <div class="invalid-feedback">
        {{ $errors->first('invoice_issue_date') }}
    </div>
@endif

<label for="invoice_pay_until_date" class="col-sm-3 col-form-label">Apmokėti iki</label>
<input name="invoice_pay_until_date" type="date" class="form-control {{ $errors->has('invoice_pay_until_date') ? 'is-invalid' : '' }}" id="invoice_pay_until_date" value="{{ old('invoice_pay_until_date', $data['additional_data']['pay_until_date']) }}">
@if ($errors->has('invoice_pay_until_date'))
    <div class="invalid-feedback">
        {{ $errors->first('invoice_pay_until_date') }}
    </div>
@endif

<label for="invoice_status" class="col-sm-3 col-form-label">Būsena</label>
<select class="form-control {{ $errors->has('invoice_status') ? 'is-invalid' : '' }}" id="invoice_status" name="invoice_status">
    @foreach($data['input_select'] as $key => $option)
        <option value="{{ $key }}" {{ old('invoice_status', $data['additional_data']['status']) == $key ? 'selected' : '' }}> {{ $option }}</option>
    @endforeach
</select>
<input name="invoice_id" type="hidden" value="{{ $data['additional_data']['id'] }}">
@if ($errors->has('invoice_status_' . $data['field_id']))
    <div class="invalid-feedback">
        {{ $errors->first('invoice_status_' . $data['field_id']) }}
    </div>
@endif
