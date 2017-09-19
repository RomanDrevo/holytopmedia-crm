<div class="panel panel-primary">
    <div class="panel-heading">Documents From Spot CRM</div>
    <div class="panel-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>File name</th>
                    <th>Created at</th>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody>
                @if(count($documents) < 1)
                    <tr>
                        <td colspan="3" class="text-center">No Documents available</td>
                    </tr>
                @else
                    @foreach($documents as $document)
                    <tr>
                        <td>{{ $document->filename }}</td>
                        <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $document->createdDate)->format('m-d-Y') }}</td>
                        <td>{{ $document->comment }}</td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">Signed Documents</div>
    <div class="panel-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>File name</th>
                    <th>Signed at</th>
                    <th>Type</th>
                    <th>Client IP</th>
                    <th>Approved?</th>
                    <th>Comments</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if(count($files) < 1)
                    <tr>
                        <td colspan="7" class="text-center">No Signed Documents available</td>
                    </tr>
                @else
                    @foreach($files as $file)
                    <tr>
                        <td>
                            <a class="fancybox" href="/public/documents/dod/{{ $file->name }}">{{ $file->name }}</a>
                        </td>
                        <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $file->created_at)->format('m-d-Y H:i:s') }}</td>
                        <td>{{ $file->type }}</td>
                        <td>{{ json_decode($file->signed_computer_data)->ip }}</td>
                        <td>{{ $file->approved ? 'YES' : 'NO' }}</td>
                        <td>{{ $file->comments }}</td>
                        <td>
                            <button class="btn btn-xs btn-success edit_comments" data-file-name="{{ $file->name }}" data-file-id="{{ $file->id }}" data-toggle="tooltip" data-placement="bottom" title="Comments"><i class="fa fa-sticky-note-o" aria-hidden="true"></i></button>
                            @if($file->approved)
                                <button class="btn btn-xs btn-danger approve_deny_file" data-action="deny" data-file-id="{{ $file->id }}" data-toggle="tooltip" data-placement="bottom" title="Deny"><i class="fa fa-power-off" aria-hidden="true"></i></button>
                            @else
                                <button class="btn btn-xs btn-success approve_deny_file" data-action="approve" data-file-id="{{ $file->id }}" data-toggle="tooltip" data-placement="bottom" title="Approve"><i class="fa fa-power-off" aria-hidden="true"></i></button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>