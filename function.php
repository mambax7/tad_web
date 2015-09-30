<?php
define("TADTOOLS_PATH", XOOPS_ROOT_PATH . "/modules/tadtools");
define("TADTOOLS_URL", XOOPS_URL . "/modules/tadtools");

if (!file_exists(XOOPS_ROOT_PATH . "/modules/tadtools/TadUpFiles.php")) {
    redirect_header("http://www.tad0616.net/modules/tad_uploader/index.php?of_cat_sn=50", 3, _TAD_NEED_TADTOOLS);
}

include_once XOOPS_ROOT_PATH . "/modules/tadtools/TadUpFiles.php";
$TadUpFiles = new TadUpFiles("tad_web");
$subdir     = isset($WebID) ? "/{$WebID}" : "";
$TadUpFiles->set_dir('subdir', $subdir);

//引入TadTools的函式庫
include_once TADTOOLS_PATH . "/tad_function.php";

/********************* 自訂函數 *********************/
function html5($content = "", $ui = false, $bootstrap = true)
{
    $jquery         = get_jquery($ui);
    $bootstrap_link = $bootstrap ? "<link rel='stylesheet' type='text/css' media='screen' href='" . XOOPS_URL . "/modules/tadtools/bootstrap3/css/bootstrap.css' />" : "";
    $main           = "<!DOCTYPE html>
      <html lang='zh-TW'>
        <head>
          <meta charset='utf-8'>
          <title></title>
          <meta name='viewport' content='width=device-width, initial-scale=1.0'>
          $bootstrap_link
          {$jquery}
        </head>
        <body>
            <div class='contain'>
                <div class='row'>
                    <div class='col-md-12'>
                        {$content}
                    </div>
                </div>
            </div>
        </body>
      </html>
      ";
    return $main;
}

function web_error($sql)
{
    global $isAdmin;
    if ($isAdmin) {
        die(html5("<div class='well'>$sql</div><div class='alert alert-danger'>" . mysql_error() . "</div>"));
    } else {
        redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());
    }
}

//取得所有網站設定值
function get_web_all_config($WebID = "")
{
    global $xoopsDB, $WebID;
    if (empty($WebID)) {
        return;
    }

    if (file_exists(XOOPS_ROOT_PATH . "/themes/for_tad_web_theme/theme_config.php")) {
        include XOOPS_ROOT_PATH . "/themes/for_tad_web_theme/theme_config.php";
        mk_dir(XOOPS_ROOT_PATH . "/uploads/tad_web/{$WebID}");
        mk_dir(XOOPS_ROOT_PATH . "/uploads/tad_web/{$WebID}/bg");
        mk_dir(XOOPS_ROOT_PATH . "/uploads/tad_web/{$WebID}/head");
        mk_dir(XOOPS_ROOT_PATH . "/uploads/tad_web/{$WebID}/logo");

        if (!file_exists(XOOPS_ROOT_PATH . "/uploads/tad_web/{$WebID}/bg/{$tad_web_config['web_bg']}")) {
            copy(XOOPS_ROOT_PATH . "/modules/tad_web/images/bg/{$tad_web_config['web_bg']}", XOOPS_ROOT_PATH . "/uploads/tad_web/{$WebID}/bg/{$tad_web_config['web_bg']}");
        }

        if (!file_exists(XOOPS_ROOT_PATH . "/uploads/tad_web/{$WebID}/head/{$tad_web_config['web_head']}")) {
            copy(XOOPS_ROOT_PATH . "/modules/tad_web/images/head/{$tad_web_config['web_head']}", XOOPS_ROOT_PATH . "/uploads/tad_web/{$WebID}/head/{$tad_web_config['web_head']}");
        }

        if (!file_exists(XOOPS_ROOT_PATH . "/uploads/tad_web/{$WebID}/logo/{$tad_web_config['web_logo']}")) {
            copy(XOOPS_ROOT_PATH . "/uploads/tad_web/{$WebID}/auto_logo/auto_logo.png", XOOPS_ROOT_PATH . "/uploads/tad_web/{$WebID}/logo/{$tad_web_config['web_logo']}");
        }

    }

    $sql = "select `ConfigName`,`ConfigValue` from " . $xoopsDB->prefix("tad_web_config") . " where `WebID`='$WebID'";

    $result = $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());

    while (list($ConfigName, $ConfigValue) = $xoopsDB->fetchRow($result)) {
        $tad_web_config[$ConfigName] = $ConfigValue;
    }

    return $tad_web_config;
}

