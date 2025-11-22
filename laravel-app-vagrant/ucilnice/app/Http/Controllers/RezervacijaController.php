<?php

namespace App\Http\Controllers;

use App\Models\tabela_ucilnice;
use App\Http\Requests\StoreRezervacijaUcilniceRequest;
use Illuminate\Http\Request;
use ShayanYS\LaraReserve\Models\Reserve;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RezervacijaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Izpisi seznam rezervacij.
     */
    public function index()
    {
        $rezervacije = auth()->user()->reserves()
            ->with('reservable')
            ->orderBy('reserved_date', 'desc')
            ->paginate(10);
            
        return view('rezervacije.index', compact('rezervacije'));
    }

    /**
     * Ustvari novo rezervacijo.
     */
    public function create()
    {
        $ucilnice = tabela_ucilnice::all();
        return view('rezervacije.create', compact('ucilnice'));
    }

    /**
     * Shrani novo rezervacijo.
     */
    public function store(StoreRezervacijaUcilniceRequest $request)
    {
        $ucilnica = tabela_ucilnice::findOrFail($request->ucilnica_id);
        
        // Preverimo razpoložljivost
        $startDate = Carbon::parse($request->datum_od);
        $endDate = Carbon::parse($request->datum_do);
        
        // Preverimo če obstajajo prekrivanja
        $conflicts = Reserve::where('reservable_type', get_class($ucilnica))
            ->where('reservable_id', $ucilnica->id)
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('reserved_date', [$startDate, $endDate])
                    ->orWhereBetween('end_reserve_date', [$startDate, $endDate])
                    ->orWhere(function($q) use ($startDate, $endDate) {
                        $q->where('reserved_date', '<=', $startDate)
                          ->where('end_reserve_date', '>=', $endDate);
                    });
            })
            ->exists();
        
        if ($conflicts) {
            return back()->withErrors(['datum_od' => 'Učilnica je v tem terminu že zasedena.'])
                        ->withInput();
        }
        
        // Ustvarimo rezervacijo
        $rezervacija = new Reserve();
        $rezervacija->customer_id = auth()->id();
        $rezervacija->customer_type = get_class(auth()->user());
        $rezervacija->reservable_id = $ucilnica->id;
        $rezervacija->reservable_type = get_class($ucilnica);
        $rezervacija->reserved_date = $startDate;
        $rezervacija->end_reserve_date = $endDate;
        $rezervacija->notes = $request->namen;
        $rezervacija->description = $request->opombe;
        $rezervacija->save();
        
        if ($rezervacija) {
            return redirect()->route('rezervacije.index')
                ->with('success', 'Rezervacija je bila uspešno ustvarjena.');
        }
        
        return back()->withErrors(['error' => 'Pri ustvarjanju rezervacije je prišlo do napake.'])
                    ->withInput();
    }

    /**
     * Prikazi rezervacije.
     */
    public function show($id)
    {
        $rezervacija = Reserve::findOrFail($id);
        
        // Preverimo če je uporabnik lastnik rezervacije
        if ($rezervacija->customer_id !== auth()->id()) {
            abort(403, 'Nimate dovoljenja za ogled te rezervacije.');
        }
        
        return view('rezervacije.show', compact('rezervacija'));
    }

    /**
     * Posodabljanje rezrevacij.
     */
    public function edit($id)
    {
        $rezervacija = Reserve::findOrFail($id);
        
        // Preverimo če je uporabnik lastnik rezervacije
        if ($rezervacija->customer_id !== auth()->id()) {
            abort(403, 'Nimate dovoljenja za urejanje te rezervacije.');
        }
        
        $ucilnice = tabela_ucilnice::all();
        
        return view('rezervacije.edit', compact('rezervacija', 'ucilnice'));
    }

    /**
     * Shranimo posodobljene spremembe.
     */
    public function update(Request $request, $id)
    {
        $rezervacija = Reserve::findOrFail($id);
        
        // Preverimo če je uporabnik lastnik rezervacije
        if ($rezervacija->customer_id !== auth()->id()) {
            abort(403, 'Nimate dovoljenja za urejanje te rezervacije.');
        }
        
        $validated = $request->validate([
            'datum_od' => 'required|date|after_or_equal:today',
            'datum_do' => 'required|date|after:datum_od',
            'namen' => 'required|string|max:255',
            'opombe' => 'nullable|string|max:500',
        ]);
        
        $startDate = Carbon::parse($request->datum_od);
        $endDate = Carbon::parse($request->datum_do);
        
        // Preverimo razpoložljivost (izključimo trenutno rezervacijo)
        $ucilnica = $rezervacija->reservable;
        
        // Preverimo če učilnica ni zasedena v tem terminu (razen trenutne rezervacije)
        $conflictingReservations = Reserve::where('reservable_type', get_class($ucilnica))
            ->where('reservable_id', $ucilnica->id)
            ->where('id', '!=', $rezervacija->id)
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('reserved_date', [$startDate, $endDate])
                    ->orWhereBetween('end_reserve_date', [$startDate, $endDate])
                    ->orWhere(function($q) use ($startDate, $endDate) {
                        $q->where('reserved_date', '<=', $startDate)
                          ->where('end_reserve_date', '>=', $endDate);
                    });
            })
            ->exists();
        
        if ($conflictingReservations) {
            return back()->withErrors(['datum_od' => 'Učilnica je v tem terminu že zasedena.'])
                        ->withInput();
        }
        
        $rezervacija->update([
            'reserved_date' => $startDate,
            'end_reserve_date' => $endDate,
            'notes' => $request->namen,
            'description' => $request->opombe,
        ]);
        
        return redirect()->route('rezervacije.index')
            ->with('success', 'Rezervacija je bila uspešno posodobljena.');
    }

    /**
     * Odstrani rzervacijo.
     */
    public function destroy($id)
    {
        $rezervacija = Reserve::findOrFail($id);
        
        // Preverimo če je uporabnik lastnik rezervacije
        if ($rezervacija->customer_id !== auth()->id()) {
            abort(403, 'Nimate dovoljenja za brisanje te rezervacije.');
        }
        
        // Ne dovolimo brisanja preteklih rezervacij
        if ($rezervacija->reserved_date < now()) {
            return redirect()->route('rezervacije.index')
                ->with('error', 'Preteklih rezervacij ni mogoče izbrisati.');
        }
        
        $rezervacija->delete();
        
        return redirect()->route('rezervacije.index')
            ->with('success', 'Rezervacija je bila uspešno preklicana.');
    }
    
    /**
     * Preverimo za AJAX requests
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'ucilnica_id' => 'required|exists:tabela_ucilnice,id',
            'datum_od' => 'required|date',
            'datum_do' => 'required|date|after:datum_od',
        ]);
        
        $ucilnica = tabela_ucilnice::find($request->ucilnica_id);
        $startDate = Carbon::parse($request->datum_od);
        $endDate = Carbon::parse($request->datum_do);
        
        // Preverimo prekrivanja
        $query = Reserve::where('reservable_type', get_class($ucilnica))
            ->where('reservable_id', $ucilnica->id)
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('reserved_date', [$startDate, $endDate])
                    ->orWhereBetween('end_reserve_date', [$startDate, $endDate])
                    ->orWhere(function($subQ) use ($startDate, $endDate) {
                        $subQ->where('reserved_date', '<=', $startDate)
                             ->where('end_reserve_date', '>=', $endDate);
                    });
            });
        
        // Če imamo exclude_id (za edit), ga izključimo
        if ($request->has('exclude_id')) {
            $query->where('id', '!=', $request->exclude_id);
        }
        
        $isAvailable = !$query->exists();
        
        return response()->json([
            'available' => $isAvailable,
            'message' => $isAvailable ? 'Učilnica je prosta.' : 'Učilnica je v tem terminu zasedena.'
        ]);
    }
}