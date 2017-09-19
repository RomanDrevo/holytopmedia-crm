<template>
    <div class="winner_wrap">
        <div class="col-md-2 winner_image">
                <img :src="imgSrc" class="img-circle" alt="representative"/>
            </div>
        <div class="col-md-6 winner_text">
                <div class="winner_info">
                    <table>
                        <tr>
                            <th colspan="2" class="name">{{info.name}}</th>
                        </tr>
                        <tr>
                            <td>Desk:</td>
                            <td>{{info.desk}}</td>
                        </tr>
                        <tr>
                            <td>TYPE:</td>
                            <td>{{ info.deskType == 1 ? 'FTD' : 'RST' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="winner_time">
                    <span>{{info.time}}</span>
                </div>
            </div>
            <div class="col-md-4 winner_amount">
                <div class="amount">{{ info.amountType + info.amount }}</div>
                <!--<div class="amount">${{ info.amountUSD }}</div>-->
                <div class="amount_type">{{ info.deskType == 1 ? 'FTD' : 'RST' }}</div>
            </div>
        </div>
</template>

<script type="text/babel">
export default {
    name: 'winner',
    props: ["info"],
    computed: {
        imgSrc(){
            if(this.info.photo){
                return '/app/' + this.info.photo;
            }
            return "/images/person.png";
        }
    }
}
</script>

<style scoped>
.roll-enter-active{
    animation: rollIn 1s forwards;
}
@keyframes rollIn {
  from {
    opacity: 0;
    -webkit-transform: translate3d(-100%, 0, 0) rotate3d(0, 0, 1, -120deg);
    transform: translate3d(-100%, 0, 0) rotate3d(0, 0, 1, -120deg);
  }
  to {
    opacity: 1;
    -webkit-transform: none;
    transform: none;
  }
}

.winner_wrap .col-md-2, .winner_wrap .col-md-6, .winner_wrap .col-md-4 {
    padding: 0px;
}
.winner_wrap {
    width: 98%;
    height: 200px;
    background: #CCCCCC;
    margin: 15px;
}
.col-md-2.winner_image {
    height: 100%;
    text-align: center;
}
.col-md-2.winner_image img {
    background: #fff;
    margin-top: 29px;
    height: 143px;
    width: 143px;
}
.winner_info table tr th {
    color: #1B7DD4;
    font-size: 33px;
}
.winner_info table {
    width: 354px;
    margin-top: 41px;
}
.winner_time {
    float: right;
    position: absolute;
    right: 21px;
    top: 50px;
    font-family: 'Roboto', sans-serif;
    font-size: 19px;
    font-weight: bolder;
}
.winner_amount .amount_type {
    font-size: 64px;
    margin-top: -8px;
}

.winner_amount .amount {
    font-size: 77px;
}
.winner_amount {
    background: #1B7DD4;
    color: #fff;
    height: 100%;
    text-align: center;
    padding-top: 10px !important;
}
</style>
