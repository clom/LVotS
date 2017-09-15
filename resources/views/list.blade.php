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
                        table = table + '<tr><td>'+data.title+'</td><td>'+data.vote_count+'</td><td><button class="btn btn-default" href="#">投票開始・終了</button><span>&nbsp;</span><button class="btn btn-primary" href="#">編集</button><span>&nbsp;</span><button class="btn btn-danger" href="#">削除</button></td></tr>';
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
                    var txt = '';
                    $.each(info, function (key, val) {
                        txt = '<a href="/view/' + val.id + '">' + val.title + '</a>';
                    });
                    $('#v_title').html(txt);
                },
                error:  function(XMLHttpRequest, textStatus, errorThrown){
                    var info = XMLHttpRequest.responseJSON;
                    var txt = '<a href="#">'+ info.msg +'</a>';
                    $('#v_title').html(txt);
                }
            });

        });
    </script>
@endsection
