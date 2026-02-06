<!DOCTYPE html>
<html>

<head>
    <title>Test Suppression</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <h1>Test de Suppression</h1>

    <h2>Journaux disponibles:</h2>
    @foreach (\App\Models\Journal::all() as $j)
        <div style="margin: 10px; padding: 10px; border: 1px solid #ccc;">
            <strong>ID: {{ $j->id }}</strong> - {{ $j->designation }}
            ({{ $j->operations()->count() }} opérations)
            <form action="{{ route('journals.destroy', $j) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Supprimer?')">Supprimer Journal</button>
            </form>
        </div>
    @endforeach

    <script>
        console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]').content);
    </script>
</body>

</html>
