@props(['value'])

<label {{ $attributes->merge(['class' => 'form-label fw-medium text-secondary mb-1']) }}>
    {{ $value ?? $slot }}
</label>