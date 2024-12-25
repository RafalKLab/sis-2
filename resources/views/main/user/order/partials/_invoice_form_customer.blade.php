@section('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
@endsection

<!-- Modal -->
<div class="modal fade" id="calculationModal" tabindex="-1" aria-labelledby="calculationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="calculationModalLabel">Pirkėjas: {{ $customer }} Užsakymas: {{ $orderData['key'] }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if(!$invoiceData['is_trans'])
                    <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Prekės pavadinimas</th>
                        <th>Kaina</th>
                        <th>Pirktas kiekis</th>
                        <th>Visa kaina</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($invoiceData['calculation_details']['items'] as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['item_name'] }}</td>
                            <td>{{ $item['item_price'] }}</td>
                            <td>{{ $item['purchased_quantity'] }}</td>
                            <td>{{ $item['total_price_for_item'] }}</td>
                        </tr>
                    @endforeach
                    <!-- More rows as needed -->
                    </tbody>
                    <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Mokėjimo suma: {{ $invoiceData['calculation_details']['total_price'] }}</th>
                    </tr>
                    </tfoot>
                </table>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

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

                        <label for="sum" class="col-sm-3 col-form-label">Mokėjimo suma
                        @if($invoiceData['calculated_sum'] !== $invoiceData['sum'] && !$invoiceData['is_trans'])
                            <span class="text-warning"><i title="Suma nėra lygi sumai, kuri buvo apskaičiuota automatiškai" class="fa-solid fa-triangle-exclamation"></i></span>
                        @endif
                        </label>
                        <input required name="sum" type="number" class="form-control {{ $errors->has('sum') ? 'is-invalid' : '' }}" id="sum" value="{{ $invoiceData['sum'] }}">
                        @if ($errors->has('sum'))
                            <div class="invalid-feedback">
                                {{ $errors->first('sum') }}
                            </div>
                        @endif
                        @if(!$invoiceData['is_trans'])
                            <div class="feedback">
                                <i><small>Automatiškai apskaičiuota suma: <b>{{ $invoiceData['calculated_sum'] }}</b></small></i>
                                <i style="cursor: pointer;" onclick="showCalculationModal()" title="Rodyti skaičiavimus" class="fa-solid fa-up-right-from-square"></i>
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
    <script>
        function showCalculationModal() {
            var myModal = new bootstrap.Modal(document.getElementById('calculationModal'), {});
            myModal.show();
        }
    </script>
@endsection
