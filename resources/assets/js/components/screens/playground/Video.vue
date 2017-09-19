<template>
    <transition name="fade" id="video">
        <div style="border:2px solid blue;">
            <video v-show="winner.intAmount <= 10000" :src=" '/app/' + videos_src.small_deposit.option_value" width="100%" height="340" autoplay style="border: 1px solid red;" loop></video>

            <video muted v-show="winner.intAmount > 10000" :src=" '/app/' + videos_src.big_deposit.option_value" width="100%" height="340" autoplay style="border: 1px solid red;" loop></video>
            <div id="Message">
                <h1>
                    <div id="congrats" v-if="winner">
                        <div>
                            {{ winner.name }}
                        </div>
                        <div id="winner-amount">
                            <!--${{ winner.amountUSD }}-->
                            {{ winner.amountType + winner.amount }}
                        </div>
                        <div>
                            <img :src="imgSrc">
                        </div>
                    </div>
                </h1>
            </div>
        </div>
    </transition>
</template>

<script type="text/babel">
    export default{
        name: 'app-video',
        props:{
            winner: {
                required: true
            },

            src: {
                required: false
            },

            videos_src:{
                required: false
            }
        },
        computed: {
            imgSrc(){
                if(this.winner.photo){
                    return '/app/' + this.winner.photo;
                }
                return "/images/person.png";
            }
        }
    }
</script>

<style>
      #notification,
    #notification_big_money {
        height: 360px;
        position: fixed;
        z-index: 1;
        text-align: center;
        width: 100%;
        margin: 0px auto;
    /*    margin-top: 150px;*/
    }

    #Video, #Message {
        width: 100%;
        height: 360px;
        position: absolute;
        top: 0;
        left: -200px;
    }
    video {
        width: 100%;
    }

    #winner-amount {
        margin-top: 58px;
        font-weight: 800;
    }

    #Message {
        text-shadow: 2px 2px 2px #000000;
        color: white;
        z-index:100;
    }

    #Message h1 {
        font-size: 10em;
        color: #FFEB3B;
        text-align: center;
        font-family: cursive;
        text-shadow: 4px 4px #000;
        margin-top: 73px;
    }


     .fade-leave-active{
         transition: opacity 1s;
         opacity: 0;
     }


    video {
        position: fixed; right: 0; bottom: 0;
        min-width: 100%; min-height: 100%;
        width: auto; height: auto; z-index: 100;
    }


</style>
