<?php
namespace master\controller;

/**
 * 用户控制器
 */
class User extends Common
{
    /**
     * [userlist 用户列表]
     * @return [type] [description]
     */
    public function index(){
        $this->checkrole('1,2');
        //user编辑表实例化
        $user = model('User');

        //定义查询用户列表的条件数组
        $data = array();

        //获取创作类型列表
        $indite = model('Indite');
        $indites = $indite->select();
        $indite_id = isset($_GET['indite_id'])?$_GET['indite_id']:0;
        if ($indite_id!=0) {
            $data['indite_id'] = $indite_id;
        }

        //获取职级职称列表
        $level  = model('Level');
        $levels = $level->select();
        $level_id = isset($_GET['level_id'])?$_GET['level_id']:0;
        if ($level_id!=0) {
            $data['level_id'] = $level_id;
        }

        //获取网站列表
        $web  = model('Web');
        $webs = $web->select();
        $web_id = isset($_GET['web_id'])?$_GET['web_id']:0;
        if ($web_id!=0) {
            $data['web_id'] = $web_id;
        }

        //获取角色列表
        $role = model('Role');
        $roles = $role->select();
        $data['role_id'] = ($_SESSION['userinfo']['role_id']==1)?array('neq',1):array('eq',3);

        //判断要获取哪种状态的用户信息
        if (isset($_GET['forbidon']) && $_GET['forbidon'] == 1) {
            $data['forbidon'] = 1;
            $forbidon = 1;
        }else{
            $data['forbidon'] = 0;
            $forbidon = 0;
        }

        //每页显示用户信息条数
        $perpage = 10;

        //获取当前页码
        $pageno = isset($_GET['pageno'])?$_GET['pageno']:1;

        //获取offset
        $offset = ($pageno-1)*$perpage;

        //获取满足条件的信息总数
        $total = $userlist = $user->where($data)->count();

        //获取用户列表
        $userlist = $user->where($data)->order(array('`role_id`'=>'asc','`user_id`'=>'desc'))->limit($offset,$perpage)->select();

        //获取用户历史数据
        $history = model('UserinfoHistory');
        $month = date('Y-m');
        $where = array();
        $where['month'] = $month;
        $historyList = $history->where($where)->field('user_id,month')->select();


        //处理用户列表
        foreach ($userlist as $key => $value) {
            $userlist[$key]['role_name'] = $roles[$value['role_id']]['role_name'];
            $userlist[$key]['indite_name'] = ($value['indite_id']!=0)?$indites[$value['indite_id']]['indite_name']:'未评级';
            $userlist[$key]['level_name'] = ($value['level_id']!=0)?($levels[$value['level_id']]['level_rand'].$levels[$value['level_id']]['level_name']):'未评级';
            $userlist[$key]['web_name'] =  ($value['web_id']!=0)?$webs[$value['web_id']]['web_name']:'未定';

            //处理用户负责的风站信息
            $weblist = $value['weblist'];
            $weblist = explode(',', $weblist);
            $weblist = array_filter($weblist);
            $webstr = '';
            foreach ($webs as $wk => $wv) {
                if (in_array($wv['web_id'],$weblist)) {
                    $webstr .= $wv['web_name'].',';
                }
            }
            $webstr = trim($webstr,',');
            $userlist[$key]['weblist'] = $webstr?$webstr:'-';

            //记录到userinfo_history表中
            if ($forbidon == 0 && !isset($historyList[$value['user_id']])) {
                $data = array();
                $data['user_id']   = $value['user_id'];
                $data['indite_id'] = $value['indite_id'];
                $data['level_id']  = $value['level_id'];
                $data['role_id']   = $value['role_id'];
                $data['price']	   = $levels[$value['level_id']]['price'];
                $data['month']	   = $month;
                $history->data($data)->add();
            }
        }

        //显示10条页码
        $pagenum = 10;

        $preurl = "/index.php?c=user";
        $preurl .= $forbidon?"&forbidon={$forbidon}":'';
        $preurl .= $web_id?"&web_id={$web_id}":'';
        $preurl .= $level_id?"&level_id={$level_id}":"";
        $preurl .= $indite_id?"&=indite_id={$indite_id}":"";
        $pagelist = createPageList($preurl,$total,$perpage,$pageno,$pagenum);

        $this->assign('levels',$levels);
        $this->assign('level_id',$level_id);
        $this->assign('indites',$indites);
        $this->assign('indite_id',$indite_id);
        $this->assign('forbidon',$forbidon);
        $this->assign('webs',$webs);
        $this->assign('web_id',$web_id);
        $this->assign('pagelist',$pagelist);
        $this->assign('forbidon',$forbidon);
        $this->assign('title','用户列表');
        $this->assign('userlist',$userlist);
        $this->display();
    }

