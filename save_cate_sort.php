<?php
require_once dirname(dirname(__DIR__)) . '/mainfile.php';
require_once __DIR__ . '/function.php';
$sort = 1;
if (isset($_POST['CateID'])) {
    foreach ($_POST['CateID'] as $CateID) {
        $sql = 'update ' . $xoopsDB->prefix('tad_web_cate') . " set `CateSort`='{$sort}' where `CateID`='{$CateID}'";
        $xoopsDB->queryF($sql) or die(_TAD_SORT_FAIL . ' (' . date('Y-m-d H:i:s') . ')' . $sql);
        $sort++;
    }
    echo _TAD_SORTED . '(' . date('Y-m-d H:i:s') . ')';
}
