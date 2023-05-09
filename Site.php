<?php
if (version_compare(phpversion(), "5.3.0", ">=")  == 1)
  error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
else
  error_reporting(E_ALL & ~E_NOTICE);
require_once('classes/CMySQL.php');
$iItemId = (int)$_POST['id']; 
$sIp = getVisitorIP();
$sName = $GLOBALS['MySQL']->escape(strip_tags($_POST['name']));
$sText = $GLOBALS['MySQL']->escape(strip_tags($_POST['text']));
if ($sName && $sText) {
    $iOldId = $GLOBALS['MySQL']->getOne("SELECT `c_item_id` FROM `s163_items_cmts` WHERE `c_item_id` = '{$iItemId}' AND `c_ip` = '{$sIp}' AND `c_when` >= UNIX_TIMESTAMP() - 600 LIMIT 1");
    if (! $iOldId) {
        $GLOBALS['MySQL']->res("INSERT INTO `s163_items_cmts` SET `c_item_id` = '{$iItemId}', `c_ip` = '{$sIp}', `c_when` = UNIX_TIMESTAMP(), `c_name` = '{$sName}', `c_text` = '{$sText}'");
        $GLOBALS['MySQL']->res("UPDATE `s163_items` SET `comments_count` = `comments_count` + 1 WHERE `id` = '{$iItemId}'");
        // и выводим последние 5 комментариев
        $sOut = '';
        $aComments = $GLOBALS['MySQL']->getAll("SELECT * FROM `s163_items_cmts` WHERE `c_item_id` = '{$iItemId}' ORDER BY `c_when` DESC LIMIT 5");
        foreach ($aComments as $i => $aCmtsInfo) {
            $sWhen = date('d/m/Y H:i', $aCmtsInfo['c_when']);
            $sOut .= <<<EOF
<div class="comment" id="{$aCmtsInfo['c_id']}">
    <p>Автор: {$aCmtsInfo['c_name']} <span>({$sWhen})</span>:</p>
    <p>{$aCmtsInfo['c_text']}</p>
</div>
EOF;
        }
        echo $sOut;
        exit;
    }
}
echo 1;
exit;
function getVisitorIP() {
    $ip = "0.0.0.0";
    if( ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) && ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) ) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif( ( isset( $_SERVER['HTTP_CLIENT_IP'])) && (!empty($_SERVER['HTTP_CLIENT_IP'] ) ) ) {
        $ip = explode(".",$_SERVER['HTTP_CLIENT_IP']);
        $ip = $ip[3].".".$ip[2].".".$ip[1].".".$ip[0];
    } elseif((!isset( $_SERVER['HTTP_X_FORWARDED_FOR'])) || (empty($_SERVER['HTTP_X_FORWARDED_FOR']))) {
        if ((!isset( $_SERVER['HTTP_CLIENT_IP'])) && (empty($_SERVER['HTTP_CLIENT_IP']))) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    }
    return $ip;
}
?>
