<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance — SSFO eLog</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(160deg, #111827 0%, #1f2937 100%);
            color: #f8fafc;
            padding: 1.5rem;
            overflow: hidden;
        }
        .container {
            width: 100%;
            max-width: 600px;
            text-align: center;
        }
        h1 { font-size: 2rem; font-weight: 800; margin: 0 0 0.5rem; text-shadow: 0 2px 4px rgba(0,0,0,0.5); }
        p { margin: 0 0 2rem; line-height: 1.6; color: #94a3b8; font-size: 1rem; }
        
        .game-container {
            position: relative;
            width: 100%;
            height: 200px;
            background: #cbd5e1;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), inset 0 2px 4px rgba(255,255,255,0.3);
            border: 4px solid #475569;
        }
        canvas {
            display: block;
            width: 100%;
            height: 100%;
        }
        .score-board {
            position: absolute;
            top: 10px;
            right: 15px;
            font-family: monospace;
            font-size: 1.25rem;
            font-weight: bold;
            color: #334155;
        }
        .start-prompt {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(15, 23, 42, 0.8);
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1rem;
            pointer-events: none;
            transition: opacity 0.2s;
        }
        .hidden { opacity: 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>We'll be back shortly!</h1>
        <p>The SSFO eLog is currently undergoing scheduled maintenance. While you wait, try to beat your high score!</p>
        
        <div class="game-container">
            <div class="score-board">Score: <span id="score">0</span> | HI: <span id="hi-score">0</span></div>
            <div id="start-prompt" class="start-prompt">Press SPACE to Jump / Start</div>
            <canvas id="gameCanvas" width="600" height="200"></canvas>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');
        const scoreEl = document.getElementById('score');
        const hiScoreEl = document.getElementById('hi-score');
        const promptEl = document.getElementById('start-prompt');

        let isPlaying = false;
        let isGameOver = false;
        let score = 0;
        let hiScore = localStorage.getItem('dinoHiScore') || 0;
        let gameSpeed = 3.5;
        let frame = 0;

        hiScoreEl.innerText = hiScore;

        const dino = {
            x: 50,
            y: 144,
            w: 30,
            h: 30,
            dy: 0,
            jumpForce: 12,
            grounded: true,
            gravity: 0.45,
            draw() {
                ctx.save();
                ctx.scale(-1, 1);
                ctx.font = '28px sans-serif';
                ctx.fillText('🦖', -this.x - 26, this.y + 24);
                ctx.restore();
            },
            jump() {
                if (this.grounded) {
                    this.dy = -this.jumpForce;
                    this.grounded = false;
                }
            },
            update() {
                this.y += this.dy;
                if (this.y + this.h < 174) {
                    this.dy += this.gravity;
                    this.grounded = false;
                } else {
                    this.dy = 0;
                    this.grounded = true;
                    this.y = 174 - this.h;
                }
                this.draw();
            }
        };

        let obstacles = [];

        class Obstacle {
            constructor() {
                this.h = 30;
                this.w = 20;
                this.x = canvas.width;
                this.y = 174 - this.h;
            }
            draw() {
                ctx.font = '28px sans-serif';
                ctx.fillText('🌵', this.x - 5, this.y + 24);
            }
            update() {
                this.x -= gameSpeed;
                this.draw();
            }
        }

        function drawGround() {
            ctx.fillStyle = '#94a3b8';
            ctx.fillRect(0, 174, canvas.width, 26);
            
            // Draw some moving ground details
            ctx.fillStyle = '#64748b';
            for(let i=0; i<10; i++) {
                let gx = ((frame * gameSpeed + i * 60) % (canvas.width + 60)) - 60;
                ctx.fillRect(canvas.width - gx, 180 + (i%3)*5, 15, 3);
            }
        }

        function init() {
            obstacles = [];
            score = 0;
            gameSpeed = 3.5;
            isGameOver = false;
            dino.y = 144;
            dino.dy = 0;
            promptEl.classList.add('hidden');
        }

        function handleObstacles() {
            if (frame % 90 === 0 || (frame % 60 === 0 && Math.random() > 0.5)) {
                obstacles.push(new Obstacle());
            }

            for (let i = 0; i < obstacles.length; i++) {
                obstacles[i].update();

                // Collision Detection
                if (
                    dino.x < obstacles[i].x + obstacles[i].w &&
                    dino.x + dino.w > obstacles[i].x &&
                    dino.y < obstacles[i].y + obstacles[i].h &&
                    dino.y + dino.h > obstacles[i].y
                ) {
                    isGameOver = true;
                }

                // Remove off-screen obstacles
                if (obstacles[i].x + obstacles[i].w < 0) {
                    obstacles.splice(i, 1);
                    i--;
                }
            }
        }

        function animate() {
            if (isGameOver) {
                promptEl.innerText = "Game Over! Press SPACE to Restart";
                promptEl.classList.remove('hidden');
                isPlaying = false;
                if (score > hiScore) {
                    hiScore = score;
                    localStorage.setItem('dinoHiScore', hiScore);
                    hiScoreEl.innerText = hiScore;
                }
                return;
            }

            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            drawGround();
            dino.update();
            handleObstacles();

            score++;
            if (score % 10 === 0) scoreEl.innerText = Math.floor(score / 10);
            if (score % 1000 === 0) gameSpeed += 0.2;

            frame++;
            requestAnimationFrame(animate);
        }

        // Draw initial state
        ctx.fillStyle = '#cbd5e1';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        drawGround();
        dino.draw();

        window.addEventListener('keydown', (e) => {
            if (e.code === 'Space' || e.code === 'ArrowUp') {
                if (!isPlaying) {
                    init();
                    isPlaying = true;
                    animate();
                } else {
                    dino.jump();
                }
            }
        });
        
        // Touch support for mobile users
        window.addEventListener('touchstart', (e) => {
            if (!isPlaying) {
                init();
                isPlaying = true;
                animate();
            } else {
                dino.jump();
            }
        });
    </script>
</body>
</html>
