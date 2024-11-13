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

    <!-- Include Phaser library -->
    <script src="https://cdn.jsdelivr.net/npm/phaser@2.6.2/build/phaser.min.js"></script>

    <style>
        .game-page {
            width: 100%;
            height: 100vh;
            background-image: url({{ Vite::asset('resources/images/Background.webp') }});
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
            this.load.audio('sliceSound', '{{ Vite::asset('resources/sounds/slice.mp3') }}');
            this.load.audio('special', '{{ Vite::asset('resources/sounds/success2.wav') }}');
            this.load.audio('backgroundMusic', '{{ Vite::asset('resources/sounds/background.mp3') }}');
            this.load.image('good1', '{{ Vite::asset('resources/images/orange.webp') }}');
            this.load.image('good2', '{{ Vite::asset('resources/images/Pomegranate.webp') }}');
            this.load.image('good3', '{{ Vite::asset('resources/images/Berry.webp') }}');
            this.load.image('good4', '{{ Vite::asset('resources/images/apple.webp') }}');
            this.load.image('good5', '{{ Vite::asset('resources/images/Fig.webp') }}'); // New object
            this.load.image('good6', '{{ Vite::asset('resources/images/grape.webp') }}'); // New object
            this.load.image('bad1', '{{ Vite::asset('resources/images/roetten-pear.webp') }}');
            this.load.image('bad2', '{{ Vite::asset('resources/images/Rotten-Apple.webp') }}');
            this.load.image('circle', '{{ Vite::asset('resources/images/circle.svg') }}');
            this.load.image('particle', '{{ Vite::asset('resources/images/particle2.svg') }}');
            this.load.image('sword', '{{ Vite::asset('resources/images/magicwand.webp') }}');
            this.load.image('glitter', '{{ Vite::asset('resources/images/particle.png') }}');
            this.load.image('berryBg', '{{ Vite::asset('resources/images/berry_effect.webp') }}');
        }

        var good_objects1, good_objects2, good_objects3, good_objects4, good_objects5, good_objects6, bad_objects1,
            bad_objects2, slashes, line, scoreLabel, score = 0,
            points = [];
        var confettiEmitter, particleEmitter, glitterEmitter, swordCursor;

        var fireRate = 3000;
        var nextFire = 0;
        var selectedFruit = null;
        var gameTime = 60; // 60 seconds time limit
        var timerLabel;

        // Mapping of items to their points
        var itemPoints = {
            'good1': 10,
            'good2': 15,
            'good3': 100,
            'good4': 20,
            'good5': 25, // New object points
            'good6': 30, // New object points
            'bad1': -10,
            'bad2': -15,
        };

        const objectSize = 160;
        const berrySize = 250;

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

            scoreLabel = game.add.text(10, 10, 'Tip: get the green ones!');
            scoreLabel.fill = 'black'; // Change text color to black

            // Create timer label
            timerLabel = game.add.text(game.world.width - 130, 10, 'Time: ' + gameTime, {
                font: '32px Arial',
                weight: 'bold',
                fill: '#000'
            });

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
            berryBg.scale.setTo(1,1);
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
            swordCursor.anchor.setTo(0.5, 1.0); // Set anchor to the bottom center
            swordCursor.scale.setTo(0.070, 0.070);

            // Create glitter emitter
            glitterEmitter = game.add.emitter(0, 0, 100);
            glitterEmitter.makeParticles('glitter');
            glitterEmitter.gravity = 0;
            glitterEmitter.setYSpeed(-50, 50);
            glitterEmitter.setXSpeed(-50, 50);
            glitterEmitter.minParticleScale = 0.001;
            glitterEmitter.maxParticleScale = 0.02;
            glitterEmitter.start(false, 1000, 10);

            sliceSound = game.add.audio('sliceSound');
            special = game.add.audio('special');

            backgroundMusic = game.add.audio('backgroundMusic');
            backgroundMusic.loop = true;
            backgroundMusic.play();

            // Start the timer
            game.time.events.loop(Phaser.Timer.SECOND, updateTimer, this);

            throwObject();
        }

        function updateTimer() {
            gameTime--;
            timerLabel.text = 'Time: ' + gameTime;

            if (gameTime <= 0) {
                gameOver();
            }
        }

        function gameOver() {
            continueThrowing = false;
            backgroundMusic.stop();
            hideAllElementsExcept(null);
            stopAllElements();

            // Display Game Over text
            var gameOverText = game.add.text(game.world.centerX, game.world.centerY - 50, 'Game Over', {
                font: '64px Arial',
                fill: '#ff0000' // Red color
            });
            gameOverText.anchor.setTo(0.5, 0.5);

            // Display Restart button
            var restartButton = game.add.text(game.world.centerX, game.world.centerY + 50, 'Restart', {
                font: '32px Arial',
                fill: '#000' // Black color
            });
            restartButton.anchor.setTo(0.5, 0.5);
            restartButton.inputEnabled = true;
            restartButton.events.onInputDown.add(restartGame, this);
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

        function throwObject() {
            if (!continueThrowing) return; // Stop throwing objects if the flag is false

            if (game.time.now > nextFire && good_objects1.countDead() > 0 && good_objects2.countDead() > 0 && good_objects3
                .countDead() > 0 && good_objects4.countDead() > 0 && good_objects5.countDead() > 0 && good_objects6
                .countDead() > 0 && bad_objects1.countDead() > 0 && bad_objects2.countDead() > 0) {
                nextFire = game.time.now + fireRate;
                throwGoodObject(good_objects1);
                throwGoodObject(good_objects2);
                throwGoodObject(good_objects4);
                throwGoodObject(good_objects5);
                throwGoodObject(good_objects6);

                if (Math.random() > .6) {
                    throwGoodObject(good_objects3);
                }


                if (Math.random() > .4) {
                    throwBadObject(bad_objects1);
                    throwBadObject(bad_objects2);
                }
            }
        }

        function throwGoodObject(group) {
            var obj = group.getFirstDead();
            obj.reset(game.world.centerX + Math.random() * 200 - Math.random() * 200, game.world.height - 150);
            obj.anchor.setTo(0.5, 0.5);
            game.physics.arcade.moveToXY(obj, game.world.centerX + Math.random() * 400 - Math.random() * 400, game.world
                .centerY - Math.random() * 400, 530);
        }

        function throwBadObject(group) {
            var obj = group.getFirstDead();
            obj.reset(game.world.centerX + Math.random() * 200 - Math.random() * 200, game.world.height - 150);
            obj.anchor.setTo(0.5, 0.5);
            game.physics.arcade.moveToXY(obj, game.world.centerX + Math.random() * 400 - Math.random() * 400, game.world
                .centerY - Math.random() * 400, 530);
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

            slashes.clear();
            slashes.beginFill(0xFFFFFF, 1); // Set fill color to white
            slashes.moveTo(points[0].x, points[0].y);
            for (var i = 1; i < points.length; i++) {
                slashes.lineTo(points[i].x, points[i].y);
            }
            slashes.lineTo(points[points.length - 1].x + 5, points[points.length - 1].y + 5); // Create a pointy end
            slashes.lineTo(points[0].x + 5, points[0].y + 5); // Create a pointy start
            slashes.lineTo(points[0].x, points[0].y);
            slashes.endFill();

            for (var i = 1; i < points.length; i++) {
                line = new Phaser.Line(points[i].x, points[i].y, points[i - 1].x);
                game.debug.geom(line);

                good_objects1.forEachExists(checkIntersects);
                good_objects2.forEachExists(checkIntersects);
                good_objects3.forEachExists(checkIntersects);
                good_objects4.forEachExists(checkIntersects);
                good_objects5.forEachExists(checkIntersects);
                good_objects6.forEachExists(checkIntersects);
                bad_objects1.forEachExists(checkIntersects);
                bad_objects2.forEachExists(checkIntersects);
            }

            // Update sword cursor position
            swordCursor.x = game.input.x;
            swordCursor.y = game.input.y;

            // Update glitter emitter position
            glitterEmitter.x = game.input.x;
            glitterEmitter.y = game.input.y;

            //update the berryBg position
            if (continueThrowing === false) {
                berryBg.x = selectedFruit.x;
                berryBg.y = selectedFruit.y;
                berryBg.visible = true;
                berryBg.width += 5;
                berryBg.height += 5;
                // berryBg.angle += 1;
                game.world.sendToBack(berryBg);
                game.world.moveUp(berryBg);
            } else {
                berryBg.visible = false;
                berryBg.width = 0;
                berryBg.height = 0;
            }
        }

        var contactPoint = new Phaser.Point(0, 0);

        function checkIntersects(fruit, callback) {
            var l1 = new Phaser.Line(fruit.body.right - fruit.width, fruit.body.bottom - fruit.height, fruit.body.right,
                fruit.body.bottom);
            var l2 = new Phaser.Line(fruit.body.right - fruit.width, fruit.body.bottom, fruit.body.right, fruit.body
                .bottom - fruit.height);
            l2.angle = 90;

            if (Phaser.Line.intersects(line, l1, true) || Phaser.Line.intersects(line, l2, true)) {
                console.log('Intersection detected with:', fruit.key); // Add this line

                contactPoint.x = game.input.x;
                contactPoint.y = game.input.y;
                var distance = Phaser.Point.distance(contactPoint, new Phaser.Point(fruit.x, fruit.y));
                if (distance > 110) {
                    return;
                }

                if (continueThrowing) {
                    console.log('Killing fruit:', fruit.key); // Add this line
                    killFruit(fruit);
                }
            }
        }

        function resetScore() {
            var highscore = Math.max(score, localStorage.getItem("highscore"));
            localStorage.setItem("highscore", highscore);

            good_objects1.forEachExists(killFruit);
            good_objects2.forEachExists(killFruit);
            good_objects3.forEachExists(killFruit);
            good_objects4.forEachExists(killFruit);
            good_objects5.forEachExists(killFruit); // New object
            good_objects6.forEachExists(killFruit); // New object
            bad_objects1.forEachExists(killFruit);
            bad_objects2.forEachExists(killFruit);

            score = 0;
            scoreLabel.text = 'Game Over!\nHigh Score: ' + highscore;
        }

        function render() {}

        function popFruit(fruit) {
            particleEmitter.x = fruit.x;
            particleEmitter.y = fruit.y;
            particleEmitter.start(true, 2000, null, 4);
            confettiEmitter.x = fruit.x;
            confettiEmitter.y = fruit.y;
            confettiEmitter.start(true, 500, null, 30);

            var pointsValue = itemPoints[fruit.key];
            points = [];
            score += pointsValue;
            scoreLabel.text = 'Score: ' + score;

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
            fruit.scale.setTo(1, 1);
            fruit.kill();
            continueThrowing = true;
        }

        function killFruit(fruit) {
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
