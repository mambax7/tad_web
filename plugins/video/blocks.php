<?php
use XoopsModules\Tadtools\Utility;
/************** list_video *************/
function list_video($WebID, $config = [])
{
    global $xoopsDB, $xoopsTpl, $TadUpFiles;
    if (empty($WebID)) {
        return;
    }
    require_once __DIR__ . '/class.php';

    $block = '';
    $tad_web_video = new tad_web_video($WebID);
    $block = $tad_web_video->list_all('', $config['limit'], 'return', '', $config['mode']);

    return $block;
}

/************** random_video *************/

function random_video($WebID, $config = [])
{
    global $xoopsDB, $xoopsTpl;
    if (empty($WebID)) {
        return;
    }
    $block = [];

    $sql = 'select * from ' . $xoopsDB->prefix('tad_web_video') . " where WebID='$WebID' order by rand() limit 0,1";
    $result = $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);
    $all = $xoopsDB->fetchArray($result);

    //以下會產生這些變數： $VideoID , $VideoName , $VideoDesc , $VideoDate , $VideoPlace , $uid , $WebID , $VideoCount
    if ($all) {
        foreach ($all as $k => $v) {
            $$k = $v;
        }
    }

    if (empty($VideoPlace)) {
        return;
    }

    $block['main_data'] = "<div class='embed-responsive embed-responsive-4by3'><iframe title='random_video' class='embed-responsive-item' src='https://www.youtube.com/embed/{$VideoPlace}?feature=oembed' frameborder='0' allowfullscreen></iframe></div>";
    $block['VideoID'] = $VideoID;
    $block['VideoName'] = $VideoName;

    return $block;
}

/************** latest_video *************/

function latest_video($WebID, $config = [])
{
    global $xoopsDB, $xoopsTpl;
    if (empty($WebID)) {
        return;
    }
    $block = [];
    $sql = 'select * from ' . $xoopsDB->prefix('tad_web_video') . " where WebID='$WebID' order by VideoDate desc , VideoID desc limit 0,1";
    $result = $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);
    $all = $xoopsDB->fetchArray($result);

    //以下會產生這些變數： $VideoID , $VideoName , $VideoDesc , $VideoDate , $VideoPlace , $uid , $WebID , $VideoCount
    if ($all) {
        foreach ($all as $k => $v) {
            $$k = $v;
        }
    }

    if (empty($VideoPlace)) {
        return;
    }

    $block['main_data'] = "<div class='embed-responsive embed-responsive-4by3'><iframe title='latest_video' class='embed-responsive-item' src='https://www.youtube.com/embed/{$VideoPlace}?feature=oembed' frameborder='0' allowfullscreen></iframe></div>";
    $block['VideoID'] = $VideoID;
    $block['VideoName'] = $VideoName;

    return $block;
}
