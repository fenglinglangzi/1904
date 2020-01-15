<!DOCTYPE html>


<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

</head>

<body>
    <p>扫描下方二维码登陆</p>
    <img src="{{$url}}">
</body>

</html>
<script src="{{asset('/js/jquery.js')}}"></script>
<script>
    var status = "{{$status}}";
    var _time = setInterval(check, 3000);

    function check() {
        $.ajax({
            type: "post",
            url: "{{url('/login/check')}}",
            data: {
                status: status
            },
            dataType: "json",
            success: function(res) {
                if (res.code == 1) {
                    alert(res.msg);
                    clearInterval(_time);
                }
            }
        });
    }
</script>
