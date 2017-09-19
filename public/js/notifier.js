Pusher.logToConsole = true;

ion.sound({
    sounds: [
        {name: "bell_ring"}
    ],

    // main config
    path: "/sounds/",
    preload: true,
    multiplay: true,
    volume: 0.9
});

// play sound

var pusher = new Pusher('a55901f4fcbdd8c002f0', {
  encrypted: true
});

var channel = pusher.subscribe('conversions_channel');
channel.bind('new_conversion', function(data) {
	ion.sound.play("bell_ring");
	$('.c_campaign').html("campaign number " + data.campaign + " (" + data.sub_campaign + ")");
	$('.c_name').html(data.name);
	$('.c_email').html(data.email);
	$("#intro").fadeIn("slow");
	setTimeout(function(){
		$("#intro").fadeOut("slow");
	},10000)
});

$("#intro").hide();