//取得網站設定值
function get_web_config($ConfigName = null, $defWebID = "")
{
    global $xoopsDB;

    $andWebID = is_null($defWebID) ? "" : "and `WebID`='$defWebID'";

    $sql = "select `ConfigValue`,`WebID` from " . $xoopsDB->prefix("tad_web_config") . " where `ConfigName`='{$ConfigName}' $andWebID ";

    $result      = $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());
    $ConfigValue = "";
    if (!is_null($defWebID)) {
        list($ConfigValue, $WebID) = $xoopsDB->fetchRow($result);
    } else {
        while (list($Value, $WebID) = $xoopsDB->fetchRow($result)) {
            $ConfigValue[$WebID] = $Value;
        }
    }
    return $ConfigValue;

}

//儲存網站設定
function save_web_config($ConfigName = "", $ConfigValue = "")
{
    global $xoopsDB, $xoopsUser, $WebID, $isMyWeb;
    if (!empty($WebID) and !$isMyWeb) {
        return;
    }

    if (is_array($ConfigValue)) {
        $ConfigValue = implode(';', $ConfigValue);
    }
    $myts        = MyTextSanitizer::getInstance();
    $ConfigValue = $myts->addSlashes($ConfigValue);

    $sql = "replace into " . $xoopsDB->prefix("tad_web_config") . "
      (`ConfigName`, `ConfigValue`, `WebID`)
      values('{$ConfigName}' , '{$ConfigValue}', '{$WebID}')";

    $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());

}

function get_db_plugins($WebID = "", $only_enable = false)
{
    global $xoopsDB;
    $andEnable = ($only_enable) ? "and PluginEnable='1'" : "";

    $sql = "select * from " . $xoopsDB->prefix("tad_web_plugins") . " where WebID='{$WebID}' {$andEnable} order by PluginSort";
    //die($sql);
    $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());

    while ($all = $xoopsDB->fetchArray($result)) {
        $dirname           = $all['PluginDirname'];
        $all['limit']      = get_web_config($dirname . '_limit', $WebID);
        $all['display']    = get_web_config($dirname . '_display', $WebID);
        $plugins[$dirname] = $all;
    }
    //die(var_export($plugins));
    return $plugins;
}

function get_db_plugin($WebID = "", $dirname = "")
{
    global $xoopsDB;

    $sql    = "select * from " . $xoopsDB->prefix("tad_web_plugins") . " where WebID='{$WebID}' and PluginDirname='{$dirname}'";
    $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());

    $all = $xoopsDB->fetchArray($result);
    return $all;
}

//取得硬碟中外掛模組的名稱陣列
function get_dir_plugins()
{
    $dir = XOOPS_ROOT_PATH . "/modules/tad_web/plugins/";
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if (filetype($dir . $file) == "dir") {
                    if (substr($file, 0, 1) == '.') {
                        continue;
                    }

                    $plugins[] = $file;
                }
            }
            closedir($dh);
        }
    }
    sort($plugins);
    return $plugins;
}

