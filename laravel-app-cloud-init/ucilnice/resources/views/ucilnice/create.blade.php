@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Nova učilnica</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('ucilnice.store') }}">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="id_ucilnice" class="form-label">ID Učilnice <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('id_ucilnice') is-invalid @enderror" 
                                   id="id_ucilnice" name="id_ucilnice" value="{{ old('id_ucilnice') }}" 
                                   placeholder="npr. P01, R12, L05" required>
                            <small class="form-text text-muted">Unikatna oznaka učilnice (npr. P01 za predavalnico 1)</small>
                            @error('id_ucilnice')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="kapaciteta" class="form-label">Kapaciteta <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('kapaciteta') is-invalid @enderror" 
                                   id="kapaciteta" name="kapaciteta" value="{{ old('kapaciteta') }}" 
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
                                <option value="Predavalnica" {{ old('vrsta_ucilnice') == 'Predavalnica' ? 'selected' : '' }}>Predavalnica</option>
                                <option value="Računalniška učilnica" {{ old('vrsta_ucilnice') == 'Računalniška učilnica' ? 'selected' : '' }}>Računalniška učilnica</option>
                                <option value="Laboratorij" {{ old('vrsta_ucilnice') == 'Laboratorij' ? 'selected' : '' }}>Laboratorij</option>
                                <option value="Seminarska soba" {{ old('vrsta_ucilnice') == 'Seminarska soba' ? 'selected' : '' }}>Seminarska soba</option>
                                <option value="Drugo" {{ old('vrsta_ucilnice') == 'Drugo' ? 'selected' : '' }}>Drugo</option>
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
                                   id="skrbnik" name="skrbnik" value="{{ old('skrbnik') }}"
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
                                <i class="fas fa-save"></i> Shrani učilnico
                            </button>
                            <a href="{{ route('ucilnice.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Prekliči
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <i class="fas fa-info-circle"></i> Navodila
                </div>
                <div class="card-body">
                    <ul>
                        <li><strong>ID Učilnice</strong> - uporabite kratko, jasno oznako (npr. P01 za Predavalnica 1)</li>
                        <li><strong>Kapaciteta</strong> - vnesite maksimalno število oseb, ki jih učilnica sprejme</li>
                        <li><strong>Vrsta učilnice</strong> - izberite tip učilnice za lažje filtriranje</li>
                        <li><strong>Skrbnik</strong> - neobvezno polje za kontaktno osebo</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection