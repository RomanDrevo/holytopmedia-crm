$(function(){

	Jackpot.init();
	
});

var Jackpot = {
	init: function() {
        this.cacheDOM();
        this.bindEvents();
        this.initialSetup();
        this.subscribeToPusher();
        setDate();
        startTime();
        this.updateStats();
    },
    cacheDOM: function() {
        this.$notification = $('#notification');
        this.$table = $('#score_board');
    },
    bindEvents: function() {
        // Bind things here...  ex: this.$button.on('click', this.addMessage.bind(this));
    },
    initialSetup: function(){
    	this.setupAjax();
        this.setupCounter();
        this.setupSounds();
    },
    setupAjax: function(){
    	$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    },
    setupCounter: function(){
    	$('.counter').counterUp({delay: 10,time: 1000});
    },
    setupSounds: function(){

    	var thisObj = this;

    	ion.sound({
		    sounds: [
		        {name: "bell_ring"},
		        {name: "slots"},
		        {name: "applause"}
		    ],

		    // main config
		    path: "/sounds/",
		    preload: true,
		    multiplay: true,
		    volume: 0.9,
		    ended_callback: thisObj.soundCallback
		});
    },
    subscribeToPusher: function(){

        var thisObj = this;

    	var pusher = new Pusher('a55901f4fcbdd8c002f0', {
          encrypted: true
        });

        var channel = pusher.subscribe('deposits_channel');
        channel.bind('new_deposit', function(data) {
            ion.sound.play("bell_ring");
            thisObj.newIncomingDeposit(data);
            thisObj.updateStats();
        });
    },
    newIncomingDeposit: function(data){

        var table_type = (data.table.type == 1) ? "FTD" : "RST";

        var context  = {
            image: "rep-example.png",
            name: data.employee.name,
            desk: data.table.name,
            desk_type: table_type,
            time: data.created_at,
            amount: this.getCurrency(data.currency) + data.amount,
            amount_type: table_type
        };

        var source   = $("#entry-template").html();
        var template = Handlebars.compile(source);
        var html     = template(context);

        if(data.amount_plain > 100000)
            this.alertWinning(data.employee.name + " <br/> " + table_type + " <br/> " + this.getCurrency(data.currency) + data.amount, true);
        else
            this.alertWinning(data.employee.name + " <br/> " + table_type + " <br/> " + this.getCurrency(data.currency) + data.amount);
        
        var that = this;

        setTimeout(function(){
            $(html).prependTo(that.$table).addClass("tada animated");
        }, 12500);
        
    },
    getCurrency: function(currencyName){
        if(currencyName == "GBP")
            return "£";

        if(currencyName == "EUR")
            return "€";

        return "$";
    },
    updateStats: function(){
        $.post('/sales/get-playground-stats', {
            shouldUpdate: true
        }, function(data) {
            this.putStatsInHTML(data);
        }.bind(this));
    },
    alertWinning: function(text, is_big){
        if(is_big){
            $("#congrats_big").html(text);
            $("#video_big_money").get(0).play();
            $("#notification_big_money").fadeIn(600).css("display","inline-block").fadeOut(100).fadeIn(600).css("display","inline-block").fadeOut(100).fadeIn(600).css("display","inline-block").fadeOut(100).fadeIn(600).css("display","inline-block");
            setTimeout(function(){
                $("#notification_big_money").fadeOut(500);
            },10000);
        }else{
            ion.sound.play("slots");
            ion.sound.play("applause");
            $("#congrats").html(text);
            $("#notification").fadeIn(600).css("display","inline-block").fadeOut(100).fadeIn(600).css("display","inline-block").fadeOut(100).fadeIn(600).css("display","inline-block").fadeOut(100).fadeIn(600).css("display","inline-block");
        }
    },
    putStatsInHTML: function(data){
        $('#today_ftd').html(data.daily_ftd);
        $('#monthly_ftd').html(data.monthly_ftd);
        $('#today_deposits').html("$" + number_format(data.today_deposits));
        $('#monthly_deposits').html("$" + number_format(data.monthly_deposits));
    },
    soundCallback: function(sound){
    	if(sound.name == "applause"){
			$("#notification").fadeOut(500);
		}
    }
}


function startTime() {
    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    m = checkTime(m);
    s = checkTime(s);
    document.getElementById('live_clock').innerHTML =
    h + ":" + m + ":" + s;
    var t = setTimeout(startTime, 500);
}
function checkTime(i) {
    if (i < 10) {i = "0" + i};
    return i;
}

function number_format(number, decimals, decPoint, thousandsSep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
    var n = !isFinite(+number) ? 0 : +number
    var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
    var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
    var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
    var s = ''
    var toFixedFix = function(n, prec) {
            var k = Math.pow(10, prec)
            return '' + (Math.round(n * k) / k).toFixed(prec)
        }
        // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || ''
        s[1] += new Array(prec - s[1].length + 1).join('0')
    }
    return s.join(dec)
}

function currencyToNumber(numberString) {
    return Number(numberString.replace(/[^0-9\.]+/g, ""));
}

function setDate() {
    var month = new Array();
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

    var d = new Date();
    var n = month[d.getMonth()];
    document.getElementById("date_wrap").innerHTML = n + " " + new Date().getDate();
}

