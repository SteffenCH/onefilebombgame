<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Playing with bombs</title>
    <script src="https://unpkg.com/vue"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://unpkg.com/vue-router/dist/vue-router.js"></script>
    <script src="http://cdn.peerjs.com/0.3/peer.js" ></script>
</head>
<style>
    .unit {
        background-color: white;
        height: 35px;
        width: 35px;
        margin: 1px;
        float: left;
    }

    .bombMan {
        background-size: cover;
        background-image: url("https://i.pinimg.com/236x/ae/59/60/ae5960a1c0f1559a753e90410f4e3700--bomberman-game-nintendo.jpg");
        height: 100%;
    }

    .bombManTwo {
        background-size: cover;
        background-image: url("http://www.gifmania.co.uk/Video-Games-Animated-Gifs/Animated-Classic-Arcade-Video-Games/Bomberman/Bomberman-Standing-73389.gif");
        height: 100%;
    }

    .healthbar {
        width: 200px;
        border: 1px solid black;
        background-color: red;
    }

    .healthbar > .one {
        height: 20px;
        background-color: green;
    }

    .healthbar > .two {
        height: 20px;
        background-color: green;
    }

    .bomb {
        background-size: cover;
        background-image: url("https://cdn3.iconfinder.com/data/icons/flat-icons-web/40/TNT-512.png");
        height: 35px;
        width: 35px;
    }

    .bombUnit {
        background-color: red;
    }

    .terrain {
        background-color: chocolate;
    }

    .healthbars {
        margin-left: 44%;
    }
</style>
<body>

<div id="vue-game">
    <router-link to="/">
        <button class="btn btn-primary" v-on:click="$root.generateGrid($root.gameSizeX, $root.gameSizeY)">Høme</button>
    </router-link>
    <router-link to="/highscore">
        <button class="btn btn-primary">Highscore</button>
    </router-link>
    <router-view></router-view>
