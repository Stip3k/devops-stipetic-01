@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Nova rezervacija učilnice</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('rezervacije.store') }}" id="rezervacijaForm">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="ucilnica_id" class="form-label">Učilnica</label>
                            <select class="form-control @error('ucilnica_id') is-invalid @enderror" 
                                    id="ucilnica_id" name="ucilnica_id" required>
                                <option value="">Izberite učilnico...</option>
                                @foreach($ucilnice as $ucilnica)
                                    <option value="{{ $ucilnica->id }}" {{ old('ucilnica_id') == $ucilnica->id ? 'selected' : '' }}>
                                        {{ $ucilnica->id_ucilnice }} - {{ $ucilnica->vrsta_ucilnice }} ({{ $ucilnica->kapaciteta }} mest)
                                    </option>
                                @endforeach
                            </select>
                            @error('ucilnica_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="datum_od" class="form-label">Datum od</label>
                                    <input type="datetime-local" class="form-control @error('datum_od') is-invalid @enderror" 
                                           id="datum_od" name="datum_od" value="{{ old('datum_od') }}" required>
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
                                           id="datum_do" name="datum_do" value="{{ old('datum_do') }}" required>
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
                                   id="namen" name="namen" value="{{ old('namen') }}" required 
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
                                      placeholder="Dodatne informacije o rezervaciji...">{{ old('opombe') }}</textarea>
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
                                Ustvari rezervacijo
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
    const ucilnica_id = document.getElementById('ucilnica_id').value;
    const datum_od = document.getElementById('datum_od').value;
    const datum_do = document.getElementById('datum_do').value;
    
    if (!ucilnica_id || !datum_od || !datum_do) {
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
            ucilnica_id: ucilnica_id,
            datum_od: datum_od,
            datum_do: datum_do
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