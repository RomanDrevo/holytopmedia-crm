<template>
        <div id="score_board">
            <div v-if="lastWinner" v-show="showNotification" id="notification">
                <div id="Video">
                    <app-video :videos_src="videos_src" :winner="lastWinner"></app-video>
                </div>
            </div>
            <winner v-for="(winner, index) in winners" :class="winner.animation" :info="winner"></winner>
        </div>
</template>

<script type="text/babel">

import Pusher from 'pusher-js';
import Winner from './Winner.vue';
import Video from './Video.vue';

require('howler');

export default {
    name: 'winners-board',
    props: {
        channel: {
            required: true
        },
        lastWinners: {
            required: false
        },

        videos_src:{
            required: false
        }
    },
    created(){
        _.map(this.lastWinners, (winner) => {
            this.winners.push(winner);
        });

        this.registerSounds();
        this.registerToPusher();
    },
    data () {
        return {
            sound: {},
            winners: [],
            lastWinner: "",
            pending: [],
            showNotification:false,
            isPlaying: false
        };
    },
    components: {
        Winner,
        appVideo: Video
    },
    methods: {
        registerSounds(){
            this.sound = new Howl({
                src: ['/sounds/bell_ring.mp3']
            });
        },
        registerToPusher(){

            this.channel.bind('deposit', (data) => {
                this.pending.unshift(data);
                this.playWinner();
            });
        },
        playSounds(amount){
            if(amount <= 1000){
                 this.sound.play();
            } else if(amount <= 5000){
                let count = 5;
                let interval = setInterval(() =>{
                    if(count <= 1)
                        clearInterval(interval);
                    
                    this.sound.play();
                    count--;
                }, 500);
            } else {
                let count = 15;
                let interval = setInterval(() =>{
                    if(count <= 1)
                        clearInterval(interval);

                    this.sound.play();
                    count--;
                }, 500);
            }
        },

        playWinner(){
            if(!this.isPlaying){
                this.isPlaying = true;

                //display the video
                this.showNotification = true;

                //play the sound * 5
                 this.lastWinner = this.pending.pop();
                 this.playSounds( this.lastWinner.intAmount );

                 let VideoTime = (this.lastWinner.intAmount <= 10000) ? 5000 : 10000 ;
                 let winnerAnimationTime = (this.lastWinner.intAmount <= 10000) ? 7000 : 12000 ;

                 this.winners.map((winner) => {
                      winner.animation = "";
                 });

                 //hide video after 5 seconds
                 setTimeout(()=>{ this.showNotification = false; }, VideoTime);

                 setTimeout(()=>{

                       this.winners.unshift(this.lastWinner);
                       setTimeout(() => {
                            this.isPlaying = false;
                            if(this.pending.length > 0){
                                  this.playWinner();
                            }
                        }, 1000);
                  }, winnerAnimationTime);
            }
        }
    }
}
</script>

<style scoped>



div#date_wrap {
    text-align: center;
    font-size: 24px;
}

div#live_clock {
    text-align: center;
    font-size: 43px;
    font-family: 'Rboto', sans-serif;
}

</style>

