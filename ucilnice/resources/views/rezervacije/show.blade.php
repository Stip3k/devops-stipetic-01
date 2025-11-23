@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Podrobnosti rezervacije</h4>
                    <div>
                        @if($rezervacija->start_date > now())
                            <a href="{{ route('rezervacije.edit', $rezervacija->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Uredi
                            </a>
                        @endif
                        <a href="{{ route('rezervacije.index') }}" class="btn btn-secondary">
                            Nazaj
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Učilnica:</th>
                            <td>
                                @if($rezervacija->reservable)
                                    {{ $rezervacija->reservable->id_ucilnice }} - {{ $rezervacija->reservable->vrsta_ucilnice }}
                                    ({{ $rezervacija->reservable->kapaciteta }} mest)
                                @else
                                    <span class="text-muted">Učilnica je bila izbrisana</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Namen rezervacije:</th>
                            <td>{{ $rezervacija->notes ?? 'Ni navedeno' }}</td>
                        </tr>
                        <tr>
                            <th>Začetek:</th>
                            <td>{{ \Carbon\Carbon::parse($rezervacija->start_date)->format('d.m.Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Konec:</th>
                            <td>{{ \Carbon\Carbon::parse($rezervacija->end_date)->format('d.m.Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Trajanje:</th>
                            <td>
                                {{ \Carbon\Carbon::parse($rezervacija->start_date)->diffForHumans(\Carbon\Carbon::parse($rezervacija->end_date), true) }}
                            </td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($rezervacija->start_date > now())
                                    <span class="badge bg-primary">Prihodnja</span>
                                @elseif($rezervacija->end_date > now())
                                    <span class="badge bg-success">V teku</span>
                                @else
                                    <span class="badge bg-secondary">Zaključena</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Opombe:</th>
                            <td>{{ $rezervacija->description ?? 'Ni opomb' }}</td>
                        </tr>
                        <tr>
                            <th>Ustvarjena:</th>
                            <td>{{ $rezervacija->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    </table>

                    @if($rezervacija->start_date > now())
                        <div class="mt-3">
                            <form action="{{ route('rezervacije.destroy', $rezervacija->id) }}" method="POST" onsubmit="return confirm('Ali ste prepričani, da želite preklicati rezervacijo?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times"></i> Prekliči rezervacijo
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection