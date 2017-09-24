@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Add Vote</div>
                    <div class="panel-body">
                        <form action="/api/adm/vote" id="vote_ask" method="post">
                            <div class="form-group">
                                <label for="title" class="col-sm-2 col-form-label">question</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="text" id="v_title" name="v_title" placeholder="question">
                                </div>
                            </div>
                            <div class="col-sm-12"><hr></div>
                            <div class="col-sm-12 form-group">
                                <div class="col-sm-10">
                                    <div class="alert alert-info">
                                        <p> Choice (Require 2 Choices.)</p>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <button class="btn btn-primary" type="button" onclick="addModule()">ADD Choice</button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="vote" class="col-sm-2 col-form-label">Choice</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" id="choice_1" name="choice_1" placeholder="Choice">
                                </div>
                                <div class="col-sm-2">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">&nbsp;</div>
                                <label for="vote" class="col-sm-2 col-form-label">Choice</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" id="choice_2" name="choice_2" placeholder="Choice">
                                </div>
                                <div class="col-sm-2">
                                </div>
                            </div>
                            <div id="voteModule">
                            </div>
                            <div class="col-sm-12">
                                <hr>
                                <button class="btn btn-success" type="submit" id="sb" name="sb">ADD!</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- module -->
    <div class="form-group ch_0" id="ch_0" hidden>
        <div class="col-sm-12">&nbsp;</div>
        <label for="vote" class="col-sm-2">Choice</label>
        <div class="col-sm-8">
            <input class="form-control" type="text" id="choice_0" name="choice_0" placeholder="Choice">
        </div>
        <div class="col-sm-2">
            <button type="button" class="btn btn-default" onclick="delModule(this)"><span class="fa fa-times fa-lg"></span></button>
        </div>
    </div>
    <script>
        var count = 2;
        function addModule() {
            if(count === 9){
                alert('Choice is "MAX".');
                return;
            }
            count++;
            var ch = $('#ch_0').clone().attr('id', 'ch_'+ count);
            ch.find("#choice_0").attr(
                {
                    id: 'choice_'+ count,
                    name: 'choice_'+ count
                });
            ch.appendTo('#voteModule').show();
        }

       function delModule(dom) {
           if(count === 2){
               alert('Choice is "MIN".');
               return;
           }
           var ch = $(dom).parent().parent();
           ch.remove();
           count--;
           moduleOrder();
       }

       function moduleOrder(){
            var par = $('#voteModule').children();
            var ord = 3;
            par.each(function (i, mod) {
                $(mod).attr('id', 'ch_'+ ord);
                $(mod).find('[id^=choice]').attr({
                    id: 'choice_'+ ord,
                    name: 'choice_'+ ord
                });
                ord++;
            });
       }

       function choiceObject(){
           var data = {};
           for(var i=0;i<count;i++){
               var j = i + 1;
               var o = $('#choice_'+j).val();
               data[i] = {text: o};
           }

           return data;
       }

       function checkEmpty(){
           if($('#v_title').val() == '')
               return false;
           for(var i=0;i<count;i++){
               var j = i + 1;
               var o = $('#choice_'+j).val();
               if(o == '')
                   return false;
           }
           return true;
       }

        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#vote_ask').submit(function (event) {
                // HTMLでの送信をキャンセル
                event.preventDefault();

                // empty check.
                if(!checkEmpty()){
                    alert('Form EMPTY.');
                    return;
                }

                // 操作対象のフォーム要素を取得
                var $form = $(this);
                // 送信ボタンを取得
                // （後で使う: 二重送信を防止する。）
                var $button = $form.find('button');
                // 送信
                $.ajax({
                    url: $form.attr('action'),
                    type: $form.attr('method'),
                    dataType: 'json',
                    data: JSON.stringify({
                        title : $('#v_title').val(),
                        menu : choiceObject()
                    }),
                    timeout: 10000,  // 単位はミリ秒
                    // 送信前
                    beforeSend: function (xhr, settings) {
                        // ボタンを無効化し、二重送信を防止
                        $button.attr('disabled', true);
                    },
                    // 応答後
                    complete: function (xhr, textStatus) {
                        // ボタンを有効化し、再送信を許可
                        $button.attr('disabled', false);
                    },
                    // 通信成功時の処理
                    success: function (result, textStatus, xhr) {
                        alert('Success!: Create Vote');
                        location.href='/view/'+result['id'];
                    },
                    // 通信失敗時の処理
                    error: function (xhr, textStatus, error) {
                        alert('Error: Create Vote.');
                    }
                });
            });
        });


    </script>
@endsection
