<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Fruit Ninja</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<style>
    @font-face {
        font-family: 'Simonetta-Black';
        src: url('{{ asset('/fonts/Simonetta-Black.ttf') }}') format('truetype');
        font-weight: normal;
        font-style: normal;
    }

    @font-face {
        font-family: 'Singulier-Bold';
        src: url('{{ asset('/fonts/Singulier-Bold.ttf') }}') format('truetype');
        font-weight: normal;
        font-style: normal;
    }

    body {
        font-family: 'Singulier-Bold', sans-serif;
        overflow: hidden;
    }

    .name h1 {
        text-align: center;
        padding: 10px;
        margin: 0;
        font-size: 60px;
        margin-top: 66px;
    }

    .finish-page {
        width: 100%;
        height: 100vh;
        background-image: url({{ asset('/images/Background.webp') }});
        background-size: cover;
        background-position: center;
        position: relative;
        transition: 0.5s;
    }

    .finish-page {
        cursor: url('{{ asset('/images/cursor.png') }}'),
            auto;
    }

    .times-up {
        position: absolute;
        top: 10vh;
        left: 50%;
        transform: translateX(-50%);
        text-align: center;
        color: #000;
        font-weight: 700;
    }

    .times-up h1 {
        font-size: 60px;
        margin-bottom: 20vh;
    }

    .times-up h2 {
        font-size: 50px;
        margin-bottom: 5vh;
        font-family: 'Simonetta-Black', sans-serif;
        width: 100%;
    }

    .times-up p {
        font-size: 50px;
        margin: 0;
    }

    .times-up .score {
        font-size: 50px;
        margin: 0;
    }

    .table-container {
        position: absolute;
        top: 6vh;
        left: 50%;
        transform: translateX(-50%);
        text-align: center;
        display: none;
        font-family: 'Simonetta-Black';
    }

    .table-container h1 {
        font-size: 60px;
        margin: 0;
        padding: 0;
    }

    .table-container h2 {
        font-size: 50px;
        margin: 0;
        padding: 0;
    }

    .table {
        margin-top: 40px;
        font-size: 30pxs
    }

    .table tr {
        font-size: 30px;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        background: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .table td,
    .table th {
        padding: 15px 10px;
        min-width: 184px;
        margin-bottom: 20px;
    }

    .user {
        background: #FFEEB8 !important;
    }

    .user tr td:first-child::before {
        position: absolute;
        content: '';
        width: 200px;
        height: 200px;
        right: 0;
        top: 0;
        background-image: url('{{ asset('/images/left.png') }}');
        background-size: contain;
        background-repeat: no-repeat;
    }

    .user tr td {
        position: relative;
    }

    .user tr td:last-child::before {
        position: absolute;
        content: '';
        width: 200px;
        height: 200px;
        left: 0;
        top: 0;
        background-image: url('{{ asset('/images/right.png') }}');
        background-size: contain;
        background-repeat: no-repeat;
    }

    .btn {
        display: block;
        width: 323px;
        height: 121px;
        margin: 20px;
        color: #000000;
        text-decoration: none;
        border-radius: 5px;
        position: absolute;
        bottom: 20%;
        left: 46%;
        transform: translate(-50%, -50%);
        border: none;
        cursor: pointer;
        outline: none;
    }

    .btn img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 5px;
    }

    .btn::after {
        content: '';
        display: block;
        position: absolute;
        bottom: 0;
        right: 0;
        width: 100%;
        height: 100%;
        /* background-image: url('{{ asset('resources/images/') }}'); */
        z-index: -1;
        transition: 0.5s;
    }
</style>

<body class="finish-page">
    <div class="times-up">
        <h1>Times up!</h1>
        <h2 class="message"></h2>
        <p>SCORE</p>
        <p class="score">0</p>
    </div>
    <div class="table-container">
        <h1>Leaderboard</h1>
        <h2 id="current-date"></h2>

        <div class="table">
            <table>
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Score</th>
                    <th>Time</th>
                </tr>
            </table>
        </div>
    </div>
    <a id="done" class="btn">
        <img src="{{ asset('/images/done.webp') }}" alt="">
    </a>
