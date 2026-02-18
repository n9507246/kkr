@props([
    'route' => null,
    'active' => false,
    'icon' => null,
    'disabled' => false
])

<li class="nav-item" role="presentation">
    @if($disabled)
        <a class="nav-link disabled" aria-disabled="true">
            @if($icon) <i class="bi {{ $icon }}"></i> @endif
            {{ $slot }}
        </a>
    @else
        <a href="{{ $route ?? '#' }}" class="nav-link {{ $active ? 'active' : '' }}">
            @if($icon) <i class="bi {{ $icon }}"></i> @endif
            {{ $slot }}
        </a>
    @endif
</li>