//取得所有外掛
function get_plugins($WebID = '', $mode = 'show', $only_enable = false)
{
    global $TadUpFiles, $xoopsDB;

    $pluginsVal = get_db_plugins($WebID, $only_enable);
    $config_arr = get_web_all_config($WebID);

    $hide_function = array();
    if (empty($pluginsVal)) {
        $hide_function = explode(';', $config_arr['hide_function']);
    }

    $dir         = XOOPS_ROOT_PATH . "/modules/tad_web/plugins/";
    $dir_plugins = get_dir_plugins();
    foreach ($dir_plugins as $file) {
        if ($only_enable and empty($pluginsVal) and in_array($file, $hide_function)) {
            continue;
        }

        $pluginVal = get_db_plugin($WebID, $file);
        include $dir . $file . "/config.php";

        if (empty($pluginVal)) {
            $sort = plugins_max_sort($WebID, $file);
            $sql  = "replace into " . $xoopsDB->prefix("tad_web_plugins") . " (`PluginDirname`, `PluginTitle`, `PluginSort`, `PluginEnable`, `WebID`) values('{$file}', '{$pluginConfig['name']}', '{$sort}', '1', '{$WebID}')";
            $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());
        }

        if (!isset($config_arr[$file . '_limit'])) {
            save_web_config($file . '_limit', $pluginConfig['limit']);
            save_web_config($file . '_display', 1);
        }
        $pluginConfigs[$file] = $pluginConfig;
    }

    $pluginsVal = get_db_plugins($WebID, $only_enable);
    $i          = 0;
    foreach ($pluginsVal as $dirname => $plugin) {
        $plugins[$i]['dirname'] = $dirname;
        $plugins[$i]['config']  = $pluginConfigs[$dirname];
        $plugins[$i]['db']      = $pluginsVal[$dirname];

        if ($mode == 'edit') {
            // $plugins[$i]['upform'] = $TadUpFiles->upform(true, $dirname, '1', false);
        }

        $i++;
    }

    //die(var_export($plugins));
    return $plugins;
}

function plugins_max_sort($WebID, $dirname)
{
    global $xoopsDB;

    $sql    = "select max(PluginSort) from " . $xoopsDB->prefix("tad_web_plugins") . " where WebID='{$WebID}' and PluginDirname='{$dirname}'";
    $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());

    list($sort) = $xoopsDB->fetchRow($result);
    $sort++;
    return $sort;
}

//共同樣板部份
function common_template($WebID)
{
    global $xoopsTpl, $TadUpFiles, $xoopsDB, $menu_var;

    if ($WebID) {
        $xoopsTpl->assign('WebID', $WebID);

        if (!file_exists(XOOPS_ROOT_PATH . "/uploads/tad_web/{$WebID}/header.png")) {
            output_head_file($WebID);
        }

        /****網站設定值****/
        $all = get_web_all_config($WebID);
        foreach ($all as $ConfigName => $ConfigValue) {
            $xoopsTpl->assign($ConfigName, $ConfigValue);
            $$ConfigName = $ConfigValue;
        }
    }

    if (empty($display_blocks)) {
        $sql    = "select bid from " . $xoopsDB->prefix("newblocks") . " where dirname='tad_web' order by weight";
        $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());
        while (list($bid) = $xoopsDB->fetchRow($result)) {
            $display_blocks_arr[] = $bid;
        }
    } else {
        $display_blocks_arr = explode(',', $display_blocks);

    }

    if (file_exists(XOOPS_ROOT_PATH . "/modules/tadtools/FooTable.php")) {
        include_once XOOPS_ROOT_PATH . "/modules/tadtools/FooTable.php";

        $FooTable   = new FooTable();
        $FooTableJS = $FooTable->render();
    }
    $xoopsTpl->assign('FooTableJS', $FooTableJS);
    $xoopsTpl->assign('display_blocks_arr', $display_blocks_arr);

}

function mk_menu_var_file($WebID = null)
{
    //die('$WebID:' . $WebID);
    $all_plugins = get_plugins($WebID, 'show');
    //die(var_export($all_plugins));
    $current = "<?php\n";
    $i       = 1;
    foreach ($all_plugins as $plugin) {

        $dirname = $plugin['dirname'];

        $current .= "\$menu_var[$i]['id']     = $i;\n";
        $current .= "\$menu_var[$i]['title']  = '{$plugin['db']['PluginTitle']}';\n";
        $current .= "\$menu_var[$i]['url']    = '{$dirname}.php?WebID={$WebID}';\n";
        $current .= "\$menu_var[$i]['target'] = '_self';\n";
        $current .= "\$menu_var[$i]['WebID']  = '{$WebID}';\n";
        $current .= "\$menu_var[$i]['dirname']  = '{$dirname}';\n";
        $current .= "\$menu_var[$i]['cate'] = '{$plugin['config']['cate']}';\n";
        $current .= "\$menu_var[$i]['short']  = '{$plugin['config']['short']}';\n";
        $current .= "\$menu_var[$i]['icon']   = '{$plugin['config']['icon']}';\n\n";

        $i++;
    }

    if (empty($WebID)) {
        $file = XOOPS_ROOT_PATH . "/uploads/tad_web/menu_var.php";
    } else {
        $file = XOOPS_ROOT_PATH . "/uploads/tad_web/{$WebID}/menu_var.php";
    }
    file_put_contents($file, $current);

}

