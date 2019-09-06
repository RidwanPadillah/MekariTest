<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Todo List</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <style>
        label.error{
            font-size: 10px;
            color: red;
        }
        input.error{
            border-color: red;
        }
        #todoList{
            margin-top: 10px;
        }
        #todoList .list span{
            display: none;
            position: absolute;
            right: 0;
            font-size: 15px;
            cursor: pointer;
        }
        #todoList .list label{
            background: #F0F0F0;
            display: block;
            padding: 10px 20px;
        }
        #todoList .list:nth-child(even) label {
            background: #CCCCCC;
        }
        .top-notif{
            background: red;
            color: white;
            padding: 10px;
            text-align: center;
            position: absolute;
            width: 100%;
            top: 0;
            left: 0;
            display: none;
        }
        .top-notif.success{
            background: green;
        }
        .container{
            margin-top: 70px;
        }
        </style>
    </head>
    <body>
        <div class="top-notif">
            Test
        </div>
        <div class="container">
            <h2>Todo List</h2>
            <form action="#" id="formTodoList">
                <div class="row">
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="name" placeholder="Input your task here">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-block btn-info">Submit</button>
                    </div>
                </div>
            </form>
            <div class="row" id="todoList">
                
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script>
            function setNotification(message, status) {
                var notifElement = $('.top-notif');
                notifElement.removeClass('success');
                if (status != undefined) {
                    notifElement.addClass(status);
                }
                notifElement.html(message);
                notifElement.slideDown();
            }
            var todoList = {
                'formElement': '#formTodoList',
                'contentElement': '#todoList',
                'contentListElement': "#todoList .list",
                'contentListDeleteElement': "#todoList .list span",
                'errorHandler': function errorHandler(errors) {
                    if (errors.responseJSON.errors != undefined) {
                        $.each(errors.responseJSON.errors, function (indexInArray, valueOfElement) { 
                            var element = $(todoList.formElement+" [name='"+indexInArray+"']");
                            element.addClass('error');
                            $('<label class="error">'+valueOfElement+'</label>').insertAfter(element);
                        });
                    }
                    if (errors.responseJSON.message != undefined) {
                        setNotification(errors.responseJSON.message);
                    }
                },
                'getList': function(){
                    $.get("{{ route('tasks.index') }}",
                        function (data, textStatus, jqXHR) {
                            $.each(data.data, function (indexInArray, valueOfElement) { 
                                 todoList.renderList(valueOfElement);
                            });
                        }
                    );
                },
                'renderList': function(data){
                    $('<div class="col-md-12 list">'+
                        '<label>'+data.name+'<span data-id="'+data.id+'">&times;</span></label>'+
                    '</div>').prependTo(todoList.contentElement);
                },
                'submit': function(){
                    $.post("{{ route('tasks.store') }}", $(todoList.formElement).serialize(),
                        function (data, textStatus, jqXHR) {
                            $(todoList.formElement).trigger("reset");
                            todoList.renderList(data.data);
                            setNotification(data.message, 'success');
                        }
                    ).fail(function (response) {
                        todoList.errorHandler(response);
                    });
                },
                'delete': function(element){
                    if (confirm('Are you sure to delete this data?')) {
                        $.post("{{ route('tasks.store') }}/" + element.data('id'), {'_method': 'DELETE'},
                            function (data, textStatus, jqXHR) {
                                element.parents('.list').remove();
                                setNotification(data.message, 'success');
                            }
                        ).fail(function (response) {
                            todoList.errorHandler(response);
                        });
                    }
                }
            }
            function resetError() {
                $(".error").removeClass('error');
            }
            
            $(function(){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
        
                $(document).delegate(todoList.contentListElement, "mouseover", function(){
                    $(this).find('span').stop().fadeIn();
                });
                
                $(document).delegate(todoList.contentListElement, "mouseleave", function(){
                    $(this).find('span').stop().fadeOut();
                });
                
                $(todoList.formElement).submit(function(e){
                    e.preventDefault();
                    resetError();
                    todoList.submit();
                });

                todoList.getList();

                $(document).delegate(todoList.contentListDeleteElement, 'click', function(){
                    todoList.delete($(this));
                });
            });
        </script>
    </body>
</html>
