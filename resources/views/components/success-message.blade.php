{{-- Mensaje de resultado. --}}
@if (session('success'))
    <div {{ $attributes->class(['success-message']) }}>
        {{ session('success') }}
    </div>
@endif
