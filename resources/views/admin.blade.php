@extends('layouts.app')

@section('content')
    <div id="fakeLoader"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Admin Dashboard</div>

                    <div class="panel-body">
                        <table class="table table-bordered" >
                            <thead>
                            <th>Username</th>
                            <th>Status</th>
                            <th style="width: 40%;">Action</th>
                            </thead>
                            <tbody id="userlist"></tbody>
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
                url: '/api/adm/user',
                dataType: 'json',
                success: function (data) {
                    var table = '';
                    data.forEach(function (data) {
                        var adm = (data.adm == 1? "Admin" : "User");
                        table = table + '<tr><td>' + data.name + '</td><td>'+ adm +'</td><td><button class="btn btn-warning" onclick="changeAdmin(' + data.id + ')">Change Admin</button></td></tr>';
                        $('#userlist').html(table);
                    });
                }
            });
        });

        function changeAdmin(uid) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '/api/adm/switch/adm',
                type: 'put',
                dataType: 'json',
                data: JSON.stringify({id: uid}),
                timeout: 10000,  // 単位はミリ秒
                success: function (data) {
                    alert('Update Done!');
                    location.reload();
                }
            });
        }
    </script>
@endsection
