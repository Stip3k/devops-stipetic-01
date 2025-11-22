@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Moje rezervacije</h4>
                    <a href="{{ route('rezervacije.create') }}" class="btn btn-primary">Nova rezervacija</a>
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

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Učilnica</th>
                                    <th>Namen</th>
                                    <th>Datum od</th>
                                    <th>Datum do</th>
                                    <th>Status</th>
                                    <th>Akcije</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rezervacije as $rezervacija)
                                    <tr>
                                        <td>
                                            @if($rezervacija->reservable)
                                                {{ $rezervacija->reservable->id_ucilnice }} - {{ $rezervacija->reservable->vrsta_ucilnice }}
                                            @else
                                                <span class="text-muted">Učilnica izbrisana</span>
                                            @endif
                                        </td>
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
                                            <a href="{{ route('rezervacije.show', $rezervacija->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($rezervacija->start_date > now())
                                                <a href="{{ route('rezervacije.edit', $rezervacija->id) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('rezervacije.destroy', $rezervacija->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Ali ste prepričani, da želite preklicati rezervacijo?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Nimate aktivnih rezervacij.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center">
                        {{ $rezervacije->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection