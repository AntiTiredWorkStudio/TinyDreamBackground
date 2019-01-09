<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-1-10
 * Time: 上午 1:09
 */
class Adapter{
    public static function UserPermissionMethord($admin,$owner,$user){
        LIB('us');
        $userId = "";
        if($admin || $owner || $user){
            if(isset($_REQUEST['openid']) || isset($_REQUEST['uid'])) {
                $userId = (isset($_REQUEST['openid']) ? $_REQUEST['openid'] : $_REQUEST['uid']);
            }
            if($userId == ""){
                return false;
            }else{
                $identity = UserManager::GetUserIdentity($userId)['identity'];
                switch ($identity){
                    case 'ADMIN':
                        return true;
                    case 'OWNER':
                        return !$admin;
                    case 'USER':
                        return !$admin && !$owner;
                }
            }
        }
        return true;
    }

    public static function AuthMethord($module,$action,$requestArray){
        LIB('auth');
        $auth = AuthManager::ConfirmRequest($module,$action,$requestArray);
        if(!$auth['result']){
            die(json_encode(RESPONDINSTANCE('97',$auth['content']),JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
    }
}
?>