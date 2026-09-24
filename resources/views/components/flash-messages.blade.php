@php
    $flashes = [];

    if (Session::has('success')) {
        $flashes[] = ['type' => 'success', 'message' => Session::get('success'), 'duration' => 3000];
    }
    if (Session::has('error')) {
        $flashes[] = ['type' => 'error', 'message' => Session::get('error'), 'duration' => 0];
    }
    if (Session::has('warning')) {
        $flashes[] = ['type' => 'warning', 'message' => Session::get('warning'), 'duration' => 7000];
    }
    if (Session::has('info')) {
        $flashes[] = ['type' => 'info', 'message' => Session::get('info'), 'duration' => 5000];
    }
    if (Session::has('alert.toast')) {
        $toast = Session::get('alert.toast');
        $flashes[] = [
            'type' => $toast['type'] ?? 'info',
            'message' => $toast['message'] ?? '',
            'title' => $toast['title'] ?? '',
            'duration' => $toast['duration'] ?? 5000
        ];
    }
@endphp

@if(count($flashes) > 0)
    <div id="flash-messages" hidden>
        @foreach($flashes as $flash)
            <div data-type="{{ $flash['type'] }}" 
                 data-duration="{{ $flash['duration'] }}"
                 @if(isset($flash['title'])) data-title="{{ $flash['title'] }}" @endif>
                {{ $flash['message'] }}
            </div>
        @endforeach
    </div>
@endif