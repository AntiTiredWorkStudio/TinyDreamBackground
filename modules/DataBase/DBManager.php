<?php
//引用此页面前需先引用conf.php
error_reporting(E_ALL ^ E_DEPRECATED);

class DBManager extends Manager{
	public static $LastSql;

    //位数
    public static function Limit($cond,$seek,$count){
        return $cond.' LIMIT '.$seek.','.$count;
    }

    //排序
    public static function OrderBy($cond,$orderField,$orderIndex){
        return $cond.' ORDER BY '.self::SqlField($orderField).' '.$orderIndex;
    }

    //链接条件 OR
    public static function C_Or($condA,$condB){
        return '('.$condA .' OR '.$condB.')';
    }

    //链接条件 AND
    public static function C_And($condA,$condB){
        return '('.$condA .' AND '.$condB.')';
    }

    public static function Symbol($parseA,$parseB,$symbol='+'){
        return '('.$parseA.$symbol.$parseB.')';
    }

    //在某值两端添加括号
    public static function Brackets($value){
        return '('.$value.')';
    }

	//创建逻辑表达式
	public static function LogicString($array,$logic='|'){
		$result = "";
		foreach($array as $key=>$value){
			$result = $result.$value.$logic;
		}
		$result = rtrim($result, $logic);
		return $result;
	}

    //判断表达式的值是否等于value(支持| & 字符)
    public static function ExpressionIsValue($field,$value,$symbol='='){
        $fieldStr = $field;
        $orcondlist = explode('|',$value);//OR条件
        if(count($orcondlist)>1) {
            $tCondStr = '(';
            foreach ($orcondlist as $sval) {
                $tCondStr = $tCondStr . ' '.$fieldStr. $symbol.'"' . $sval . '" OR';
            }
            $tCondStr = rtrim($tCondStr,'OR');
            $tCondStr = $tCondStr . ')';
        }else {

            $andcondlist = explode('&', $value);//AND条件
            if (count($andcondlist) > 1) {
                $tCondStr = '(';
                foreach ($andcondlist as $sval) {
                    $tCondStr = $tCondStr . ' ' . $fieldStr . $symbol . '"' . $sval . '" AND';
                }
                $tCondStr = rtrim($tCondStr, 'AND');
                $tCondStr = $tCondStr . ')';
            }else{
                return $fieldStr.$symbol.'"'.$value.'"';
            }
        }
        return $tCondStr;
    }

    //判断字段的值是否等于value(支持| & 字符)
    public static function FieldIsValue($field,$value,$symbol='='){
        $fieldStr = self::SqlField($field);
        return self::ExpressionIsValue($fieldStr,$value,$symbol);
    }

    //判断字段的值是否与value相似
    public static function FieldLikeValue($field,$value){
        $fieldStr = self::SqlField($field);
        return self::ExpressionIsValue($fieldStr,$value,"LIKE");
    }

    public static function SqlValue($val){
        return "'$val'";
    }
    //生成sql字段
    public static function SqlField($name){
        return "`$name`";
    }


    public function info()
    {
        return "数据库模块"; // TODO: Change the autogenerated stub
    }
    //获取配置文件
	public function C(){
		return $GLOBALS['options'];
	}
	//获取数据表
	public function T(){
		return $GLOBALS['tables'];
	}

	//获取数据表名
	public function TName($id){
        return $GLOBALS['tables'][$id]['name'];
    }

	public $dbLink = null;

	//创建数据库链接
	public function CreateDBLink(){
		if($this->dbLink == null)
			$this->dbLink = $this->DBLink();
	}

    //关闭数据库链接
	public function Finished(){
		if(!empty($this->dbLink)){
			
			mysql_close($this->dbLink);
		}
	}

	//获取表中行号
	public function CountTableRow($tableName){
        $con = $this->DBLink();
		$sql = 'select count(*) as value from `'.$tableName.'`';
		//file_put_contents('count.txt',$sql);
		self::$LastSql = $sql;
		$result = mysql_query($sql,$con);
		return mysql_fetch_array($result)[0];
	}

