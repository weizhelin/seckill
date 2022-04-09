<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Brand</a>
    </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <{if $userinfo.role_id lt 3}>
        <li class="dropdown <{if in_array($controller,['web','indite','config','js','statistics'])}>active<{/if}>">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">系统设置<span class="caret"></span></a>
          <ul class="dropdown-menu">
              <li><a href="/index.php?m=master&c=web"  target="_self">网站列表</a></li>
              <!--li><a href="/index.php?m=master&c=indite"  target="_self">创作类型</a></li-->
              <li class="hiddenonoff"><a href="/index.php?m=master&c=js" target="_self">JS管理</a></li>
              <li><a href="#" target="_self">角色管理</a></li>
              <{if $userinfo.role_id eq 1}>
              <li><a href="/index.php?m=master&c=statistics" target="_self">来量统计</a></li>
              <{/if}>
          </ul>
        </li>
      	<li class="dropdown <{if $controller eq 'user'}>active<{/if}>">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">用户管理 <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/index.php?m=master&c=user"  target="_self">用户列表</a></li>
            <li><a href="/index.php?m=master&c=user&forbidon=1"  target="_self">禁用列表</a></li>
            <li><a href="/index.php?m=master&c=user&a=add"  target="_self">添加用户</a></li>
          </ul>
        </li>
        <{/if}> 	
        <li class="dropdown <{if in_array($controller,['standard.star','standard.factor','standard.level','standard.quality','standard.amount','standard.reward'])}>active<{/if}>">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">考核标准 <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/index.php?m=master&c=standard.star" target="_self">绩效标准</a></li>
            <li><a href="/index.php?m=master&c=standard.factor" target="_self">考核因素当前分数构成</a></li>
            <li><a href="/index.php?m=master&c=standard.factor&a=month" target="_self">考核因素每月分数构成</a></li>
            <li><a href="/index.php?m=master&c=standard.reward&a=description" target="_self">奖励说明</a></li>
          </ul>
        </li>
        <li class="dropdown <{if $controller eq 'error'}>active<{/if}>">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">量化标准 <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/index.php?m=master&c=standard.level" target="_self">职级职称与流量基价</a></li>
            <li><a href="/index.php?m=master&c=standard.quality&indite_id=2" target="_self">文章质量要求</a></li>
          </ul>
        </li>
          <li class="dropdown <{if $controller eq 'error'}>active<{/if}>">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">错误管理 <span class="caret"></span></a>
          <ul class="dropdown-menu">              
            <{if $userinfo.role_id gt 1}>
              <li><a href="/index.php?m=master&c=error&a=myrecord" target="_self">我的记录</a></li>
            <{/if}>
            <li><a href="/index.php?m=master&c=error" target="_self">记录列表</a></li>
            <{if $userinfo.role_id lt 3}>
              <li><a href="/index.php?m=master&c=error&a=add" target="_self">添加记录</a></li>
            <{/if}>
          </ul>
        </li>
        <li class="dropdown <{if $controller|in_array:['laonanren.star','laonanren.month','laonanren.month2018','laonanren.reward']}>active<{/if}>">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">老男人 <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/index.php?m=master&c=star&web_id=1" target="_self">星级考核</a></li>
            <!--li><a href="/index.php?m=master&c=laonanren.month" target="_self">月度考核</a></li-->
            <li><a href="/index.php?m=master&c=laonanren.month2018" target="_self">2018版月度考核</a></li>
            <li><a href="/index.php?m=master&c=laonanren.reward" target="_self">奖励排行</a></li>
            <!--li><a href="/index.php?m=master&c=laonanren.year&year=2017" target="_self">2017年度总评</a></li-->
          </ul>
        </li>
        <li class="dropdown <{if $controller|in_array:['nvren.star','nvren.month','nvren.reward']}>active<{/if}>">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">女人网<span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/index.php?m=master&c=star&web_id=2" target="_self">星级考核</a></li>
            <!--li><a href="/index.php?m=master&c=nvren.month" target="_self">月度考核</a></li-->
            <li><a href="/index.php?m=master&c=nvren.month2018" target="_self">2018版月度考核</a></li>
            <li><a href="/index.php?m=master&c=nvren.reward" target="_self">奖励排行</a></li>
          </ul>
        </li>
        <li class="<{if $controller eq 'baidu'}>active<{/if}>"><a href="/index.php?m=master&c=baidu" target="_self">百度热搜榜</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#" target="_self"><{$userinfo.nick}></a></li>
        <li><a href="/index.php?m=master&c=user&a=pass" target="_self">修改密码</a></li>
        <li><a href="/index.php?m=master&c=log&a=logout" target="_self">退出登录</a></li>
      </ul>

    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>