<?php
include_once 'header.php';
include_once "../function.php";
$op   = isset($_REQUEST['op']) ? $_REQUEST['op'] : '';
$sort = 1;
if ($op == 'plugin') {
    foreach ($_POST['tr'] as $dirname) {
        $sql = "update " . $xoopsDB->prefix("tad_web_plugins") . " set `PluginSort`='{$sort}' where `PluginDirname`='{$dirname}' and WebID='{$WebID}'";
        $xoopsDB->queryF($sql) or die(_TAD_SORT_FAIL . " (" . date("Y-m-d H:i:s") . ")" . $sql);
        $sort++;
    }
    mk_menu_var_file(0);
} else {

    foreach ($_POST['tr'] as $WebID) {
        $sql = "update " . $xoopsDB->prefix("tad_web") . " set `WebSort`='{$sort}' where `WebID`='{$WebID}'";
        $xoopsDB->queryF($sql) or die(_MA_TCW_UPDATE_FAIL . " (" . date("Y-m-d H:i:s") . ")" . $sql);
        $sort++;
    }
}

echo _MA_TCW_SAVE_SORT_OK . "(" . date("Y-m-d H:i:s") . ")";
