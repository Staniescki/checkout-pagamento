@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'block w-full rounded-md text-primary-200 bg-tertiary-800 border-[#69727d] shadow-sm focus:border-primary-200 focus:ring-primary-200 sm:text-sm']) !!}>
