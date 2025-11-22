@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Seznam učilnic</h4>
                    <a href="{{ route('ucilnice.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nova učilnica
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($ucilnice->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID Učilnice</th>
                                        <th>Kapaciteta</th>
                                        <th>Vrsta učilnice</th>
                                        <th>Skrbnik</th>
                                        <th width="280px">Akcije</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ucilnice as $ucilnica)
                                        <tr>
                                            <td><strong>{{ $ucilnica->id_ucilnice }}</strong></td>
                                            <td>{{ $ucilnica->kapaciteta }} mest</td>
                                            <td>{{ $ucilnica->vrsta_ucilnice }}</td>
                                            <td>{{ $ucilnica->skrbnik ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('ucilnice.show', $ucilnica->id) }}" class="btn btn-sm btn-info" title="Pregled">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('ucilnice.calendar', $ucilnica->id) }}" class="btn btn-sm btn-success" title="Koledar">
                                                    <i class="fas fa-calendar"></i>
                                                </a>
                                                <a href="{{ route('ucilnice.edit', $ucilnica->id) }}" class="btn btn-sm btn-warning" title="Uredi">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('ucilnice.destroy', $ucilnica->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Ali ste prepričani, da želite izbrisati to učilnico?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Izbriši">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                <a href="{{ route('rezervacije.create') }}?ucilnica_id={{ $ucilnica->id }}" class="btn btn-sm btn-primary" title="Rezerviraj">
                                                    <i class="fas fa-plus"></i> Rezerviraj
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <h5>Ni učilnic v sistemu</h5>
                            <p>Začnite z dodajanjem prve učilnice.</p>
                            <a href="{{ route('ucilnice.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Dodaj prvo učilnico
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            @if($ucilnice->count() > 0)
                <div class="card mt-3">
                    <div class="card-header">
                        <i class="fas fa-info-circle"></i> Informacije
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <h3>{{ $ucilnice->count() }}</h3>
                                <p>Skupno učilnic</p>
                            </div>
                            <div class="col-md-3 text-center">
                                <h3>{{ $ucilnice->sum('kapaciteta') }}</h3>
                                <p>Skupna kapaciteta</p>
                            </div>
                            <div class="col-md-3 text-center">
                                <h3>{{ $ucilnice->where('vrsta_ucilnice', 'Predavalnica')->count() }}</h3>
                                <p>Predavalnic</p>
                            </div>
                            <div class="col-md-3 text-center">
                                <h3>{{ $ucilnice->where('vrsta_ucilnice', 'Računalniška učilnica')->count() }}</h3>
                                <p>Računalniških učilnic</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection