<div class="footer" style="position: absolute; bottom: 0; left: 0; right: 0; border-top: 1px solid #e2e8f0; padding-top: 10px; text-align: center; font-size: 7.5px; color: #94a3b8; line-height: 1.4;">
    <p style="margin-bottom: 3px; color: #64748b;">
        <strong style="color: #1a2e3d;">ONTARIO GROUP S.A.</strong> &nbsp;|&nbsp; 
        Siège Social : Liberté 6 Extension, Dakar, Sénégal &nbsp;|&nbsp; 
        NINEA : 009876543 &nbsp;|&nbsp; 
        RCCM : SN.DKR.2023.B.12345
    </p>
    <p style="font-style: italic; font-size: 7px;">
        Document sécurisé généré électroniquement. Valable sans signature manuscrite sauf mention contraire.
    </p>
</div>

<!-- DomPDF Script for automatic page numbering across all documents -->
<script type="text/php">
    if (isset($pdf)) {
        $font = $fontMetrics->getFont("Helvetica", "normal");
        $size = 7;
        $pageText = "{PAGE_NUM} / {PAGE_COUNT}";
        $y = $pdf->get_height() - 20;
        $x = $pdf->get_width() - 40 - $fontMetrics->getTextWidth($pageText, $font, $size);
        $pdf->text($x, $y, $pageText, $font, $size, array(0.58, 0.64, 0.72));
    }
</script>
