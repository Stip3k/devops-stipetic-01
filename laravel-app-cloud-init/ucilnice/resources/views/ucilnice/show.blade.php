@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Podrobnosti učilnice: {{ $ucilnica->id_ucilnice }}</h4>
                    <div>
                        <a href="{{ route('rezervacije.create') }}?ucilnica_id={{ $ucilnica->id }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nova rezervacija
                        </a>
                        <a href="{{ route('ucilnice.calendar', $ucilnica->id) }}" class="btn btn-success">
                            <i class="fas fa-calendar"></i> Koledar
                        </a>
                        <a href="{{ route('ucilnice.edit', $ucilnica->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Uredi
                        </a>
                        <a href="{{ route('ucilnice.index') }}" class="btn btn-secondary">
                            Nazaj
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Osnovni podatki</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">ID Učilnice:</th>
                                    <td>{{ $ucilnica->id_ucilnice }}</td>
                                </tr>
                                <tr>
                                    <th>Kapaciteta:</th>
                                    <td>{{ $ucilnica->kapaciteta }} mest</td>
                                </tr>
                                <tr>
                                    <th>Vrsta učilnice:</th>
                                    <td>{{ $ucilnica->vrsta_ucilnice }}</td>
                                </tr>
                                <tr>
                                    <th>Skrbnik:</th>
                                    <td>{{ $ucilnica->skrbnik ?? 'Ni določen' }}</td>
                                </tr>
                                <tr>
                                    <th>Ustvarjena:</th>
                                    <td>{{ $ucilnica->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Statistika</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="60%">Trenutne rezervacije:</th>
                                    <td>{{ $rezervacije->where('end_date', '>', now())->count() }}</td>
                                </tr>
                                <tr>
                                    <th>Prihodnje rezervacije:</th>
                                    <td>{{ $rezervacije->where('start_date', '>', now())->count() }}</td>
                                </tr>
                                <tr>
                                    <th>Skupaj rezervacij:</th>
                                    <td>{{ $rezervacije->count() }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <h5 class="mt-4">Zadnje rezervacije</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Uporabnik</th>
                                    <th>Namen</th>
                                    <th>Od</th>
                                    <th>Do</th>
                                    <th>Status</th>
                                    <th>Akcije</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rezervacije->take(10) as $rezervacija)
                                    <tr>
                                        <td>{{ $rezervacija->customer->name ?? 'Neznan' }}</td>
                                        <td>{{ $rezervacija->notes ?? 'Brez naziva' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($rezervacija->start_date)->format('d.m.Y H:i') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($rezervacija->end_date)->format('d.m.Y H:i') }}</td>
                                        <td>
                                            @if($rezervacija->start_date > now())
                                                <span class="badge bg-primary">Prihodnja</span>
                                            @elseif($rezervacija->end_date > now())
                                                <span class="badge bg-success">V teku</span>
                                            @else
                                                <span class="badge bg-secondary">Zaključena</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('rezervacije.show', $rezervacija->id) }}" class="btn btn-sm btn-info" title="Pregled">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Ni rezervacij za to učilnico.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection