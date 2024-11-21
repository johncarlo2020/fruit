<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Location</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            color: black;
        }

        .welcome-page {
            width: 100vw;
            height: 100vh;
            background-image: url('images/Background.webp');
            /* Ensure the path is correct */
            background-size: cover;
            background-position: center;
            position: relative;
            transition: 0.5s;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        h2 {
            color: black;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .btn {
            padding: 10px 20px;
        }
    </style>
</head>

<body>
    <div class="welcome-page">
        <h2>Select a Location</h2>

        <!-- Dropdown for selecting location -->
        <div class="form-group"><select id="locationSelect" class="form-control">
                <option value="">Select a Location</option>
                <!-- Loop through locations from the controller -->
                @foreach ($locations as $location)
                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Confirm button -->
        <button id="confirmLocation" class="btn btn-primary" disabled>Confirm</button>
    </div>

    <!-- Include the necessary JavaScript libraries -->
    <script>
        // Enable confirm button when a location is selected
        document.getElementById('locationSelect').addEventListener('change', function() {
            const confirmButton = document.getElementById('confirmLocation');
            confirmButton.disabled = !this.value; // Enable if a value is selected
        });

        // Save location to local storage when confirmed
        document.getElementById('confirmLocation').addEventListener('click', function() {
            const selectedLocationId = document.getElementById('locationSelect').value;

            if (selectedLocationId) {
                // Save to localStorage
                localStorage.setItem('LocationId', selectedLocationId);
                alert('Location saved to local storage');
                window.location.href = '{{ url('/') }}';
            } else {
                alert('Please select a location first.');
            }
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