//取得回覆數量
function get_re_num($DiscussID = "")
{
    global $xoopsDB, $xoopsUser;
    if (empty($DiscussID)) {
        return 0;
    }

    $sql           = "select count(*) from " . $xoopsDB->prefix("tad_web_discuss") . " where ReDiscussID='$DiscussID'";
    $result        = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());
    list($counter) = $xoopsDB->fetchRow($result);
    return $counter;
}

function get_tad_web_mems($MemID)
{
    global $xoopsDB;

    $sql    = "select * from " . $xoopsDB->prefix("tad_web_mems") . " where MemID='{$MemID}'";
    $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());
    $all    = $xoopsDB->fetchArray($result);
    return $all;
}

function get_tad_web_link_mems($MemID)
{
    global $xoopsDB;

    $sql    = "select * from " . $xoopsDB->prefix("tad_web_link_mems") . " where MemID='{$MemID}'";
    $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());
    $all    = $xoopsDB->fetchArray($result);
    return $all;
}

//以流水號取得某筆tad_web資料
function get_tad_web($WebID = "")
{
    global $xoopsDB;
    if (empty($WebID)) {
        return;
    }

    $sql    = "select * from " . $xoopsDB->prefix("tad_web") . " where WebID='$WebID'";
    $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());
    $data   = $xoopsDB->fetchArray($result);
    return $data;
}

//取得網頁下成員的人數
function memAmount($WebID = "")
{
    global $xoopsDB;

    $sql         = "select count(*) from " . $xoopsDB->prefix("tad_web_link_mems") . " where WebID='{$WebID}'";
    $result      = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());
    list($count) = $xoopsDB->fetchRow($result);
    return $count;
}

//判斷是否為管理員
function isAdmin()
{
    global $xoopsUser, $xoopsModule;
    $isAdmin = false;
    if ($xoopsUser) {
        $module_id = $xoopsModule->getVar('mid');
        $isAdmin   = $xoopsUser->isAdmin($module_id);
    }
    return $isAdmin;
}

//登出按鈕
function logout_button($interface_menu = array())
{
    return $interface_menu;
}

//取得目前的學年學期陣列
function get_seme()
{
    global $xoopsDB;
    $y = date("Y");
    $m = date("n");
    if ($m >= 8) {
        $ys[0] = $y - 1911;
        //$ys[1]=1;
    } elseif ($m >= 2) {
        $ys[0] = $y - 1912;
        //$ys[1]=2;
    } else {
        $ys[0] = $y - 1912;
        //$ys[1]=1;
    }
    return $ys[0];
}

//
function getAllCateName($ColName = "", $WebID = "", $CateID = "")
{
    return array();
}

//更新刪除時是否限制身份
function onlyMine()
{
    global $xoopsUser, $isAdmin, $MyWebs, $WebID;
    if ($isAdmin) {
        return;
    } elseif (in_array($WebID, $MyWebs)) {
        return;
    }
    $uid = $xoopsUser->uid();
    return "and uid='$uid'";
}

//是否為網站擁有者
function MyWebID()
{
    global $xoopsUser, $xoopsDB;
    if ($xoopsUser) {
        $uid    = $xoopsUser->uid();
        $sql    = "select WebID from " . $xoopsDB->prefix("tad_web") . " where WebOwnerUid='$uid'";
        $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());
        $total  = $xoopsDB->getRowsNum($result);
        if (empty($total)) {
            return;
        }

        while (list($WebID) = $xoopsDB->fetchRow($result)) {
            $MyWebs[] = $WebID;
        }
    }
    return $MyWebs;
}

//取得網站資訊
function getWebInfo($WebID = null)
{
    global $xoopsDB;
    $WebID = intval($WebID);

    $sql    = "select * from " . $xoopsDB->prefix("tad_web") . " where WebID='{$WebID}'";
    $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());

    $Web = $xoopsDB->fetchArray($result);
    return $Web;
}

