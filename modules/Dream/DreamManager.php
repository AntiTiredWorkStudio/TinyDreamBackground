<?php
//引用此页面前需先引用conf.php
error_reporting(E_ALL ^ E_DEPRECATED);

LIB('db');

class DreamManager extends DBManager{
    public function info()
    {
        return "梦想模块"; // TODO: Change the autogenerated stub
    }
	public function DreamManager(){
		parent::__construct();
	}
}
?>