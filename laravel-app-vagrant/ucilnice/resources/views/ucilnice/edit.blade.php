@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Uredi učilnico: {{ $ucilnica->id_ucilnice }}</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('ucilnice.update', $ucilnica->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="id_ucilnice" class="form-label">ID Učilnice <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('id_ucilnice') is-invalid @enderror" 
                                   id="id_ucilnice" name="id_ucilnice" 
                                   value="{{ old('id_ucilnice', $ucilnica->id_ucilnice) }}" 
                                   placeholder="npr. P01, R12, L05" required>
                            <small class="form-text text-muted">Unikatna oznaka učilnice</small>
                            @error('id_ucilnice')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="kapaciteta" class="form-label">Kapaciteta <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('kapaciteta') is-invalid @enderror" 
                                   id="kapaciteta" name="kapaciteta" 
                                   value="{{ old('kapaciteta', $ucilnica->kapaciteta) }}" 
                                   min="1" max="500" required>
                            <small class="form-text text-muted">Število sedežev v učilnici</small>
                            @error('kapaciteta')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="vrsta_ucilnice" class="form-label">Vrsta učilnice <span class="text-danger">*</span></label>
                            <select class="form-control @error('vrsta_ucilnice') is-invalid @enderror" 
                                    id="vrsta_ucilnice" name="vrsta_ucilnice" required>
                                <option value="">-- Izberite vrsto --</option>
                                <option value="Predavalnica" {{ old('vrsta_ucilnice', $ucilnica->vrsta_ucilnice) == 'Predavalnica' ? 'selected' : '' }}>Predavalnica</option>
                                <option value="Računalniška učilnica" {{ old('vrsta_ucilnice', $ucilnica->vrsta_ucilnice) == 'Računalniška učilnica' ? 'selected' : '' }}>Računalniška učilnica</option>
                                <option value="Laboratorij" {{ old('vrsta_ucilnice', $ucilnica->vrsta_ucilnice) == 'Laboratorij' ? 'selected' : '' }}>Laboratorij</option>
                                <option value="Seminarska soba" {{ old('vrsta_ucilnice', $ucilnica->vrsta_ucilnice) == 'Seminarska soba' ? 'selected' : '' }}>Seminarska soba</option>
                                <option value="Drugo" {{ old('vrsta_ucilnice', $ucilnica->vrsta_ucilnice) == 'Drugo' ? 'selected' : '' }}>Drugo</option>
                            </select>
                            @error('vrsta_ucilnice')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="skrbnik" class="form-label">Skrbnik</label>
                            <input type="text" class="form-control @error('skrbnik') is-invalid @enderror" 
                                   id="skrbnik" name="skrbnik" 
                                   value="{{ old('skrbnik', $ucilnica->skrbnik) }}"
                                   placeholder="Ime in priimek skrbnika (neobvezno)">
                            <small class="form-text text-muted">Oseba odgovorna za učilnico</small>
                            @error('skrbnik')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Posodobi učilnico
                            </button>
                            <a href="{{ route('ucilnice.show', $ucilnica->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Prekliči
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <i class="fas fa-info-circle"></i> Informacije o učilnici
                </div>
                <div class="card-body">
                    <p><strong>Zadnja sprememba:</strong> {{ $ucilnica->updated_at->format('d.m.Y ob H:i') }}</p>
                    <p><strong>Ustvarjena:</strong> {{ $ucilnica->created_at->format('d.m.Y ob H:i') }}</p>
                    
                    @php
                        $aktivneRezervacije = \ShayanYS\LaraReserve\Models\Reserve::where('reservable_type', get_class($ucilnica))
                            ->where('reservable_id', $ucilnica->id)
                            ->where('end_reserve_date', '>', now())
                            ->count();
                    @endphp
                    
                    @if($aktivneRezervacije > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> 
                            Ta učilnica ima <strong>{{ $aktivneRezervacije }}</strong> {{ $aktivneRezervacije == 1 ? 'aktivno rezervacijo' : 'aktivnih rezervacij' }}.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection