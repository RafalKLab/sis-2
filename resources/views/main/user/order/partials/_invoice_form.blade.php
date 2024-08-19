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

<label for="sum" class="col-sm-3 col-form-label">Mokėjimo suma
    @if($data['additional_data']['auto_calculated_sum']['exists'] && $data['additional_data']['auto_calculated_sum']['amount'] !== $data['additional_data']['sum'])
        <span class="text-warning"><i title="Suma nėra lygi susieto lauko sumai" class="fa-solid fa-triangle-exclamation"></i></span>
    @endif
</label>
<input required name="sum" type="number" class="form-control {{ $errors->has('sum') ? 'is-invalid' : '' }}" id="sum" value="{{ $data['additional_data']['sum'] }}">
@if ($errors->has('sum'))
    <div class="invalid-feedback">
        {{ $errors->first('sum') }}
    </div>
@endif
<div class="feedback">
    @if($data['additional_data']['auto_calculated_sum']['exists'])
        <i><small>Susietas sumos laukas -> {{$data['additional_data']['auto_calculated_sum']['represented_field']}}: {{$data['additional_data']['auto_calculated_sum']['amount']}} </small></i>
    @else
        <i><small>Sąskaita faktūra neturi susieto sumos lauko </small></i>
    @endif

</div>
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
