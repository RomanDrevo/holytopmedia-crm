<template>
    <div class="time_wrapper">
        <div class="well">
            <div id="date_wrap">{{ date }}</div>
            <div id="live_clock">{{ time }}</div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'clock',
    data () {
        return {
            date: "",
            time: ""
        };
    },
    created(){
        this.setDate();
        this.setTime();
    },
    methods: {
        setDate(){
            let month = new Array();
            month[0] = "January";
            month[1] = "February";
            month[2] = "March";
            month[3] = "April";
            month[4] = "May";
            month[5] = "June";
            month[6] = "July";
            month[7] = "August";
            month[8] = "September";
            month[9] = "October";
            month[10] = "November";
            month[11] = "December";

            let d = new Date();
            let n = month[d.getMonth()];
            this.date = n + " " + new Date().getDate();
        },

        setTime(){
            var today = new Date();
            var h = today.getHours();
            var m = today.getMinutes();
            var s = today.getSeconds();
            m = this.checkTime(m);
            s = this.checkTime(s);
            this.time = h + ":" + m + ":" + s;
            var t = setTimeout(this.setTime, 500);
        },

        checkTime(i) {
            if (i < 10) {i = "0" + i};
            return i;
        }
    }
}
</script>

<style scoped>
div#date_wrap {
    text-align: center;
    font-size: 20px;
}

div#live_clock {
    text-align: center;
    font-size: 30px;
    font-family: 'Rboto', sans-serif;
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
</style>

