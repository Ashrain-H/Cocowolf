<?php
require '../../../config.inc.php';
header('content-type:application/json;charset=UTF-8');
if (!defined('__TYPECHO_ROOT_DIR__')) {
    $ret_resp = [
		'status' => false,
		'message' => 'Access Denied! 此内容不允许直接调用!'
	];
    echo(json_encode($ret_resp));
    exit;
}
$chkuser = Typecho_Widget::widget('Widget_User');
$options = Typecho_Widget::widget('Widget_Options');
if((isset($_GET['apitoken']) && $_GET['apitoken'] == __TYPECHO_LORE_API_TOKEN__)) {
    switch($_GET['action']) {
        case "changelore":
            if ($chkuser->hasLogin()) {
                echo('HelloWorld');
            } else {
                $ret_resp = [
		            'status' => false,
		            'message' => 'Access Denied! 您还未登录!'
	            ];
	            exit(json_encode($ret_resp));
            }
        break;
    }
}