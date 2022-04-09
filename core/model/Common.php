<?php
// +----------------------------------------------------------------------
// | dbClass   db类	其他类扩展自此类
// +----------------------------------------------------------------------
// | Author: 炜哲  <360093893@qq.com>
// +----------------------------------------------------------------------
namespace Master\Model;

class Common {
	protected 	$host;
	protected 	$user;
	protected 	$pwd;
	protected 	$dbName;
	protected 	$charset;
	protected 	$conn  			= null;	//保存服务器连接资源
	public  	$table;
	public  	$prKey;					//关键字段
	public  	$join  			= '';
	public  	$where 			= '1';
	public  	$order 			= '';
	public  	$limit 			= '';
	public 		$fields 		= null;
	public 		$lastSql 		= '';
	public 		$lastDbError	= '';
	public 		$except;
	public 		$group 			= '';
	public 		$mysql_error 	= '';
    /**
     * @var mixed
     */
    private $data;

    public function  __construct ($database = 'master'){
		//在构造方法里读取配置文件，根据配置文件来设置私有属性
		$cfg 			= require(BASEDIR.'/'.CONF_PATH.'/'.MODULE.'/config.php');
		$cfg 			= $cfg[$database];
		$this->host 	= $cfg['host'];
		$this->user 	= $cfg['user'];
		$this->pwd 		= $cfg['pwd'];
		$this->dbName 	= $cfg['db'];
		$this->charset 	= $cfg['charset'];
		//连接
		$this->connect($this->host,$this->user,$this->pwd);
		//切换库
		$this->switchDb($this->dbName);
		//设置字符集
		$this->setChar($this->charset);
	}

	public function __set($key,$name){
		$this->$key = $name;
		return $this;
	}

	//负责连接
	public function connect($h,$u,$p){
		$conn = mysqli_connect($h,$u,$p);
		$this->conn = $conn;
        return $this->conn;
	}
	

	//负责切换数据库，网站大的时候，可能用到不止一个库
	public function switchDb($db){
		$sql = 'use '.$db;
		$this->query($sql);
	}


	//负责设置字符集
	public function setChar($char){
		$sql = 'set names '.$char;
		$this->query("set character set ".$char);
		$this->query("SET CHARACTER_SET_RESULTS=".$char); 
		$this->query($sql);
	}


	//负责发送sql查询
	public function query($sql){
		if (!$this->conn) {
			return 'Mysql Server Has Gone Away';
		}
		$rs = mysqli_query($this->conn,$sql);
		if (!$rs) {
			$error = mysqli_error($this->conn);
			$this->lastDbError = $error;
			$this->mysql_error = $error;
		}
		if (WRITELOG) {
			$file = BASEDIR.'/'.MODULE.'/Runtime/Logs/log_'.date('Ymd').'.txt';
			$path = dirname($file);
			is_dir($path) || mkdir($path,0777,true);
			$str  = "\n\r==============================\n\r";
			$str .= date("Y-m-d H:i:s")."\n\r".$sql;
			if (isset($error)) {
				$str .= "\n\r".$error;
			}
			$str .= "\n\r==============================";
			file_put_contents($file, $str,FILE_APPEND);
		}
		$this->lastSql = $sql;
		return $rs;
	}

	//获取错误提示
	public function getError(){
		return $this->mysql_error;
	}

	//负责获取多行多列的select结果
	public function getAll($sql,$key = false){
		$rs = $this->query($sql);
		if(!$rs){
			return false;
		}else{
			$row = array();
			while ($temp = mysqli_fetch_assoc($rs)) {
				if ($key) {
					$row[$temp[$key]]=$temp;
				}else{
					$row[]=$temp;
				}				
			}			
		}

		if ($this->except) {
			foreach ($row as $k2 => $rl) {
				foreach ($this->except as $k3 => $v3) {
					if (isset($rl[$v3])) {
						unset($row[$k2][$v3]);
					}
				}
			}
		}
		return $row;
	}

	//负责获取一行的select结果
	public function getRow($sql){
		$rs = $this->query($sql);
		if(!$rs){
			return false;
		}else{
			$res = mysqli_fetch_assoc($rs);
            var_dump($res);
            exit();
		}
	}
	
	//负责获取一行的select结果
	public function getOne($sql){
		$rs = $this->query($sql);
		if(!$rs){
			return false;
		}else{
			$row = mysqli_fetch_row($rs);
			return $row[0];
		}
	}

	//切换操作表格
	public function table($table): Common
    {
		$this->table = $table;
		return $this;
	}

	//添加或更新数据
	public function data($data): Common
    {
		$this->data = $data;
		return $this;
	}

