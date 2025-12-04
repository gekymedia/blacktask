@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div id="calendar" class="bg-white rounded-lg shadow overflow-hidden"></div>
</div>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: '/api/tasks',
        eventClick: function(info) {
            window.location.href = `/tasks/${info.event.id}/edit`;
        }
    });
    calendar.render();
});
</script>
@endsection