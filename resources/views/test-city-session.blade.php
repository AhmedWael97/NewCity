<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test City Modal - Session Check</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3>City Modal Session Test</h3>
        </div>
        <div class="card-body">
            <h5>Current Session Status:</h5>
            <ul class="list-group mb-4">
                <li class="list-group-item">
                    <strong>Selected City:</strong> 
                    {{ session('selected_city') ?? 'Not Set' }}
                </li>
                <li class="list-group-item">
                    <strong>Selected City Name:</strong> 
                    {{ session('selected_city_name') ?? 'Not Set' }}
                </li>
                <li class="list-group-item">
                    <strong>Selected City ID:</strong> 
                    {{ session('selected_city_id') ?? 'Not Set' }}
                </li>
                <li class="list-group-item">
                    <strong>Has Selected City?:</strong> 
                    <span class="badge bg-{{ session('selected_city') ? 'success' : 'warning' }}">
                        {{ session('selected_city') ? 'YES' : 'NO' }}
                    </span>
                </li>
            </ul>

            <h5>Expected Modal Behavior:</h5>
            <div class="alert alert-info">
                @if(session('selected_city'))
                    <i class="fas fa-check-circle"></i>
                    <strong>Modal should NOT appear automatically</strong> (city already selected)
                    <br>But you can still open it manually with the button below.
                @else
                    <i class="fas fa-info-circle"></i>
                    <strong>Modal SHOULD appear automatically</strong> (no city selected yet)
                @endif
            </div>

            <h5>Actions:</h5>
            <div class="btn-group d-flex gap-2 flex-wrap">
                <a href="{{ route('home') }}" class="btn btn-primary">
                    <i class="fas fa-home"></i> Go to Home
                </a>
                <button onclick="showCityModal()" class="btn btn-success">
                    <i class="fas fa-map-marker-alt"></i> Show Modal Manually
                </button>
                <a href="{{ url('/clear-city-session') }}" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Clear Session
                </a>
                <button onclick="clearLocalStorage()" class="btn btn-warning">
                    <i class="fas fa-eraser"></i> Clear LocalStorage
                </button>
            </div>

            <hr class="my-4">

            <h5>Debug Info:</h5>
            <pre class="bg-light p-3 rounded">{{ print_r(session()->all(), true) }}</pre>
        </div>
    </div>
</div>

<script>
function clearLocalStorage() {
    localStorage.clear();
    alert('LocalStorage cleared! Refresh the page to see the modal.');
}
</script>

</body>
</html>
