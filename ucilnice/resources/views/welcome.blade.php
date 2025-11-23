@extends('layouts.app')

@section('content')
<div class="welcome-container">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-8 text-center">

                <h1 class="display-2 fw-bold mb-4 main-title">
                    Spletne tehnologije
                </h1>
                
                <p class="lead text-muted mb-5 fs-4">
                    Sistem za rezervacijo učilnic
                </p>
                
                <div class="description-box mb-5 mx-auto">
                    <p class="mb-4">
                        Aplikacija omogoča enostavno rezervacijo učilnic v realnem času. 
                        Uporabniki lahko pregledujejo razpoložljivost, ustvarjajo rezervacije 
                        in upravljajo svoje termine preko preglednega spletnega vmesnika.
                    </p>
                    <div class="features-list">
                        <span class="feature-badge">
                            <i class="fas fa-check-circle"></i> Rezervacije v realnem času
                        </span>
                        <span class="feature-badge">
                            <i class="fas fa-check-circle"></i> Koledarski pregled
                        </span>
                        <span class="feature-badge">
                            <i class="fas fa-check-circle"></i> Upravljanje učilnic
                        </span>
                    </div>
                </div>
                
                @guest
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-sign-in-alt me-2"></i>Prijava
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg px-5">
                            <i class="fas fa-user-plus me-2"></i>Registracija
                        </a>
                    </div>
                @else
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="{{ route('home') }}" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-home me-2"></i>Nadzorna plošča
                        </a>
                        <a href="{{ route('ucilnice.index') }}" class="btn btn-outline-primary btn-lg px-5">
                            <i class="fas fa-door-open me-2"></i>Učilnice
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</div>

<style>
    .welcome-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        min-height: 100vh;
        position: relative;
    }
    
    .main-title {
        color: #212529;
        font-weight: 700;
        letter-spacing: -1px;
    }
    
    .description-box {
        max-width: 700px;
        padding: 2rem;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .description-box p {
        font-size: 1.125rem;
        line-height: 1.7;
        color: #495057;
    }
    
    .features-list {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .feature-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: #f8f9fa;
        border-radius: 50px;
        font-size: 0.9rem;
        color: #495057;
        border: 1px solid #dee2e6;
    }
    
    .feature-badge i {
        color: #28a745;
    }
    
    .btn-lg {
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background: #0d6efd;
        border-color: #0d6efd;
    }
    
    .btn-primary:hover {
        background: #0b5ed7;
        border-color: #0a58ca;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(13,110,253,0.3);
    }
    
    .btn-outline-primary:hover {
        transform: translateY(-2px);
    }
    
    @media (max-width: 768px) {
        .display-2 {
            font-size: 3rem;
        }
        
        .features-list {
            flex-direction: column;
            align-items: center;
        }
        
        .feature-badge {
            width: fit-content;
        }
        
        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }
    }
</style>
@endsection