@extends('layouts.app')

@section('content')
    <div id="fakeLoader"></div>
    <div class="container">
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">Vote List</div>

                <div class="panel-body">
                    <div class="alert alert-info">
                        <p>Now Voting: <span id="v_title"></span> </p>
                    </div>
                    <table class="table table-bordered" >
                        <thead>
                            <th>Title</th>
                            <th style="width: 15%;">Vote Count</th>
                            <th style="width: 40%;">Action</th>
                        </thead>
                        <tbody id="listvote"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            // List
            $.ajax({
                type: 'get',
                url: '/api/list/vote',
                dataType: 'json',
                success: function (data) {
                    var table = '';
                    data.forEach(function(data){
                        table = table + '<tr><td><a href="/view/'+ data.id +'">'+data.title+'</a></td><td>'+data.vote_count+'</td><td><button class="btn btn-default" onclick="changeVote(\''+ data.id+'\')">投票開始・終了</button><span>&nbsp;</span><a type="button" class="btn btn-primary" href="/edit/'+data.id+'">編集</a><span>&nbsp;</span><button class="btn btn-danger" onclick="deleteVote(\''+ data.id+'\')">削除</button></td></tr>';
                        $('#listvote').html(table);
                    });
                }
            });

            // Now Voting.
            $.ajax({
                type: 'get',
                url: '/api/nowvote',
                dataType: 'json',
                success: function (data) {
                    var info = data.info;
                    var txt = '<a href="/view/' + info.id + '">' + info.title + '</a>';
                    $('#v_title').html(txt);
                },
                error:  function(XMLHttpRequest, textStatus, errorThrown){
                    var info = XMLHttpRequest.responseJSON;
                    var txt = '<a href="#">'+ info.msg +'</a>';
                    $('#v_title').html(txt);
                }
            });

        });

        function changeVote(v_id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '/api/adm/switch/vote',
                type: 'put',
                dataType: 'json',
                data: JSON.stringify({id: v_id}),
                timeout: 10000,  // 単位はミリ秒
                success: function (data) {
                    alert('Update Done!');
                    location.reload();
                }
            });
        }

        function deleteVote(v_id) {
            if(!confirm('you will this vote Delete. are you sure?'))
                return;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '/api/adm/vote/'+ v_id,
                type: 'delete',
                timeout: 10000,  // 単位はミリ秒
                success: function (data) {
                    alert('Delete Done!');
                    location.reload();
                }
            });

        }

    </script>
@endsection