	//数据库模板
	public function DBPHPTemplate(){
        if(isset($_REQUEST['tablename'])){
            $fields = $this->GetTableFields($_REQUEST['tablename']);
            echo '<h3>表:'.$_REQUEST['tablename'].'</br>$'.$_REQUEST['tablename'].'Array = [</br>';
            foreach ($fields as $value){
                echo '"'.$value.'"=>'.'"",</br>';
            }
            echo '];</br>';

            echo '<h3>快捷数组:</br>[</br>';
            foreach ($fields as $value){
                echo '"'.$value.'"=>'.'$'.$value.',</br>';
            }
            echo '];</br></br>';

            echo '方法参数:';
            foreach ($fields as $value){
                echo '$'.$value.',';
            }

            echo '</br></br>请求参数:[';


            foreach ($fields as $value){
                echo '"'.$value.'",';
            }
            echo ']';


            echo '</br></br>测试请求格式:';

            foreach ($fields as $value){
                echo '&'.$value.'=VALUE';
            }
        }else{
            echo '<h3>数据库表:</h3>';
            foreach ($GLOBALS['tables'] as $key=>$values){
                echo '['.$key.']</br><a href="?db=template&tablename='.$values['name'].'">'.$values['name'].'</a></br></h3>';
            }
        }
    }

    //表单字段
	public function GetTableFields($tableName){
	    $con = $this->DBLink();
        $sql = 'select column_name from information_schema.columns where table_name="'.$tableName.'" and TABLE_SCHEMA="'.$GLOBALS['options']['database'].'"';
		self::$LastSql = $sql;
        $result = mysql_query($sql,$con);
        return array_keys(DBResultToArray($result));
    }

	//判断是否存在数据
	public function ExistRowInTable($tableName,$conditionArray,$closeDBLink = false){
		$con = $this->DBLink();
		$sql = 'SELECT * FROM `'.$tableName.'` WHERE ';
		foreach($conditionArray as $key=>$value){
			$sql = $sql.$key.'= "'.$value['var'].'" '.((isset($value['log']))?$value['log']:'');
		}
		//file_put_contents('sql.txt',$sql);
		self::$LastSql = $sql;
		$result = mysql_query($sql,$con);
		if($closeDBLink){
			mysql_close($con);
		}
		
		return mysql_fetch_row($result);
	}

    //插入多行数据
    public function InsertDatasToTable($tableName,$array,$closeDBLink = false){
        $con = $this->DBLink();
        $sqlPart0 = 'INSERT INTO `'.$tableName.'`(';
        $sqlPart1 = ') VALUES ';
        $keys = '';
        $targetValues = "";

        foreach($array['key'] as $key){
            $keys = $keys.'`'.$key.'`,';
        }
        $keys = substr($keys, 0, -1);
        foreach($array['values'] as $value){
            $currentValue = '(';
            foreach ($value as $item) {
                $currentValue = $currentValue.'"'.$item.'"'.',';
            }
            $currentValue = substr($currentValue, 0, -1);
            $currentValue = $currentValue.'),';
            $targetValues = $targetValues.$currentValue;
        }
        $targetValues = substr($targetValues, 0, -1);

        if(empty($targetValues)){
            $targetValues = "()";
        }

        $sql = $sqlPart0.$keys.$sqlPart1.$targetValues;
        //echo $sql;
		self::$LastSql = $sql;
        $result = mysql_query($sql,$con);

        if($closeDBLink){
            mysql_close($con);
        }
        return $result;
    }


	//插入数据
	public function InsertDataToTable($tableName,$array,$closeDBLink = false){
		$con = $this->DBLink();
		$sqlPart0 = 'INSERT INTO `'.$tableName.'`(';			
		$sqlPart1 = ') VALUES (';
		$sqlPart2 = ')';
		$keys = '';
		$values = '';
		foreach($array as $key=>$value){
			$keys = $keys.'`'.$key.'`,';
			$values = $values.'"'.$value.'"'.',';
		}
		$keys = substr($keys, 0, -1);
		$values = substr($values, 0, -1);
		self::$LastSql = $sqlPart0.$keys.$sqlPart1.$values.$sqlPart2;
		$result = mysql_query($sqlPart0.$keys.$sqlPart1.$values.$sqlPart2,$con);
		
		//file_put_contents('testselect.txt',$sqlPart0.$keys.$sqlPart1.$values.$sqlPart2);
		//echo $sqlPart0.$keys.$sqlPart1.$values.$sqlPart2;
		if($closeDBLink){
			mysql_close($con);
		}
		return $result;
	}

