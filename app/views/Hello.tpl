<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <!-- 引入 layui.css -->
    <link rel="stylesheet" href="//unpkg.com/layui@2.6.8/dist/css/layui.css">
</head>
<body>

<ul class="layui-nav">
    <li class="layui-nav-item"><a href="">藏青导航</a></li>
    <li class="layui-nav-item layui-this"><a href="">选中</a></li>
    <li class="layui-nav-item">
        <a href="javascript:;">常规</a>
    </li>
    <li class="layui-nav-item">
        <a href="">带徽章<span class="layui-badge">9</span></a>
    </li>
    <li class="layui-nav-item">
        <a href="">小圆点<span class="layui-badge-dot"></span></a>
    </li>
    <li class="layui-nav-item"><a href="">导航</a></li>
    <li class="layui-nav-item">
        <a href="javascript:;">子级</a>
        <dl class="layui-nav-child">
            <dd><a href="">菜单1</a></dd>
            <dd><a href="">菜单2</a></dd>
            <dd><a href="">菜单3</a></dd>
        </dl>
    </li>
    <li class="layui-nav-item">
        <a href="javascript:;">选项</a>
        <dl class="layui-nav-child">
            <dd><a href="">选项1</a></dd>
            <dd class="layui-this"><a href="">选项2</a></dd>
            <dd><a href="">选项3</a></dd>
        </dl>
    </li>
    <li class="layui-nav-item" lay-unselect="">
        <a href="javascript:;"><img src="//t.cn/RCzsdCq" class="layui-nav-img"></a>
        <dl class="layui-nav-child">
            <dd><a href="javascript:;">子级菜单</a></dd>
            <dd><a href="javascript:;">横线隔断</a></dd>
            <hr>
            <dd style="text-align: center;"><a href="">退出</a></dd>
        </dl>
    </li>
    <li class="layui-nav-item"><a href="">演示</a></li>
</ul>
<div style="width: 100%">
    <ul class="layui-nav layui-nav-tree layui-inline" lay-filter="demo" style="margin-right: 10px;">
        <li class="layui-nav-item layui-nav-itemed">
            <a href="javascript:;">默认展开</a>
            <dl class="layui-nav-child">
                <dd><a href="javascript:;">选项一</a></dd>
                <dd><a href="javascript:;">选项二</a></dd>
                <dd><a href="javascript:;">选项三</a></dd>
                <dd><a href="">跳转项</a></dd>
            </dl>
        </li>
        <li class="layui-nav-item layui-nav-itemed">
            <a href="javascript:;">解决方案</a>
            <dl class="layui-nav-child">
                <dd><a href="">移动模块</a></dd>
                <dd><a href="">后台模版</a></dd>
                <dd><a href="">电商平台</a></dd>
            </dl>
        </li>
        <li class="layui-nav-item"><a href="">云市场</a></li>
        <li class="layui-nav-item"><a href="">社区</a></li>
    </ul>
</div>


<!-- 引入 layui.js -->
<script src="//unpkg.com/layui@2.6.8/dist/layui.js"></script>
<script>
    layui.use(['layer', 'form'], function(){
        var layer = layui.layer
            ,form = layui.form;

        layer.msg('Hello World');
    });
</script>
</body>
</html>