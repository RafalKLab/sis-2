@extends('main.templates.main')
@section('title')
    Dashboard
@endsection

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Dashboard</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active"></li>
        </ol>

        <form class="form-control" method="post" action="{{ route('feedback.create') }}">
            @csrf
            <div class="form-group">
                <label for="feedback">Feedback</label>
                <textarea id="feedback" name="feedback" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Submit</button>
        </form>

        <div class="row mt-3 mb-5">
            <div class="col-xl-12 col-md-12 mb-3">
                <div class="card">
                    <div class="card-body bg-primary text-white"><b>Pataisų užrašai</b> <i>2024-09-30</i></div>
                    <div class="card-footer align-items-center justify-content-between">

                        <h6>Ištaisytos klaidos:</h6>
                        <ul>
                            <li>Užsakymo numerio blogas eiliškumas, kai prieš tai užsakymas turėjo susietą užsakymą.</li>
                            <li>Pirkimo/pardavimo kiekis suapvalintas iki sveikojo skaičiaus.</li>
                            <li>Pakoreguotas klijų pavadinimas: WBP</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-xl-12 col-md-12 mb-3">
                <div class="card">
                    <div class="card-body bg-primary text-white"><b>Pataisų užrašai</b> <i>2024-09-25</i></div>
                    <div class="card-footer align-items-center justify-content-between">
                        <h6>Jūsų informacijai:</h6>
                        <ul>
                            <li>Kai norite įrašyti tikslųjį skaičių, naudokite tašką, o ne kablelį, pavyzdžiui, 7.5 o ne 7,5.</li>
                            <li>Sumažintas šriftas sandėlio bendros vertės atvaizdavime.</li>
                            <li>Pridėtas papildomas <i>scroll bar</i> užsakymų apžvalgos lentelės viršuje.</li>
                            <li>Pridėtas prekės matavimo vieneto laukelis užsakymų apžvalgos lentelėje.</li>
                            <li>Nustatytas užsakymų apžvalgos lentelės eiliškumas pagal užsakymo sukūrimo datą.</li>
                        </ul>

                        <h6>Ištaisytos klaidos:</h6>
                        <ul>
                            <li>Klaida, kai neteisingai ivestas skaičius naudojant kablelį, mesdavo klaidą.</li>
                            <li>Klaida, kai sandėlio pavadinime galėjo atsirasti /, kuris metė klaidą.</li>
                        </ul>

                        <h6>Žinomos klaidos, kurios dar neišspręstos:</h6>
                        <ul>
                            <li>Užsakymo numerio blogas eiliškumas, kai prieš tai užsakymas turėjo susietą užsakymą.</li>
                            <li>Pirkimo/pardavimo kiekis suapvalintas iki sveikojo skaičiaus.</li>
                        </ul>
                    </div>
                </div>
            </div>

            @foreach($feedback as $entry)
                <div class="col-xl-12 col-md-12 mb-3">
                    <div class="card">
                        <div class="card-body"><b>{{ $entry->user }}</b> <i>{{ $entry->created_at->format('Y-m-d') }}</i></div>
                        <div class="card-footer align-items-center justify-content-between">
                            <p>
                                {{ $entry->message }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