//取得網站資訊
function getAllWebInfo($get_col = 'WebTitle')
{
    global $xoopsDB;

    $sql    = "select `WebID`, `{$get_col}` from " . $xoopsDB->prefix("tad_web") . " order by WebSort";
    $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());

    while (list($WebID, $data) = $xoopsDB->fetchRow($result)) {
        $Webs[$WebID] = $data;
    }
    return $Webs;
}

//取得分類名稱
function getLevelName($WebID = "")
{
    global $xoopsDB;
    $sql            = "select `WebTitle` from " . $xoopsDB->prefix("tad_web") . " where WebID='$WebID'";
    $result         = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());
    list($WebTitle) = $xoopsDB->fetchRow($result);

    return $main;
}

//把模組設定項目轉為選項
function mc2arr($name = "", $def = "", $type = 'option', $other = "")
{
    global $xoopsModuleConfig;
    $arr = explode(",", $xoopsModuleConfig[$name]);
    foreach ($arr as $item) {
        if (ereg("=", $item)) {
            $vv          = explode("=", $item);
            $k           = $vv[0];
            $v           = $vv[1];
            $new_arr[$k] = $v;
            $v_as_k      = false;
        } else {
            $new_arr[] = $item;
            $v_as_k    = true;
        }
    }

    if ($type == "checkbox") {
        $opt = arr2chk($name, $new_arr, $def, $def, $v_as_k, $other);
    } elseif ($type == "radio") {
        $opt = arr2radio($name, $new_arr, $def, $v_as_k, $other);
    } elseif ($type == "array") {
        $opt = $new_arr;
    } else {
        $opt = arr2opt($new_arr, $def, $v_as_k, $other);
    }
    return $opt;
}

//把陣列轉為選項
function arr2opt($arr, $def = "", $v_as_k = false, $other = "")
{
    if (is_array($def)) {
        $def_arr = $def;
    } else {
        $def_arr = array($def);
    }
    foreach ($arr as $k => $v) {
        if ($v_as_k) {
            $k = $v;
        }

        $selected = (in_array($k, $def_arr)) ? "selected" : "";
        $main .= "<option value='$k' $selected $other>$v</option>";
    }
    return $main;
}

//把陣列轉為選項
function arr2chk($name, $arr, $def = "", $v_as_k = false, $other = "")
{
    if (is_array($def)) {
        $def_arr = $def;
    } else {
        $def_arr = array($def);
    }
    $i = 1;
    foreach ($arr as $k => $v) {
        if ($v_as_k) {
            $k = $v;
        }

        $checked = (in_array($k, $def_arr)) ? "checked" : "";
        $main .= "<span style='white-space:nowrap;'><input type='checkbox' name='{$name}[]' value='$k' id='{$name}_{$i}' $checked $other>
        <label for='{$name}_{$i}'>$v</label></span> ";
        $i++;
    }
    return $main;
}

//把陣列轉為單選項
function arr2radio($name, $arr, $def = "", $v_as_k = false, $other = "")
{
    $i = 1;
    foreach ($arr as $k => $v) {
        if ($v_as_k) {
            $k = $v;
        }

        $checked = ($def == $k) ? "checked" : "";
        $main .= "<span style='white-space:nowrap;'><input type='radio' name='{$name}' value='$k' id='{$name}_{$i}' $checked $other>
      <label for='{$name}_{$i}'>$v</label></span> ";
        $i++;
    }
    return $main;
}

//立即寄出
function send_now($email = "", $title = "", $content = "", $address = "", $name = "")
{
    global $xoopsConfig, $xoopsDB, $xoopsModuleConfig;

    $xoopsMailer                           = &getMailer();
    $xoopsMailer->multimailer->ContentType = "text/html";
    $xoopsMailer->addHeaders("MIME-Version: 1.0");
    if (!empty($address)) {
        $xoopsMailer->AddReplyTo($address, $name);
    }
    $msg = ($xoopsMailer->sendMail($email, $title, $content, $headers)) ? true : false;
    return $msg;
}

