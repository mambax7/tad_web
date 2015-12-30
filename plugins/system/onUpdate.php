<?php

if (system_onUpdate1_chk()) {
    system_onUpdate1_go();
}

//修改欄位
function system_onUpdate1_chk()
{
    global $xoopsDB;
    $sql    = "select count(*) from " . $xoopsDB->prefix("tad_web_blocks") . " where `plugin`='xoops'";
    $result = $xoopsDB->query($sql);
    if (!empty($result)) {
        return true;
    }

    return false;
}

function system_onUpdate1_go()
{
    global $xoopsDB;
    $sql    = "select bid,show_func from " . $xoopsDB->prefix("newblocks") . " where dirname='tad_web' order by weight";
    $result = $xoopsDB->query($sql) or web_error($sql);

    $myts = MyTextSanitizer::getInstance();
    //來自系統的區塊
    while (list($bid, $show_func) = $xoopsDB->fetchRow($result)) {
        $sql2    = "select BlockID,BlockTitle,BlockEnable,BlockPosition,BlockSort,WebID from " . $xoopsDB->prefix("tad_web_blocks") . " where `plugin`='xoops' and BlockName='{$bid}'";
        $result2 = $xoopsDB->queryF($sql2);
        while (list($BlockID, $BlockTitle, $BlockEnable, $BlockPosition, $BlockSort, $WebID) = $xoopsDB->fetchRow($result2)) {
            $BlockTitle = $myts->addSlashes($BlockTitle);
            if ($show_func == 'tad_web_menu') {
                $sql3 = "update " . $xoopsDB->prefix("tad_web_blocks") . " set `BlockTitle`='{$BlockTitle}',BlockEnable='{$BlockEnable}',BlockPosition='{$BlockPosition}' ,BlockSort='{$BlockSort}'  where `plugin`='system' and BlockName='my_menu' and WebID='{$WebID}'";
                $xoopsDB->queryF($sql3);

                $BlockSort = max_blocks_sort($WebID, 'side');
                $sql3      = "update " . $xoopsDB->prefix("tad_web_blocks") . " set `BlockTitle`='" . _MD_TCW_SYSTEM_BLOCK_LOGIN . "',BlockEnable='1',BlockPosition='side' ,BlockSort='{$BlockSort}'  where `plugin`='system' and BlockName='login' and WebID='{$WebID}'";
                $xoopsDB->queryF($sql3);

            } elseif ($show_func == 'tad_web_list') {
                $sql3 = "update " . $xoopsDB->prefix("tad_web_blocks") . " set `BlockTitle`='{$BlockTitle}',BlockEnable='{$BlockEnable}',BlockPosition='{$BlockPosition}' ,BlockSort='{$BlockSort}'  where `plugin`='system' and BlockName='web_list' and WebID='{$WebID}'";
                $xoopsDB->queryF($sql3);
            } elseif ($show_func == 'tad_web_image') {
                $sql3 = "update " . $xoopsDB->prefix("tad_web_blocks") . " set `BlockTitle`='{$BlockTitle}',BlockEnable='{$BlockEnable}',BlockPosition='{$BlockPosition}' ,BlockSort='{$BlockSort}'  where `plugin`='action' and BlockName='action_slide' and WebID='{$WebID}'";
                $xoopsDB->queryF($sql3);
            }
        }
        $sql2 = "delete from " . $xoopsDB->prefix("tad_web_blocks") . " where `plugin`='xoops' and BlockName='{$bid}'";
        $xoopsDB->queryF($sql2);
    }

    return true;
}
