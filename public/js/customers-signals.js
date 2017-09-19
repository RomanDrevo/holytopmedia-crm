var broker = $("#broker").val();


var pusher = new Pusher('a55901f4fcbdd8c002f0', {
  encrypted: true
});

var channel = pusher.subscribe('signals_channel');
channel.bind('new_customer', function(data) {
    console.log(data);
    var html = "<tr><td><a href='https://spotcrm." + data.broker + ".com/crm/customers/page/"+ data.customer_id +"'>"+ data.customer_id +"</a>";
        html += "</td><td>"+ data.name +"</td><td>"+ data.email +"</td>";
        html += "<td>"+ data.created_at +"</td></tr>";

    if(data.broker == broker || broker == "all"){
        $("#customersSignalsTable tbody").prepend(html);
      notifyMe();
    }
});


document.addEventListener('DOMContentLoaded', function () {
    if (Notification.permission !== "granted")
        Notification.requestPermission();
});

function notifyMe() {
    if (!Notification) {
        console.log('Desktop notifications not available in your browser. Try Chromium.'); 
        return;
    }

    if (Notification.permission !== "granted")
        Notification.requestPermission();
    else {
        var notification = new Notification('New Customer is watching signals', {
        icon: 'http://s33.postimg.org/da84wdbfj/Screen_Shot_2016_05_29_at_5_54_47_PM.png',
        body: "New customer just checked in for new signals!",
    });

    notification.onclick = function () {
        window.focus();   
    };

  }

}