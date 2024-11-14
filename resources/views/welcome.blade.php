<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
    @endif
</head>
<style>
    .name {
        position: absolute;
        top: 400px;
        left: 50%;
        transform: translateX(-50%);
        width: 535px;
        font-size: 70px;
        background: url(http://[::1]:5173/resources/images/nametag.webp) no-repeat;
        background-size: cover !important;
        height: 180px;
    }

    .name h1 {
        text-align: center;
        padding: 10px;
        margin: 0;
        font-size: 60px;
        margin-top: 66px;
    }

    .welcome-page {
        width: 100%;
        height: 100vh;
        background-image: url('{{ Vite::asset('resources/images/HomePage.webp') }}');
        background-size: cover;
        background-position: center;
        position: relative;
        transition: 0.5s;
    }

    .welcome-page {
        cursor: url('{{ Vite::asset('resources/images/cursor.png') }}'), auto;
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
        left: 50%;
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
        /* background-image: url('{{ Vite::asset('resources/images/') }}'); */
        z-index: -1;
        transition: 0.5s;
    }

    #scannerContainer {
        position: absolute;
        width: 300px;
        height: 200px;
        border: 1px solid #ccc;
        top: 1016px;
        left: 366px;
    }
</style>

<body class="welcome-page">
    <div class="welcome">
        <div id="scannerContainer" class="scanner-container">
            <!-- <button id="close" class="mx-auto mt-4 camera-btn">x</button> -->
            <div id="reader"></div>
        </div>
    </div>
    <a class="btn">
        <img src="{{ Vite::asset('resources/images/start.webp') }}" alt="">
    </a>
</body>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    const audio = new Audio('{{ Vite::asset('resources/sounds/Background.mp3') }}');
    audio.loop = true;
    const countdownSound = new Audio('{{ Vite::asset('resources/sounds/countdown.mp3') }}');
    var currentUser = null;

    document.body.addEventListener('click', () => {
        audio.play();
    }, {
        once: true
    });

    var QRmode = false;
    const page = document.querySelector('.welcome-page');
    const btn = document.querySelector('.btn');

    const flow = ['welcome', 'howtoplay', 'scanqr', 'countdown'];

    let currentFlow = 0;

    function extractDetails(qrData) {
        // Remove the curly braces at the start and end
        const trimmedData = qrData.slice(1, -1);

        // Split the data by "\\" to get each section
        const dataParts = trimmedData.split("\\");

        // Get the phone, email, and name from the relevant parts
        const id = dataParts[3];
        const phone = dataParts[4];
        const email = dataParts[5];
        const name = dataParts[6];

        return {
            id,
            phone,
            email,
            name
        };
    }

    page.addEventListener('click', () => {
        if (currentFlow === 0) {
            page.style.backgroundImage = 'url({{ Vite::asset('resources/images/Howtoplay.webp') }})';
            btn.style.display = 'none';
            currentFlow++;
        } else if (currentFlow === 1) {
            page.style.backgroundImage = 'url({{ Vite::asset('resources/images/ScanQR.webp') }})';

            const html5QrCode = new Html5Qrcode("reader");
            html5QrCode.start({
                        facingMode: "environment"
                    }, {
                        fps: 10,
                        qrbox: 200,
                        aspectRatio: 9 / 16 // Set the aspect ratio to 16:9
                    },
                    qrCodeMessage => {
                        html5QrCode.stop();
                        console.log(`${qrCodeMessage}`);
                        const {
                            id,
                            phone,
                            email,
                            name
                        } = extractDetails(`${qrCodeMessage}`);
                        console.log("Id:", id);
                        console.log("Phone:", phone);
                        console.log("Email:", email);
                        console.log("Name:", name);
                        const storedUserString = localStorage.getItem('currentUser');

                        if (storedUserString) {
                            //remove the stored user
                            localStorage.removeItem('currentUser');
                            console.log('asdasda');
                        }
                        console.log('a');


                        currentUser = {
                            id: id,
                            name: name,
                            score: 0,
                            phone: phone,
                            email: email

                        };

                        // Convert the object to a JSON string and store it in local storage
                        localStorage.setItem('currentUser', JSON.stringify(currentUser));
                        countdown();


                    },
                    errorMessage => {
                        console.log(`QR Code no longer in front of camera.`);
                    })
                .catch(err => {
                    console.log(`Unable to start scanning, error: ${err}`);
                });
        };
    });

    function countdown() {
        console.log('asdasda');
        countdownSound.play();
        page.style.backgroundImage = 'url({{ Vite::asset('resources/images/countdown.png') }})';
        const name = document.createElement('h1');

        //get the stored user
        const storedUserString = localStorage.getItem('currentUser');
        const user = JSON.parse(storedUserString).name;
        name.innerText = user;

        const nameContainer = document.createElement('div');
        nameContainer.classList.add('name');
        nameContainer.appendChild(name);
        page.appendChild(nameContainer);

        let count = 3;


        // create an img with center position
        const img = document.createElement('img');
        img.src = `{{ Vite::asset('resources/images/Welcome_3') }}.webp`;
        img.style.position = 'absolute';
        img.style.top = '50%';
        img.style.left = '50%';
        img.style.transform = 'translate(-50%, -50%)';
        img.style.width = '15vw';
        img.style.height = 'auto';
        page.appendChild(img);


        const interval = setInterval(() => {
            count--;
            img.src = `{{ Vite::asset('resources/images/Welcome_') }}${count}.webp`;
            if (count === 0) {
                clearInterval(interval);
                window.location.href = '{{ url('/game') }}';
            }
        }, 1000);
    }
</script>

</html>
