@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Dashboard</div>

                    <div class="panel-body">
                        Welcome to LVoS Service!
                        <hr>
                        <div class="alert alert-info">
                            <p>Now Voting: <span id="v_title"></span> </p>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                            <th>Title</th>
                            <th style="width: 15%">Vote Count</th>
                            </thead>
                            <tbody id="listVote"></tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $.ajax({
                type: 'get',
                url: '/api/list/vote',
                dataType: 'json',
                success: function (data) {
                    var table = '';
                    data.forEach(function(data){
                        table = table + '<tr><td><a href="/view/' + data.id + '">'+data.title+'</a></td><td>'+data.vote_count+'</td></tr>';
                        $('#listVote').html(table);
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
    </script>
@endsection