# Getting Started

Probably the easiest way to get familiar with the chess commands is by reading the WebSocket messages sent back and forth between the web browser and the chess server as you interact with a web app. To do this, you may want to use the [website](https://github.com/chesslablab/website) as shown in the example below.

![Figure 1](https://raw.githubusercontent.com/chesslablab/chess-server/main/docs/getting-started_01.png)

**Figure 1**. Open Google Chrome developer tools for command examples.

![Figure 2](https://raw.githubusercontent.com/chesslablab/chess-server/main/docs/getting-started_02.png)

**Figure 2**. As chess moves are played, the chess server response is displayed on the **Network > WS > Messages** tab.

Also a WebSocket connection with the chess server can be opened in the JavaScript console.

```js
const ws = new WebSocket('wss://async.chesslablab.org:8443');
```

That's it!

Now you're set up to start playing chess.

```js
ws.send('/start classical analysis');
```

The `/start` command starts a new classical chess game and retrieves a JSON response from the server.

```text
{
  "/start": {
    "variant": "classical",
    "mode": "analysis",
    "turn": "w",
    "movetext": "",
    "fen": [
      "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq -"
    ]
  }
}
```

On successful server response a FEN string representing the starting position is returned as well as the chess variant and the game mode. This is the classical starting position in FEN format.

Let's make the very first move.

What about 1.e4?

This is the so-called King's Pawn Game, one of the most popular chess openings, in Portable Game Notation (PGN) format. Humans can understand chess games in PGN easily but this format is not that great for computers and for graphic user interfaces (GUI) which often prefer the Long Algebraic Notation (LAN) format instead.

Let's play 1.e4 in LAN format.

```js
ws.send('/play_lan w e2e4');
```

The `/play_lan` command above retrieves the following JSON response.

```text
{
  "/play_lan": {
    "turn": "b",
    "pgn": "e4",
    "castlingAbility": "KQkq",
    "movetext": "1.e4",
    "fen": "rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq e3",
    "isCapture": false,
    "isCheck": false,
    "isMate": false,
    "isStalemate": false,
    "isFivefoldRepetition": false,
    "isFiftyMoveDraw": false,
    "isDeadPositionDraw": false,
    "doesDraw": false,
    "doesWin": false,
    "mode": "analysis",
    "variant": "classical",
    "isValid": true
  }
}
```

A popular response to 1.e4 is 1...e5 which in LAN format is e7e5.

```js
ws.send('/play_lan b e7e5');
```

Once again the `/play_lan` command makes a chess move, this time retrieving the following JSON response.

```text
{
  "/play_lan": {
    "turn": "w",
    "pgn": "e5",
    "castlingAbility": "KQkq",
    "movetext": "1.e4 e5",
    "fen": "rnbqkbnr/pppp1ppp/8/4p3/4P3/8/PPPP1PPP/RNBQKBNR w KQkq e6",
    "isCapture": false,
    "isCheck": false,
    "isMate": false,
    "isStalemate": false,
    "isFivefoldRepetition": false,
    "isFiftyMoveDraw": false,
    "isDeadPositionDraw": false,
    "doesDraw": false,
    "doesWin": false,
    "mode": "analysis",
    "variant": "classical",
    "isValid": true
  }
}
```

Let's recap.

Described below is the series of steps required to start a classical chess game with 1.e4 e5. Remember, computers and graphic user interfaces (GUIs) usually prefer the Long Algebraic Notation (LAN) format instead: e2e4 and e7e5.

```js
const ws = new WebSocket('wss://async.chesslablab.org:8443');
ws.send('/start classical analysis');
ws.send('/play_lan w e2e4');
ws.send('/play_lan b e7e5');
```

Now let's have a look at the WebSocket commands available! The list of commands could have been sorted in alphabetical order but it is more convenient to begin with the `/start` command and continue in a way that's easier to understand.
