@props(['messages'])

@if ($messages)
    <div class="error-messages">
        @foreach ((array) $messages as $message)
            <div>{{ $message }}</div>
        @endforeach
    </div>
@endif
