const pongGame = {
    playerPaddle: document.getElementById("player"),
    computerPaddle: document.getElementById("computer"),
    ball: document.getElementById("ball"),
    playerY: 50,
    computerY: 50,
    ballX: 50,
    ballY: 50,
    ballSpeedX: 2,
    ballSpeedY: 2,
    paddleSpeed: 4,
    gameWidth: 800,
    gameHeight: 400,

    moveBall() {
        this.ballX += this.ballSpeedX;
        this.ballY += this.ballSpeedY;

        if (this.ballY <= 0 || this.ballY >= this.gameHeight - 15) {
            this.ballSpeedY = -this.ballSpeedY;
        }

        if (this.ballX <= 20 && this.ballY >= this.playerY && this.ballY <= this.playerY + 100) {
            this.ballSpeedX = -this.ballSpeedX;
        }

        if (this.ballX >= this.gameWidth - 35 && this.ballY >= this.computerY && this.ballY <= this.computerY + 100) {
            this.ballSpeedX = -this.ballSpeedX;
        }

        if (this.ballX <= 0 || this.ballX >= this.gameWidth) {
            this.resetBall();
        }

        this.updateBallPosition();
    },

    movePaddles() {
        window.addEventListener("mousemove", (event) => {
            this.playerY = event.clientY - 50;
            if (this.playerY < 0) this.playerY = 0;
            if (this.playerY > this.gameHeight - 100) this.playerY = this.gameHeight - 100;
        });

        this.computerY = this.ballY - 50;
        if (this.computerY < 0) this.computerY = 0;
        if (this.computerY > this.gameHeight - 100) this.computerY = this.gameHeight - 100;
    },

    resetBall() {
        this.ballX = this.gameWidth / 2 - 7;
        this.ballY = this.gameHeight / 2 - 7;
        this.ballSpeedX = -this.ballSpeedX;
    },

    updateBallPosition() {
        this.ball.style.left = `${this.ballX}px`;
        this.ball.style.top = `${this.ballY}px`;
    },

    updatePaddlePositions() {
        this.playerPaddle.style.top = `${this.playerY}px`;
        this.computerPaddle.style.top = `${this.computerY}px`;
    },

    gameLoop() {
        this.moveBall();
        this.movePaddles();
        this.updatePaddlePositions();
        requestAnimationFrame(this.gameLoop.bind(this));
    },

    startGame() {
        this.gameLoop();
    }
};

pongGame.startGame();