</div>
<script>
    var gamePage = {template: `
<div>
    <div class="healthbars">
        player 1:
        <div class="healthbar">
            <div class="one" v-bind:style="'width:' + $root.healthOne + '%'"></div>
        </div>
        <br>
        player 2:
        <div class="healthbar ">
            <div class="two" v-bind:style="'width:' + $root.healthTwo + '%'"></div>
        </div>
    </div>
    <br>
    <center>
        <div id="container"></div>
        <br/>
        <button class="btn btn-success" v-on:click="$root.spawn">Start game</button>
        </div>
        </center>
</div>
    `};
    var highscore = {template: `
 <div>
    {{$root.healthOne}}
 </div> `};

    var routes = [
        {path: '/', component: gamePage},
        {path: "/highscore", component: highscore}
    ];

    var router = new VueRouter({routes: routes});
    const app = new Vue({
        el: '#vue-game',
        router: router,
        data: {
            healthOne: 100,
            healthTwo: 100,
            player1: ".bombMan",
            player2: ".bombManTwo",
            gameSizeX: 15,
            gameSizeY: 17,
            remotePeerJsId: "kekker",
            localPeerJsId: "kekistan",
        },
        mounted: function () {
            this.generateGrid(this.gameSizeX, this.gameSizeY);
        },
        methods: {
            generateGrid: function (xIn, yIn) {
                $(function () {
                    for (var x = 0; x < xIn; x++) {
                        for (var y = 0; y < yIn; y++) {
                            if (Math.abs(y % 2) == 1 && Math.abs(x % 2) == 1) {
                                $("<div>").addClass("unit terrain").appendTo('#container');
                            }
                            else {
                                $("<div>").addClass("unit").appendTo('#container');
                            }
                        }
                    }
                    var styles = {
                        'background-color': "black",
                        'height': (xIn * 37) + 'px',
                        'width': (yIn * 37) + 'px'
                    };
                    $('#container').css(styles);
                });
            },
            spawn: function () {
                var $divelement = $('.unit');
                $('<div>').addClass("bombMan").attr("id", "0").appendTo($divelement.first());
                $('<div>').addClass("bombManTwo").attr("id", Number(this.gameSizeY * this.gameSizeX - 1).toString()).appendTo($divelement.last());
                this.startKeyboardListener();
            },
            startKeyboardListener: function () {
                document.addEventListener('keydown', function (event) {
                    switch (event.keyCode) {
                        // bomb player 1
                        case 13:
                            console.log("place bomb");
                            app.bombHandler(app.getCurrentPos(app.player1));
                            break;
                        // bomb player2
                        case 32:
                            console.log("place bomb");
                            app.bombHandler(app.getCurrentPos(app.player2));
                            break;
                        // player 1
                        case 37:
                            console.log("left");
                            app.move(app.player1, -1);
                            break;
                        case 38:
                            console.log("up");
                            app.move(app.player1, -app.gameSizeY);
                            break;
                        case 39:
                            console.log("right");
                            app.move(app.player1, +1);
                            break;
                        case 40:
                            console.log("down");
                            app.move(app.player1, app.gameSizeY);
                            break;
                        // player 2
                        case 65:
                            console.log("left");
                            app.move(app.player2, -1);
                            break;
                        case 87:
                            ;
                            app
                            console.log("up");
                            app.move(app.player2, -app.gameSizeY);
                            break;
                        case 68:
                            console.log("right");
                            app.move(app.player2, +1);
                            break;
                        case 83:
                            console.log("down");
                            app.move(app.player2, app.gameSizeY);
                            break;
                    }
                });
            },
            move: function (player, move) {
                var $elements = $('.unit');
                var currentPos = this.getCurrentPos(player);
                var newPos = Number(currentPos + move);
                if (!$($elements[newPos]).hasClass("terrain") && !$($elements[newPos]).hasClass("bomb")) {
                    $(player).appendTo($elements[newPos]);
                    console.log(newPos.toString());
                    $(player).attr("id", newPos.toString());
                }
                else {
                    console.log(player + ": Is walking into the terrain")
                }
            },
            bombHandler: function (position) {

                var gridElement = $(".unit");
                $(gridElement[position]).addClass("bomb").delay(1800).queue(function (next) {
                    var y = 0;
                    for (x = 0; x < 3; x++) {
                        $(gridElement[position + x]).addClass("bombUnit");
                        $(gridElement[position - x]).addClass("bombUnit");
                        $(gridElement[position + y]).addClass("bombUnit");
                        $(gridElement[position - y]).addClass("bombUnit");
                        y += app.gameSizeY;
                    }
                    if ($(".bombMan").parent().hasClass("bombUnit")) {
                        app.healthOne -= 25;
                    }
                    if ($(".bombManTwo").parent().hasClass("bombUnit")) {
                        app.healthTwo -= 25;
                    }
                    $(this).removeClass("bomb").delay(1000).queue(function () {
                        var y = 0;
                        for (x = 0; x < 3; x++) {
                            $(gridElement[position + x]).removeClass("bombUnit");
                            $(gridElement[position - x]).removeClass("bombUnit");
                            $(gridElement[position + y]).removeClass("bombUnit");
                            $(gridElement[position - y]).removeClass("bombUnit");
                            y += app.gameSizeY;
                        }
                        next();
                    });
                    next();
                })
            },
            getCurrentPos: function (player) {
                return parseInt($(player).attr('id'));
            },
            peerJsHandler: function () {
                var peer = new Peer(app.localPeerJsId, {key: '9f7uxt6pp23dte29'});
                var conn = peer.connect(app.remotePeerJsId);
                console.log(conn);
                conn.on('open', function () {
                    conn.send('hi!');
                });

                peer.on('connection', function (conn) {
                    conn.on('data', function (data) {
                        // Will print 'hi!'
                        console.log(data);
                    });
                });
                return conn;
            },

            // funktion
            jsonWrite: function () {
                // request url
                fetch("http://127.0.0.1:3000/highscores", {
                    // data til vores request
                    data: {id: 1},
                    // methode til vores request, fx GET PUT POST osv osv
                    method: "POST",
                    //Headers som definere hvilken datatype vi afsender, kan også indeholde andre informationer.
                    header: {
                        "Content-Type": "application/json"
                    }
                })
            }
        }
    })
</script>
</body>
</html>