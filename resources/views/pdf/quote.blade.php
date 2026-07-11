<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cotización {{ $quote->quote_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 13px;
            color: #1e293b;
            background: #ffffff;
            line-height: 1.6;
        }

        .page { padding: 36px 44px 40px; }

        /* ─── HEADER ──────────────────────────────────────── */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 28px;
            padding-bottom: 20px;
            border-bottom: 3px solid #4f46e5;
        }

        .header-left  { display: table-cell; vertical-align: middle; width: 55%; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; }

        .brand-name {
            font-size: 24px;
            font-weight: 700;
            color: #4f46e5;
            letter-spacing: -0.5px;
        }

        .brand-sub {
            font-size: 12px;
            color: #64748b;
            margin-top: 3px;
        }

        .doc-number {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
        }

        .doc-date {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
        }

        .badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 6px;
        }

        .badge-draft    { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .badge-sent     { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .badge-approved { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-rejected { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

        /* ─── PARTIES ─────────────────────────────────────── */
        .parties {
            display: table;
            width: 100%;
            margin-bottom: 24px;
        }

        .party-from { display: table-cell; width: 48%; vertical-align: top; padding-right: 16px; }
        .party-to   { display: table-cell; width: 52%; vertical-align: top; padding-left: 16px;
                      border-left: 2px solid #e2e8f0; }

        .party-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #94a3b8;
            margin-bottom: 8px;
        }

        .party-name {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .party-rut {
            font-size: 12px;
            color: #475569;
            font-family: 'Courier New', monospace;
            margin-bottom: 2px;
        }

        .party-detail {
            font-size: 12px;
            color: #64748b;
            margin-top: 1px;
        }

        /* ─── META ROW ────────────────────────────────────── */
        .meta-row {
            display: table;
            width: 100%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin-bottom: 24px;
        }

        .meta-cell {
            display: table-cell;
            padding: 11px 16px;
            border-right: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .meta-cell:last-child { border-right: none; }

        .meta-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #94a3b8;
            margin-bottom: 3px;
        }

        .meta-value {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
        }

        /* ─── TÍTULO ──────────────────────────────────────── */
        .quote-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 18px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        /* ─── TABLA ÍTEMS ─────────────────────────────────── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
            font-size: 13px;
        }

        .items-table thead tr {
            background: #4f46e5;
        }

        .items-table thead th {
            padding: 10px 14px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #fff;
        }

        .items-table thead th.right  { text-align: right; }
        .items-table thead th.center { text-align: center; }

        .items-table tbody tr { border-bottom: 1px solid #f1f5f9; }
        .items-table tbody tr:nth-child(even) { background: #f8fafc; }

        .items-table tbody td {
            padding: 11px 14px;
            vertical-align: top;
        }

        .item-num {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 600;
        }

        .item-description {
            font-size: 13px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 3px;
        }

        .item-detail {
            font-size: 11px;
            color: #64748b;
            line-height: 1.5;
        }

        .text-right  { text-align: right; }
        .text-center { text-align: center; }
        .font-mono   { font-family: 'Courier New', monospace; }

        /* ─── TOTALES ─────────────────────────────────────── */
        .totals-section {
            display: table;
            width: 100%;
            margin-top: 0;
        }

        .totals-notes { display: table-cell; vertical-align: top; width: 55%; padding-right: 20px; padding-top: 16px; }
        .totals-box-wrap { display: table-cell; vertical-align: top; width: 45%; }

        .totals-box {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
            margin-top: 0;
        }

        .totals-row {
            display: table;
            width: 100%;
            border-bottom: 1px solid #f1f5f9;
        }

        .totals-row:last-child { border-bottom: none; }

        .totals-row-label { display: table-cell; padding: 9px 14px; color: #475569; font-size: 12px; }
        .totals-row-value { display: table-cell; padding: 9px 14px; text-align: right;
                            font-weight: 700; font-size: 13px; font-family: 'Courier New', monospace; }

        .totals-row.total-final { background: #4f46e5; }
        .totals-row.total-final .totals-row-label { color: #fff; font-size: 14px; font-weight: 700; }
        .totals-row.total-final .totals-row-value { color: #fff; font-size: 15px; }

        /* ─── NOTAS / TÉRMINOS ────────────────────────────── */
        .section-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            margin-bottom: 6px;
        }

        .section-body {
            font-size: 12px;
            color: #475569;
            line-height: 1.7;
        }

        .separator {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 20px 0;
        }

        /* ─── FOOTER ──────────────────────────────────────── */
        .footer {
            margin-top: 28px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            display: table;
            width: 100%;
        }

        .footer-left  { display: table-cell; font-size: 10px; color: #94a3b8; }
        .footer-right { display: table-cell; text-align: right; font-size: 10px; color: #94a3b8; }

        .validity-warning {
            font-size: 11px;
            color: #d97706;
            font-weight: 700;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- ─── HEADER ───────────────────────────────────────────────── --}}
    <div class="header">
        <div class="header-left">
            <div class="brand-name">{{ config('app.name') }}</div>
            <div class="brand-sub">Desarrollo de Software &amp; Consultoría Tecnológica</div>
        </div>
        <div class="header-right">
            <div class="doc-number">{{ $quote->quote_number }}</div>
            <div class="doc-date">
                {{ $quote->created_at->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
            </div>
            <div>
                <span class="badge badge-{{ $quote->status }}">{{ $quote->status_name }}</span>
            </div>
        </div>
    </div>

    {{-- ─── PARTES ────────────────────────────────────────────────── --}}
    <div class="parties">
        <div class="party-from">
            <div class="party-label">Emitida por</div>
            <div class="party-name">{{ $quote->creator->name }}</div>
            <div class="party-detail">{{ $quote->creator->email }}</div>
        </div>
        <div class="party-to">
            <div class="party-label">Cliente</div>
            <div class="party-name">{{ $quote->client->name }}</div>
            <div class="party-rut">RUT: {{ $quote->client->rut }}</div>
            @if($quote->client->contact_person)
                <div class="party-detail">Attn: {{ $quote->client->contact_person }}</div>
            @endif
            @if($quote->client->email)
                <div class="party-detail">{{ $quote->client->email }}</div>
            @endif
            @if($quote->client->city)
                <div class="party-detail">
                    {{ $quote->client->city }}{{ $quote->client->region ? ', ' . $quote->client->region : '' }}
                </div>
            @endif
        </div>
    </div>

    {{-- ─── META ──────────────────────────────────────────────────── --}}
    <div class="meta-row">
        <div class="meta-cell">
            <div class="meta-label">Número</div>
            <div class="meta-value">{{ $quote->quote_number }}</div>
        </div>
        <div class="meta-cell">
            <div class="meta-label">Fecha de emisión</div>
            <div class="meta-value">{{ $quote->created_at->format('d/m/Y') }}</div>
        </div>
        @if($quote->valid_until)
        <div class="meta-cell">
            <div class="meta-label">Válida hasta</div>
            <div class="meta-value">{{ $quote->valid_until->format('d/m/Y') }}</div>
        </div>
        @endif
        <div class="meta-cell">
            <div class="meta-label">Moneda</div>
            <div class="meta-value">{{ $quote->currency }}</div>
        </div>
        <div class="meta-cell">
            <div class="meta-label">IVA aplicado</div>
            <div class="meta-value">{{ $quote->tax_rate }}%</div>
        </div>
    </div>

    {{-- ─── TÍTULO ─────────────────────────────────────────────────── --}}
    <div class="quote-title">{{ $quote->title }}</div>

    {{-- ─── TABLA ÍTEMS ────────────────────────────────────────────── --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:4%;">#</th>
                <th style="width:47%;">Descripción</th>
                <th class="center" style="width:9%;">Cant.</th>
                <th class="right"  style="width:20%;">Precio Unitario</th>
                <th class="right"  style="width:20%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quote->items as $index => $item)
            <tr>
                <td class="text-center item-num">{{ $index + 1 }}</td>
                <td>
                    <div class="item-description">{{ $item->description }}</div>
                    @if($item->detail)
                        <div class="item-detail">{{ $item->detail }}</div>
                    @endif
                </td>
                <td class="text-center font-mono" style="font-size:13px;">{{ $item->quantity }}</td>
                <td class="text-right font-mono" style="font-size:13px;">{{ $item->formatted_unit_price }}</td>
                <td class="text-right font-mono" style="font-size:13px; font-weight:700;">{{ $item->formatted_subtotal }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ─── TOTALES + NOTAS ────────────────────────────────────────── --}}
    <div class="totals-section">

        {{-- Notas al costado izquierdo de los totales --}}
        <div class="totals-notes">
            @if($quote->notes)
                <div class="section-label">Notas</div>
                <div class="section-body">{{ $quote->notes }}</div>
            @endif
        </div>

        {{-- Caja de totales --}}
        <div class="totals-box-wrap">
            <div class="totals-box" style="margin-top:16px;">
                <div class="totals-row">
                    <span class="totals-row-label">Subtotal neto</span>
                    <span class="totals-row-value">{{ $quote->formatted_subtotal }}</span>
                </div>
                <div class="totals-row">
                    <span class="totals-row-label">IVA ({{ $quote->tax_rate }}%)</span>
                    <span class="totals-row-value">{{ $quote->formatted_tax_amount }}</span>
                </div>
                <div class="totals-row total-final">
                    <span class="totals-row-label">TOTAL</span>
                    <span class="totals-row-value">{{ $quote->formatted_total }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── TÉRMINOS ────────────────────────────────────────────────── --}}
    <hr class="separator">

    <div class="section-label">Términos y condiciones</div>
    <div class="section-body">
        @if($quote->terms)
            {{ $quote->terms }}
        @else
            Los precios indicados son en pesos chilenos (CLP) y no incluyen IVA salvo indicación contraria.
            Esta cotización tiene una vigencia de 30 días desde la fecha de emisión.
            El inicio del proyecto queda sujeto a la aprobación formal de esta cotización y al pago del anticipo acordado.
            Los plazos de entrega se confirmarán al momento de la aceptación.
        @endif
    </div>

    {{-- ─── FOOTER ─────────────────────────────────────────────────── --}}
    <div class="footer">
        <div class="footer-left">
            Generado el {{ now()->timezone('America/Santiago')->locale('es')->isoFormat('D [de] MMMM [de] YYYY, HH:mm') }} hrs
        </div>
        <div class="footer-right">
            @if($quote->valid_until && $quote->valid_until->isPast())
                <span class="validity-warning">⚠ Esta cotización ha vencido</span>
            @elseif($quote->valid_until)
                Válida hasta el {{ $quote->valid_until->format('d/m/Y') }}
            @endif
        </div>
    </div>

</div>
</body>
</html>