	public function UpdateDataByQuery($tableName,$valString,$conString = null,$closeDBLink = false){
        $con = $this->DBLink();
        $sql = 'UPDATE `'.$tableName.'` SET ';

        $val = '';

        if(empty($valString)){
            return false;
        }

        $val = $valString;

        if(empty($conString)){
            $sql = $sql.$val.' WHERE 1';
        }else{
            $sql = $sql.$val.' WHERE '.$conString;
        }
        //file_put_contents('updateByQuery.txt',$sql);
        //echo $sql;
		self::$LastSql = $sql;
        $result = mysql_query($sql,$con);
        $result = mysql_affected_rows();
        if($closeDBLink){
            mysql_close($con);
        }


        return $result;
    }

	//通过自定义条件更新表
	public function UpdateDataToTableByQuery($tableName,$valArray,$conString = null,$closeDBLink = false){
        $con = $this->DBLink();
        $sql = 'UPDATE `'.$tableName.'` SET ';

        $val = '';

        foreach($valArray as $key=>$value){
            if(is_array($value)){
                $val = $val . ' `' . $key . '`='.$value['field'].$value['operator'].'"' . $value['value'] . '",';
            }else {
                $val = $val . ' `' . $key . '`="' . $value . '",';
            }
        }
        $val = substr($val, 0, -1);

        if(empty($conString)){
            $sql = $sql.$val.' WHERE 1';
        }else{
            $sql = $sql.$val.' WHERE '.$conString;
        }
		//file_put_contents('updateByQuery.txt',$sql);
        //echo $sql;
		self::$LastSql = $sql;
        $result = mysql_query($sql,$con);
		$result = mysql_affected_rows();
        if($closeDBLink){
            mysql_close($con);
        }


        return $result;
    }

    //更新数据
	public function UpdateDataToTable($tableName,$valArray,$conArray,$closeDBLink = false){
		$hasCond = false;
		$con = $this->DBLink();  
		$sql = 'UPDATE `'.$tableName.'` SET ';			
		
		$cond = ((isset($conArray['_logic']) && $conArray['_logic']=="AND")?(1):(0));
		
		$val = '';
		$logic = ((isset($conArray['_logic']))?$conArray['_logic']:'AND');
		foreach($valArray as $key=>$value){
		    if(is_array($value)){
                $val = $val . ' `' . $key . '`='.$value['field'].$value['operator'].'"' . $value['value'] . '",';
            }else {
                $val = $val . ' `' . $key . '`="' . $value . '",';
            }
		}
		
		foreach($conArray as $key=>$value){
			if($value=="" || $key=='_logic'){
				continue;
			}
			if($cond=='1' || $cond=='0'){
				$cond = "";
			}
			if(!$hasCond){
				$hasCond = true;
			}
			$cond =$cond.' `'.$key.'`="'.$value.'" '.$logic.' ';
		}
		if($hasCond){
			$cond = substr($cond, 0, (($logic=='AND')?(-4):(-3)));
		}
		$val = substr($val, 0, -1);
		$sql = $sql.$val.' WHERE '.$cond;
		self::$LastSql = $sql;
		$result = mysql_query($sql,$con);
		//echo $sql.'</br>';
		if($closeDBLink){
			mysql_close($con);
		}


		return $result;
	}

	//通过sql条件语句删除数据
    public function DeletDataByQuery($tableName,$conString,$closeDBLink = false){
        $con = $this->DBLink();
        $sql = 'DELETE FROM `'.$tableName.'`';

        if(empty($conString)){
            $sql = $sql.' WHERE 0';
        }else{
            $sql = $sql.' WHERE '.$conString;
        }
		self::$LastSql = $sql;
        $result = mysql_query($sql,$con);
        if($closeDBLink){
            mysql_close($con);
        }
        return $result;
    }

