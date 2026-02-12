@php
    $agency = \App\Models\Proprietaire::where('nom', 'LIKE', '%Ontario%')->first();
@endphp
<div class="header">
    <div class="header-content">
        <table style="width: 100%;">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <div class="agency-name">ONTARIO GROUP S.A.</div>
                    <div class="agency-info">
                        {{ $agency->adresse ?? 'Dakar, Sénégal' }}<br>
                        Tél : {{ $agency->telephone ?? '+221 33 000 00 00' }}<br>
                        Email : {{ $agency->email ?? 'contact@ontariogroup.net' }}
                    </div>
                </td>
                <td style="width: 40%; text-align: right; vertical-align: top;">
                    <div class="doc-title-section">
                        <div class="doc-title">{{ $title ?? 'DOCUMENT OFFICIEL' }}</div>
                        <div class="doc-ref">
                            @if(isset($ref)) Réf : {{ $ref }} @endif
                            @if(isset($date)) | Date : {{ $date }} @endif
                        </div>
                        <div class="generation-date" style="font-size: 8px; color: #94a3b8; margin-top: 5px;">
                            Généré le {{ date('d/m/Y à H:i') }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>
