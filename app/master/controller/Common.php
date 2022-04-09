<?php
namespace master\controller;
class Common extends \Core\Controller\Common
{
    public function __construct(){
        parent::__construct();
        if (!isset($_SESSION['userinfo'])) {
            session_start();
            unset($_SESSION);
            session_destroy();
            header("location:/index.php?m=master&c=log&a=login");
        }else{
            $this->assign('userinfo',$_SESSION['userinfo']);
        }
        if (!isset($_SESSION['do_auto'])){
            $this->auto();
        }

        $controller = strtolower(str_replace('/','.',CTRL));
        $this->assign('controller',$controller);
        $this->assign('userinfo',$_SESSION['userinfo']);
    }


    /**
     * 功能：权限校验
     * param role_id 角色id
    */
    public function checkrole($role_id = 1){
        $arr = explode(',', $role_id);
        if (!in_array($_SESSION['userinfo']['role_id'], $arr)) {
            session_start();
            unset($_SESSION);
            session_destroy();
            switch (URL_MODE) {
                case 'PATHINFO':
                    header("location:/index.php/Log/logout");
                    break;
                case 'URL':
                    header("location:/index.php?c=Log&a=logout");
                    break;
            }
            exit;
        }
    }

    /**
     * 访问不存在的方法时
    */
    public function __call($method,$arg){
        p($method);
        exit;
        header('location:/');
    }

    /**
     * [auto 自动执行错误记录签收]
     * @return [type] [description]
     */
    public function auto(){
        //获取未签收的错误记录
        $errorRecordModel = model('ErrorRecord');

        $where = array();
        $where['sign'] = 0;
        $where['month'] = array('neq',date('Y-m'));

        $data = array();
        $data['score'] 		= 'score * 2';
        $data['sign_type'] 	= 'auto';
        $data['sign']		= 1;
        $data['sign_at']	= time();
        $rs = $errorRecordModel->where($where)->save($data);
        $_SESSION['do_auto'] =1;
    }


    /**
     * 功能：获取最高加分
     * month 月份
     * alias
     * webid 网站id
    */
    public function getMaxBonus($month,$alias,$webId){
        $factorHistoryModel = model('FactorHistory');
        $where = array();
        $where['month'] = $month;
        $where['alias'] = $alias;
        $where['web_id'] = $webId;
        $maxBonus = $factorHistoryModel->where($where)->getField('max_bonus');
        return $maxBonus;
    }


    /**
     * 功能：获取职级列表
     * param month 月份
     * web_id 网站id
    */
    public function get_level_list($month,$web_id){
        $levelHistoryModel = model('LevelHistory');
        $where = array();
        $where['month'] 	= $month;
        $where['web_id'] 	= $web_id;
        $historylist = $levelHistoryModel->where($where)->order(array('level_id'=>'asc'))->getField('level_id,history_id,flow_upper_limit,flow_lower_limit,basic_salary,price,seo');
        if (!$historylist) {
            $this->refreshLevel($month,$web_id);
            $historylist = $levelHistoryModel->where($where)->order(array('level_id'=>'asc'))->getField('level_id,history_id,flow_upper_limit,flow_lower_limit,basic_salary,price,seo');
        }
        return $historylist;
    }

    /**
     * 同步数量要求
    */
    public function syncAmountRequire($month = false){

        if (!$month){
            $month = date('Y-m');
        }

        //获取创作类型列表
        $indite = model('Indite');
        $indites = $indite->select();

        $require_amount = model('RequireAmount');
        foreach ($indites as $ik => $iv) {
            $data = array();
            $data['indite_id'] 	= $iv['indite_id'];
            $data['month'] 		= $month;
            $count = $require_amount->where($data)->count();
            if ($count == 0) {
                $where = [];
                $where['month'] = date('Y-m',strtotime($month) - 10);
                $where['indite_id'] = $iv['indite_id'];
                $amountinfo = $require_amount->where($where)->find();

                $data['year'] = date('Y',strtotime($month));
                $data['articlesnum'] = $amountinfo['articlesnum'];
                $data['pagesnum'] = $amountinfo['pagesnum'];
                $rs = $require_amount->add($data);
                if (!$rs){
                    p($require_amount->getError());
                    p($require_amount->getLastSQL());
                }
            }
        }
    }