	//联表查询，待完善
	public function join($join){
		$this->join = $join;
		return $this;
	}
	/**
	 * [where where赋值函数]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function where($data){
		if (is_string($data)) {
			$this->where = $data;
		}elseif(is_array($data)){
			if (empty($data)) {
				$this->where = 1;
			}else{
				if (isset($data['_logic']) && $data['_logic'] == 'or') {
					$logic = ' or ';
					unset($data['_logic']);
				}else{
					$logic = ' and ';
				}
				foreach ($data as $key => $value) {
					if (is_array($value)) {
						if (is_string($value[0])) {
							$data[$key] = $this->getwhere($key,$value);
						}else{
							$temp = array();
							$glue = ' and ';
							foreach ($value as $k1 => $v1) {
								if (is_array($v1)) {
									$temp[] = $this->getwhere($key,$v1);
								}else{
									$glue = " {$v1} ";
								}
							}
							$data[$key] = "(". implode($glue, $temp).")";
						}
						
					}else{
						$data[$key] = $key ."='".$value."'";
					}					
				}
				$this->where = implode($logic, $data);
			}
		}
		return $this;
	}


	public function getwhere($key,$value){
		switch ($value[0]) {
			case 'eq':
				$result = $key ." = '".$value[1]."'";
				break;
			case 'neq':
				$result = $key ." != '".$value[1]."'";
				break;
			case 'like':
				$result = $key ." like '%$value[1]%'";
				break;
			case 'notlike':
				$result = $key ."not like '%$value[1]%'";
				break;
			case 'in':
				$result = $key ." in ('".implode($value[1],"','")."')";
				break;
			case 'not in':
				$result = $key ." not in ('".implode($value[1],"','")."')";
				break;
			case 'gt':
				$result = $key ." > ".$value[1];
				break;
			case 'egt':
				$result = $key ." >= ".$value[1];
				break;
			case 'lt':
				$result = $key ." < ".$value[1];
				break;
			case 'elt':
				$result = $key ." <= ".$value[1];
				break;
			case 'between':
				$result = $key .">=" .$value[1][0] ." and ".$key."<=".$value[1][1];
				break;
            case 'exp':
                $result = $key.' ' .$value[1];
		}
		return $result;
	}

	//查询排序
	public function order($order){
		if (!is_array($order)) {
			$this->order =$order;
		}else{
			$this->order = ' order by ';
			foreach ($order as $key => $value) {
				$this->order .= $key.' '.$value.',';
			}
			$this->order = trim($this->order,",");
		}		
		return $this;
	}


	//查询条数区间
	public function limit($a,$b = null){
		if (!$b) {
			$this->limit = " LIMIT 0,$a";
		}else{
			$this->limit = " LIMIT $a,$b";
		}
		return $this;
	}

	//操作字段
	public function field($fields){
		if (is_array($fields)) {
			$this->fields = implode(",", $fields);
		}else{
			$this->fields = $fields;
		}		
		return $this;
	}
	
	//分组
	public function group($str){
		$this->group = " GROUP BY {$str} ";
		return $this;
	}

	//排除字段
	public function except($except){
		if (is_string($except)) {
			$this->except = explode(',', $except);
		}else{
			$this->except = except;
		}
		return $this;
	}

	/**
	 * [add 添加记录]
	 * @param [type] $data [description]
	 */
	public function add($data = false){
		if ($data) {
			$this->data = $data;
		}
		$data['create_time'] = $data['update_time'] = time();
		$sql = "INSERT INTO `{$this->table}` (".implode(',', array_keys($this->data)).") values ('".implode("','",$this->data)."')";
		return $this->query($sql);
	}

	//批量添加数据记录
	public function groupAdd($data = false){
		if ($data) {
			$this->data = $data;
		}
		$groupData = array_values($this->data);
		foreach ($groupData as $k1 => $v1){
		    $groupData[$k1]['create_time'] = $groupData[$k1]['update_time'] = time();
        }
		$sql 	= "INSERT INTO `{$this->table}` (".implode(',', array_keys($groupData[0])).") values ";
		foreach ($groupData as $key => $value) {
			$sql .= "('".implode("','",$value)."'),";
		}
		$sql 	= trim($sql,',');
		$rs 	= $this->query($sql);
		return $rs;
	}

	//按关键字删除记录
	public function del($id){
		if (!$this->prkey) {return false;}
		$sql ="DELETE FROM `{$this->table}` WHERE {$this->prkey}='{$id}'";
		return $this->query($sql);
	}

