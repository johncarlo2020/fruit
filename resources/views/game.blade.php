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
</head>
<style>
    .game-page {
        width: 100%;
        height: 100vh;
        background-image: url('build/assets/images/Background.webp');
        background-size: cover;
        background-position: center;
        position: relative;
    }

    html,
    body {
        padding: 0;
        margin: 0;
        background: transparent;
    }

    div#game {
        width: 100%;
        height: 100%;
    }
</style>

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
        }, true); // The 'true' here sets the background to be transparent

        function preload() {
            this.load.image('toast', 'http://www.pngmart.com/files/5/Toast-PNG-Free-Download.png');
            this.load.image('burnt',
                'http://pluspng.com/img-png/burnt-food-png-the-first-incident-involved-toast-or-to-be-more-precise-burnt-toast-246.png'
            );
            this.load.image('toaster',
                'https://purepng.com/public/uploads/large/purepng.com-toastertoastertoast-makerelectric-smalltoast-sliced-breadheat-17015284328352zoyd.png'
            );
            this.load.image('good1', '{{ url('build/assets/images/orange.webp') }}');
            this.load.image('good2', '{{ url('build/assets/images/Pomegranate.webp') }}');
            this.load.image('bad1', '{{ url('build/assets/images/Rotten-peach.webp') }}');
            this.load.image('bad2', '{{ url('build/assets/images/Rotten-apple.webp') }}');
        }

        var good_objects1, good_objects2, bad_objects1, bad_objects2, slashes, line, scoreLabel, score = 0,
            points = [];

        var fireRate = 3000;
        var nextFire = 0;

        function create() {
            game.physics.startSystem(Phaser.Physics.ARCADE);
            game.physics.arcade.gravity.y = 100;

            good_objects1 = createGroup(4, 'good1');
            good_objects2 = createGroup(4, 'good2');
            bad_objects1 = createGroup(4, 'bad1');
            bad_objects2 = createGroup(4, 'bad2');

            slashes = game.add.graphics(0, 0);

            scoreLabel = game.add.text(10, 10, 'Tip: get the green ones!');
            scoreLabel.fill = 'white';

            emitter = game.add.emitter(0, 0, 300);
            emitter.makeParticles('parts');
            emitter.gravity = 300;
            emitter.setYSpeed(-400, 400);

            throwObject();
        }

        function createGroup(numItems, spriteKey) {
            var group = game.add.group();
            group.enableBody = true;
            group.physicsBodyType = Phaser.Physics.ARCADE;
            group.createMultiple(numItems, spriteKey);
            group.setAll('checkWorldBounds', true);
            group.setAll('outOfBoundsKill', true);
            return group;
        }

        function throwObject() {
            if (game.time.now > nextFire && good_objects1.countDead() > 0 && good_objects2.countDead() > 0 && bad_objects1
                .countDead() > 0 && bad_objects2.countDead() > 0) {
                nextFire = game.time.now + fireRate;
                throwGoodObject(good_objects1);
                throwGoodObject(good_objects2);
                if (Math.random() > .5) {
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
            slashes.beginFill(0xFFFFFF);
            slashes.alpha = .5;
            slashes.moveTo(points[0].x, points[0].y);
            for (var i = 1; i < points.length; i++) {
                slashes.lineTo(points[i].x, points[i].y);
            }
            slashes.endFill();

            for (var i = 1; i < points.length; i++) {
                line = new Phaser.Line(points[i].x, points[i].y, points[i - 1].x, points[i - 1].y);
                game.debug.geom(line);

                good_objects1.forEachExists(checkIntersects);
                good_objects2.forEachExists(checkIntersects);
                bad_objects1.forEachExists(checkIntersects);
                bad_objects2.forEachExists(checkIntersects);
            }
        }

        var contactPoint = new Phaser.Point(0, 0);

        function checkIntersects(fruit, callback) {
            var l1 = new Phaser.Line(fruit.body.right - fruit.width, fruit.body.bottom - fruit.height, fruit.body.right,
                fruit.body.bottom);
            var l2 = new Phaser.Line(fruit.body.right - fruit.width, fruit.body.bottom, fruit.body.right, fruit.body
                .bottom - fruit.height);
            l2.angle = 90;

            if (Phaser.Line.intersects(line, l1, true) ||
                Phaser.Line.intersects(line, l2, true)) {

                contactPoint.x = game.input.x;
                contactPoint.y = game.input.y;
                var distance = Phaser.Point.distance(contactPoint, new Phaser.Point(fruit.x, fruit.y));
                if (Phaser.Point.distance(contactPoint, new Phaser.Point(fruit.x, fruit.y)) > 110) {
                    return;
                }

                if (fruit.parent == good_objects1 || fruit.parent == good_objects2) {
                    killFruit(fruit);
                } else {
                    resetScore();
                }
            }

        }

        function resetScore() {
            var highscore = Math.max(score, localStorage.getItem("highscore"));
            localStorage.setItem("highscore", highscore);

            good_objects1.forEachExists(killFruit);
            good_objects2.forEachExists(killFruit);
            bad_objects1.forEachExists(killFruit);
            bad_objects2.forEachExists(killFruit);

            score = 0;
            scoreLabel.text = 'Game Over!\nHigh Score: ' + highscore;
        }

        function render() {}

        function killFruit(fruit) {
            emitter.x = fruit.x;
            emitter.y = fruit.y;
            emitter.start(true, 2000, null, 4);
            fruit.kill();
            points = [];
            score++;
            scoreLabel.text = 'Score: ' + score;
        }
    </script>
</body>

</html>
