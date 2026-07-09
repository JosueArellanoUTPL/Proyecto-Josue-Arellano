@if ($errors->any())
    <div {{ $attributes->class(['form-errors']) }}>
        <div class="form-errors__title">Revisar campos</div>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
