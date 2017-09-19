$(function() {
    $.fn.fixMe = function() {
        return this.each(function() {
            var $this = $(this),
                $t_fixed;

            function init() {
                $this.wrap('<div class="container-fluid" />');
                $t_fixed = $this.clone();
                $t_fixed.attr("id", "copy_" + $t_fixed.attr("id"));
                $t_fixed.find("tbody").remove().end().addClass("fixed").insertBefore($this);
                resizeFixed();
            }

            function resizeFixed() {
                $t_fixed.find("th").each(function(index) {
                    $(this).css("width", $this.find("th").eq(index).outerWidth() + "px");
                });
            }

            function scrollFixed() {
                var offset = $(this).scrollTop(),
                    tableOffsetTop = $this.offset().top,
                    tableOffsetBottom = tableOffsetTop + $this.height() - $this.find("thead").height();
                if (offset < tableOffsetTop || offset > tableOffsetBottom) $t_fixed.hide();
                else if (offset >= tableOffsetTop && offset <= tableOffsetBottom && $t_fixed.is(":hidden")) $t_fixed.show();
            }
            $(window).resize(resizeFixed);
            $(window).scroll(scrollFixed);
            init();
        });
    };
    ScoreboardRST.init();
});
var ScoreboardRST = {
    init: function() {
        this.cacheDOM();
        this.bindEvents();
        this.initialSetup();
        this.subscribeToPusher();
        setDate();
        startTime();
        //setInterval(this.checkForUpdates.bind(this), 10000);
    },
    cacheDOM: function() {
        this.$table = $('#score_board');
        this.$employees = $("tr[name^='employee_row_']");
        this.$table_id = $('#table_id').val();
    },
    bindEvents: function() {
        // Bind things here...  ex: this.$button.on('click', this.addMessage.bind(this));
    },
    initialSetup: function() {
        this.setupAjax();
        this.$table.fixMe();
        // this.setupCounter();
        this.setupSounds();
        //this.setupScroller();
    },
    setupAjax: function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    },
    setupScroller: function() {
        $("html, body").animate({
            scrollTop: $(document).height()
        }, 65000);
        setTimeout(function() {
            $('html, body').animate({
                scrollTop: 0
            }, 2000);
        }, 65000);
        setInterval(function() {
            $("html, body").animate({
                scrollTop: $(document).height()
            }, 65000);
            setTimeout(function() {
                $('html, body').animate({
                    scrollTop: 0
                }, 2000);
            }, 65000);
        }, 85000);
    },
    setupSounds: function() {
        var thisObj = this;
        ion.sound({
            sounds: [{
                name: "button_tiny"
            },{
                name: "bell_ring"
            }, {
                name: "slots"
            }, {
                name: "applause"
            }],
            // main config
            path: "/sounds/",
            preload: true,
            multiplay: true,
            volume: 0.9,
            ended_callback: thisObj.soundCallback
        });
    },
    subscribeToPusher: function() {
        var pusher = new Pusher('a55901f4fcbdd8c002f0', {
            encrypted: true
        });
        var channel = pusher.subscribe('scoreboard_channel');
        channel.bind('needs_to_update', function(data) {
            if(data.is_updated){
                ion.sound.play("button_tiny");
                this.checkForUpdates.bind(this)
            }
        }.bind(this));
    },
    checkForUpdates: function() {
        var thisObj = this;
        $.post('/sales/get-scoreboard-updates', {
            table_id: thisObj.$table_id
        }, function(data) {
            if (data == "no") {
                console.log("No deposits found");
            } else {
                thisObj.processNewData(data);
            }
        });
    },
    soundCallback: function(sound) {
        //   	if(sound.name == "applause"){
        // 	$("#notification").fadeOut(500);
        // }
        console.log(sound);
    },
    processNewData: function(data) {
        var thisObj = this;
        var hasNewDeposit = false;
        $.each(data, function(i, employee) {
            var employee_row = thisObj.$table.find("#employee_row_" + employee.id);
            employee_row.find(".employee_goal_daily").html("$" + number_format(employee.goal.daily));
            employee_row.find(".employee_goal_monthly").html("$" + number_format(employee.goal.monthly));

            var dailyField = employee_row.find(".employee_daily_deposit");
            var monthlyField = employee_row.find(".employee_monthly_deposit");

            if (currencyToNumber(dailyField.html()) != employee.dailyTotal && employee.dailyTotal != undefined) {
                hasNewDeposit = true;
                dailyField.html("$" + number_format(employee.dailyTotal));
            }

            if (currencyToNumber(monthlyField.html()) != employee.monthlyTotal && employee.monthlyTotal != undefined) {
                hasNewDeposit = true;
                monthlyField.html("$" + number_format(employee.monthlyTotal));
            }
        });
        if (hasNewDeposit) {
            sortData('score_board', 3);
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
    document.getElementById('live_clock').innerHTML = h + ":" + m + ":" + s;
    var t = setTimeout(startTime, 500);
}

function checkTime(i) {
    if (i < 10) {
        i = "0" + i
    };
    return i;
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

function sortData(table_id, sortColumn){
    var tableData = document.getElementById(table_id).getElementsByTagName('tbody').item(0);
    var rowData = tableData.getElementsByTagName('tr');            
    for(var i = 0; i < rowData.length - 1; i++){
        for(var j = 0; j < rowData.length - (i + 1); j++){
            if(Number(rowData.item(j).getElementsByTagName('td').item(sortColumn).innerHTML.replace(/[^0-9\.]+/g, "")) < Number(rowData.item(j+1).getElementsByTagName('td').item(sortColumn).innerHTML.replace(/[^0-9\.]+/g, ""))){
        		tableData.insertBefore(rowData.item(j+1),rowData.item(j));
    		}
	    }
	}

	for(var i = 0; i < rowData.length ; i++){
		rowData.item(i).getElementsByTagName('td').item(0).innerHTML = i + 1;
	}
}	
