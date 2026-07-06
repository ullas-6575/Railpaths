@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' => 'form-control form-control-rail' . ($errors->has($attributes->get('name')) ? ' is-invalid' : '')
]) !!}>