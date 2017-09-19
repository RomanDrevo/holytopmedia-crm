<!-- Modal -->
<div id="fileCommentsModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <form action="/customers/{{ $customer->id }}/edit-comments" id="edit_comments" method="POST">
            {{ csrf_field() }}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Comments on file: <span class="file_name"></span></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="file_id" id="file_id" value="" />
                <textarea class="form-control" name="comments" id="comments"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </form>
    </div>

  </div>
</div>