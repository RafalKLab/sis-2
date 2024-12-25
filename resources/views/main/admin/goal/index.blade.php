@extends('main.templates.main')
@section('title')
    Goals
@endsection
@section('styles')
    <link href="{{ asset('css/goals.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <div class="container-fluid py-2">

        <h4 class="mt-4">Goals</h4>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="col-md-1"><i class="fa-solid fa-medal"></i></div>
                <div class="col-md-1 d-flex justify-content-end">
                    <a href="{{ route('goals.add') }}" class="btn btn-outline-primary"><i class="fa-solid fa-plus"></i></a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Amount</th>
                            <th>Start date</th>
                            <th>Visible</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($goals as $goal)
                            <tr>
                                <td>{{$goal['name']}}</td>
                                <td>{{$goal['amount']}}</td>
                                <td>{{$goal['start_date']}}</td>
                                <td>
                                    @if($goal['is_visible'])
                                        <span class="span-success">YES</span>
                                    @else
                                        <span class="span-danger">NO</span>
                                    @endif
                                </td>
                                <td>
                                        <div class="btn-group" style="display: flex; width: 100%;">
                                            <a href="{{ route('goals.edit', ['id'=>$goal['id']]) }}" title="Edit" class="btn btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
                                            <a onclick="return confirmAction();" href="{{ route('goals.delete', ['id'=>$goal['id']]) }}" title="Remove" class="btn btn-outline-danger"><i class="fa-solid fa-trash"></i></a>
                                        </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
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

        document.addEventListener("DOMContentLoaded", function (event) {
            let circle = document.querySelectorAll('.circle');
            circle.forEach(function (progress) {
                let degree = 0;
                var targetDegree = parseInt(progress.getAttribute('data-degree'));
                let color = progress.getAttribute('data-color')
                let number = progress.querySelector('.number');

                var interval = setInterval(function (){
                    degree += 1;
                    if (degree > targetDegree) {
                        clearInterval(interval);
                        return;
                    }
                    progress.style.background = `conic-gradient(${color} ${degree}%, #222 0%)`;
                },10)
            })
        });
    </script>
@endsection