	//更新数据
	public function save($data = null){
		if (!$data) {
			$data = $this->data;
		}
		$data['update_time'] = time();
		$str = '';
		$sql = '';
		$this->table = strtolower($this->table);
		if (isset($data[$this->prkey])) {
			$keyvalue = $data[$this->prkey];
			unset($data[$this->prkey]);
			$str = '';
			foreach ($data as $k2 => $v2) {
				if (is_numeric($v2)) {
					$str .= ",`{$k2}`={$v2}";
				}else{
					$str .= ",`{$k2}`='{$v2}'";
				}			
			}
			$str = trim($str,",");
			$sql = "UPDATE `{$this->table}` SET {$str} WHERE `{$this->prkey}`='{$keyvalue}'";
		}else{
			foreach ($data as $k2 => $v2) {
				if (is_numeric($v2)) {
					$str .= ",`{$k2}`={$v2}";
				}elseif(strpos($v2, '*') || strpos($v2, '/')){
					$str .= ",`{$k2}`={$v2}";
				}else{
					$str .= ",`{$k2}`='{$v2}'";
				}
			}
			$str = trim($str,",");
			$sql =  "UPDATE {$this->table} SET {$str} WHERE {$this->where}";
		}

		return $this->query($sql);
	}
	//连接操作SELECT  .... FROM ..... WHERE.... GROUP BY....ORDER BY ....LIMIT。

	
	/**
	 * [select 查找满足条件的所有记录]
	 * @param  [type] $str [description]
	 * @return [type]      [description]
	 */
	public function select(){
		if (!$this->fields) {
			$sql  = "SELECT * FROM `{$this->table}`";
			$key = $this->prkey;
		}else{
			$sql  = "SELECT {$this->fields} FROM `{$this->table}` ";
			$key = explode(',', $this->fields)[0];
		}
		$sql .= $this->join;
		$sql .= $this->where?" WHERE {$this->where}":'';
		$sql .= $this->group;
		$sql .= $this->order;
		$sql .= $this->limit;
		return $this->getAll($sql,$key);
	}

	/**
	 * [find 定向查找唯一满足条件的一条记录]
	 * @param  [type] $str [description]
	 * @return [type]      [description]
	 */
	public function find(){
		$fields = empty($this->fields)?'*':$this->fields;
		$sql = "SELECT {$fields} FROM `{$this->table}` ";
		$sql .=  $this->where?"WHERE {$this->where}":'';
		return $this->getRow($sql);
	}


	//查询
	public function getField($fieldStr,$flag = false){
		$fieldArr = explode(',', $fieldStr);
		if (count($fieldArr) == 1) {
			if (is_bool($flag)) {
				if ($flag) {
					$sql = "SELECT {$fieldArr[0]} FROM `{$this->table}`  WHERE {$this->where} {$this->order}";
					$rs = $this->getAll($sql);
					$list = array();
					if ($rs) {
						foreach ($rs as $key => $value) {
							$list[] = $value[$fieldArr[0]];
						}
					}
					return $list;
				}else{
					$sql = "SELECT {$fieldArr[0]} FROM `{$this->table}`  WHERE {$this->where} {$this->order} limit 0,1";
					return $this->getOne($sql);
				}
			}elseif(is_numeric($flag)){
				if ($flag > 1) {
					$sql = "SELECT {$fieldArr[0]} FROM `{$this->table}`  WHERE {$this->where} {$this->order} limit 0,{$flag}";
					$rs = $this->getAll($sql);
					$list = array();
					if ($rs) {
						foreach ($rs as $key => $value) {
							$list[] = $value[$fieldArr[0]];
						}
					}
					return $list;
				}else{
					$sql = "SELECT {$fieldArr[0]} FROM `{$this->table}`  WHERE {$this->where} {$this->order} limit 0,1";
					return $this->getOne($sql);
				}
			}

		}elseif (count($fieldArr)>1){
			$key = $fieldArr[0];
			if (is_numeric($flag) && $flag > 0) {
				$sql = "SELECT {$fieldStr} FROM `{$this->table}`  WHERE {$this->where} {$this->order} limit 0,{$flag}";
			}else{
				$sql = "SELECT {$fieldStr} FROM `{$this->table}`  WHERE {$this->where} {$this->order} {$this->limit}";
			}
			
			return $this->getAll($sql,$key);
		}
	}
	
	//计数
	public function count(){
		$sql = "SELECT count(*) FROM `{$this->table}` WHERE {$this->where}";
		return $this->getOne($sql);
	}

	//自增
	public function setInc($field,$value = 1){
		if (empty($this->where)) {
			return false;
		}
		$sql = "UPDATE `{$this->table}` SET {$field}={$field}+{$value} WHERE {$this->where}";
		return $this->query($sql);
	}

	//自减
	public function setDec($field,$value = 1){
		if (empty($this->where)) {
			return false;
		}
		$sql = "UPDATE `{$this->table}` SET {$field}={$field}-{$value} WHERE {$this->where}";
		return $this->query($sql);
	}

	//求最大值
	public function getMax($field = null){
		if (!$field) {
			$field = $this->prkey;
		}
		$sql = "SELECT MAX({$field}) FROM `{$this->table}` where {$this->where}";
		return $this->getOne($sql);
	}

	//求最小值
	public function getMin($field = null){
		if (!$field) {
			$field = $this->prkey;
		}
		$sql = "SELECT min({$field}) FROM `{$this->table}` where {$this->where}";
		return $this->getOne($sql);
	}

	//获取最后一条mysql语句
	public function getLastSQL(){
		return $this->lastSql;
	}

	//获取mysql错误提示
	public function getDbError(){
		return $this->lastDbError;
	}

	//负责关闭资源
	public function close(){
		mysql_close($this->conn);
	}

	public function __destruct(){
		if ($this->conn) {
			mysqli_close($this->conn);
		}		
	}
}