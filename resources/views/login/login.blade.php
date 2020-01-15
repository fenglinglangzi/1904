<!DOCTYPE html>



<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>登陆</title>
    <script src="{{asset('/js/jquery.js')}}"></script>
</head>
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<body>
    <form action="{{url('login/login_do')}}" method="POST">
        用户名:<input type="text" name="name"><br>
        密码: <input type="password" name="pwd"><br>
        <input type="submit" value="登陆">
    </form>
    <a href="{{url('/login/create_do')}}">点击微信扫码登陆</a>
</body>

</html>
<!-- <script>
    $(document).on('click', "#deng", function() {
        var name = $('[name="user_name"]').val();
        var pwd = $('[name="user_pwd"]').val();
        var gongyao = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwA0q6qu6lPOGLZ8FzxSnHXNEi0jaB259zSniIr/E+2yMdUcJf/K84luaHjGBnMBip8kEv5U4EqkVO//Kj8hEet0BNC5yeJmoYgkPpZnuTTXDoKD2N/52RCmyM5xiMFRX4Yi7HsDCbXKLagOaNE2+gdyrEVO1sUEf8Rv9u4tOcLo5BMrwyIRJLZ9xQCPY70VZ9gvffa41dEPe8GRsfBqgcHnL6X84J0QgVjvu8RsgdswRgA89YnslPzR3DGcDA1xxgBJhEPIQxnBCPRGlh0l9QZQf+c5IFrHs+Mfvtwa/YZU/1xz1bf1qiOVoai8HDcjJ2+UJEgMX/J8NeMGgb6OTcwIDAQAB";
        $.ajax({
            url: "http://api.dongpengyuan.com/api/login",
            type: "post",
            data: {
                name: name,
                pwd: pwd,
                gongyao: gongyao
            },
            dataType: "json",
            success: function(res) {
                alert(res.msg);
                if (res.code == 200) {
                    location.href = "http://admin.dongpengyuan.com/admin/index";
                }
            }
        })
    })
</script>
 -->