//製作logo圖
function mklogoPic($WebID = "")
{
    $Class    = getWebInfo($WebID);
    $WebName  = $Class['WebName'];
    $WebTitle = $Class['WebTitle'];

    if (function_exists('mb_strwidth')) {
        $n = mb_strwidth($WebName) / 2;
    } else {
        $n = strlen($WebName) / 3;
    }
    //$width=50*$n+35;
    $size = round(350 / $n, 0);
    if ($size > 70) {
        $size  = 70;
        $x     = $size + 10;
        $size2 = 20;
    } else {
        $x     = round(350 / $n, 0) + 10;
        $size2 = 17;
    }
    $y = $size + 55;

    header('Content-type: image/png');
    $im = @imagecreatetruecolor(520, 140)
    or die(_MD_TCW_MKPIC_ERROR);
    imagesavealpha($im, true);

    $white = imagecolorallocate($im, 255, 255, 255);

    //$trans_colour = imagecolorallocatealpha($im, 157,211,223, 127);
    $trans_colour = imagecolorallocatealpha($im, 255, 255, 255, 127);
    imagefill($im, 0, 0, $trans_colour);

    $text_color  = imagecolorallocate($im, 0, 0, 0);
    $text_color2 = imagecolorallocatealpha($im, 255, 255, 255, 50);

    $gd = gd_info();
    if ($gd['JIS-mapped Japanese Font Support']) {
        $WebTitle = iconv("UTF-8", "shift_jis", $WebTitle);
        $WebName  = iconv("UTF-8", "shift_jis", $WebName);
    }

    imagettftext($im, $size, 0, 0, $x, $text_color, XOOPS_ROOT_PATH . "/modules/tad_web/class/font.ttf", $WebName);
    imagettftextoutline(
        $im, // image location ( you should use a variable )
        $size, // font size
        0, // angle in °
        0, // x
        $x, // y
        $text_color,
        $white,
        XOOPS_ROOT_PATH . "/modules/tad_web/class/font.ttf",
        $WebName, // pattern
        2// outline width
    );

    imagettftext($im, $size2, 0, 0, $y, $text_color, XOOPS_ROOT_PATH . "/modules/tad_web/class/font.ttf", $WebTitle);
    imagettftextoutline(
        $im, // image location ( you should use a variable )
        $size2, // font size
        0, // angle in °
        0, // x
        $y, // y
        $text_color,
        $white,
        XOOPS_ROOT_PATH . "/modules/tad_web/class/font.ttf",
        $WebTitle, // pattern
        1// outline width
    );

    mk_dir(XOOPS_ROOT_PATH . "/uploads/tad_web/{$WebID}");
    mk_dir(XOOPS_ROOT_PATH . "/uploads/tad_web/{$WebID}/auto_logo");

    imagepng($im, XOOPS_ROOT_PATH . "/uploads/tad_web/{$WebID}/auto_logo/auto_logo.png");
    imagedestroy($im);
}

function imagettftextoutline(&$im, $size, $angle, $x, $y, &$col, &$outlinecol, $fontfile, $text, $width)
{
    // For every X pixel to the left and the right
    for ($xc = $x - abs($width); $xc <= $x + abs($width); $xc++) {
        // For every Y pixel to the top and the bottom
        for ($yc = $y - abs($width); $yc <= $y + abs($width); $yc++) {
            // Draw the text in the outline color
            $text1 = imagettftext($im, $size, $angle, $xc, $yc, $outlinecol, $fontfile, $text);
        }
    }
    // Draw the main text
    $text2 = imagettftext($im, $size, $angle, $x, $y, $col, $fontfile, $text);
}

//取得圖片選項
function import_img($path = '', $col_name = "logo", $col_sn = '', $desc = "", $safe_name = false)
{
    global $xoopsDB;
    if (strpos($path, "http") !== false) {
        $path = str_replace(XOOPS_URL, XOOPS_ROOT_PATH, $path);
    }
    if (empty($path)) {
        return;
    }

    if (!is_dir($path) and !is_file($path)) {
        return;
    }

    $db_files = array();

    $sql = "select files_sn,file_name,original_filename from " . $xoopsDB->prefix("tad_web_files_center") . " where col_name='{$col_name}' and col_sn='{$col_sn}'";

    $result          = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error() . "<br>" . $sql);
    $db_files_amount = 0;
    while (list($files_sn, $file_name, $original_filename) = $xoopsDB->fetchRow($result)) {
        $db_files[$files_sn] = $original_filename;
        $db_files_amount++;
    }
    if (!empty($db_files_amount)) {
        return;
    }

    if (is_dir($path)) {
        if ($dh = opendir($path)) {
            while (($file = readdir($dh)) !== false) {
                if ($file == "." or $file == ".." or $file == "Thumbs.db") {
                    continue;
                }

                $type = filetype($path . "/" . $file);

                if ($type != "dir") {
                    if (!in_array($file, $db_files)) {
                        import_file($path . "/" . $file, $col_name, $col_sn, null, null, $desc, $safe_name);
                    }
                }
            }
            closedir($dh);
        }
    } elseif (is_file($path)) {
        import_file($path, $col_name, $col_sn, null, null, $desc, $safe_name);
    }
}

