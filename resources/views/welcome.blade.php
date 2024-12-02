<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Fruit Ninja</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/simple-keyboard/build/css/index.css">
    <script src="https://unpkg.com/simple-keyboard/build/index.js"></script>

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<style>
    @font-face {
        font-family: 'Simonetta-Black';
        src: url('/fonts/Simonetta-Black.ttf') format('truetype');
        font-weight: normal;
        font-style: normal;
    }

    @font-face {
        font-family: 'Singulier-Bold';
        src: url('fonts/Singulier-Bold.ttf') format('truetype');
        font-weight: normal;
        font-style: normal;
    }

    body {
        font-family: 'Singulier-Bold', sans-serif;
        overflow: hidden;
    }

    .name {
        position: absolute;
        top: 56%;
        left: 50%;
        transform: translateX(-50%);
        width: 535px;
        font-size: 70px;
        background: url(http://[::1]:5173/images/nametag.webp) no-repeat;
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
        background-image: url('images/Howtoplay.webp');
        background-size: cover;
        background-position: center;
        position: relative;
        transition: 0.5s;
    }

    .welcome-page {
        cursor: url('images/cursor.png'), auto;
    }

    .start {
        display: block;
        width: 323px;
        height: 121px;
        margin: 20px;
        color: #000000;
        text-decoration: none;
        border-radius: 5px;
        position: absolute;
        bottom: 15%;
        left: 50%;
        transform: translate(-50%, -50%);
        border: none;
        cursor: pointer;
        outline: none;
    }

    .start img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 5px;
    }

    .start::after {
        content: '';
        display: block;
        position: absolute;
        bottom: 0;
        right: 0;
        width: 100%;
        height: 100%;
        /* background-image: url('resources/images/'); */
        z-index: -1;
        transition: 0.5s;
    }

    #scannerContainer {
        position: absolute;
        width: 300px;
        height: 200px;
        top: 38%;
        left: 36%;
        display: none;
        aspect-ratio: 9 / 16;
    }


    #reader {
        transform: rotate(-90deg) scaleX(-1);
    }

    .options {
        display: none;
        position: absolute;
        bottom: 20%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .options .btn {
        display: block;
        margin-bottom: 20px;
    }

    .input-container {
        display: none;
        position: absolute;
        bottom: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 50%;
    }

    .input-container input {
        width: 100%;
        height: 50px;
        border: 5px solid #000000;
        border-radius: 5px;
        padding: 10px;
        font-size: 20px;
        outline: none;
        background: #FFEEB8;
    }
</style>

<body class="welcome-page">
    <div class="welcome">
        <div id="scannerContainer" class="scanner-container">
            <!-- <button id="close" class="mx-auto mt-4 camera-btn">x</button> -->
            <div id="reader"></div>
        </div>
    </div>
    <a class="btn start">
        <img src="{{ asset('images/start.webp') }}" alt="">
    </a>

    <div class="options">
        <a class="btn member">
            <img src="{{ asset('images/MEMBER.webp') }}" alt="">
        </a>
        <a class="btn non-member">
            <img src="{{ asset('images/NONMEMEBER.webp') }}" alt="">
        </a>
    </div>

    <div class="input-container">
        <input type="text" id="textInput">
        <div id="keyboardContainer" class="simple-keyboard"></div>


    </div>

</body>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js" type="text/javascript"></script>


<script>
    document.addEventListener("DOMContentLoaded", () => {
        const keyboard = new SimpleKeyboard.default({
            onChange: input => onInputChange(input),
            onKeyPress: button => onKeyPress(button),
            layout: {
                default: ["q w e r t y u i o p", "a s d f g h j k l", "{shift} z x c v b n m {bksp}",
                    "{space}"
                ],
                shift: ["Q W E R T Y U I O P", "A S D F G H J K L", "{shift} Z X C V B N M {bksp}",
                    "{space}"
                ]
            },
            theme: "hg-theme-default myCustomTheme",
            debug: true, // Enable debugging to catch issues
            inputName: "textInput", // Link to the input field
            onInit: () => console.log("Keyboard initialized"),
            keyboardDOMClass: "keyboard-wrapper" // Class for the keyboard DOM
        });

        // Function to update the input field
        function onInputChange(input) {
            document.getElementById("textInput").value = input;
        }

        // Handle special keys (like shift)
        function onKeyPress(button) {
            if (button === "{shift}") {
                keyboard.setOptions({
                    layoutName: keyboard.options.layoutName === "default" ? "shift" : "default"
                });
            }
        }

        // Show keyboard when input is focused
        document.getElementById("textInput").addEventListener("focus", () => {
            document.getElementById("keyboardContainer").style.display = "block";
            console.log('asdas');
        });

        // Hide the keyboard when clicking outside
        document.addEventListener("click", (event) => {
            if (!event.target.closest("#textInput") && !event.target.closest("#keyboardContainer")) {
                document.getElementById("keyboardContainer").style.display = "none";
            }
        });
    });

    const audio = new Audio('{{ asset('sounds/background.mp3') }}');
    audio.loop = true;
    const countdownSound = new Audio('{{ asset('sounds/countdown.mp3') }}');
    var currentUser = null;
    const locationId = localStorage.getItem('LocationId');
    console.log(locationId);

    document.body.addEventListener('click', () => {
        audio.play();
    }, {
        once: true
    });

    var QRmode = false;
    const page = document.querySelector('.welcome-page');
    const btn = document.querySelector('.start');
    const member = document.querySelector('.member');
    const nonMember = document.querySelector('.non-member');
    const options = document.querySelector('.options');
    const inputContainer = document.querySelector('.input-container');

    const flow = ['welcome', 'howtoplay', 'scanqr', 'countdown'];

    let currentFlow = 0;

    // Initialize the virtual keyboard



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
    nonMember.addEventListener('mouseenter', () => {
        options.style.display = 'none';
        inputContainer.style.display = 'block';
    });

    btn.addEventListener('mouseenter', () => {

        page.style.backgroundImage = 'url({{ asset('images/memberpage.webp') }})';
        btn.style.display = 'none';
        options.style.display = 'block';
    });

    member.addEventListener('mouseenter', () => {
        options.style.display = 'none';
        page.style.backgroundImage = 'url({{ asset('images/Howtoplay.webp') }})';
        btn.style.display = 'none';
        currentFlow++;
        const scanner = document.querySelector('#scannerContainer');
        page.style.backgroundImage = 'url({{ asset('images/ScanQR.webp') }})';
        scanner.style.display = 'block';

        const html5QrCode = new Html5Qrcode("reader");
        html5QrCode.start({
                    facingMode: "environment"
                }, {
                    fps: 10,
                    qrbox: function(viewfinderWidth, viewfinderHeight) {
                        return {
                            width: viewfinderWidth,
                            height: viewfinderHeight
                        }; // Full coverage
                    },
                    aspectRatio: 9 / 16 // Portrait orientation
                },
                qrCodeMessage => {
                    html5QrCode.stop();
                    const {
                        id,
                        phone,
                        email,
                        name
                    } = extractDetails(`${qrCodeMessage}`);

                    const storedUserString = localStorage.getItem('currentUser');

                    if (storedUserString) {
                        //remove the stored user
                        localStorage.removeItem('currentUser');
                    }

                    currentUser = {
                        id: id,
                        name: name,
                        score: 0,
                        phone: phone,
                        email: email

                    };

                    console.log(qrCodeMessage);
                    console.log(currentUser);

                    // Convert the object to a JSON string and store it in local storage
                    localStorage.setItem('currentUser', JSON.stringify(currentUser));
                    countdown();


                },
                errorMessage => {

                })
            .catch(err => {
                console.log(`Unable to start scanning, error: ${err}`);
            });
    });



    function countdown() {
        countdownSound.play();
        const scanner = document.querySelector('#scannerContainer');
        scanner.style.display = 'none';
        page.style.backgroundImage = 'url({{ asset('images/countdown.webp') }})';
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
        img.src = `{{ asset('images/Welcome_3.webp') }}`;
        img.style.position = 'absolute';
        img.style.top = '75%';
        img.style.left = '50%';
        img.style.transform = 'translate(-50%, -50%)';
        img.style.width = '15vw';
        img.style.height = 'auto';
        page.appendChild(img);


        const interval = setInterval(() => {
            count--;
            const basePath = `{{ asset('images/Welcome_') }}`;
            img.src = `${basePath}${count}.webp`;
            if (count === 0) {
                clearInterval(interval);
                window.location.href = '{{ url('/game') }}';
            }
        }, 1000);
    }
</script>

</html>
