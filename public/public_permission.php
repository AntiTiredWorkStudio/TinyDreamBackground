<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-5-12
 * Time: 下午 11:13
 */


define('PERMISSION_LOCAL',1);//只允许用过localhost请求
define('PERMISSION_AUTH_FREE',2);//强制无需校验签名
define('PERMISSION_AUTH_FORCE',4);//强制校验签名
define('PERMISSION_USER_ADMIN',8);//超级管理员权限
define('PERMISSION_USER_OWNER',16);//管理员/池主权限
define('PERMISSION_USER_USER',32);//用户权限
define('PERMISSION_ALL','all');//无需权限可访问


class PermissionManager{
    public static function CheckPermissions($permission,$targetPermission){
        return ($targetPermission & $permission) == $permission;
    }

    public $permission_free = true;

    public $targetPermission = PERMISSION_ALL;
    public function PermissionManager($tPermission){
        $this->targetPermission = $tPermission;
        $this->permission_free = $this->targetPermission == PERMISSION_ALL;
    }

    public function CheckServerName($serverName){
        if($this->permission_free){
            return true;
        }

        return !self::CheckPermissions(PERMISSION_LOCAL,$this->targetPermission) || $serverName=='localhost';
    }

    public function AuthFree($auth_option){
        if($this->permission_free && !$auth_option){
            return true;
        }

        return (!$auth_option && !self::CheckPermissions(PERMISSION_AUTH_FORCE,$this->targetPermission)) || self::CheckPermissions(PERMISSION_AUTH_FREE,$this->targetPermission);
    }

    public function UserAuth(){
        if($this->permission_free){
            return true;
        }
        $admin = self::CheckPermissions(PERMISSION_USER_ADMIN,$this->targetPermission);
        $owner = self::CheckPermissions(PERMISSION_USER_OWNER,$this->targetPermission);
        $user = self::CheckPermissions(PERMISSION_USER_USER,$this->targetPermission);
        return Adapter::UserPermissionMethord($admin,$owner,$user);
    }
}


?>