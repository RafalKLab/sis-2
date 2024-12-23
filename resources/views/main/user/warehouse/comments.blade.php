@extends('main.templates.main')
@section('title')
    Prekės komentarai
@endsection
@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/odometer.js/0.4.8/themes/odometer-theme-default.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/odometer.js/0.4.8/odometer.min.js"></script>
    <link href="{{ asset('css/warehouse.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <div class="container-fluid px-4">
        <h4 class="mt-4"></h4>

        <div class="row">
            <div class="col-md-6">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="col-md-5"><i class="fa-solid fa-list"></i> Prekė
                            </div>
                            <div class="col-md-3 d-flex justify-content-end">
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table_with_comment">
                                    <thead>
                                    <tr>
                                        <th scope="col">Užsakymas</th>
                                        <th scope="col">Pavadinimas</th>
                                        <th scope="col">Išmatavimai</th>
                                        <th scope="col">Klijai</th>
                                        <th scope="col">Kokybė</th>
                                        <th scope="col">Kiekis</th>
                                        <th scope="col">Vieneto kaina</th>
                                        <th scope="col">Vieneto savikaina</th>
                                        <th scope="col">Bendra vertė</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($items['items'] as $item)
                                        <tr>
                                            <td>
                                                <a class="custom-link" href="{{ route('orders.view', ['id'=>$item['order']['id']]) }}">
                                                    {{ $item['order']['key'] }}
                                                </a>
                                            </td>
                                            <td>{{ $item['name'] }}</td>
                                            <td>{{ $item['measurement'] }}</td>
                                            <td>{{ $item['glue'] }}</td>
                                            <td>{{ $item['quality'] }}</td>
                                            <td>{{ $item['amount'] }} {{ $item['measurement_unit'] }}</td>
                                            <td>{{ $item['price'] }}</td>
                                            <td>{{ $item['prime_cost'] }}</td>
                                            <td>{{ $item['total_price'] }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="col-md-5"><i class="fa-solid fa-list"></i> Komentarai ({{ count($comments) }})
                            </div>
                            <div class="col-md-3 d-flex justify-content-end">
                            </div>
                        </div>

                        <div class="card-body">
                            @foreach($comments as $comment)
                                <div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p>{{$comment->message }}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-secondary">{{ $comment->author_email }} {{ $comment->created_at }}</small>
                                        </div>
                                        @can('Delete warehouse item comments')
                                            <div class="col-md-6 text-end remove-button">
                                                <a onclick="return confirmAction();" href="{{ route('warehouses.remove-comments', ['noteId'=>$comment->id]) }}" title="Remove comment">Remove <i class="fa-regular fa-trash-can"></i></a>
                                            </div>
                                        @endcan
                                    </div>
                                    <hr>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="col-md-12">
                    @can('Write warehouse item comments')
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="col-md-5">Rašyti naują
                                </div>
                                <div class="col-md-3 d-flex justify-content-end">
                                </div>
                            </div>

                            <div class="card-body">
                                <form action="{{ route('warehouses.add-comments', ['warehouseId'=>$warehouse->id, 'itemId'=>$items['items'][0]['item_id']]) }}" method="post">
                                    <div class="form-group">
                                        <textarea name="message" required class="form-control" id="exampleFormControlTextarea1" rows="9"></textarea>
                                    </div>
                                    <br>
                                    @csrf
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </form>
                            </div>
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        function confirmAction() {
            return confirm('Are you sure you want to remove ?');
        }
    </script>
@endsection