    /**
     * [add 添加用户表单]
     */
    public function add(){
        $this->checkrole('1,2');
        $indite = model('Indite');
        $indites = $indite->select();

        $level  = model('Level');
        $levels = $level->select();

        $web = model('Web');
        $webs = $web->select();

        $this->assign('indites',$indites);
        $this->assign('levels',$levels);
        $this->assign('webs',$webs);
        $this->assign('title','添加用户');
        $this->display('user/add.html');
    }

    /**
     * [doadduser 执行添加用户]
     * @return [type] [description]
     */
    public function doadduser(){
        $this->checkrole('1,2');
        $user = model('User');

        //校验昵称或用户名是否重复
        $data = array();
        $data['nick'] = $_POST['nick'];
        $count = $user->where($data)->count();
        if ($count!=0) {
            json_reply(400,'昵称重复');
        }

        $data = array();
        $data['username'] = $_POST['username'];
        $count = $user->where($data)->count();
        if ($count!=0) {
            json_reply(400,'用户名重复');
        }

        //校验通过后执行添加用户操作
        $data = array();
        $_POST['role_id'] = empty($_POST['role_id'])?3:$_POST['role_id'];
        $data = $_POST;
        $data['password'] = md5(md5('123456').'shenduan');
        $rs = $user->add($data);
        if ($rs){
            $this->refresh();
            json_reply(200,'用户添加成功','/index.php?c=user');
        }else{
            json_reply(400,'用户添加失败:'.$user->getError());
        }
    }


    /**
     * 功能：更新用户表信息至本地件
    */
    public function refresh(){
        //获取除超管外、非禁用状态、且正在或曾经负责老男人的编辑

        $user = model('User');
        //老男人
        $web_id = 1;
        $userlist = $user->where("`forbidon` = 0 and role_id > 1 and (`web_id` = {$web_id} or `weblist` like '%,{$web_id},%')")->getField('nick',true);
        if ($userlist) {
            $str = "<?php\r\n  return ".var_export($userlist,true).";";
            file_put_contents(BASEDIR.'/'.APP_PATH.'/master/config/laonanren_user.php', $str);
            file_put_contents(BASEDIR.'/lib/laonanren_user.php', $str);
        }

        $nickstr = implode("\r\n", $userlist);
        $rs = file_put_contents(BASEDIR.'/lib/txt.txt', $nickstr);

        //保存署名信息
        $signature = $user->where("`forbidon` = 0 and role_id > 1")->getField('nick,signature');
        $str = "<?php\r\n  return ".var_export($signature,true).";";
        file_put_contents(BASEDIR.'/'.APP_PATH.'/master/config/signature.php', $str);
        file_put_contents(BASEDIR.'/nvren/signature.php', $str);

        //女人网
        $web_id = 2;
        $userlist = $user->where("`forbidon` = 0 and role_id > 1 and (`web_id` = {$web_id} or `weblist` like '%,{$web_id},%')")->getField('nick',true);
        $str = "<?php\r\n  return ".var_export($userlist,true).";";
        file_put_contents(BASEDIR.'/'.APP_PATH.'/master/config/nvren_user.php', $str);
        file_put_contents(BASEDIR.'/nvren/nvren_user.php', $str);

        foreach ($userlist as $key => $value) {
            if (!empty($signature[$value]['signature'])){
                $userlist[$key] = $signature[$value]['signature'];
            }
        }
        $nvren_user_str = implode($userlist, '$');
        curl_get("http://tj.nvren.com/index/saveuserlist?userlist={$nvren_user_str}");
        return $rs;
    }