//匯入圖檔
function import_file($file_name = '', $col_name = "", $col_sn = "", $main_width = "", $thumb_width = "90", $desc = "", $safe_name = false)
{
    global $xoopsDB, $xoopsUser, $xoopsModule, $xoopsConfig;

    if ($col_name == "bg") {
        $TadUpFilesBg = TadUpFilesBg();
        if (is_object($TadUpFilesBg)) {
            $TadUpFilesBg->set_col($col_name, $col_sn);
            $TadUpFilesBg->import_one_file($file_name, null, $main_width, $thumb_width, null, $desc, $safe_name);
        } else {
            die('Need TadUpFilesBg Object!');
        }
    } elseif ($col_name == "logo") {
        $TadUpFilesLogo = TadUpFilesLogo();
        if (is_object($TadUpFilesLogo)) {
            $TadUpFilesLogo->set_col($col_name, $col_sn);
            $TadUpFilesLogo->import_one_file($file_name, null, $main_width, $thumb_width, null, $desc, $safe_name);
        } else {
            die('Need TadUpFilesLogo Object!');
        }
    } elseif ($col_name == "head") {
        $TadUpFilesHead = TadUpFilesHead();
        if (is_object($TadUpFilesHead)) {
            $TadUpFilesHead->set_col($col_name, $col_sn);
            $TadUpFilesHead->import_one_file($file_name, null, $main_width, $thumb_width, null, $desc, $safe_name);
        } else {
            die('Need TadUpFilesHead Object!');
        }
    }
}

function TadUpFilesBg()
{
    global $WebID;
    include_once XOOPS_ROOT_PATH . "/modules/tadtools/TadUpFiles.php";
    $TadUpFilesBg = new TadUpFiles("tad_web", "/{$WebID}/bg", null, "", "/thumbs");
    $TadUpFilesBg->set_thumb("100px", "60px", "#000", "center center", "no-repeat", "contain");
    return $TadUpFilesBg;
}

function TadUpFilesLogo()
{
    global $WebID;
    include_once XOOPS_ROOT_PATH . "/modules/tadtools/TadUpFiles.php";
    $TadUpFilesLogo = new TadUpFiles("tad_web", "/{$WebID}/logo", null, "", "/thumbs");
    $TadUpFilesLogo->set_thumb("100px", "60px", "#000", "center center", "no-repeat", "contain");
    return $TadUpFilesLogo;
}

function TadUpFilesHead()
{
    global $WebID;
    include_once XOOPS_ROOT_PATH . "/modules/tadtools/TadUpFiles.php";
    $TadUpFilesHead = new TadUpFiles("tad_web", "/{$WebID}/head", null, "", "/thumbs");
    $TadUpFilesHead->set_thumb("100px", "60px", "#000", "center center", "no-repeat", "contain");
    return $TadUpFilesHead;
}

//取得tad_web_cate分類選單的選項（單層選單）
function get_tad_web_cate_menu_options($default_CateID = "0")
{
    global $xoopsDB, $xoopsModule;
    $sql = "select `CateID`, `CateName`
    from `" . $xoopsDB->prefix("tad_web_cate") . "` order by `CateSort`";
    $result = $xoopsDB->query($sql)
    or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());

    $option = '';
    while (list($CateID, $CateName) = $xoopsDB->fetchRow($result)) {
        $selected = ($CateID == $default_CateID) ? 'selected = "selected"' : '';
        $option .= "<option value='{$CateID}' $selected>{$CateName}</option>";
    }
    return $option;
}

