@extends('layouts.app')

@section('content')
    <div id="fakeLoader"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Vote</div>

                    <div class="panel-body">
                        <h3>Question: <span id="v_title"></span></h3>
                        <hr>
                        <div id="menuList"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="menu_0" class="menu_0" hidden>
        <div class="col-md-2">
            <label id="q_0">Ans1</label>
        </div>
        <div class="col-md-10">
            <div class="progress">
                <span id="pg_0" class="progress-bar bg-success" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></span>
            </div>
        </div>
    </div>

    <script>
        // Loader!
        $('#fakeLoader').fakeLoader();

        var menuCount = 0;
        voteInit();

        // VoteData
        function voteInit() {
            $.ajax({
                type: 'get',
                url: '/api/vote/{{ $id }}',
                dataType: 'json',
                success: function (data) {
                    var info = data.info;
                    var menu = data.menu;
                    var answer = data.ansCount;
                    $('#v_title').text(info.title);
                    menu.forEach(function (no) {
                        menuCount++;
                        var avg = no.vote_count / answer * 100
                        md = $('#menu_0').clone().attr({id: 'menu_'+menuCount });
                        md.find('#q_0').attr({id: 'q_'+menuCount }).text(no.text);
                        md.find('#pg_0').attr({id: 'pg_'+menuCount}).css('width', avg+'%');
                        md.appendTo('#menuList').show();
                    });
                    $('#fakeLoader').fadeOut();
                },
                error:  function(XMLHttpRequest, textStatus, errorThrown){
                }
            });
        }

        function voteUpdate() {
            $.ajax({
                type: 'get',
                url: '/api/vote/{{ $id }}',
                dataType: 'json',
                success: function (data) {
                    var menu = data.menu;
                    var answer = data.ansCount;
                    menu.forEach(function (no) {
                        var avg = no.vote_count / answer * 100
                        $('#pg_'+no.no).css('width', avg+'%');
                    });
                },
                error:  function(XMLHttpRequest, textStatus, errorThrown){
                }
            });
        }

        setInterval(voteUpdate, 5000);

    </script>
@endsection
