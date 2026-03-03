@php
    $agency = \App\Models\Proprietaire::where('nom', 'LIKE', '%Ontario%')->first();
    $logoPath = public_path('images/ontorio-logo.png');
    $logoData = '';
    if (file_exists($logoPath)) {
        $logoData = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }
@endphp

<div class="header" style="margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <!-- LOGO: Left -->
            <td style="width: 20%; vertical-align: middle;">
                @if($logoData)
                    <img src="{{ $logoData }}" style="max-height: 48px; width: auto;" alt="Logo Ontario Group">
                @else
                    <div style="font-size: 16px; font-weight: 900; color: #cb2d2d; letter-spacing: 1px; text-transform: uppercase;">ONTARIO GROUP</div>
                @endif
            </td>
            
            <!-- AGENCY DETAILS: Center-Left -->
            <td style="width: 40%; vertical-align: middle; padding-left: 10px;">
                <div style="font-size: 14px; font-weight: 900; color: #1a2e3d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px;">ONTARIO GROUP S.A.</div>
                <div style="font-size: 8px; font-weight: 400; color: #64748b; line-height: 1.4;">
                    {{ $agency->adresse ?? 'Dakar, Plateau, Sénégal' }}<br>
                    Tél : {{ $agency->telephone ?? '+221 33 822 32 67' }} | Email : {{ $agency->email ?? 'contact@ontariogroup.net' }}
                </div>
            </td>
            
            <!-- DOCUMENT METADATA: Right -->
            <td style="width: 40%; text-align: right; vertical-align: middle;">
                <div style="font-size: 12px; font-weight: 900; color: #cb2d2d; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 6px;">
                    {{ $title ?? 'DOCUMENT OFFICIEL' }}
                </div>
                
                <table style="width: 100%; border-collapse: collapse;">
                    @if(isset($ref))
                    <tr>
                        <td style="text-align: right; font-size: 8px; color: #94a3b8; font-weight: 600; text-transform: uppercase; padding-bottom: 2px;">RÉFÉRENCE :</td>
                        <td style="text-align: right; font-size: 8px; color: #1a2e3d; font-weight: 800; width: 60px; padding-bottom: 2px;">{{ $ref }}</td>
                    </tr>
                    @endif
                    @if(isset($date))
                    <tr>
                        <td style="text-align: right; font-size: 8px; color: #94a3b8; font-weight: 600; text-transform: uppercase;">DATE :</td>
                        <td style="text-align: right; font-size: 8px; color: #1a2e3d; font-weight: 800;">{{ $date }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>
</div>
