<div class="report-header">
    <div>
        {{-- Encabezado reutilizable para todos los reportes. --}}
        <div class="title">{{ $title }}</div>
        <div class="muted" style="margin-top:6px;">{{ $subtitle }}</div>
        <div class="muted" style="margin-top:6px;">
            Generado el {{ now()->format('d/m/Y H:i') }} por {{ auth()->user()->name }}.
        </div>
    </div>

    <div class="report-actions no-print">
        {{-- Este boton usa el navegador para imprimir o guardar como PDF. --}}
        <button type="button" class="btn" onclick="window.print()">Imprimir / PDF</button>
        <a href="{{ route('reportes.index') }}" class="btn">Volver</a>
    </div>
</div>
