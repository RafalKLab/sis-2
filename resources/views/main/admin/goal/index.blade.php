@extends('main.templates.main')
@section('title')
    Goals
@endsection
@section('styles')
    <link href="{{ asset('css/goals.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <div class="container-fluid py-2">

        @if(empty($goals))
            <h3>Nėra užsibrėžtų tikslų</h3>
        @else
            @foreach($goals as $goal)
                <div class="goal">
                    <h3>{{ $goal['name'] }}</h3>
                    <div class="d-flex flex-wrap" style="gap:20px;">
                        <div class="col-lg-4 goal-card target">
                            <div class="row">
                                <div class="col-md-6 left">
                                    <div class="circle-container">
                                        <div class="circle" data-degree="100" data-color="#4781FF">
                                            <h2 class="number"><i class="fa-solid fa-medal" style="color: #4781FF"></i></h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 right">
                                    TIKSLAS
                                    <br>{{ $goal['amount'] }} €
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 goal-card target">
                            <div class="row">
                                <div class="col-md-6 left">
                                    <div class="circle-container">
                                        <div class="circle" data-degree="{{ $goal['sales_percentage'] }}" data-color="#0FDA67">
                                            <h2 class="number" style="color: #0FDA67">{{ $goal['sales_percentage'] }}<span>%</span></h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 right">
                                    Įvykdyta
                                    <br>{{ $goal['sales'] }} €
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 goal-card target">
                            <div class="row">
                                <div class="col-md-6 left">
                                    <div class="circle-container">
                                        <div class="circle" data-degree="{{ $goal['left_percentage'] }}" data-color="#ff2972">
                                            <h2 class="number" style="color: #ff2972">{{ $goal['left_percentage'] }}<span>%</span></h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 right">
                                    Liko
                                    <br>{{ $goal['left_sales'] }} €
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
            @endforeach
        @endif

    </div>
@endsection
@section('script')
    <script>
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

