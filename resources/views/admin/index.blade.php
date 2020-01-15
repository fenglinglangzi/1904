@extends('layouts.app')
@section('title', '后台')
@section('sidebar')
@parent

@endsection
@section('content')
<table border=1>
    <tr>
        <td>用户id</td>
        <td>用户名称</td>
        <td>是否锁定</td>
    </tr>
    @foreach($data as $k=>$v)
    <tr user_id="{{$v['user_id']}}">
        <td>{{$v['user_id']}}</td>
        <td>{{$v['user_name']}}</td>
        <td> <a href="javascript:;" class="suo">{{$v['is_suo']==1 ? '未锁定' : '已锁定'}}</a> </td>
    </tr>
    @endforeach
</table>
<script>
    $(document).on('click', ".suo", function() {
        var suo = $(this).text();
        var user_id = $(this).parents('tr').attr('user_id');

        $.ajax({
            url: "{{url('admin/suo')}}",
            type: "post",
            data: {
                user_id: user_id
            },
            dataType: "json",
            success: function(res) {
                if (res.code == 200) {
                    location.href = "http://admin.dongpengyuan.com/admin/index";
                }
            }
        })
    })
</script>

@endsection
