<?php
//區塊主函式 (班級選單(tad_webs))
function tad_web_list($options)
{
    global $xoopsDB;

    $DefWebID          = isset($_REQUEST['WebID']) ? intval($_REQUEST['WebID']) : '';
    $block['DefWebID'] = $DefWebID;

    $sql = "SELECT * FROM " . $xoopsDB->prefix("tad_web") . " WHERE WebEnable='1' ORDER BY CateID,WebSort";
    $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
    $i = 0;
    while ($all = $xoopsDB->fetchArray($result)) {
        foreach ($all as $k => $v) {
            $$k = $v;
        }

        $block['webs'][$i]['WebID'] = $WebID;
        $block['webs'][$i]['title'] = $WebTitle;
        $block['webs'][$i]['name']  = $WebName;
        $block['webs'][$i]['url']   = preg_match('/modules\/tad_web/', $_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] . "?WebID={$WebID}" : XOOPS_URL . "/modules/tad_web/index.php?WebID={$WebID}";

        $i++;
    }

    return $block;
}
