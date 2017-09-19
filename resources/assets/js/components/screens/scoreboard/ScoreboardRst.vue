<template>
    <div class="container-fluid rst-container">
        <div ref="top"></div>
        <div class="playground_header"></div>
        <div class="" id="sidebar">
            <div class="logo">
                <img v-if="(broker == 'ivory_option')" src="/images/logo_gold.png"/>
                <img style="max-width:85%" v-if="(broker == '72_option')" src="/images/72option_logo.png"/>
            </div>
            <clock></clock>
            <h2>${{ formattedNumber(tableStats.dailyTotal) }}</h2>
            <stats :table="tableStats"></stats>
        </div>
        <div id="main_table">
            <table id="score_board">
                <thead>
                <tr>
                    <th style="padding-left:20px;">&#8470;</th>
                    <th>Agent</th>
                    <th>Today</th>
                    <th>Monthly Total</th>
                    <th>Left To Goal</th>
                    <th>Goal</th>
                </tr>
                </thead>
                <tbody>

                <tr v-for="(employee, index) in allEmployees">
                    <td style="padding-left:20px;" class="employee_position">{{ index + 1 }}</td>
                    <td class="employee_name">{{ employee.employee.name }}</td>
                    <td class="employee_daily_total">${{ formattedNumber(employee.daily) }}</td>
                    <td class="employee_monthly_total">${{ formattedNumber(employee.monthly) }}</td>
                    <td class="employee_goal_daily">${{ leftToGoal(employee.monthly, employee.monthly_goal) }}</td>
                    <td class="employee_goal_monthly">${{ formattedNumber(employee.monthly_goal) }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!--video-->
        <div v-if="lastDeposit" v-show="showNotification" id="notification">
            <app-video :videos_src="videos_src" :winner="lastDeposit"></app-video>
        </div>
        <div class="clearfix"></div>
        <div ref="bottom"></div>
    </div>
</template>

<script type="text/babel">
    import Clock from './Clock.vue';
    import Stats from './Stats.vue';
    import {numberFormat} from '../../../helpers/functions';
    import Pusher from 'pusher-js';
    import Video from './Video.vue';
    require('howler');

    export default {
        name: 'scoreboard-rst',

        props: {

            employees: {
                required: true
            },
            table: {
                required: true
            },
            broker: {
                required: true
            },
            app_key: {
                required: true
            },
            videos_src: {
                required: false
            }
        },

        data() {
            return {
                allEmployees: [],
                tableStats : this.table,
                pusher: "",
                channel: "",
                sound: {},
                pending: [],
                lastDeposit: {},
                showNotification: false,
                isPlaying: false,
                top: true
            }
        },

        created()   {
            //employees to array and sort
            this.allEmployees = Object.values(this.employees);
            this.sortEmployees();

            //update employees and table stats every 30 secs
            setInterval(this.updateStats, 30 * 1000);

            this.pusher = new Pusher(this.app_key, {encrypted: true});
            this.channel = this.pusher.subscribe(this.broker + 'playground_channel');

            this.registerSounds();
            this.registerToPusher();
        },

        methods: {
            topOrBottom(){
                if (this.top) {
                    this.top = false;
                    this.doScrolling.bind(null, this.$refs.top, 30000)();
                } else {
                    this.top = true;
                    this.doScrolling.bind(null, this.$refs.bottom, 30000)();
                }
            },

            getElementY(elem) {
                return window.pageYOffset + elem.getBoundingClientRect().top
            },
            doScrolling(element, duration) {
                var vm = this;
                //Get current location
                var startingY = window.pageYOffset;
                var elementY = this.getElementY(element)
                // If element is close to page's bottom then window will scroll only to some position above the element.
                var targetY = document.body.scrollHeight - elementY < window.innerHeight ? document.body.scrollHeight - window.innerHeight : elementY
                var diff = targetY - startingY
                // Easing function: easeInOutCubic
                // From: https://gist.github.com/gre/1650294
                var easing = (t) => {
                    return t < .5 ? 4 * t * t * t : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1
                }
                var start;

                if (!diff) return

                // Bootstrap our animation - it will get called right before next frame shall be rendered.
                window.requestAnimationFrame(function step(timestamp) {
                    if (!start) start = timestamp
                    // Elapsed miliseconds since start of scrolling.
                    var time = timestamp - start
                    // Get percent of completion in range [0, 1].
                    var percent = Math.min(time / duration, 1)
                    // Apply the easing.
                    // It can cause bad-looking slow frames in browser performance tool, so be careful.
                    percent = easing(percent)

                    window.scrollTo(0, startingY + diff * percent)

                    // Proceed with animation as long as we wanted it to.
                    if (time < duration) {
                        window.requestAnimationFrame(step)
                    } else {
                        vm.topOrBottom();
                    }
                })
            },

            registerSounds(){
                this.sound = new Howl({
                    src: ['/sounds/bell_ring.mp3']
                });
            },
            registerToPusher(){
                this.channel.bind('deposit', (data) => {
                    // if(data.desk == this.table.name) {
                    this.pending.unshift(data);
                    this.playWinner();
                    // }
                });
            },
            playSounds(amount){
                if (amount <= 1000) {
                    this.sound.play();
                } else if (amount <= 5000) {
                    let count = 5;
                    let interval = setInterval(() => {
                        if (count <= 1)
                            clearInterval(interval);

                        this.sound.play();
                        count--;
                    }, 500);
                } else {
                    let count = 15;
                    let interval = setInterval(() => {
                        if (count <= 1)
                            clearInterval(interval);

                        this.sound.play();
                        count--;
                    }, 500);
                }
            },
            playWinner(){
                if (!this.isPlaying) {
                    this.isPlaying = true;

                    //display the video
                    this.showNotification = true;

                    this.lastDeposit = this.pending.pop();
                    this.playSounds(this.lastDeposit.intAmount);

                    let VideoTime = (this.lastDeposit.intAmount <= 10000) ? 5000 : 10000;

                    setTimeout(()=> {
                        this.showNotification = false;

                        setTimeout(() => {
                            this.isPlaying = false;
                            if (this.pending.length > 0) {
                                this.playWinner();
                            }
                        }, 3000);

                    }, VideoTime);

                }
            },

            updateStats() {
                Vue.http.get('/screens/scoreboard/get-stats/' + this.table.id).then((response) => {
                    this.tableStats = response.body.table;
                    this.allEmployees = Object.values(response.body.employees);
                    this.sortEmployees();
                });
            },

            formattedNumber(num) {
                return numberFormat(num);
            },

            sortEmployees(){
                this.allEmployees.sort(this.compare)
            },

            compare(a, b) {
                if (a.monthly < b.monthly)
                    return 1;
                if (a.monthly > b.monthly)
                    return -1;
                return 0;
            },

            leftToGoal(total, goal){
                if((goal - total) < 0){
                    return "0";
                }

                return this.formattedNumber(goal - total);
            },
            getDocumentHeight() {
                let body = document.body;
                return body.clientHeight;
            },
        },
        computed: {
            screenHeight() {
                return window.screen.height;
            }
        },

        mounted() {
            this.documentHeigth = this.getDocumentHeight();
            if (this.documentHeigth > this.screenHeight) {
                this.doScrolling.bind(null, this.$refs.bottom, 30000)();
            }
        },


        components: {
            Clock,
            Stats,
            appVideo: Video
        }
    }
</script>

<style scoped>
 @import url(https://fonts.googleapis.com/css?family=Roboto);
body {
    font-family: 'Roboto', sans-serif;
}
.rst-container {
    background-color: black;
}
.container-fluid {
    padding-right: 0px;
    padding-left: 0px;
}

#sidebar{
    position: fixed;
    width: 25%;
    float: left;
    background:
    radial-gradient(black 15%, transparent 16%) 0 0,
    radial-gradient(black 15%, transparent 16%) 8px 8px,
    radial-gradient(rgba(255,255,255,.1) 15%, transparent 20%) 0 1px,
    radial-gradient(rgba(255,255,255,.1) 15%, transparent 20%) 8px 9px;
    background-color:#282828;
    background-size:16px 16px;
    color: #fff;
    padding: 10px;
}

#sidebar h2 {
    text-align: center;
    font-size: 80px;
}

