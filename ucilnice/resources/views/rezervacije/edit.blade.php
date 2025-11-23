@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Uredi rezervacijo</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('rezervacije.update', $rezervacija->id) }}" id="rezervacijaForm">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="ucilnica_info" class="form-label">Učilnica</label>
                            <input type="text" class="form-control" id="ucilnica_info" 
                                   value="{{ $rezervacija->reservable->id_ucilnice }} - {{ $rezervacija->reservable->vrsta_ucilnice }} ({{ $rezervacija->reservable->kapaciteta }} mest)" 
                                   readonly disabled>
                            <small class="text-muted">Učilnice ni mogoče spremeniti. Za drugo učilnico ustvarite novo rezervacijo.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="datum_od" class="form-label">Datum od</label>
                                    <input type="datetime-local" class="form-control @error('datum_od') is-invalid @enderror" 
                                           id="datum_od" name="datum_od" 
                                           value="{{ old('datum_od', \Carbon\Carbon::parse($rezervacija->start_date)->format('Y-m-d\TH:i')) }}" 
                                           required>
                                    @error('datum_od')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="datum_do" class="form-label">Datum do</label>
                                    <input type="datetime-local" class="form-control @error('datum_do') is-invalid @enderror" 
                                           id="datum_do" name="datum_do" 
                                           value="{{ old('datum_do', \Carbon\Carbon::parse($rezervacija->end_date)->format('Y-m-d\TH:i')) }}" 
                                           required>
                                    @error('datum_do')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="namen" class="form-label">Namen rezervacije</label>
                            <input type="text" class="form-control @error('namen') is-invalid @enderror" 
                                   id="namen" name="namen" 
                                   value="{{ old('namen', $rezervacija->notes) }}" 
                                   required 
                                   placeholder="npr. Predavanje iz matematike">
                            @error('namen')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="opombe" class="form-label">Opombe (neobvezno)</label>
                            <textarea class="form-control @error('opombe') is-invalid @enderror" 
                                      id="opombe" name="opombe" rows="3" 
                                      placeholder="Dodatne informacije o rezervaciji...">{{ old('opombe', $rezervacija->description) }}</textarea>
                            @error('opombe')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="alert alert-info" id="availabilityMessage" style="display: none;">
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                Posodobi rezervacijo
                            </button>
                            <button type="button" class="btn btn-info" onclick="checkAvailability()">
                                Preveri razpoložljivost
                            </button>
                            <a href="{{ route('rezervacije.index') }}" class="btn btn-secondary">
                                Prekliči
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function checkAvailability() {
    const datum_od = document.getElementById('datum_od').value;
    const datum_do = document.getElementById('datum_do').value;
    
    if (!datum_od || !datum_do) {
        alert('Prosimo, izpolnite vse obvezne podatke.');
        return;
    }
    
    fetch('{{ route("rezervacije.check-availability") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            ucilnica_id: {{ $rezervacija->reservable_id }},
            datum_od: datum_od,
            datum_do: datum_do,
            exclude_id: {{ $rezervacija->id }}
        })
    })
    .then(response => response.json())
    .then(data => {
        const messageDiv = document.getElementById('availabilityMessage');
        messageDiv.style.display = 'block';
        
        if (data.available) {
            messageDiv.className = 'alert alert-success';
            messageDiv.textContent = data.message;
        } else {
            messageDiv.className = 'alert alert-danger';
            messageDiv.textContent = data.message;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Prišlo je do napake pri preverjanju razpoložljivosti.');
    });
}

// Nastavimo minimalni datum na danes
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const dateString = today.toISOString().slice(0, 16);
    document.getElementById('datum_od').min = dateString;
    document.getElementById('datum_do').min = dateString;
});
</script>
@endpush
@endsection