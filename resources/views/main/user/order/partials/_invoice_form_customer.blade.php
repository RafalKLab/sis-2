@section('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
@endsection

<div class="col-md-6">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="col-md-5">
                Edit SF. {{ $customer }}
            </div>
            <div class="col-md-1 d-flex justify-content-end">
                <button class="btn-outline-primary btn" onclick="submitForm()">Save</button>
            </div>
        </div>
        <div class="card-body">
            <form id="edit-customer-invoice-form" method="POST" action="{{ route('orders.save-customer-invoice', ['orderId'=>$orderId, 'customer'=>$customer]) }}">
                <div class="form-group row mb-3">
                    <label class="col-sm-3 col-form-label">SF. {{ $customer }}</label>
                    <div class="col-sm-9">
                        @csrf
                        <label for="invoice_number">Sąskaitos faktūros numeris</label>
                        <input name="invoice_number" type="text" class="form-control {{ $errors->has('invoice_number') ? 'is-invalid' : '' }}" id="invoice_number" value="{{ $invoiceData['number'] }}">
                        @if ($errors->has('invoice_number'))
                            <div class="invalid-feedback">
                                {{ $errors->first('invoice_number') }}
                            </div>
                        @endif

                        <label for="invoice_issue_date" class="col-sm-3 col-form-label">Išrašymo data</label>
                        <input name="invoice_issue_date" type="date" class="form-control {{ $errors->has('invoice_issue_date') ? 'is-invalid' : '' }}" id="invoice_issue_date" value="{{ $invoiceData['issue_date'] }}">
                        @if ($errors->has('invoice_issue_date'))
                            <div class="invalid-feedback">
                                {{ $errors->first('invoice_issue_date') }}
                            </div>
                        @endif

                        <label for="invoice_pay_until_date" class="col-sm-3 col-form-label">Apmokėti iki</label>
                        <input name="invoice_pay_until_date" type="date" class="form-control {{ $errors->has('invoice_pay_until_date') ? 'is-invalid' : '' }}" id="invoice_pay_until_date" value="{{ $invoiceData['pay_until_date'] }}">
                        @if ($errors->has('invoice_pay_until_date'))
                            <div class="invalid-feedback">
                                {{ $errors->first('invoice_pay_until_date') }}
                            </div>
                        @endif

                        <label for="invoice_status" class="col-sm-3 col-form-label">Būsena</label>
                        <select class="form-control {{ $errors->has('invoice_status') ? 'is-invalid' : '' }}" id="invoice_status" name="invoice_status">
                            @foreach($invoiceStatusSelect as $key => $option)
                                <option value="{{ $key }}" {{ old('invoice_status', $invoiceData['status']) == $key ? 'selected' : '' }}> {{ $option }}</option>
                            @endforeach
                        </select>
                        @if(!$invoiceData['is_new'])
                            <input name="invoice_id" type="hidden" value="{{ $invoiceData['id'] }}">
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@section('script')
    <script>
        function submitForm() {
            // Ensure the ID here matches your form's ID
            document.getElementById('edit-customer-invoice-form').submit();
        }
    </script>
@endsection
