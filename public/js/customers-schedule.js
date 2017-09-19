$(function(){

  $('body').on('click', '.view_customer', function(e){
    var customer_id = $(this).data('customer-id');
    $(this).parent().parent().removeClass('not_clicked');

    $.post('/update-schedule-clicked', {customer_id: customer_id}, function(data){
      console.log(data);
      if(data == "error"){
        alert("error has occured please refresh the page.");
      }else{
        console.log(data);
      }
    });
  });

});


var pusher = new Pusher('a55901f4fcbdd8c002f0', {
  encrypted: true
});

var channel = pusher.subscribe('signals_channel');
channel.bind('new_schedule', function(data) {
	var broker = $("#user_broker").val();
	if(data.broker == broker || broker == "all"){
		  var html = "<tr class='not_clicked'><td><a data-customer-id='"+data.db_id+"'  class='view_customer' href='https://spotcrm."+data.broker+".com/crm/customers/page/"+data.customer_id+"' target='_blank'>"+data.customer_id+"</a></td><td>"+data.broker+"</td><td>"+data.phone+"</td><td>"+data.timeframe+"</td><td>"+data.country_code+"</td><td>NO</td><td>"+data.created_at+"</td></tr>";
		  $('#customersSignalsTable').fadeOut(500);
	    $('#customersSignalsTable tbody').prepend(html);
	    $('#customersSignalsTable').fadeIn(500);
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
      body: "New customer just register for a callback!",
    });

    notification.onclick = function () {
      window.focus();     
    };

  }

}