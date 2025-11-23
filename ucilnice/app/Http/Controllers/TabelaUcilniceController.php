<?php

namespace App\Http\Controllers;

use App\Models\tabela_ucilnice;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRezervacijaUcilniceRequest;
use ShayanYS\LaraReserve\Models\Reserve;
use Carbon\Carbon;

class TabelaUcilniceController extends Controller
{
    /**
     * Prikaz ucilnic.
     */
    public function index()
    {
        $ucilnice = tabela_ucilnice::all();
        return view('ucilnice.index', compact('ucilnice'));
    }

    /**
     * Stvarimo novo ucilnico.
     */
    public function create()
    {
        return view('ucilnice.create');
    }

    /**
     * Shrnanimo novo ucilnico.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_ucilnice' => 'required|string|max:50|unique:tabela_ucilnice',
            'kapaciteta' => 'required|integer|min:1|max:500',
            'vrsta_ucilnice' => 'required|string|max:50',
            'skrbnik' => 'nullable|string|max:50',
        ]);
        
        $ucilnica = tabela_ucilnice::create($validated);
        
        return redirect()->route('ucilnice.index')
            ->with('success', 'Učilnica je bila uspešno dodana.');
    }

    /**
     * Prikazi rezervacje ucilnic
     */
     public function show(tabela_ucilnice $ucilnica)
    {
        // Pridobimo rezervacije za učilnico
        $rezervacije = Reserve::where('reservable_type', get_class($ucilnica))
            ->where('reservable_id', $ucilnica->id)
            ->orderBy('reserved_date', 'desc')
            ->get();
        error_log($ucilnica);
        return view('ucilnice.show', compact('ucilnica', 'rezervacije'));
    }
    /*
    public function show($id)
    {
        //Prejsnji nacin, ki je bil uporabljen
        // Pridobimo rezervacije za učilnico
        $ucilnica = tabela_ucilnice::findOrFail($id);
        $rezervacije = Reserve::where('reservable_type', get_class($ucilnica))
            ->where('reservable_id', $ucilnica->id)
            ->orderBy('reserved_date', 'desc')
            ->get();
        error_log($ucilnica);
        return view('ucilnice.show', compact('ucilnica', 'rezervacije'));
    }
    */

    /**
     * Posodabljanja info o ucilnicah.
     */
    public function edit(tabela_ucilnice $ucilnica)
    {
        return view('ucilnice.edit', compact('ucilnica'));
    }

    /**
     * Shrani posodabljanja info o ucilnicah.
     */
    public function update(Request $request, tabela_ucilnice $ucilnica)
    {
        $validated = $request->validate([
            'id_ucilnice' => 'required|string|max:50|unique:tabela_ucilnice,id_ucilnice,' . $ucilnica->id,
            'kapaciteta' => 'required|integer|min:1|max:500',
            'vrsta_ucilnice' => 'required|string|max:50',
            'skrbnik' => 'nullable|string|max:50',
        ]);
        
        $ucilnica->update($validated);
        
        return redirect()->route('ucilnice.index')
            ->with('success', 'Učilnica je bila uspešno posodobljena.');
    }

    /**
     * Akcija izbiris.
     */
    public function destroy(tabela_ucilnice $ucilnica)
    {
        // Preverimo če ima učilnica prihodnje rezervacije
        $prihodnjeRezervacije = Reserve::where('reservable_type', get_class($ucilnica))
            ->where('reservable_id', $ucilnica->id)
            ->where('reserved_date', '>=', now())
            ->exists();
            
        if ($prihodnjeRezervacije) {
            return redirect()->route('ucilnice.index')
                ->with('error', 'Učilnice ni mogoče izbrisati, ker ima prihodnje rezervacije.');
        }
        
        $ucilnica->delete();
        
        return redirect()->route('ucilnice.index')
            ->with('success', 'Učilnica je bila uspešno izbrisana.');
    }
    
    /**
     * Koledar zasedenosti učilnice.
     */
    public function calendar(tabela_ucilnice $ucilnica)
    {
        return view('ucilnice.calendar', compact('ucilnica'));
    }
    
    /**
     * Pridobi rezervacijo ucilnice (AJAX)
     */
    public function getReservations(tabela_ucilnice $ucilnica)
    {
        $rezervacije = Reserve::where('reservable_type', get_class($ucilnica))
            ->where('reservable_id', $ucilnica->id)
            ->get()
            ->map(function ($rezervacija) {
                return [
                    'id' => $rezervacija->id,
                    'title' => $rezervacija->customer->name . ' - ' . ($rezervacija->notes ?? 'Rezervacija'),
                    'start' => $rezervacija->reserved_date,
                    'end' => $rezervacija->end_reserve_date,
                    'color' => '#3788d8',
                ];
            });
            
        return response()->json($rezervacije);
    }
}