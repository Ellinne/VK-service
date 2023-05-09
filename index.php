<?php
if (version_compare(phpversion(), "5.3.0", ">=")  == 1)
  error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
else
  error_reporting(E_ALL & ~E_NOTICE);
require_once('classes/CMySQL.php'); 
$sCode = '';
$iItemId = (int)$_GET['id'];
if ($iItemId) { // Просматриваем данные
    $aItemInfo = $GLOBALS['MySQL']->getRow("SELECT * FROM `s163_items` WHERE `id` = '{$iItemId}'"); 
    $sCode .= '<h1>'.$aItemInfo['title'].'</h1>';
    $sCode .= '<h3>'.date('d/m/Y', $aItemInfo['when']).'</h3>';
    $sCode .= '<h2>Description:</h2>';
    $sCode .= '<h3>'.$aItemInfo['description'].'</h3>';
    $sCode .= '<h3><a href="'.$_SERVER['PHP_SELF'].'">Back</a></h3>';
    $sComments = '';
    $aComments = $GLOBALS['MySQL']->getAll("SELECT * FROM `s163_items_cmts` WHERE `c_item_id` = '{$iItemId}' ORDER BY `c_when` DESC LIMIT 5");
    foreach ($aComments as $i => $aCmtsInfo) {
        $sWhen = date('d/m/Y H:i', $aCmtsInfo['c_when']);
        $sComments .= <<<EOF
<div class="comment" id="{$aCmtsInfo['c_id']}">
    <p>Nickname: {$aCmtsInfo['c_name']} <span>({$sWhen})</span>:</p>
    <p>{$aCmtsInfo['c_text']}</p>
</div>
EOF;
    }
    ob_start();
    ?>
    <div class="container" id="comments">
        <h2>Комментарии</h2>
        <script type="text/javascript">
        function submitComment(e) {
            var sName = $('#name').val();
            var sText = $('#text').val();
            if (sName && sText) {
                $.post('comment.php', { name: sName, text: sText, id: <?= $iItemId ?> },
                    function(data){
                        if (data != '1') {
                          $('#comments_list').fadeOut(1000, function () {
                            $(this).html(data);
                            $(this).fadeIn(1000);
                          });
                        } else {
                          $('#comments_warning2').fadeIn(1000, function () {
                            $(this).fadeOut(1000);
                          });
                        }
                    }
                );
            } else {
              $('#comments_warning1').fadeIn(1000, function () {
                $(this).fadeOut(1000);
              });
            }
        };
        </script> 
        <div id="comments_warning1" style="display:none">Not all textboxes filled</div>
        <form onsubmit="submitComment(this); return false;">
            <table>
                <tr><td class="label"><label>Your nickname: </label></td><td class="field"><input type="text" value="" title="Input your name" id="name" /></td></tr>
                <tr><td class="label"><label>Comment: </label></td><td class="field"><textarea name="text" id="text"></textarea></td></tr>
                <tr><td class="label">&nbsp;</td><td class="field"><input type="submit" value="Отправить" /></td></tr>
            </table>
        </form>

      <div id="comments_list"><?= $sComments ?></div>
    </div>

    <?

    $sCommentsBlock = ob_get_clean();

} else {
    $sCode .= '<h1>Список пунктов:</h1>';
    $aItems = $GLOBALS['MySQL']->getAll("SELECT * FROM `s163_items` ORDER by `when` ASC"); // Получаем информацию обо всех пунктах из базы данных
    foreach ($aItems as $i => $aItemInfo) {
        $sCode .= '<h2><a href="'.$_SERVER['PHP_SELF'].'?id='.$aItemInfo['id'].'">'.$aItemInfo['title'].'</a></h2>';
    }
}
 
088
?>