//取得所有分類下的網站
function get_web_cate_arr()
{
    global $xoopsDB, $isAdmin, $MyWebs;
    $other_web_url_arr = get_web_config('other_web_url');

    $sql    = "select * from `" . $xoopsDB->prefix("tad_web") . "` where WebEnable='1' and WebID > 0 order by WebTitle";
    $result = $xoopsDB->query($sql)
    or redirect_header($_SERVER['PHP_SELF'], 3, mysql_error());
    $data_arr = '';
    while ($all = $xoopsDB->fetchArray($result)) {
        foreach ($all as $k => $v) {
            $$k = $v;
        }
        if (empty($WebID)) {
            continue;
        }
        $all['other_web_url']               = $other_web_url_arr[$WebID];
        $all['isMyWeb']                     = ($isAdmin) ? true : in_array($WebID, $MyWebs);
        $data_arr[$CateID][$WebID]          = $all;
        $data_arr[$CateID]['WebID'][$WebID] = $WebID;
    }
    //die(var_export($data_arr));
    return $data_arr;
}

//製作文字圖片
function output_head_file($WebID)
{
    global $xoopsUser;
    if (empty($WebID)) {
        return;
    }
    //先刪掉舊檔
    $filename = XOOPS_ROOT_PATH . "/uploads/tad_web/{$WebID}/header.png";
    if (file_exists($filename)) {
        unlink($filename);
    }

    $width  = 1140;
    $height = 200;

    $all_config = get_web_all_config($WebID);

    foreach ($all_config as $k => $v) {
        $$k = $v;
    }

    $im = @imagecreatetruecolor($width, $height);
    imagecolortransparent($im, imagecolorallocatealpha($im, 0, 0, 0, 127));
    imagealphablending($im, true);
    imagesavealpha($im, true);

    $bg_filename = XOOPS_ROOT_PATH . "/uploads/tad_web/{$WebID}/head/{$web_head}";
    if (file_exists($bg_filename)) {
        list($bg_width, $bg_height) = getimagesize($bg_filename);

        //縮放比例
        $rate = round($bg_width / $width, 2);

        $type = strtolower(substr(strrchr($bg_filename, "."), 1));
        if ($type == 'jpeg') {
            $type = 'jpg';
        }

        switch ($type) {
            case 'bmp':$bg_im = imagecreatefromwbmp($bg_filename);
                break;
            case 'gif':$bg_im = imagecreatefromgif($bg_filename);
                break;
            case 'jpg':$bg_im = imagecreatefromjpeg($bg_filename);
                break;
            case 'png':$bg_im = imagecreatefrompng($bg_filename);
                break;
            default:return "Unsupported picture type!";
        }

        $head_top = abs($head_top);
        $bg_top   = round($head_top * $rate, 0);

        //背景圖
        imagecopyresampled($im, $bg_im, 0, 0, 0, $bg_top, $width, $bg_height, $bg_width, $bg_height);
    }

    $logo_filename = XOOPS_ROOT_PATH . "/uploads/tad_web/{$WebID}/logo/{$web_logo}";
    if (file_exists($logo_filename)) {
        list($logo_width, $logo_height) = getimagesize($logo_filename);

        $type = strtolower(substr(strrchr($logo_filename, "."), 1));
        if ($type == 'jpeg') {
            $type = 'jpg';
        }

        switch ($type) {
            case 'bmp':$logo_im = imagecreatefromwbmp($logo_filename);
                break;
            case 'gif':$logo_im = imagecreatefromgif($logo_filename);
                break;
            case 'jpg':$logo_im = imagecreatefromjpeg($logo_filename);
                break;
            case 'png':$logo_im = imagecreatefrompng($logo_filename);
                imagecolortransparent($logo_im, imagecolorallocatealpha($logo_im, 0, 0, 0, 127));
                imagealphablending($logo_im, true);
                imagesavealpha($logo_im, true);
                break;
            default:return "Unsupported picture type!";
        }

        //logo圖
        imagecopyresampled($im, $logo_im, $logo_left, $logo_top, 0, 0, $logo_width, $logo_height, $logo_width, $logo_height);
    }

    header('Content-type: image/png');
    imagepng($im, XOOPS_ROOT_PATH . "/uploads/tad_web/{$WebID}/header.png");
    imagedestroy($im);
}
