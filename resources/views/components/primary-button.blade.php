<button {{ $attributes->merge([
    'type' => 'submit', 
    'class' => 'btn btn-rail-blue w-100 py-3 fw-semibold fs-5 rounded-3'
]) }}>
    {{ $slot }}
</button>
