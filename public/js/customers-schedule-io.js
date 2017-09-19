$(function(){

  $('body').on('click', '.view_customer', function(e){
    var customer_id = $(this).data('customer-id');
    $(this).parent().parent().removeClass('not_clicked');

    $.post('/update-schedule-clicked-io', {customer_id: customer_id}, function(data){
      if(data == "error"){
        alert("error has occured please refresh the page.");
      }else{
        console.log(data);
      }
    });
  });

});

//Pusher.logToConsole = true;


var pusher = new Pusher('a55901f4fcbdd8c002f0', {
  encrypted: true
});

var channel = pusher.subscribe('signals_channel');
channel.bind('new_schedule_io', function(data) {
	var broker = $("#user_broker").val();
	if(data.broker == broker || broker == "all"){
		  var html = "<tr class='not_clicked'><td><a data-customer-id='"+data.db_id+"'  class='view_customer' href='https://spotcrm."+data.broker+".com/crm/customers/page/"+data.customer_id+"' target='_blank'>"+data.customer_id+"</a></td><td>"+data.broker+"</td><td>"+data.phone+"</td><td>"+data.timeframe+"</td><td>"+data.country_code+"</td><td>"+data.campaign_name+"</td><td>"+data.email_campaign_name+"</td><td>NO</td><td>"+data.created_at+"</td></tr>";
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
    var notification = new Notification('A call has been scheduled!', {
      icon: 'http://s33.postimg.org/js09io4an/small_ivory.png',
      body: "New customer just scheduled a call in Ivory Option",
    });

    notification.onclick = function () {
      window.focus();    
    };

  }

}