    /**
     * 功能：输出获取用户信息
     * param user_id 用户id
     */
    public function getUserInfo(){
        $this->checkrole('1,2');
        //获取用户信息
        $user = model('User');
        $data = array();
        $data['user_id'] = $_GET['user_id'];
        $userinfo = $user->where($data)->find();
        unset($userinfo['password']);
        $webstr = $userinfo['weblist'];
        $webarr = explode(',', $webstr);
        $webarr = array_filter($webarr);

        //获取角色列表
        $role = model('Role');
        $roles = $role->select();

        //获取创作类型列表
        $indite = model('Indite');
        $indites = $indite->select();

        //获取职级职称列表
        $level  = model('Level');
        $levels = $level->select();

        //获取网站列表
        $web  = model('Web');
        $webs = $web->select();


        $html = '<td>'.$userinfo['user_id'].'</td>';
        $html .= '<td>'.$userinfo['nick'].'</td>';
        $html .= '<td>'.$userinfo['username'].'</td>';
        $html .= '<td>'.$roles[$userinfo['role_id']]['role_name'].'</td>';
        $html .= '<td><input type="text" value="'.$userinfo['signature'].'"></td>';

        $html .= '<td><select name="level_id">';
            $html .= '<option value="">请选择职级</option>';
            foreach ($levels as $lk => $lev) {
                $html .= '<option value="'.$lev['level_id'].'"'.(($lev['level_id'] == $userinfo['level_id'])?'selected=selected':'').'>'.$lev['level_rand'].$lev['level_name'].'</option>';
            }
        $html .= '</select></td>';

        $html .= '<td><select name="web_id">';
            $html .= '<option value="">请选择网站</option>';
            foreach ($webs as $wk => $wv) {
                $html .= '<option value="'.$wv['web_id'].'"'.(($wv['web_id'] == $userinfo['web_id'])?'selected=selected':'').'>'.$wv['web_name'].'</option>';
            }
        $html .= '</select></td>';

        $weblist = '';
        foreach ($webs as $wk => $wv) {
            $checked = in_array($wv['web_id'], $webarr)?1:0;
            $weblist .= "<input type=\"checkbox\" name=\"weblist\" value=\"{$wv['web_id']}\"" ;
            $weblist .= $checked?'checked="checked"':'';
            $weblist .= " >{$wv['web_name']}\r\n";
        }
        $html .= "<td>{$weblist}</td>";

        $html .= '<td><input type="button" class="button border-main post" value="提交" userid="'.$userinfo['user_id'].'"><button class="cancle">取消</button></td>';
        echo $html;
    }


    /**
     * 功能：提交修改后的用户信息
     * @return [type] [description]
     */
    public function postUserInfo(){
        $this->checkrole('1,2');
        //保存用户信息至user表
        $user = model('User');

        $rs = $user->data($_GET)->save();
        if (!$rs) {
            json_reply(400,'保存失败',array('sql'=>$user->lastSql,'mysqli_error'=>$user->getError()));
            exit;
        }

        //获取职级信息
        $level = model('Level');
        $where = array();
        $where['level_id'] = $_GET['level_id'];
        $levelinfo = $level->where($where)->find();

        //修改userinfo_history
        //获取用户信息历史记录
        $history = model('UserinfoHistory');
        $where = array();
        $where['user_id'] = $_GET['user_id'];
        $where['month']	  = date('Y-m');
        $historyinfo = $history->where($where)->find();


        //保存修改后的用户信息至userinfo_history表
        $data = array();
        //$data['signature']  = $_GET['signature'];
        $data['history_id'] = $historyinfo['history_id'];
        $data['level_id']	= $_GET['level_id'];
        $data['price']		= $levelinfo['price'];
        $history->data($data)->save();

        $dataModel = model('Data');
        if (isset($_GET['web_id'])) {
            $where['web_id'] = $_GET['web_id'];
        }
        $datainfo = $dataModel->where($where)->find();

        $data = array();
        $data['data_id']		= $datainfo['data_id'];
        $data['level_id']		= $_GET['level_id'];
        $data['level_rand']	= $levelinfo['level_rand'];
        $data['level_name']	= $levelinfo['level_name'];
        $dataModel->save($data);

        $this->refresh();
        json_reply(200,'保存成功 ');

    }

