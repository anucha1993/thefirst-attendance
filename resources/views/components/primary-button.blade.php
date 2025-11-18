<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-login']) }}>
    {{ $slot }}
</button>
