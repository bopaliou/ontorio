<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Paiement Enregistré</title>
</head>
<body>
    <script>
        // Notifier le parent que le paiement a réussi
        if (window.parent && window.parent.loySection) {
            window.parent.loySection.onStoreSuccess('{{ $message }}');
        }
        if (window.parent && window.parent.paiSection) {
            window.parent.paiSection.onStoreSuccess('{{ $message }}');
        }
    </script>
</body>
</html>