div#main_table {
    float: right;
    width: 75%;
    margin-top: 35px;
}
table#score_board thead,
table#copy_score_board thead {
    background: #E8E8E8;
}
#score_board,
#copy_score_board {
    height: 86%;
    width: 100%;
    bottom: 0;
    left: 0;
    right: 0;
    border: 1px solid;
    overflow-y: auto;
    background: #000;
}
table#score_board tr td,
table#copy_score_board tr td{
    height: 80px;
    font-size: 34px;
    text-align: center;
}
table#score_board thead tr th:nth-child(1),
table#copy_score_board thead tr th:nth-child(1){
    width: 20px;
}
table#score_board tbody tr{
    border-bottom: #fff 1px solid;
}
table#score_board tbody tr:nth-child(3){
    border-bottom: red 1px solid;
}
table#score_board tr th,
table#copy_score_board tr th {
    height: 58px;
    text-align: center;
    font-size: 28px;
}

#score_board tbody{
    background-repeat: no-repeat;
    color: #000;
}
.playground_header .well {
    position: absolute;
    right: 18px;
    top: 5px;
    height: 74px;
    width: 200px;
    font-size: 19px;
    padding-top: 7px;
}
.fixed{
  top:87px;
  position:fixed;
  width:auto;
  display:none;
  border:none;
}
table#score_board tbody tr td:nth-child(1),
table#score_board tbody tr td:nth-child(2) {
    color: #fff;
}
table#score_board tbody tr td:nth-child(3),
table#score_board tbody tr td:nth-child(4),
table#score_board tbody tr td:nth-child(5),
table#score_board tbody tr td:nth-child(6),
table#score_board tbody tr td:nth-child(7)
{
    color: #6AB8DC;
}


.playground_header img {
    width: 400px;
    margin: 0px auto;
}
.playground_header {
    position: fixed;
    height: 30px;
    width: 100%;
    /* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#4c4c4c+0,595959+12,666666+25,474747+39,2c2c2c+50,000000+51,111111+60,2b2b2b+76,1c1c1c+91,131313+100;Black+Gloss+%231 */
background: #4c4c4c; /* Old browsers */
background: -moz-linear-gradient(top,  #4c4c4c 0%, #595959 12%, #666666 25%, #474747 39%, #2c2c2c 50%, #000000 51%, #111111 60%, #2b2b2b 76%, #1c1c1c 91%, #131313 100%); /* FF3.6-15 */
background: -webkit-linear-gradient(top,  #4c4c4c 0%,#595959 12%,#666666 25%,#474747 39%,#2c2c2c 50%,#000000 51%,#111111 60%,#2b2b2b 76%,#1c1c1c 91%,#131313 100%); /* Chrome10-25,Safari5.1-6 */
background: linear-gradient(to bottom,  #4c4c4c 0%,#595959 12%,#666666 25%,#474747 39%,#2c2c2c 50%,#000000 51%,#111111 60%,#2b2b2b 76%,#1c1c1c 91%,#131313 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#4c4c4c', endColorstr='#131313',GradientType=0 ); /* IE6-9 */
}
.stats {
    margin-top: 28px;
}

#score_board tr {
   font-size: 27px;
}

</style>
