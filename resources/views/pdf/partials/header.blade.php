@php
    $agency = \App\Models\Proprietaire::where('nom', 'LIKE', '%Ontario%')->first();
    // Encodage base64 pour garantir l'affichage dans DomPDF
    $logoPath = public_path('images/ontorio-logo.png');
    $logoData = '';
    if (file_exists($logoPath)) {
        $logoData = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }
@endphp
<div class="header" style="margin-bottom: 25px; border-bottom: 2px solid #cb2d2d; padding-bottom: 15px;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 15%; vertical-align: middle;">
                @if($logoData)
                    <img src="{{ $logoData }}" style="max-height: 55px; width: auto;" alt="Logo">
                @endif
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 15px;">
                <div style="font-size: 18px; font-weight: 900; color: #1a2e3d; text-transform: uppercase;">ONTARIO GROUP S.A.</div>
                <div style="font-size: 9px; color: #64748b; line-height: 1.4; margin-top: 4px;">
                    {{ $agency->adresse ?? 'Dakar, Plateau, Sénégal' }}<br>
                    Tél : {{ $agency->telephone ?? '+221 33 822 32 67' }}<br>
                    Email : {{ $agency->email ?? 'commercial@ontariogroup.net' }}
                </div>
            </td>
            <td style="width: 35%; text-align: right; vertical-align: top;">
                <div style="display: inline-block; background-color: #1a2e3d; color: #fff; padding: 6px 15px; border-radius: 4px; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">
                    {{ $title ?? 'DOCUMENT OFFICIEL' }}
                </div>
                <div style="font-size: 8px; color: #94a3b8; font-weight: bold; text-transform: uppercase;">
                    @if(isset($ref)) REF: {{ $ref }} @endif
                </div>
                <div style="font-size: 8px; color: #94a3b8; font-weight: bold;">
                    @if(isset($date)) DATE: {{ $date }} @endif
                </div>
                <div style="font-size: 7px; color: #cbd5e1; margin-top: 3px;">
                    Généré le {{ date('d/m/Y H:i') }}
                </div>
            </td>
        </tr>
    </table>
</div>