    //删除数据
	public function DeletDataFromTable($tableName,$conArray,$closeDBLink = false){
		$hasCond = false;
		$con = $this->DBLink();  
		$sql = 'DELETE FROM `'.$tableName.'`';			

		$cond = ((isset($conArray['_logic']) && $conArray['_logic']=="AND")?(1):(0));
		
		$logic = ((isset($conArray['_logic']))?$conArray['_logic']:'AND');
		
		
		foreach($conArray as $key=>$value){
			if($value=="" || $key=='_logic'){
				continue;
			}
			if($cond=='1' || $cond=='0'){
				$cond = "";
			}
			if(!$hasCond){
				$hasCond = true;
			}
			$cond =$cond.' `'.$key.'`="'.$value.'" '.$logic.' ';
		}
		if($hasCond){
			$cond = substr($cond, 0, (($logic=='AND')?(-4):(-3)));
		}
		$sql = $sql.' WHERE '.$cond;
		self::$LastSql = $sql;
		$result = mysql_query($sql,$con);
		//echo $sql;
		if($closeDBLink){
			mysql_close($con);
		}
		return $result;
	}
	

    //查找数据
	public function SelectDataFromTable($tableName,$conArray,$closeDBLink = false,$field='*'){
		$hasCond = false;
		$con = $this->DBLink();  
		$sql = 'SELECT '.$field.' FROM `'.$tableName.'`';	
		

		if(!empty($conArray)){
			$cond = ((isset($conArray['_logic']) && $conArray['_logic']=="AND")?(1):(0));
			
			$logic = ((isset($conArray['_logic']))?$conArray['_logic']:'AND');
			
			
			foreach($conArray as $key=>$value){
			    if(strpos($key, '_') === 0){
			        continue;
                }
				if($value=="" || $key=='_logic'){
					continue;
				}
				if($cond=='1' || $cond=='0'){
					$cond = "";
				}
				if(!$hasCond){
					$hasCond = true;
				}
				$cond =$cond.' `'.$key.'`="'.$value.'" '.$logic.' ';
			}
			if($hasCond){
				$cond = substr($cond, 0, (($logic=='AND')?(-4):(-3)));
			}
		}
		
		if(!empty($conArray)){
			$sql = $sql.' WHERE '.$cond;
		}
		
		if(isset($conArray['_orderby']) && isset($conArray['_orderrule'])){
			
            $sql = $sql.' ORDER BY '.'`'.$conArray['_orderby'].'` '.$conArray['_orderrule'];
        }

        if(isset($conArray['_limfrom']) && isset($conArray['_limto'])){
            $sql = $sql.' LIMIT '.$conArray['_limfrom'].','.$conArray['_limto'];
        }
		self::$LastSql = $sql;
		$result = mysql_query($sql,$con);
		if($closeDBLink){
			mysql_close($con);
		}
		return $result;
	}




	public function SelectDataByQuery($tableName,$query,$closeDBLink=false,$field='*'){
        $con = $this->DBLink();
        $sql = 'SELECT '.$field.' FROM `'.$tableName.'` WHERE '.$query;
       // echo $sql;
		self::$LastSql = $sql;
        $result = mysql_query($sql,$con);
        if($closeDBLink){
        }
        return $result;
        mysql_close($con);
    }

