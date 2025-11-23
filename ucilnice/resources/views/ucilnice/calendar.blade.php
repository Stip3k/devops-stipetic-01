@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Koledar rezervacij - {{ $ucilnica->id_ucilnice }}</h4>
                    <div>
                        <a href="{{ route('rezervacije.create') }}?ucilnica_id={{ $ucilnica->id }}" class="btn btn-primary">Nova rezervacija</a>
                        <a href="{{ route('ucilnice.show', $ucilnica->id) }}" class="btn btn-info">Podrobnosti</a>
                        <a href="{{ route('ucilnice.index') }}" class="btn btn-secondary">Nazaj</a>
                    </div>
                </div>

                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/sl.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'sl',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: '{{ route("ucilnice.rezervacije", $ucilnica->id) }}',
        eventColor: '#3788d8',
        eventClick: function(info) {
            if (confirm('Ali želite pregledati to rezervacijo?')) {
                window.location.href = '/rezervacije/' + info.event.id;
            }
        },
        height: 'auto'
    });
    
    calendar.render();
});
</script>
@endpush
@endsection