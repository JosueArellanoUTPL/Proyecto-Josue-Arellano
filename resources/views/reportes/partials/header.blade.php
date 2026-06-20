<div class="report-header">
    <div>
        {{-- Encabezado del reporte. --}}
        <div class="title">{{ $title }}</div>
        <div class="muted" style="margin-top:6px;">{{ $subtitle }}</div>
        <div class="muted" style="margin-top:6px;">
            Generado el {{ now()->format('d/m/Y H:i') }} por {{ auth()->user()->name }}.
        </div>
    </div>

    <div class="report-actions no-print">
        <button type="button" class="btn" onclick="window.print()">Imprimir / PDF</button>
        <a href="{{ route('reportes.index') }}" class="btn">Volver</a>
    </div>
</div>
