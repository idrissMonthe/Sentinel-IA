<section class="empty-state {{ $class ?? '' }}" aria-labelledby="{{ $id ?? 'empty-state-title' }}">
    <div class="empty-state-icon" aria-hidden="true">{{ $icon ?? '⌁' }}</div>
    <h2 id="{{ $id ?? 'empty-state-title' }}">{{ $title }}</h2>
    <p>{{ $message }}</p>
    @isset($actionUrl)
        <a href="{{ $actionUrl }}" class="btn {{ $actionClass ?? 'btn-primary' }}">{{ $actionLabel }}</a>
    @endisset
</section>
