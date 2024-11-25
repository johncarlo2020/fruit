<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fruit Ninja</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- SheetJS (for Excel export) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<style>
    .finish-page {
        font-family: 'Figtree', sans-serif;
        padding: 20px;
    }

    .venues {
        margin-top: 30px;
    }

    .table-container {
        margin-bottom: 30px;
    }

    .btn-success {
        margin-bottom: 15px;
    }

    table {
        width: 100%;
        table-layout: auto;
    }

    @media (max-width: 767px) {

        table th,
        table td {
            padding: 8px;
            font-size: 12px;
        }

        table th {
            font-size: 14px;
        }

        .table-container h3 {
            font-size: 18px;
        }

        .venues form div {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .venues form label {
            margin-bottom: 5px;
        }
    }
</style>

<body class="finish-page">
    <form method="GET" action="{{ route('leaderboard.list') }}">
        <div class="mb-3">
            <label for="date" class="form-label">Select Date:</label>
            <input type="date" id="date" name="date" value="{{ $selectedDate }}" class="form-control"
                onchange="this.form.submit()">
        </div>
    </form>

    <div class="venues">
        @foreach ($venues as $venue)
            <div class="table-container">
                <h1>Venue: {{ $venue->name }}</h1>
                <h3> Total Users : {{ $leaderboards[$venue->id]['total_users'] }}</h3>
                <h3> Total Games Played : {{ $leaderboards[$venue->id]['total_games'] }}</h3>

                <button class="btn btn-success" onclick="exportToExcel('table-{{ $venue->id }}')">Export to
                    Excel</button>

                <div class="table-responsive">
                    <table class="table table-bordered datatable" id="table-{{ $venue->id }}">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Name</th>
                                <th>Unique Id</th>
                                <th>Score</th>
                                <th>Games Played</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($leaderboards[$venue->id]['top_leaderboard'] as $index => $leaderboard)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $leaderboard->name }}</td>
                                    <td>{{ $leaderboard->code }}</td>
                                    <td>{{ $leaderboard->score }}</td>
                                    <td>{{ $leaderboard->play_count }}</td>

                                    <td>{{ $leaderboard->updated_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function exportToExcel(tableId) {
            const wb = XLSX.utils.table_to_book(document.getElementById(tableId), {
                sheet: "Leaderboard"
            });
            XLSX.writeFile(wb, tableId + ".xlsx");
        }
    </script>
</body>

</html>