    //查找数据
    public function SelectDatasFromTable($tableName,$conArray,$closeDBLink = false,$field='*',$orderRule=[]){
        $hasCond = false;
        $con = $this->DBLink();
        $sql = 'SELECT '.$field.' FROM `'.$tableName.'`';

        if(!empty($conArray)){
            $cond = ((isset($conArray['_logic']) && $conArray['_logic']=="AND")?(1):(0));

            $logic = ((isset($conArray['_logic']))?$conArray['_logic']:'AND');


            foreach($conArray as $key=>$value){
                if($value=="" || $key=='_logic'){
                    continue;
                }
                if($cond=='1' || $cond=='0'){
                    $cond = "";
                }
                if(!$hasCond){
                    $hasCond = true;
                }
                $tCondStr = '';
                $orcondlist = explode('|',$value);//OR条件
                if(count($orcondlist)>1) {
                    $tCondStr = '(';
                    foreach ($orcondlist as $sval) {
                        $tCondStr = $tCondStr . ' `' . $key . '`="' . $sval . '" OR';
                    }
                    $tCondStr = rtrim($tCondStr,'OR');
                    $tCondStr = $tCondStr . ')';

                }

                $andcondlist = explode('&',$value);//AND条件
                if(count($andcondlist)>1) {
                    $tCondStr = '(';
                    foreach ($andcondlist as $sval) {
                        $tCondStr = $tCondStr . ' `' . $key . '`="' . $sval . '" AND';
                    }
                    $tCondStr = rtrim($tCondStr,'AND');
                    $tCondStr = $tCondStr . ')';
                }

                if($tCondStr!='') {
                    $cond = $cond . ' ' . $tCondStr . ' ' . $logic . ' ';
                }else{
                    $cond = $cond . ' `' . $key . '`="' . $value . '" AND';
                }
                $hasCond = true;
            }
            if($hasCond){
                $cond = substr($cond, 0, (($logic=='AND')?(-4):(-3)));
            }

        }

        if(!empty($conArray)){
            $sql = $sql.' WHERE '.$cond;
        }
		
		if(!empty($orderRule) && isset($orderRule['by']) && isset($orderRule['rule'])){
			$sql = $sql.' ORDER BY `'.$orderRule['by'].'` '.$orderRule['rule'];
		}
       // echo '</br>'.$sql.'</br>';
        //file_put_contents(time().'.txt',$sql);
		self::$LastSql = $sql;
        $result = mysql_query($sql,$con);
        if($closeDBLink){
            mysql_close($con);
        }
        return $result;
    }
	
	public function DBManager(){
		$con = $this->DBLink();
		$this->dbLink = $con;
		if(!$con)
		{
		  die('Could not connect: ' . mysql_error());
		}

		if(mysql_query("CREATE DATABASE ".$this->C()['database'],$con))
		{
		  echo "数据库创建</br>";
		}
		else
		{
			if(mysql_errno() != 1007){
				echo "Can not creating database: " . mysql_errno()."</br>";
			}
		}
		mysql_close($con);
	}

    //初始化数据库
	public function InitDB(){
		$link = $this->DBLink();
		foreach($this->T() as $key=>$value){
			if(!$this->ExistTable($value['name'],$link)){
				$real_command = str_replace('#DBName#',$value['name'],$value['command']);
				$r = mysql_query($real_command,$link);
				if($r){
					echo '表 '.$value['name'].' 创建</br>';
				}else{
					echo '表 '.$value['name'].' 创建失败</br>';
				}
			}else{
				echo '表 '.$value['name'].' 存在</br>';
			}

			if(array_key_exists('default',$value)){
                foreach ($value['default'] as $dkey => $dvalue) {
                    if($this->InsertDataToTable($value['name'],$dvalue)) {
                        echo '<div style="white-space:pre">   *初始化数据项[' . $dkey . "]成功</br></div>";
                    }else{
                        echo '<div style="white-space:pre">   *初始化数据项[' . $dkey . "]失败</br></div>";
                    }
			    }
            }
		}
		mysql_close($link);
	}
	
	public function ExistTable($tableName,$con){
		$result =mysql_fetch_row(mysql_query("SHOW TABLES LIKE '".$tableName."' ",$con));
		if($result){
			return true;
		}else{
			return false;
		}
	}

	//快速获取数据库链接
	public function DBLink(){
	    //test_server
        $ipLink = "localhost";
        if(isset($_REQUEST['dblink']) && $_REQUEST['dblink']=="test"){
            $ipLink = $this->C()['test_server'];
        }else{
            $ipLink = $this->C()['server'];
        }
		$con = mysql_connect($ipLink,$this->C()['admin'],$this->C()['password']);
		mysql_set_charset($this->C()['charset']);
		if($con){
			mysql_select_db($this->C()['database']);
		}
		return $con;
	}
}
?>