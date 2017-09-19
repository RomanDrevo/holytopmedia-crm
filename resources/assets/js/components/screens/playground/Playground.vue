<template>
    <div class="container-fluid">
        <div class="" id="sidebar">
            <div class="logo">
                <img v-if="(broker == 'ivory_option')" src="/images/logo_gold.png"/>
                <img style="max-width:85%" v-if="(broker == '72_option')" src="/images/72option_logo.png"/>
            </div>
            <clock></clock>

            <stats :channel="channel" :broker="broker"></stats>

        </div>
        <div id="main_table">
            <winners-board :channel="channel" :videos_src="videos_src" :lastWinners="lastWinners"></winners-board>
        </div>
    </div>
</template>

<script type="text/babel">
import Clock from './Clock.vue';
import WinnersBoard from './WinnersBoard.vue';
import Stats from './Stats.vue';
import Pusher from 'pusher-js';

export default {
    name: 'playground',

    props: {
        broker: {
            required: true
        },
        lastWinners: {
            required: false
        },
        videos_src:{
            required: false
        },

        appKey: {

            required: true
        }
    },

    data(){
        return {
            pusher: "",
            channel: ""
        };
    },

    created(){
        this.pusher = new Pusher(this.appKey, { encrypted: true });

        this.channel = this.pusher.subscribe(this.broker + 'playground_channel');
    },

    components: {
        Clock,
        WinnersBoard,
        Stats
    }
}
</script>

<style>
@import url(https://fonts.googleapis.com/css?family=Roboto);
body {
    font-family: 'Roboto', sans-serif;
}
#sidebar{
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
#main_table{
    width: 75%;
    float: left;
}
.logo {
    text-align: center;
}
.logo img {
    margin: 0px auto;
    width: 100%;
    margin-top: 31px;
    margin-bottom: 49px;
}

.well {
    min-height: 20px;
    padding: 10px;
    width: 90%;
    margin-bottom: 20px;
    margin: 0px auto;
    color: #fff;
    border: none;
    border-radius: 5px;
    border: #564949 1px solid;
    background: linear-gradient(27deg, #151515 5px, transparent 5px) 0 5px, linear-gradient(207deg, #151515 5px, transparent 5px) 10px 0px, linear-gradient(27deg, #222 5px, transparent 5px) 0px 10px, linear-gradient(207deg, #222 5px, transparent 5px) 10px 5px, linear-gradient(90deg, #1b1b1b 10px, transparent 10px), linear-gradient(#1d1d1d 25%, #1a1a1a 25%, #1a1a1a 50%, transparent 50%, transparent 75%, #242424 75%, #242424);
    background-color: #131313;
    background-size: 20px 20px;
}
.stats table tr {
    border-bottom: #525151 1px solid;
    font-size: 32px;
}
.stats { 
    margin-top: 67px; 
    margin-bottom: 450px; 
}
.stats table tr td:nth-child(1) { font-weight: bolder; }
.stats table tr td { padding: 7px; }
.stats table { width: 100%; }


.container-fluid{
    padding-left: 0px;
    padding-right: 0px;
}

</style>