</body>
<script>
    document.querySelector('#current-date').addEventListener('click', () => {
        const doneButton = document.getElementById('done');

        // Check if the button is hidden
        if (doneButton && getComputedStyle(doneButton).display === 'none') {
            // Redirect to the next page
            window.location.href = '/'; // Replace '/next-page' with your desired URL
        }
    });
    const today = new Date();
    const currentUser = JSON.parse(localStorage.getItem('currentUser'));

    const formattedDate = (today.getMonth() + 1).toString().padStart(2, '0') + '/' +
        today.getDate().toString().padStart(2, '0') + '/' +
        today.getFullYear();

    // Set the date inside the h2 element
    document.getElementById('current-date').innerText = formattedDate;

    // Set the date inside the h2 element
    document.getElementById('current-date').innerText = formattedDate;
    const audio = new Audio('{{ asset('/sounds/Background.mp3') }}');
    audio.loop = true;
    audio.play();

    const data = JSON.parse(localStorage.getItem('currentUser'));

    document.querySelector('.score').innerText = data.score;
    document.querySelector('.message').innerHTML = `Well done,<br>${data.name}!`;


    document.querySelector('#done').addEventListener('mouseenter', () => {
        // Change the background image and show/hide elements
        document.querySelector('.finish-page').style.backgroundImage =
            `url('{{ asset('/images/leaderBoard.png') }}')`;
        document.querySelector('.times-up').style.display = 'none';
        document.querySelector('.btn').style.display = 'none';
        document.querySelector('.table-container').style.display = 'block';

        // Retrieve current user data from local storage
        const locationId = localStorage.getItem('LocationId');

        // Make an AJAX request to save the leaderboard data
        fetch('{{ route('leaderboard.save') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    currentUser: data,
                    locationId: locationId
                })
            })
            .then(response => {
                if (!response.ok) {
                    // If the response is not OK, log the error and throw an exception
                    console.error('Error response:', response.statusText);
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {

                // Render the leaderboard
                renderLeaderboard(data);
            })
            .catch(error => {
                console.error('Errorasda:', error);
            });

        const interval = setInterval(() => {
            window.location.href = '{{ url('/') }}';
        }, 10000);
    });

    function renderLeaderboard(leaderboard) {
        const tableBody = document.querySelector('.table tbody');
        leaderboard.forEach((user, index) => {
            const row = document.createElement('tr');
            const last4Digits = user.phone;

            if (index == 0) {
                localStorage.setItem('highscore', JSON.stringify(user));
            }

            const updatedAt = new Date(user.updated_at);
            const formattedDate = updatedAt.toLocaleString('en-US', {

                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            });

            row.innerHTML = `
            <td>${index + 1}</td>
            <td>${user.name}</td>
            <td>${user.highest_score}</td>
            <td>${formattedDate}</td>

        `;

            if (user.code === currentUser.id) {
                row.classList.add('user');
                const firstTd = row.querySelector('td:first-child');
                const lastTd = row.querySelector('td:last-child');

                const left = document.createElement('div');
                left.style.width = '100px';
                left.style.height = '100px';
                left.style.position = 'absolute';
                left.style.right = '-72px';
                left.style.top = '172px';
                left.style.backgroundImage = `url('{{ asset('/images/right.png') }}')`;
                left.style.backgroundSize = 'contain';
                left.style.backgroundRepeat = 'no-repeat';
                firstTd.appendChild(left);

                const right = document.createElement('div');
                right.style.width = '100px';
                right.style.height = '100px';
                right.style.position = 'absolute';
                right.style.left = '-37px';
                right.style.top = '133px';
                right.style.backgroundImage = `url('{{ asset('/images/left.png') }}')`;
                right.style.backgroundSize = 'contain';
                right.style.backgroundRepeat = 'no-repeat';
                lastTd.appendChild(right);
            }

            tableBody.appendChild(row);
        });
    }
</script>


</html>
