<html>

<head>
    <title>应用名称 - @yield('title')</title>
    <script src="{{asset('/js/jquery.js')}}"></script>
</head>

<body>
    @section('sidebar')
    <p>
        <li>
            <a href="#">
                <i class="fa fa fa-bar-chart-o"></i>
                <span class="nav-label">用户管理</span>
                <span class="fa arrow"></span>
            </a>
            <ul class="nav nav-second-level">
                <li>
                    <a class="J_menuItem" href="{{url('admin/index')}}">用户列表</a>
                </li>
                <li>
                    <a class="J_menuItem" href="{{url('admin/adminlist')}}">添加新用户</a>
                </li>
            </ul>
        </li>
    </p>
    @show
    <div class="container">
        @yield('content')
    </div>
</body>

</html>
