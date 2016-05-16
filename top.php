<?php
/*-----------引入檔案區--------------*/
include_once "header.php";
$xoopsOption['template_main'] = 'tad_web_top_b3.html';
include_once XOOPS_ROOT_PATH . "/header.php";
/*-----------function區--------------*/

function list_top()
{
    global $xoopsTpl, $xoopsDB;

    define('_DISPLAY_MODE', 'index');
    $web_plugin_enable_arr = get_web_config("web_plugin_enable_arr", 0);
    if (empty($web_plugin_enable_arr)) {
        $enable_arr = get_dir_plugins();
    } else {
        $enable_arr = explode(',', $web_plugin_enable_arr);
    }
    $i       = 0;
    $all_top = $king = $king_rank = $WebNames = $WebTitles = '';
    foreach ($enable_arr as $dirname) {
        if (empty($dirname)) {
            continue;
        }
        $pluginConfig = '';
        include_once "plugins/{$dirname}/config.php";
        if ($pluginConfig['top_table'] == '') {
            continue;
        }

        $sql    = "select a.WebID, count(*) as cc , b.WebName ,b.WebTitle from " . $xoopsDB->prefix($pluginConfig['top_table']) . " as a  left join " . $xoopsDB->prefix("tad_web") . " as b on a.WebID=b.WebID where b.`WebEnable`='1' group by a.WebID order by cc desc limit 0,10";
        $result = $xoopsDB->query($sql) or web_error($sql);
        $top    = '';
        $j      = 1;
        while (list($WebID, $count, $WebName, $WebTitle) = $xoopsDB->fetchRow($result)) {
            $top[$j]['count']    = $count;
            $top[$j]['WebName']  = $WebName;
            $top[$j]['WebTitle'] = $WebTitle;
            $top[$j]['WebID']    = $WebID;

            $king_rank[$WebID] += $count * $pluginConfig['top_score'];
            $WebNames[$WebID]  = $WebName;
            $WebTitles[$WebID] = $WebTitle;
            $j++;

        }

        $all_top[$i]['pluginName'] = $pluginConfig['name'];
        $all_top[$i]['dirname']    = $dirname;
        $all_top[$i]['top']        = $top;

        $i++;
    }

    //標籤部份
    $sql    = "select a.WebID, count(*) as cc , b.WebName ,b.WebTitle from " . $xoopsDB->prefix('tad_web_tags') . " as a  left join " . $xoopsDB->prefix("tad_web") . " as b on a.WebID=b.WebID where b.`WebEnable`='1' group by a.WebID order by cc desc limit 0,10";
    $result = $xoopsDB->query($sql) or web_error($sql);
    $top    = '';
    $j      = 1;
    while (list($WebID, $count, $WebName, $WebTitle) = $xoopsDB->fetchRow($result)) {
        $top[$j]['count']    = $count;
        $top[$j]['WebName']  = $WebName;
        $top[$j]['WebTitle'] = $WebTitle;
        $top[$j]['WebID']    = $WebID;

        $king_rank[$WebID] += $count;
        $WebNames[$WebID]  = $WebName;
        $WebTitles[$WebID] = $WebTitle;
        // $king[$WebID]['WebName'] = $WebName;
        $j++;
    }
    $all_top[$i]['pluginName'] = _MD_TCW_TAG;
    $all_top[$i]['dirname']    = 'tag';
    $all_top[$i]['top']        = $top;

    $i++;

    //點閱數部份
    $sql    = "select WebID, WebCounter , WebName ,WebTitle from " . $xoopsDB->prefix('tad_web') . " where `WebEnable`='1' group by WebID order by WebCounter desc limit 0,10";
    $result = $xoopsDB->query($sql) or web_error($sql);
    $top    = '';
    $j      = 1;
    while (list($WebID, $count, $WebName, $WebTitle) = $xoopsDB->fetchRow($result)) {
        $top[$j]['count']    = $count;
        $top[$j]['WebName']  = $WebName;
        $top[$j]['WebTitle'] = $WebTitle;
        $top[$j]['WebID']    = $WebID;

        $king_rank[$WebID] += round($count / 500, 0);
        $WebNames[$WebID]  = $WebName;
        $WebTitles[$WebID] = $WebTitle;
        // $king[$WebID]['WebName'] = $WebName;
        $j++;
    }
    $all_top[$i]['pluginName'] = _MD_TCW_WEB_COUNTER;
    $all_top[$i]['dirname']    = 'index';
    $all_top[$i]['top']        = $top;

    $i++;

    //區塊部份
    $sql    = "select a.WebID, count(*) as cc , a.`ShareFrom`, b.WebName ,b.WebTitle from " . $xoopsDB->prefix('tad_web_blocks') . " as a  left join " . $xoopsDB->prefix("tad_web") . " as b on a.WebID=b.WebID where a.`BlockEnable`=1 and a.`BlockPosition`!='uninstall' and a.`plugin`='custom' and b.`WebEnable`='1' group by a.WebID order by cc desc limit 0,10";
    $result = $xoopsDB->query($sql) or web_error($sql);
    $top    = '';
    $j      = 1;
    while (list($WebID, $count, $ShareFrom, $WebName, $WebTitle) = $xoopsDB->fetchRow($result)) {
        $top[$j]['count']    = $count;
        $top[$j]['WebName']  = $WebName;
        $top[$j]['WebTitle'] = $WebTitle;
        $top[$j]['WebID']    = $WebID;

        $king_rank[$WebID] += ($ShareFrom == 0) ? $count * 2 : $count;
        $WebNames[$WebID]  = $WebName;
        $WebTitles[$WebID] = $WebTitle;
        // $king[$WebID]['WebName'] = $WebName;
        $j++;
    }
    $all_top[$i]['pluginName'] = _MD_TCW_WEB_BLOCK;
    $all_top[$i]['dirname']    = 'index';
    $all_top[$i]['top']        = $top;

    $i++;

    arsort($king_rank);
    // die(var_export($WebNames));
    $xoopsTpl->assign('king_rank', $king_rank);
    $xoopsTpl->assign('WebNames', $WebNames);
    $xoopsTpl->assign('WebTitles', $WebTitles);
    $xoopsTpl->assign('all_top', $all_top);
    $xoopsTpl->assign('plugin_data_total', 10);
}

/*-----------執行動作判斷區----------*/
include_once $GLOBALS['xoops']->path('/modules/system/include/functions.php');
$op    = system_CleanVars($_REQUEST, 'op', '', 'string');
$WebID = system_CleanVars($_REQUEST, 'WebID', 0, 'int');

common_template($WebID, $web_all_config);

switch ($op) {

    //預設動作
    default:
        list_top($WebID);

        break;

}

/*-----------秀出結果區--------------*/
include_once 'footer.php';
include_once XOOPS_ROOT_PATH . '/footer.php';