    /**
     * 功能：刷新用户级别
    */
    public function refreshLevel(){
        //获取职级列表
        $level = model('Level');
        $levellist = $level->select();

        //获取上月记录
        $levelHistoryModel = model('LevelHistory');

        //获取保存的最后一个月的记录
        $where = array();
        $lastmonth 	= $levelHistoryModel->where($where)->getField('max(`month`)');
        if (!$lastmonth){
            $lastmonth = date('Y-m');
        }

        $where['month']  = $lastmonth;
        $lastmonthrecord = $levelHistoryModel->where($where)->select();
        if (!$lastmonthrecord){
            $lastmonthrecord = $levellist;
        }else{
            $lastmonthrecord = array_create_key($lastmonthrecord,'level_id');
        }


        $groupdata= array();
        while (strtotime($lastmonth) <= time()){
            $data = array();
            $data['month'] = $lastmonth;
            foreach ($levellist as $k2 => $l) {
                $data['level_id'] = $l['level_id'];
                if (!$levelHistoryModel->where($data)->count()) {
                    $data['basic_salary'] 		= $lastmonthrecord[$l['level_id']]['basic_salary'];
                    $data['price']				= $lastmonthrecord[$l['level_id']]['price'];
                    $data['seo']				= $lastmonthrecord[$l['level_id']]['seo'];
                    $groupdata[] = $data;
                }
            }
            $lastmonth = date('Y-m',strtotime($lastmonth) + 86400*32);
        }

        if ($groupdata) {
            $rs = $levelHistoryModel->data($groupdata)->groupAdd();
        }
        return;
    }

    /**
     * 功能：将编辑人员的星级和分数对应保存到星级记录表中
    */
    public function sync2starhistory($param){
        $data_ids = is_array($param)?$param:array($param);
        $dataModel = model('Data');
        $where = array();
        $where['data_id'] = array('in',$data_ids);
        $datalist = $dataModel->where($where)->select();

        if ($datalist) {
            $starHistoryModel = model('StarHistory');
            foreach ($datalist as $key => $data) {
                $where = array();
                $where['user_id'] = $data['user_id'];
                $where['web_id']  = $data['web_id'];
                $where['month']	  = $data['month'];
                $historyInfo = $starHistoryModel->where($where)->find();
                if ($historyInfo) {
                    $map = array();
                    $map['history_id'] = $historyInfo['history_id'];

                    $info = array();
                    $info['indite_id'] = $data['indite_id'];
                    $info['level_id']	= $data['level_id'];
                    $info['grade']		= $data['grade'];
                    $info['starnum']	= $this->getstarnum($data['grade']);
                    $starHistoryModel->where($map)->save($info);
                }else{
                    $info = array();
                    $info['user_id'] 	= $data['user_id'];
                    $info['web_id']	= $data['web_id'];
                    $info['indite_id']	= $data['indite_id'];
                    $info['level_id']	= $data['level_id'];
                    $info['month']		= $data['month'];
                    $info['year']		= date('Y',strtotime($data['month']));
                    $info['grade']		= $data['grade'];
                    $info['starnum']	= $this->getstarnum($data['grade']);
                    $starHistoryModel->add($info);
                }
            }
        }
    }

    /**
     * 功能：根据分数确定星级
     * param score 分数
     * param grade 星级
    */
    public function getGrade($score){
        if ($score >= 110) {
            $grade = 'S';
        }elseif ($score>=100 && $score <110) {
            $grade = 'A';
        }elseif($score>=80 && $score <100){
            $grade = 'B';
        }elseif ($score>=60 && $score <80) {
            $grade = 'C';
        }else{
            $grade = 'D';
        }
        return $grade;
    }

    /**
     * 功能：根据星级确定星数
     * param grade 星级S A B C D
     * return num -2~2
    */
    public function getstarnum($grade){
        switch ($grade) {
            case 'S':
                return 2;
                break;
            case 'A':
                return 1;
                break;
            case 'B':
                return 0;
                break;
            case 'C':
                return -1;
                break;
            case 'D':
                return -2;
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * 功能：获取考核的网站ID
     * return array ids
    */
    public function getExamineWebIds(){
        $configlist = require(BASEDIR.'/'.APP_PATH.'/master/config/configlist.php');
        $ids = $configlist['examine_web_id']['config_info'];
        $ids = explode(',', $ids);
        return $ids;
    }

    /**
     * 功能：获取网站列表
     * param wbe_ids 网站id数组
     * return array weblist 网站列表
    */
    public function getWebList($web_ids = []){
        $webModel = model('Web');
        $where = array();
        if ($web_ids){
            $where['web_id'] = array('in',$web_ids);
        }
        $weblist = $webModel->where($where)->select();
        return $weblist;
    }

}