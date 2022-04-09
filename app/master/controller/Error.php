<?php
namespace master\controller;
class Error extends Common
{
    /**
     * 功能：展示错误记录列表
     * param user_id 可选 用户id
    */
    public function index(){
        //获取用户列表
        $user = model('User');
        $data = array();
        $data['forbidon'] = 0;
        $userlist = $user->where($data)->field('user_id,nick,role_id')->select();

        //获取网站列表
        $web = model('Web');
        $weblist = $web->select();

        //获取创作类型列表
        $indite = model('Indite');
        $inditelist = $indite->select();

        //获取质量要求
        $require_quality = model('RequireQuality');
        $qualitylist = $require_quality->select();


        //获取错误记录列表
        $error_record = model('ErrorRecord');
        $data = array();
        if (isset($_GET['user_id']) && $_GET['user_id'] != 0) {
            $data['user_id'] = $_GET['user_id'];
        }else{
            $data['user_id'] = array('in',array_keys($userlist));
        }
        if (isset($_GET['indite_id']) && $_GET['indite_id'] != 0) {
            $data['indite_id'] = $_GET['indite_id'];
        }
        if (isset($_GET['web_id']) && $_GET['web_id'] != 0) {
            $data['web_id'] = $_GET['web_id'];
        }
        if (isset($_GET['month']) && $_GET['month'] != 0) {
            $data['day'] = array('like',$_GET['month']);
        }


        //获取分页数据
        $total 		= $error_record->where($data)->count();		//满足条件的记录数量

        $perpage 	= 10;									//每页显示条数
        $curr 		= isset($_GET['pageno'])?$_GET['pageno']:1;
        $offset 	= $perpage * ($curr - 1);

        $errorlist 	= $error_record->where($data)->order(array('record_id'=>'desc'))->limit($offset,$perpage)->select();
        if (count($errorlist) == 0 && $curr>1) {
            header("location:/index.php?c=error&pageno=".($curr-1));
            exit;
        }

        $preurl = '/index.php?c=error';
        $preurl .= (isset($_GET['user_id']) && $_GET['user_id'] != 0)?('&user_id='.$_GET['user_id']):'';
        $preurl .= (isset($_GET['indite_id']) && $_GET['indite_id'] != 0)?('&indite_id='.$_GET['indite_id']):'';
        $preurl .= (isset($_GET['web_id']) && $_GET['web_id'] != 0)?('&web_id='.$_GET['web_id']):'';
        $preurl .= (isset($_GET['month']) && $_GET['month'] != 0)?('&month='.$_GET['month']):'';

        //显示10条页码
        $pagenum = 6;

        $pagelist = createPageList($preurl,$total,$perpage,$curr,$pagenum);

        foreach ($errorlist as $key => $el) {
            $errorlist[$key]['nick'] 		= $userlist[$el['user_id']]['nick'];
            $errorlist[$key]['web_name'] 	= $weblist[$el['web_id']]['web_name'];
            $errorlist[$key]['indite_name'] = $inditelist[$el['indite_id']]['indite_name'];
            $errorlist[$key]['requirement'] = $qualitylist[$el['quality_id']]['requirement'];
        }

        $userlist  = dealUserList($userlist);
        $monthlist = getmonthlist(2017,3,1);							//获取月份页表

        $user_id 	= (isset($_GET['user_id']) && $_GET['user_id'] != 0)?$_GET['user_id']:0;
        $indite_id 	= (isset($_GET['indite_id']) && $_GET['indite_id'] != 0)?$_GET['indite_id']:0;
        $web_id 	= (isset($_GET['web_id']) && $_GET['web_id'] != 0)?$_GET['web_id']:0;
        $month 		= (isset($_GET['month']) && $_GET['month'] != 0)?$_GET['month']:0;

        $this->assign('user_id',$user_id);
        $this->assign('indite_id',$indite_id);
        $this->assign('web_id',$web_id);
        $this->assign('month',$month);
        $this->assign('userlist',$userlist);
        $this->assign('inditelist',$inditelist);
        $this->assign('weblist',$weblist);
        $this->assign('monthlist',$monthlist);
        $this->assign('errorlist',$errorlist);
        $this->assign('pagelist',$pagelist);
        $this->assign('title','错误记录列表');
        $this->display();
    }

    /**
     * 功能：添加错误信息页面
    */
    public function add(){
        $this->checkrole();
        //获取编辑列表
        $user = model('User');
        $data = array();
        $data['role_id'] = array('gt',$_SESSION['userinfo']['role_id']);
        $data['forbidon'] = 0;
        $userlist = $user->where($data)->select();

        //获取网站列表
        $web = model('Web');
        $weblist = $web->select();

        $this->assign('title','添加错误记录');
        $this->assign('userlist',$userlist);
        $this->assign('weblist',$weblist);
        $this->display();
    }


    public function getUserInfo(){
        //获取用户信息
        $user = model('User');
        $data = array();
        $data['user_id'] = $_GET['user_id'];
        $userinfo = $user->where($data)->find();
        if ($userinfo['indite_id'] == 0) {
            echo 0;
            exit;
        }

        //获取创作类型
        $indite = model('Indite');
        $data = array();
        $data['indite_id'] = $userinfo['indite_id'];
        $inditeinfo = $indite->where($data)->find();
        $indite_name = $inditeinfo['indite_name'];

        //获取该创作类型下的质量要求及其分值
        $require_quality = model('RequireQuality');
        $data['state'] = 1;
        $qualitylist = $require_quality->where($data)->select();
        $qualitylisthtml = '<option value="">===='.$indite_name.'====</option>';
        foreach ($qualitylist as $key => $ql) {
            $qualitylisthtml .= '<option value="'.$ql['quality_id'].'">'.$ql['requirement'].$ql['score'].'</option>';
        }
        echo $qualitylisthtml;
    }

