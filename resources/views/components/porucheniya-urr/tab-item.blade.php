{{-- resources/views/components/porucheniya-urr/tab-item.blade.php --}}
@props([
    'active' => false, 
    'url' => '#',  // переименовали route в url
    'icon' => null,
    'disabled' => false
])

@php
    $href = $url;
    $isDisabled = $disabled || $active;
@endphp

<li class="nav-item">
    @if($isDisabled)
        <span class="nav-link {{ $active ? 'active' : '' }} {{ $disabled ? 'disabled' : '' }}" 
              @if($disabled) aria-disabled="true" @endif
              @if($active) aria-current="page" @endif>
            @if($icon)
                <i class="bi {{ $icon }} me-1"></i>
            @endif
            {{ $slot }}
        </span>
    @else
        <a class="nav-link" href="{{ $href }}">
            @if($icon)
                <i class="bi {{ $icon }} me-1"></i>
            @endif
            {{ $slot }}
        </a>
    @endif
</li>