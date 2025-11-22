@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h5>Dobrodošli, {{ Auth::user()->name }}!</h5>
                    <p>Uspešno ste prijavljeni v sistem za rezervacijo učilnic.</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-door-open fa-3x mb-3 text-primary"></i>
                            <h5 class="card-title">Učilnice</h5>
                            <p class="card-text">Preglejte vse razpoložljive učilnice in njihovo zasedenost.</p>
                            <a href="{{ route('ucilnice.index') }}" class="btn btn-primary">
                                <i class="fas fa-list"></i> Seznam učilnic
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-calendar-plus fa-3x mb-3 text-success"></i>
                            <h5 class="card-title">Nova rezervacija</h5>
                            <p class="card-text">Rezervirajte učilnico za vaše potrebe.</p>
                            <a href="{{ route('rezervacije.create') }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Ustvari rezervacijo
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-calendar-check fa-3x mb-3 text-info"></i>
                            <h5 class="card-title">Moje rezervacije</h5>
                            <p class="card-text">Preglejte in upravljajte svoje rezervacije.</p>
                            <a href="{{ route('rezervacije.index') }}" class="btn btn-info">
                                <i class="fas fa-list"></i> Moje rezervacije
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-bar"></i> Statistika
                        </div>
                        <div class="card-body">
                            <p><strong>Skupno učilnic:</strong> {{ \App\Models\tabela_ucilnice::count() }}</p>
                            <p><strong>Vaše aktivne rezervacije:</strong> {{ Auth::user()->reserves()->where('end_reserve_date', '>', now())->count() }}</p>
                            <p><strong>Vaše vse rezervacije:</strong> {{ Auth::user()->reserves()->count() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-clock"></i> Vaše prihodnje rezervacije
                        </div>
                        <div class="card-body">
                            @php
                                $prihodnjeRezervacije = Auth::user()->reserves()
                                    ->where('reserved_date', '>', now())
                                    ->orderBy('reserved_date')
                                    ->take(3)
                                    ->get();
                            @endphp
                            
                            @if($prihodnjeRezervacije->count() > 0)
                                <ul class="list-unstyled">
                                    @foreach($prihodnjeRezervacije as $rezervacija)
                                        <li class="mb-2">
                                            <strong>{{ $rezervacija->reservable->id_ucilnice ?? 'N/A' }}</strong><br>
                                            {{ \Carbon\Carbon::parse($rezervacija->reserved_date)->format('d.m.Y H:i') }} - 
                                            {{ \Carbon\Carbon::parse($rezervacija->end_reserve_date)->format('H:i') }}<br>
                                            <small class="text-muted">{{ $rezervacija->notes ?? 'Brez opisa' }}</small>
                                        </li>
                                    @endforeach
                                </ul>
                                <a href="{{ route('rezervacije.index') }}" class="btn btn-sm btn-primary">
                                    Vse rezervacije
                                </a>
                            @else
                                <p class="text-muted">Nimate prihodnjih rezervacij.</p>
                                <a href="{{ route('rezervacije.create') }}" class="btn btn-sm btn-success">
                                    Ustvari prvo rezervacijo
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection