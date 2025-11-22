<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ShayanYS\LaraReserve\Interfaces\ReservableInterface;
use ShayanYS\LaraReserve\Traits\Reservable;

class tabela_ucilnice extends Model implements ReservableInterface
{
    use HasFactory,Reservable;
    
    protected $table = 'tabela_ucilnice';
    
    protected $fillable = [
        'id_ucilnice',
        'kapaciteta', 
        'vrsta_ucilnice', 
        'skrbnik'
    ];
    
    // Definiramo relacijo z rezervacijami
    public function rezervacije()
    {
        return $this->morphMany(\ShayanYS\LaraReserve\Models\Reserve::class, 'reservable');
    }
}