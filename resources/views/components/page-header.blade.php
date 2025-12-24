@props(['title', 'backUrl' => null, 'action' => null])

<header class="page-header">
    @if($backUrl)
        <a href="{{ $backUrl }}" class="back-btn">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
    @endif

    <h1>{{ $title }}</h1>

    @if($action)
        {{ $action }}
    @endif
</header>