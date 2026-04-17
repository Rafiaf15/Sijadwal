@extends('layouts.app')

@section('title', 'Kalender')

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.css">
    <style>
        .fc .fc-button-primary {
            background: #b51234;
            border-color: #b51234;
        }

        .fc .fc-button-primary:hover,
        .fc .fc-button-primary:focus {
            background: #8f0d2a;
            border-color: #8f0d2a;
        }

        .fc .fc-toolbar-title {
            color: #8f0d2a;
        }

        .fc .fc-timegrid-slot-label,
        .fc .fc-col-header-cell-cushion {
            color: #52525b;
        }

        .fc .fc-event {
            background: #b51234;
            border-color: #8f0d2a;
        }
    </style>
@endpush

@section('content')
    <div class="p-8 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Kalender</h1>
                <p class="text-sm text-zinc-500 mt-1">Visualisasi jadwal perkuliahan hasil optimisasi otomatis</p>
            </div>
        </div>

        <div class="bg-white border border-zinc-200 rounded-lg p-6 shadow-sm">
            <div id="calendar"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            if (!calendarEl) return;

            const events = @json($events);

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'timeGridWeek,timeGridDay,dayGridMonth'
                },
                events: events
            });

            calendar.render();
        });
    </script>
@endpush
