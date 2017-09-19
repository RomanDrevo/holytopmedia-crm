<div class="panel panel-primary">
    <div class="panel-heading">Comments</div>
    <div class="panel-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="text-center">User</th>
                    <th class="text-center">Created at</th>
                    <th class="text-center">Comment</th>
                </tr>
            </thead>
            <tbody>
                @if(count($customer->comments))
                    @foreach($customer->comments as $comment)
                    <tr>
                        <td>{{ $comment->user->name }}</td>
                        <td>{{ $comment->created_at->format('m-d-Y H:i A') }}</td>
                        <td>{{ $comment->content }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3">No comments...</td>
                    </tr>
                @endif
            </tbody>
        </table>
        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
        <textarea name="content" id="content" class="form-control" placeholder="Write your comment here..."></textarea>
    </div>
</div>