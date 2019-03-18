var TestUserList = [
    <?php
        echo $configUserList;
    ?>
];

var PERMISSION_USER = function (uid) {
    return TestUserList.indexOf(uid)!=-1;
}

var result=