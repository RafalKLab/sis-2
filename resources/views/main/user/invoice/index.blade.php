@extends('main.templates.main')
@section('title')
    Invoices
@endsection
@section('styles')
    <link href="{{ asset('css/custom_data_table.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4">Invoices</h4>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="col-md-1"><i class="fa-solid fa-file-invoice"></i></div>
                <div class="col-md-3 d-flex justify-content-end">
                    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0" method="GET">
                        <div class="input-group">
                            <input
                                    class="form-control"
                                    type="text"
                                    placeholder="Search for invoice"
                                    aria-label="Search for invoice"
                                    aria-describedby="btnNavbarSearch"
                                    name="search"
                                    value="{{ $search }}"
                            />
                            <button class="btn btn-primary" id="btnNavbarSearch" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="datatablesInvoice">
                        <thead>
                        <tr>
                            <th>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        Invoice Number
                                    </div>
                                    <div class="d-flex justify-content-end sort-button">
                                        <a class="sort-link" href="{{ route('invoices.index', [
                                            'sortColumn' => 'invoice_number',
                                            'sortOrder' => $currentSortOrder === 'asc' ? 'desc' : 'asc'
                                        ]) }}">
                                            <i class="fa-solid fa-sort"></i>
                                        </a>
                                    </div>
                                </div>
                            </th>
                            <th>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        Issue Date
                                    </div>
                                    <div class="d-flex justify-content-end sort-button">
                                        <a class="sort-link" href="{{ route('invoices.index', [
                                            'sortColumn' => 'issue_date',
                                            'sortOrder' => $currentSortOrder === 'asc' ? 'desc' : 'asc'
                                        ]) }}">
                                            <i class="fa-solid fa-sort"></i>
                                        </a>
                                    </div>
                                </div>
                            </th>
                            <th>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        Pay Until Date
                                    </div>
                                    <div class="d-flex justify-content-end sort-button">
                                        <a class="sort-link" href="{{ route('invoices.index', [
                                            'sortColumn' => 'pay_until_date',
                                            'sortOrder' => $currentSortOrder === 'asc' ? 'desc' : 'asc'
                                        ]) }}">
                                            <i class="fa-solid fa-sort"></i>
                                        </a>
                                    </div>
                                </div>
                            </th>
                            <th>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        Status
                                    </div>
                                    <div class="d-flex justify-content-end sort-button">
                                        <a class="sort-link" href="{{ route('invoices.index', [
                                            'sortColumn' => 'status',
                                            'sortOrder' => $currentSortOrder === 'asc' ? 'desc' : 'asc'
                                        ]) }}">
                                            <i class="fa-solid fa-sort"></i>
                                        </a>
                                    </div>
                                </div>
                            </th>
                            <th>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        Sum
                                    </div>
                                    <div class="d-flex justify-content-end sort-button">
                                        <a class="sort-link" href="{{ route('invoices.index', [
                                            'sortColumn' => 'sum',
                                            'sortOrder' => $currentSortOrder === 'asc' ? 'desc' : 'asc'
                                        ]) }}">
                                            <i class="fa-solid fa-sort"></i>
                                        </a>
                                    </div>
                                </div>
                            </th>
                            <th>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        Order
                                    </div>
                                    <div class="d-flex justify-content-end sort-button">
                                        <a class="sort-link" href="{{ route('invoices.index', [
                                            'sortColumn' => 'order_id',
                                            'sortOrder' => $currentSortOrder === 'asc' ? 'desc' : 'asc'
                                        ]) }}">
                                            <i class="fa-solid fa-sort"></i>
                                        </a>
                                    </div>
                                </div>
                            </th>
                            <th>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        Field name
                                    </div>
                                    <div class="d-flex justify-content-end sort-button">
                                        <a class="sort-link" href="{{ route('invoices.index', [
                                            'sortColumn' => 'field_id',
                                            'sortOrder' => $currentSortOrder === 'asc' ? 'desc' : 'asc'
                                        ]) }}">
                                            <i class="fa-solid fa-sort"></i>
                                        </a>
                                    </div>
                                </div>
                            </th>
                            <th>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        Customer
                                    </div>
                                    <div class="d-flex justify-content-end sort-button">
                                        <a class="sort-link" href="{{ route('invoices.index', [
                                            'sortColumn' => 'customer',
                                            'sortOrder' => $currentSortOrder === 'asc' ? 'desc' : 'asc'
                                        ]) }}">
                                            <i class="fa-solid fa-sort"></i>
                                        </a>
                                    </div>
                                </div>
                            </th>
                            <th>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        Created At
                                    </div>
                                    <div class="d-flex justify-content-end sort-button">
                                        <a class="sort-link" href="{{ route('invoices.index', [
                                            'sortColumn' => 'created_at',
                                            'sortOrder' => $currentSortOrder === 'asc' ? 'desc' : 'asc'
                                        ]) }}">
                                            <i class="fa-solid fa-sort"></i>
                                        </a>
                                    </div>
                                </div>
                            </th>
                            <th>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        Updated At
                                    </div>
                                    <div class="d-flex justify-content-end sort-button">
                                        <a class="sort-link" href="{{ route('invoices.index', [
                                            'sortColumn' => 'updated_at',
                                            'sortOrder' => $currentSortOrder === 'asc' ? 'desc' : 'asc'
                                        ]) }}">
                                            <i class="fa-solid fa-sort"></i>
                                        </a>
                                    </div>
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($invoices as $invoice)
                        <tr>
                                <td>
                                    @if($invoice->customer)
                                        <a class="more-details-link" href="{{ route('orders.edit-customer-invoice', ['orderId'=>$invoice->order_id, 'customer'=>$invoice->customer]) }}">
                                            {{$invoice->invoice_number}}
                                        </a>
                                    @elseif($invoice->field_id)
                                        <a class="more-details-link" href="{{ route('orders.edit-field', ['orderId'=>$invoice->order_id, 'fieldId'=>$invoice->field_id]) }}">
                                            {{$invoice->invoice_number}}
                                        </a>
                                    @endif
                                </td>
                                <td>{{$invoice->issue_date}}</td>
                                <td>{{$invoice->pay_until_date}}</td>
                                <td>
                                    <div class="{{$invoiceStatusColorClass[$invoice->invoice_number]}}">
                                        {{$invoiceStatusMap[$invoice->status]}}
                                    </div>
                                </td>
                                <td>{{ number_format($invoice->sum, 2, '.', ' ') }}</td>
                                <td>
                                    <a class="more-details-link" href="{{ route('orders.view', ['id'=>$invoice->order_id]) }}">
                                        {{$invoice->order->getKeyField()}}
                                    </a>
                                </td>
                                <td>{{$invoice->field?->name}}</td>
                                <td>{{$invoice->customer}}</td>
                                <td>{{$invoice->created_at}}</td>
                                <td>{{$invoice->updated_at}}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $invoices->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
@endsection

