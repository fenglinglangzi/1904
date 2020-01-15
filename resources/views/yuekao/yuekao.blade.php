<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="{{asset('/js/jquery.js')}}"></script>
</head>

<body>
    <button id="name">点击</button>
</body>

</html>
<script>
    $(document).on('click', '#name', function() {
        alert(11);
    })
</script>
