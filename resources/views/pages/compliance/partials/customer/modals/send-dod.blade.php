<!-- Modal -->
<div id="sendDodModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <form action="/customer/{{ $customer->id }}/create-dod-form" id="send_dod_form" method="POST">
            {{ csrf_field() }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Send DOD to {{ $customer->name() }}</h4>
            </div>
            <div class="modal-body">
                <div id="deposits_list">
                  
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Send</button>
            </div>
        </form>
    </div>

  </div>
</div>