    /**
     * 功能：禁用用户
     * param user_id 目标用户的id
     */
    public function inactive(){
        $this->checkrole('1,2');
        $user = model('User');
        $data['user_id'] = $_GET['user_id'];
        $data['forbidon'] = 1;
        $data['role_id'] = 3;
        $rs = $user->save($data);
        $this->refresh();
        echo 1;
    }

    /**
     * 功能：启用用户
     * param user_id 目标用户的id
     */
    public function active(){
        $this->checkrole('1,2');
        $user = model('User');
        $data['user_id'] = $_GET['user_id'];
        $data['forbidon'] = 0;
        $rs = $user->save($data);
        $this->refresh();
        echo 1;
    }

    /**
     * 功能：升管理员
     * param user_id 目标用户的id
     */
    public function up(){
        $this->checkrole('1');
        $role_id = 2;
        //保存修改后的用户信息至user表
        $user = model('User');
        $data = array();
        $data['user_id'] = $_GET['user_id'];
        $data['role_id'] = $role_id;
        $rs = $user->save($data);

        //保存修改后的用户信息至userinfo_history表
        $history = model('UserinfoHistory');
        $where = array();
        $where['user_id']	= $_GET['user_id'];
        $where['month']		= date('Y-m');
        $historyinfo = $history->where($where)->find();

        $data = array();
        $data['history_id'] = $historyinfo['history_id'];
        $data['role_id']	= $role_id;
        $history->data($data)->save();
        $this->refresh();
        echo 1;
    }

    /**
     * 功能：降为编辑
     * param user_id 目标用户的id
     */
    public function down(){
        $this->checkrole('1');
        $role_id = 3;
        //保存修改后的用户信息至user表
        $user = model('User');
        $data = array();
        $data['user_id'] =$_GET['user_id'];
        $data['role_id'] = $role_id;
        $rs = $user->save($data);

        //保存修改后的用户信息至userinfo_history表
        $history = model('UserinfoHistory');
        $where = array();
        $where['user_id']	= $_GET['user_id'];
        $where['month']		= date('Y-m');
        $historyinfo = $history->where($where)->find();

        $data = array();
        $data['history_id'] = $historyinfo['history_id'];
        $data['role_id']	= $role_id;
        $history->data($data)->save();
        $this->refresh();
        echo 1;
    }

    /**
     * 功能：修改密码表单页
     */
    public function pass(){
        $roleModel = model('Role');
        $where = array();
        $where['role_id'] = $_SESSION['userinfo']['role_id'];
        $role_name = $roleModel->where($where)->getField('role_name');

        $this->assign('title','修改密码');
        $this->assign('role_name',$role_name);
        $this->display('user/pass.html');
    }

    /**
     * [dopass 执行修改密码]
     * @return [type] [description]
     */
    public function dopass(){
        if (md5(md5($_POST['oldpwd']).'shenduan') != $_SESSION['userinfo']['password']) {
            echo "<script>alert('原始密码有误');window.history.go(-1);</script>";
            exit;
        }
        if (strlen($_POST['newpwd']) <6 || strlen($_POST['newpwd']) > 20) {
            echo "<script>alert('请输入6到20位新密码');window.history.go(-1);</script>";
        }
        if ($_POST['newpwd'] != $_POST['confirmpwd']) {
            echo "<script>alert('两次输入的新密码不一致，请重新输入');window.history.go(-1);</script>";
        }
        $data = array();
        $data['user_id'] = $_SESSION['userinfo']['user_id'];
        $data['password'] = md5(md5($_POST['newpwd']).'shenduan');
        $userModel = model('User');
        $rs = $userModel->data($data)->save();
        echo "<script>alert('密码修改成功，请重新登录');window.location.href = \"/index.php?c=Log&a=logout\"</script>";
    }

    /**
     * [reset 密码重置]
     * @return [type] [description]
     */
    public function reset(){
        $this->checkrole('1');
        $user = model('User');
        $data['user_id'] = $_GET['user_id'];
        $data['password']= md5(md5('123456').'shenduan');
        $rs = $user->save($data);
        echo $rs?1:0;
    }
}