    public function getWebName(){
        //获取用户信息
        $user = model('User');
        $data = array();
        $data['user_id'] = $_GET['user_id'];
        $userinfo = $user->where($data)->find();

        $web_id = $userinfo['web_id'];

        $web = model('Web');
        $where = array();
        $where['web_id'] = $web_id;
        $webinfo = $web->where($where)->find();
        echo $webinfo?$webinfo['web_name']:0;
    }

    //执行添加错误信息
    public function doadd(){
        $this->checkrole();
        //获取用户信息
        $user = model('User');
        $data = array();
        $data['user_id'] = $_POST['user_id'];
        $userinfo = $user->where($data)->find();
        unset($userinfo['password']);

        if ($userinfo['user_id'] == 0 || $userinfo['indite_id'] == 0) {
            echo '<script>alert("该编辑未设置职级职称或创作类型");history.go(-1);</script>';
            exit;
        }

        $require_quality = model('RequireQuality');
        $qualitylist = $require_quality->select();


        //生成错误信息
        $info = array();
        $info['user_id'] 	= $userinfo['user_id'];
        $info['level_id'] 	= $userinfo['level_id'];
        $info['indite_id'] 	= $userinfo['indite_id'];
        $info['web_id'] 	= $userinfo['web_id'];
        $info['article_id'] = $_POST['article_id'];
        $info['quality_id'] = $_POST['quality_id'];
        $info['score'] 		= $qualitylist[$_POST['quality_id']]['score'];
        $info['addtime'] 	= time();
        $strtotime 			= strtotime($_POST['date']);
        $info['day'] 		= date('Y-m-d',$strtotime);
        $info['month'] 		= date('Y-m',$strtotime);
        $info['year'] 		= date('Y',$strtotime);
        $info['by_user_id'] = $_SESSION['userinfo']['user_id'];


        //添加错误信息
        $error_record = model('ErrorRecord');
        $rs = $error_record->add($info);
        if ($rs) {
            header("location:/index.php?c=error");
        }else{
            echo "<script>alert('添加错误');history.go(-1);</script>";
        }
    }


    //错误签收
    public function dosign(){
        $error_record =  model('ErrorRecord');
        $data = array();
        $data['record_id'] = $_GET['record_id'];
        $recordinfo = $error_record->where($data)->find();
        //判断user_id
        if ($recordinfo['user_id'] != $_SESSION['userinfo']['user_id']) {
            echo "<script>alert('无权访问');window.location.href='/index.php?c=Log&a=logout';</script>";
        }
        $data = array();
        $data['record_id'] 	= $_GET['record_id'];
        $data['sign'] 		= 1;
        $data['sign_at']	= time();
        $rs = $error_record->save($data);
        echo $rs?1:0;
    }

    //删除错误信息
    public function del(){
        $this->checkrole();
        $error_record =  model('ErrorRecord');
        $rs = $error_record->del($_GET['record_id']);
        echo $rs?1:0;
    }


    public function myrecord(){
        //获取用户列表
        $user = model('User');
        $data = array();
        $data['forbidon'] = 0;
        $userlist = $user->where($data)->field('user_id,nick,role_id')->select();

        //获取网站列表
        $web = model('Web');
        $weblist = $web->select();

        //获取创作类型列表
        $indite = model('Indite');
        $inditelist = $indite->select();

        //获取质量要求
        $require_quality = model('RequireQuality');
        $qualitylist = $require_quality->select();


        $errorModel =  model('ErrorRecord');
        $where = array();
        $where['user_id'] = $_SESSION['userinfo']['user_id'];

        $total 		= $errorModel->where($where)->count();		//满足条件的记录数量

        $perpage 	= 10;									//每页显示条数
        $curr 		= isset($_GET['pageno'])?$_GET['pageno']:1;
        $offset 	= $perpage * ($curr - 1);

        $errorlist 	= $errorModel->where($where)->order(array('record_id'=>'desc'))->limit($offset,$perpage)->select();
        if (count($errorlist) == 0 && $curr>1) {
            header("location:/index.php?c=error&pageno=".($curr-1));
            exit;
        }

        foreach ($errorlist as $key => $el) {
            $errorlist[$key]['nick'] 		= $userlist[$el['user_id']]['nick'];
            $errorlist[$key]['web_name'] 	= $weblist[$el['web_id']]['web_name'];
            $errorlist[$key]['indite_name'] = $inditelist[$el['indite_id']]['indite_name'];
            $errorlist[$key]['requirement'] = $qualitylist[$el['quality_id']]['requirement'];
        }

        $preurl = '/index.php?c=error';
        $preurl .= (isset($_GET['user_id']) && $_GET['user_id'] != 0)?('&user_id='.$_GET['user_id']):'';
        $preurl .= (isset($_GET['indite_id']) && $_GET['indite_id'] != 0)?('&indite_id='.$_GET['indite_id']):'';
        $preurl .= (isset($_GET['web_id']) && $_GET['web_id'] != 0)?('&web_id='.$_GET['web_id']):'';
        $preurl .= (isset($_GET['month']) && $_GET['month'] != 0)?('&month='.$_GET['month']):'';

        //显示10条页码
        $pagenum = 6;

        $pagelist = createPageList($preurl,$total,$perpage,$curr,$pagenum);

        $this->assign('title','我的错误记录');
        $this->assign('errorlist',$errorlist);
        $this->assign('pagelist',$pagelist);
        $this->display();
    }
}