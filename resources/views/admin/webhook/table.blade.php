<div class="table-responsive">
    <table id="basic-datatables" class="table display table-striped table-hover">
        <thead>
            <tr>
                <th>STT</th>
                <th>OA ID</th>
                <th>Tên OA</th>
                <th>Tên</th>
                <th>User Id</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($messages as $index => $message)
                <tr>
                    <td>{{ $messages->firstItem() + $index }}</td>
                    <td>{{ $message->oa_id}}</td>
                    <td>{{ $message->referencedOa ?  $message->referencedOa->name : '' }}</td>
                    <td>{{ $message->name }}</td>
                    <td>{{ $message->user_id }}</td>

                </tr>
            @endforeach
        </tbody>
    </table>
</div>
