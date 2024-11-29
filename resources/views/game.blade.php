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

    <!-- Include Phaser library -->
    <script src="https://cdn.jsdelivr.net/npm/phaser@2.6.2/build/phaser.min.js"></script>

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

        .game-page {
            width: 100%;
            height: 100vh;
            background-image: url('images/Background.webp');
            background-size: cover;
            background-position: center;
            position: relative;
        }

        html,
        body {
            padding: 0;
            margin: 0;
            background: transparent;
            cursor: none;
            /* Hide the default cursor */
        }

        div#game {
            width: 100%;
            height: 100%;
        }
    </style>
</head>

<body class="game-page">
    <div id="game"></div>
    <script>
        // create an item for local storage for leaderboard data is name and score and phone number

        var w = window.innerWidth,
            h = window.innerHeight;

        var game = new Phaser.Game(w, h, Phaser.AUTO, 'game', {
            preload: preload,
            create: create,
            update: update,
            render: render
        }, true);

        var continueThrowing = true;

        function preload() {
            this.load.crossOrigin = 'anonymous'; // Set crossOrigin
            this.load.text('Singulier-Bold', '{{ asset('fonts/Singulier-Bold.ttf') }}');
            this.load.text('Simonetta-Black', '{{ asset('fonts/Simonetta-Black.ttf') }}');
            this.load.audio('sliceSound', '{{ asset('sounds/slice.mp3') }}');
            this.load.audio('goodSound', '{{ asset('sounds/good.mp3') }}');
            this.load.audio('badSound', '{{ asset('sounds/bad.mp3') }}');
            this.load.audio('pop', '{{ asset('sounds/pop.mp3') }}');
            this.load.audio('special', '{{ asset('sounds/success2.wav') }}');
            this.load.audio('backgroundMusic', '{{ asset('sounds/background.mp3') }}');
            this.load.image('good1', '{{ asset('images/orange.webp') }}');
            this.load.image('good2', '{{ asset('images/Pomegranate.webp') }}');
            this.load.image('good3', '{{ asset('images/Berry.webp') }}');
            this.load.image('good4', '{{ asset('images/apple.webp') }}');
            this.load.image('good5', '{{ asset('images/Fig.webp') }}'); // New object
            this.load.image('good6', '{{ asset('images/grape.webp') }}'); // New object
            this.load.image('bad1', '{{ asset('images/roetten-pear.webp') }}');
            this.load.image('bad2', '{{ asset('images/Rotten-Apple.webp') }}');
            this.load.image('circle', '{{ asset('images/circle.svg') }}');
            this.load.image('particle', '{{ asset('images/particle2.svg') }}');
            this.load.image('sword', '{{ asset('images/magicwand.webp') }}');
            this.load.image('glitter', '{{ asset('images/particle.png') }}');
            this.load.image('berryBg', '{{ asset('images/berry_effect.webp') }}');
        }

        var good_objects1, good_objects2, good_objects3, good_objects4, good_objects5, good_objects6, bad_objects1,
            bad_objects2, slashes, line, scoreLabel, score = 0,
            points = [];
        var confettiEmitter, particleEmitter, glitterEmitter, swordCursor;

        var fireRate = 1000;
        var nextFire = 0;
        var selectedFruit = null;
        var gameTime = 60;
        var timerLabel;
        var highscore = 0;

        // Mapping of items to their points
        var itemPoints = {
            'good1': 10,
            'good2': 15,
            'good3': 100,
            'good4': 20,
            'good5': 25,
            'good6': 30,
            'bad1': -10,
            'bad2': -15,
        };

        let timeElapsed = 0;

        const objectSize = 200;
        const berrySize = 200;

        function create() {
            game.physics.startSystem(Phaser.Physics.ARCADE);
            game.physics.arcade.gravity.y = 100;

            good_objects1 = createGroup(4, 'good1', objectSize);
            good_objects2 = createGroup(4, 'good2', objectSize);
            good_objects3 = createGroup(4, 'good3', berrySize);
            good_objects4 = createGroup(4, 'good4', objectSize);
            good_objects5 = createGroup(4, 'good5', objectSize);
            good_objects6 = createGroup(4, 'good6', objectSize);
            bad_objects1 = createGroup(4, 'bad1', objectSize);
            bad_objects2 = createGroup(4, 'bad2', objectSize);

            slashes = game.add.graphics(0, 0);

            // Create timer label on the left side
            const timeTextLabel = game.add.text(40, 10, 'TIME', {
                fontFamily: 'Singulier-Bold',
                fontSize: '32px',
                fontWeight: 'bold',
                color: '#000'
            });

            // Create timer value label below the "Time" label
            timerLabel = game.add.text(75, 10 + timeTextLabel.height, '00', {
                fontFamily: 'Singulier-Bold',
                fontSize: '32px',
                fontWeight: 'bold',
                color: '#000'
            });

            let leaderboard = JSON.parse(localStorage.getItem("highscore")) || [];
            // console.log(JSON.parse(localStorage.getItem('highscore')))
            console.log(leaderboard);
            let highscore = leaderboard['highest_score'];

            const hightTextLabel = game.add.text(game.world.centerX, 10, 'HIGH SCORE OF THE DAY', {
                fontFamily: 'Singulier-Bold',
                fontSize: '32px',
                fontWeight: 'bold',
                color: '#000'
            });

            hightTextLabel.anchor.set(0.5, 0);

            highscoreLabel = game.add.text(game.world.centerX, hightTextLabel.height + 10, highscore, {
                fontFamily: 'Singulier-Bold',
                fontSize: '32px',
                fontWeight: 'bold',
                color: '#000'
            });

            highscoreLabel.anchor.set(0.5, 0);

            const scoreLabelText = game.add.text(game.world.width - 150, 10, 'SCORE', {
                fontFamily: 'Singulier-Bold',
                fontSize: '32px',
                fontWeight: 'bold',
                color: '#000'
            });

            // Create score label initially on the right side
            scoreLabel = game.add.text(game.world.width - 150, 10 + scoreLabelText.height, '0', {
                fontFamily: 'Singulier-Bold',
                fontSize: '32px',
                fontWeight: 'bold',
                color: '#000'
            });
            scoreLabel.anchor.set(1, 0);
            scoreLabel.x = game.world.width - 50;


            // Create particle emitter
            particleEmitter = game.add.emitter(0, 0, 300);
            particleEmitter.makeParticles('particle');
            particleEmitter.gravity = 300;
            particleEmitter.setYSpeed(-400, 400);
            particleEmitter.minParticleScale = 0.01;
            particleEmitter.maxParticleScale = 0.1;

            //create berryBg
            berryBg = game.add.sprite(game.world.centerX, game.world.centerY, 'berryBg');
            berryBg.anchor.setTo(0.5, 0.5);
            berryBg.scale.setTo(1, 1);
            berryBg.visible = false;

            // Create confetti emitter
            confettiEmitter = game.add.emitter(0, 0, 100);
            confettiEmitter.makeParticles('circle');
            confettiEmitter.gravity = 200;
            confettiEmitter.setYSpeed(-300, 300);
            confettiEmitter.setXSpeed(-300, 300);
            confettiEmitter.minParticleScale = 0.01;
            confettiEmitter.maxParticleScale = 0.1;

            // Create sword cursor
            swordCursor = game.add.sprite(game.world.centerX, game.world.centerY, 'sword');
            swordCursor.anchor.setTo(1, 0);
            swordCursor.scale.setTo(0.070, 0.070);

            // Create glitter emitter
            glitterEmitter = game.add.emitter(0, 0, 100);
            glitterEmitter.makeParticles('glitter');
            glitterEmitter.gravity = 1;
            glitterEmitter.setYSpeed(-50, 60);
            glitterEmitter.setXSpeed(-50, 50);
            glitterEmitter.minParticleScale = 0.002;
            glitterEmitter.maxParticleScale = 0.006;
            glitterEmitter.start(false, 1000, 5);

            sliceSound = game.add.audio('sliceSound');
            special = game.add.audio('special');
            pop = game.add.audio('pop');
            goodSound = game.add.audio('goodSound');
            badSound = game.add.audio('badSound');

            backgroundMusic = game.add.audio('backgroundMusic');
            backgroundMusic.loop = true;
            backgroundMusic.play();

            // Start the timer
            game.time.events.loop(Phaser.Timer.SECOND, updateTimer, this);

            throwObject();
        }

        function updateTimer() {
            gameTime--;
            timerLabel.text = gameTime;

            if (gameTime <= 0) {
                gameOver();
            }
        }

        function gameOver() {
            var leaderboard = JSON.parse(localStorage.getItem('leaderboard')) || [];

            // Get the current user from local storage
            var currentUser = JSON.parse(localStorage.getItem('currentUser'));

            // Check if the current user already exists on the leaderboard
            let existingUserIndex = leaderboard.findIndex(user => user.id === currentUser.id);
            console.log(existingUserIndex);
            const data = {
                id: currentUser.id,
                name: currentUser.name,
                score: score,
                phone: currentUser.phone,
                email: currentUser.email
            };
            localStorage.setItem('currentUser', JSON.stringify(data));


            // go to finished page
            window.location.href = '{{ url('/finished') }}';

        }

        function restartGame() {
            // Reset game state
            score = 0;
            gameTime = 60;
            scoreLabel.text = 0;
            scoreLabel.fill = 'black';
            timerLabel.text = 'Time: ' + gameTime;
            timerLabel.fill = '#000';
            game.world.removeAll();
            backgroundMusic.play();

            continueThrowing = true;
            showAllElements();
            throwObject();
        }

        function createGroup(numItems, spriteKey, fixedWidth = objectSize) {
            var group = game.add.group();
            group.enableBody = true;
            group.physicsBodyType = Phaser.Physics.ARCADE;
            group.createMultiple(numItems, spriteKey);
            group.setAll('checkWorldBounds', true);
            group.setAll('outOfBoundsKill', true);
            group.forEach(function(item) {
                item.width = fixedWidth;
                item.scale.y = item.scale.x; // Maintain aspect ratio
            }, this);
            return group;
        }

        var elapsedTime = 0;
        var isFirstThrow = true;
        var good3Counter = 0; // Counter for good3 objects
        var maxGood3Count = 9;

        function throwObject() {
            if (!continueThrowing) return; // Stop throwing objects if the flag is false

            elapsedTime += game.time.elapsed; // Update elapsed time

            // Adjust fireRate based on elapsed time
            var adjustedFireRate = fireRate - Math.floor(elapsedTime / 10000) * 500;
            adjustedFireRate = Math.max(adjustedFireRate, 500);

            var badObjectProbability = 0.4 + Math.min(elapsedTime / 60000, 0.4); // Increase bad object probability over time

            if (game.time.now > nextFire) {
                // Check if there are any items on the screen
                var itemsOnScreen = good_objects1.countLiving() + good_objects2.countLiving() + good_objects3.countLiving() +
                    good_objects4.countLiving() + good_objects5.countLiving() + good_objects6.countLiving() +
                    bad_objects1.countLiving() + bad_objects2.countLiving();

                if (itemsOnScreen > 0) {
                    // If there are items on the screen, just increase the speed of the next throw
                    nextFire = game.time.now + adjustedFireRate;
                    return;
                }

                nextFire = game.time.now + adjustedFireRate;

                // Determine the number of objects to throw
                var numObjectsToThrow;
                if (elapsedTime < 5000) {
                    numObjectsToThrow = isFirstThrow ? 1 : Math.floor(Math.random() * 5) + 3; // 3-7 objects in the first 10 seconds
                } else {
                    numObjectsToThrow = Math.floor(Math.random() * 6) + 3; // 3-8 objects after 10 seconds
                }
                isFirstThrow = false; // Reset the first throw flag after the first throw

                var good3Thrown = false; // Track if a good3 object has been thrown

                // Define a central point for the clump at the center of the screen
                var clumpCenterY = game.world.height - 10;

                var clumpCenterX = game.world.centerX; // Center of the world

                // Randomly position to the left or right by adding/subtracting an offset
                var maxOffset = 200; // Increase this value for a wider spread
                var randomOffset = (Math.random() < 0.5 ? -1 : 1) * Math.random() * maxOffset;

                var finalPositionX = clumpCenterX + randomOffset;

                for (var i = 0; i < numObjectsToThrow; i++) {
                    var randomGoodObjectGroup;
                    do {
                        randomGoodObjectGroup = Math.floor(Math.random() * 6) + 1;
                    } while (randomGoodObjectGroup === 3 && (good3Thrown || good3Counter >= maxGood3Count));

                    if (randomGoodObjectGroup === 3) {
                        good3Counter++;
                        good3Thrown = true; // Mark that a good3 object has been thrown
                    }

                    throwGoodObject(eval('good_objects' + randomGoodObjectGroup), finalPositionX, clumpCenterY, i);
                }

                if (Math.random() < badObjectProbability) {
                    throwBadObject(bad_objects1, finalPositionX, clumpCenterY, numObjectsToThrow);
                }

                if (Math.random() < badObjectProbability) {
                    throwBadObject(bad_objects2, finalPositionX, clumpCenterY, numObjectsToThrow);
                }
            }
        }

        function throwGoodObject(group, clumpCenterX, clumpCenterY, index) {
            var obj = group.getFirstDead();

            console.log('Throwing object:', obj);

            if (obj) {
                // Random position within a clump range
                var randomX = Phaser.Math.clamp(clumpCenterX + (Math.random() * 200 - 100), 0, game.world.width); // Increase spread
                var startY = clumpCenterY;

                // Set object position at random x and bottom y
                obj.reset(randomX, startY);
                obj.anchor.setTo(0.5, 0.5);

                // Random target within the clump range
                var targetX = Phaser.Math.clamp(clumpCenterX + (Math.random() * 200 - 100) + index * 100, 0, game.world.width); // Increase spread
                var targetY = Math.random() * (game.world.centerY - Math.random() * 100);

                // Base speed and gravity
                var baseSpeed = 800; // Increase base speed
                var baseGravity = 120; // Increase base gravity

                // Increase falling speed and gravity as time progresses
                var speed = baseSpeed + Math.floor(elapsedTime / 5000) * 20; // Increase speed increment
                var gravity = baseGravity + Math.floor(elapsedTime / 5000) * 20; // Increase gravity increment

                // Random horizontal throw velocity
                var randomHorizontalVelocity = (Math.random() < 0.5 ? -1 : 1) * (Math.random() * 200 + 100); // Increase horizontal velocity

                // Additional speed and gravity for 'good3' objects
                if (obj.key === 'good3') {
                    speed += 200;
                    gravity += 400;
                }

                // Set velocity directly to add randomness in horizontal movement
                obj.body.velocity.setTo(randomHorizontalVelocity, -speed);
                obj.body.gravity.y = gravity;
            } else {
                console.log("No dead objects available in the group.");
            }
        }

        function throwBadObject(group, clumpCenterX, clumpCenterY, index) {
            var obj = group.getFirstDead();

            console.log('Throwing object:', obj);

            if (obj) {
                // Random position within a clump range
                var randomX = Phaser.Math.clamp(clumpCenterX + (Math.random() * 200 - 100), 0, game.world.width); // Increase spread
                var startY = clumpCenterY;

                // Set object position at random x and bottom y
                obj.reset(randomX, startY);
                obj.anchor.setTo(0.5, 0.5);

                // Random target within the clump range
                var targetX = Phaser.Math.clamp(clumpCenterX + (Math.random() * 200 - 100) + index * 100, 0, game.world.width); // Increase spread
                var targetY = Math.random() * (game.world.centerY - Math.random() * 100);

                // Base speed and gravity
                var baseSpeed = 800; // Increase base speed
                var baseGravity = 120; // Increase base gravity

                // Increase falling speed and gravity as time progresses
                var speed = baseSpeed + Math.floor(elapsedTime / 5000) * 20; // Increase speed increment
                var gravity = baseGravity + Math.floor(elapsedTime / 5000) * 20; // Increase gravity increment

                // Random horizontal throw velocity
                var randomHorizontalVelocity = (Math.random() < 0.5 ? -1 : 1) * (Math.random() * 200 + 100); // Increase horizontal velocity

                // Set velocity directly to add randomness in horizontal movement
                obj.body.velocity.setTo(randomHorizontalVelocity, -speed);
                obj.body.gravity.y = gravity;
            } else {
                console.log("No dead objects available in the group.");
            }
        }


        var contactPoint = new Phaser.Point(0, 0);

        function checkIntersects(fruit) {
            var l1 = new Phaser.Line(fruit.body.x, fruit.body.y, fruit.body.x + fruit.body.width, fruit.body.y + fruit.body.height);
            var l2 = new Phaser.Line(fruit.body.x, fruit.body.y + fruit.body.height, fruit.body.x + fruit.body.width, fruit.body.y);

            if (Phaser.Line.intersects(line, l1, true) || Phaser.Line.intersects(line, l2, true)) {
                console.log('Intersection detected with:', fruit.key);

                contactPoint.x = game.input.x;
                contactPoint.y = game.input.y;
                var distance = Phaser.Point.distance(contactPoint, new Phaser.Point(fruit.x, fruit.y));
                if (distance > 110) {
                    return false;
                }

                console.log('Distance:', distance, 'continueThrowing:', continueThrowing);

                if (continueThrowing) {
                    console.log('Killing fruit:', fruit.key);
                    return true;
                }
            }
            return false;
        }

        function update() {
            throwObject();

            points.push({
                x: game.input.x,
                y: game.input.y
            });

            points = points.splice(points.length - 10, points.length);

            if (points.length < 1 || points[0].x == 0) {
                return;
            }

            timeElapsed += this.game.time.elapsed / 1000; // Convert to seconds if needed
            // Initialize the slash effect
            slashes.clear();

            // Define colors and line thickness
            let color = 0xFFFF99; // Light yellow for better contrast
            let maxThickness = 12; // Maximum thickness in the middle of the slash

            // Calculate the dynamic thickness for a pointy effect at start and end
            let thickness;
            let midPoint = Math.floor(points.length / 2);

            for (let i = 0; i < points.length - 1; i++) {
                // Calculate thickness: thin at start and end, thicker in the middle
                if (i < midPoint) {
                    thickness = (maxThickness / midPoint) * i + 2; // Gradually increase
                } else {
                    thickness = (maxThickness / midPoint) * (points.length - i - 1) + 2; // Gradually decrease
                }

                // Set line style for the current segment
                slashes.lineStyle(thickness, color, 1);

                // Draw each segment individually to control the thickness dynamically
                slashes.moveTo(points[i].x, points[i].y);
                slashes.lineTo(points[i + 1].x, points[i + 1].y);
            }

            // Optional: Add a soft glow effect using blend mode
            slashes.blendMode = Phaser.blendModes.ADD;

            // Fade out effect based on timeElapsed (make sure timeElapsed is defined and incremented in your update)
            slashes.alpha = Math.max(0, 1 - timeElapsed * 0.05);

            if (slashes.alpha <= 0) {
                timeElapsed = 0; // Reset timeElapsed for a new slash effect
            }

            let fruitsToSlice = [];

            for (var i = 1; i < points.length; i++) {
                line = new Phaser.Line(points[i].x, points[i].y, points[i - 1].x, points[i - 1].y);
                // game.debug.geom(line);

                good_objects1.forEachExists(fruit => {
                    if (checkIntersects(fruit)) fruitsToSlice.push(fruit);
                });
                good_objects2.forEachExists(fruit => {
                    if (checkIntersects(fruit)) fruitsToSlice.push(fruit);
                });
                good_objects3.forEachExists(fruit => {
                    if (checkIntersects(fruit)) fruitsToSlice.push(fruit);
                });
                good_objects4.forEachExists(fruit => {
                    if (checkIntersects(fruit)) fruitsToSlice.push(fruit);
                });
                good_objects5.forEachExists(fruit => {
                    if (checkIntersects(fruit)) fruitsToSlice.push(fruit);
                });
                good_objects6.forEachExists(fruit => {
                    if (checkIntersects(fruit)) fruitsToSlice.push(fruit);
                });
                bad_objects1.forEachExists(fruit => {
                    if (checkIntersects(fruit)) fruitsToSlice.push(fruit);
                });
                bad_objects2.forEachExists(fruit => {
                    if (checkIntersects(fruit)) fruitsToSlice.push(fruit);
                });
            }

            fruitsToSlice.forEach(fruit => killFruit(fruit));

            swordCursor.x = game.input.x;
            swordCursor.y = game.input.y;

            glitterEmitter.x = game.input.x;
            glitterEmitter.y = game.input.y;

            if (continueThrowing === false) {
                berryBg.x = selectedFruit.x;
                berryBg.y = selectedFruit.y;
                berryBg.visible = true;
                berryBg.width += 5;
                berryBg.height += 5;
                // berryBg.angle += 2;
                game.world.sendToBack(berryBg);
                game.world.moveUp(berryBg);
            } else {
                berryBg.visible = false;
                berryBg.width = 0;
                berryBg.height = 0;
            }
        }


        function resetScore() {
            good_objects1.forEachExists(killFruit);
            good_objects2.forEachExists(killFruit);
            good_objects3.forEachExists(killFruit);
            good_objects4.forEachExists(killFruit);
            good_objects5.forEachExists(killFruit);
            good_objects6.forEachExists(killFruit);
            bad_objects1.forEachExists(killFruit);
            bad_objects2.forEachExists(killFruit);

            score = 0;
        }

        function render() {}

        function popFruit(fruit) {
            particleEmitter.x = fruit.x;
            particleEmitter.y = fruit.y;
            particleEmitter.start(true, 2000, null, 10);
            confettiEmitter.x = fruit.x;
            confettiEmitter.y = fruit.y;
            confettiEmitter.start(true, 500, null, 30);

            var pointsValue = itemPoints[fruit.key];
            points = [];
            score += pointsValue;
            scoreLabel.text = score;

            var pointsText = game.add.text(fruit.x - fruit.width / 2, fruit.y - fruit.height / 2, (pointsValue > 0 ? '+' :
                '') + pointsValue, {
                font: '32px Arial',
                fill: '#000'
            });
            game.add.tween(pointsText).to({
                y: pointsText.y - 50,
                alpha: 0
            }, 500, Phaser.Easing.Linear.None, true);

            // Reset the scale of the fruit before killing it
            fruit.width = objectSize;
            fruit.height = objectSize;
            fruit.kill();

            if (fruit.key === 'bad1' || fruit.key === 'bad2') {
                badSound.play();
            } else {
                goodSound.play();
            }
            continueThrowing = true;
        }

        function killFruit(fruit) {
            console.log(fruit.key, ' continues throwing:', continueThrowing);
            selectedFruit = fruit;
            sliceSound.play();
            if (fruit.key === 'good3') {
                specialEffect(fruit);
                return;
            }
            popFruit(fruit);
        }

        function stopAllElements() {
            var allGroups = [good_objects1, good_objects2, good_objects3, good_objects4, good_objects5, good_objects6,
                bad_objects1, bad_objects2
            ];
            allGroups.forEach(function(group) {
                group.forEachAlive(function(item) {
                    if (item.key !== 'good3') {
                        item.body.velocity.setTo(0, 0);
                        item.body.allowGravity = false;
                        item.body.moves = false;
                    }
                });
            });
        }

        function hideAllElementsExcept(slicedFruit) {
            var allGroups = [good_objects1, good_objects2, good_objects3, good_objects4, good_objects5, good_objects6,
                bad_objects1, bad_objects2
            ];

            allGroups.forEach(function(group) {
                group.forEachAlive(function(item) {
                    if (slicedFruit === null || item !== slicedFruit) {
                        item.visible = false;
                    }
                });
            });
        }

        function showAllElements() {
            var allGroups = [good_objects1, good_objects2, good_objects3, good_objects4, good_objects5, good_objects6,
                bad_objects1, bad_objects2
            ];
            allGroups.forEach(function(group) {
                group.forEachAlive(function(item) {
                    item.visible = true;
                });
            });
        }

        const specialEffectTimeout = 1000;

        function specialEffect(fruit) {
            continueThrowing = false;
            backgroundMusic.stop();
            hideAllElementsExcept(fruit);
            game.world.bringToTop(fruit);
            var background = game.add.graphics(0, 0);
            background.beginFill(0x000000, 0.2);
            background.drawRect(0, 0, game.world.width, game.world.height);
            background.endFill();
            game.world.sendToBack(background);

            var specialTween = game.add.tween(fruit).to({
                x: game.world.centerX,
                y: game.world.centerY - 50,
                width: fruit.width * 1.9,
                height: fruit.height * 1.9
            }, specialEffectTimeout, Phaser.Easing.Linear.None, true);

            specialTween.onComplete.addOnce(() => {
                background.destroy();
                backgroundMusic.play();
                showAllElements();
                popFruit(fruit);
            });

            special.play();
        }
    </script>
</body>

</html>
