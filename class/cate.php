<?php
/*

$web_cate = new web_cate($WebID, "news","tad_web_news");

//設定「CateID」欄位預設值
$CateID    = (!isset($DBV['CateID'])) ? "" : $DBV['CateID'];
//表單
$cate_menu = $web_cate->cate_menu($CateID);
$xoopsTpl->assign('cate_menu', $cate_menu);

//儲存
$CateID = $web_cate->save_tad_web_cate($CateID, $newCateName);

//取得單一分類資料
$cate = $web_cate->get_tad_web_cate($CateID);
$xoopsTpl->assign('cate', $cate);

<ol class="breadcrumb">
<li><a href="news.php?WebID=<{$WebID}>"><{$smarty.const._MD_TCW_NEWS}></a> <span class="divider">/</span></li>
<{if isset($cate.CateID)}><li><a href="news.php?WebID=<{$WebID}>&CateID=<{$cate.CateID}>"><{$cate.CateName}></a> <span class="divider">/</span></li><{/if}>
<li><{$NewsInfo}></li>
</ol>

//取得tad_web_cate所有資料陣列
$web_cate = new web_cate($WebID, "news","tad_web_news");
$web_cate->set_WebID($WebID);
$cate = $web_cate->get_tad_web_cate_arr();

<{if isset($news.cate.CateID)}>
<span class="label label-info"><a href="news.php?WebID=<{$news.WebID}>&CateID=<{$news.cate.CateID}>" style="color: #FFFFFF;"><{$news.cate.CateName}></a></span>
<{/if}>
 */

class web_cate
{
    public $WebID = 0;
    public $ColName;
    public $ColSN               = 0;
    public $table               = 0;
    public $demo_txt            = '';
    public $default_value       = '';
    public $default_option_text = '';
    public $button_value        = '';
    public $label               = '';
    public $label_col_md        = '2';
    public $menu_col_md         = '3';
    public $custom_change_js;
    public $menu_name = 'CateID';
    public $menu_id   = 'CateID';
    public $power;

    public function __construct($WebID = "0", $ColName = "", $table = "")
    {
        include_once "power.php";
        $this->power = new power($WebID);

        if (!empty($WebID)) {
            $this->set_WebID($WebID);
        }

        if (!empty($ColName)) {
            $this->set_ColName($ColName);
        }

        if (!empty($table)) {
            $this->set_table($table);
        }

    }

    public function set_WebID($WebID = "")
    {
        $WebID = intval($WebID);

        $this->WebID = $WebID;
    }

    public function set_ColName($ColName = "")
    {
        $this->ColName = $ColName;
    }

    public function set_table($table = "")
    {
        $this->table = $table;
    }

    public function set_demo_txt($demo_txt = "")
    {
        $this->demo_txt = $demo_txt;
    }

    public function set_default_value($default_value = "")
    {
        $this->default_value = $default_value;
    }

    public function set_default_option_text($default_option_text = "")
    {
        $this->default_option_text = $default_option_text;
    }

    public function set_label($label = "")
    {
        $this->label = $label;
    }

    public function set_button_value($button_value = "")
    {
        $this->button_value = $button_value;
    }
    public function set_col_md($label_md, $menu_md)
    {
        $this->label_col_md = $label_md;
        $this->menu_col_md  = $menu_md;
    }

    public function set_custom_change_js($change_js)
    {
        $this->custom_change_js = $change_js;
    }

    public function set_var($var = "", $val = "")
    {
        $this->$var = $val;
    }

    //隱藏表單
    public function hidden_cate_menu($CateID = "", $show_label = true)
    {
        $cate = $this->get_tad_web_cate($CateID);

        $default_option_text = empty($this->default_option_text) ? _MD_TCW_SELECT_CATE : $this->default_option_text;
        $show_label_txt      = empty($this->label) ? $default_option_text : $this->label;
        $label               = $show_label ? "<label class=\"col-sm-{$this->label_col_md} control-label\">
          {$show_label_txt}
          </label>" : "";
        $menu_col_md = 12 - $this->label_col_md;
        $menu        = "
        <div class=\"row\" style=\"margin-bottom: 10px;\">
            $label
            <div id='cate_menu' class=\"col-sm-{$menu_col_md}\">
              <p class='form-control-static text-info'>{$cate['CateName']}</p>
              <input type='hidden' name='CateID' value='{$CateID}'>
            </div>
        </div>
        ";
        return $menu;
    }

    //分類選單 $mode = "form" ,"menu","page"
    public function cate_menu($defCateID = "", $mode = "form", $newCate = true, $change_page = false, $show_label = true, $show_tools = false, $show_select = true, $required = false, $default_opt = true)
    {
        global $xoopsDB;

        // if (empty($this->WebID)) {
        //     return;
        // }

        $option = "";
        $sql    = "select * from `" . $xoopsDB->prefix("tad_web_cate") . "` where `WebID` = '{$this->WebID}' and `ColName`='{$this->ColName}' and `CateEnable`='1' order by CateSort";
        // die($sql);
        $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
        while ($data = $xoopsDB->fetchArray($result)) {
            foreach ($data as $k => $v) {
                $$k = $v;
            }
            $selected = ($defCateID == $CateID) ? "selected" : "";
            $option .= "<option value='{$CateID}' $selected>{$CateName}</option>";
        }

        $button_value = empty($this->button_value) ? _MD_TCW_CATE_TOOLS : $this->button_value;
        $tools        = $show_tools ? "<div class=\"col-sm-2\"><a href='cate.php?WebID={$this->WebID}&ColName={$this->ColName}&table={$this->table}' class='btn btn-warning' >$button_value</a></div>" : "";

        $default_option_text = empty($this->default_option_text) ? _MD_TCW_SELECT_CATE : $this->default_option_text;

        $validate = $required ? 'validate[required]' : '';
        $def_opt  = $default_opt ? "<option value=''>$default_option_text</option>" : '';
        $menu     = "<select name='{$this->menu_name}' id='{$this->menu_id}' class='{$validate} form-control' >
                    {$def_opt}
                    {$option}
                  </select>";

        if ($mode == "menu") {
            return $menu;
        }

        if ($option and $show_select) {
            $cate_menu = "
            <div id='cate_menu' class=\"col-sm-{$this->menu_col_md}\">
              $menu
            </div>
            ";
        } elseif ($show_select) {
            $cate_menu = "";
        } else {
            return;
        }

        $demo_txt = "";
        if (!empty($this->demo_txt)) {
            $demo_txt = ", {$this->demo_txt}";
        }

        $new_cate = empty($this->label) ? _MD_TCW_NEW_CATE : sprintf(_MD_TCW_NEW_SOMETHING, $this->label);

        if ($newCate) {
            if (empty($option)) {
                $hide   = "";
                $botton = "";
            } else {
                $hide   = "style='display:none;'";
                $botton = "
                <div class=\"col-sm-2\" id=\"newCate\">
                  <button type='button' class='btn btn-info' id=\"add_cate\">{$new_cate}</button>
                </div>
                <div class=\"col-sm-2\" id=\"showMenu\" style='display:none;'>
                  <button type='button' class='btn btn-success' id=\"show_menu\">" . _MD_TCW_TO_MENU . "</button>
                </div>";
            }
            $new_input = "
            <div class=\"col-sm-5\" id=\"newCateName\" $hide>
              <input type='text' name='newCateName' placeholder='{$new_cate} {$demo_txt}' class='validate[required] form-control' value='{$this->default_value}'>
            </div>
            $botton";
        } else {
            $new_input = "";
        }

        $label_title    = ($show_select) ? $default_option_text : _MD_TCW_NEW_CATE;
        $show_label_txt = empty($this->label) ? $label_title : $this->label;

        $label = $show_label ? "<label class=\"col-sm-{$this->label_col_md} control-label\">
          {$show_label_txt}
          </label>" : "";

        $row = ($mode == "form") ? "form-group" : "row";

        $op             = isset($_REQUEST['op']) ? $_REQUEST['op'] : '';
        $change_page_js = $change_page ? "location.href='{$_SERVER['PHP_SELF']}?WebID={$this->WebID}&op={$op}&CateID=' + $('#CateID').val();" : "";

        $newCate_js = ($mode == "form") ? "if(\$('#CateID').val()==''){\$('#newCate').show(); }else{ \$('#newCate').hide();}" : "";

        $hide_newCate_js = empty($defCateID) ? '' : "\$('#newCate').hide();";

        $menu = "
        <script>
        $(function() {
            {$hide_newCate_js}
            $('#{$this->menu_id}').change(function(){
                {$change_page_js}
                {$newCate_js}
                {$this->custom_change_js}
            });

            $('#add_cate').click(function(){
                $('#cate_menu').hide();
                $('#newCate').hide();
                $('#newCateName').show();
                $('#showMenu').show();
            });
            $('#show_menu').click(function(){
                $('#cate_menu').show();
                $('#newCate').show();
                $('#newCateName').hide();
                $('#showMenu').hide();
            });


        });
        </script>
        <div class=\"{$row}\" style=\"margin-bottom: 10px;\">
          $label
          $cate_menu
          $new_input
          $tools
        </div>
        ";
        return $menu;
    }

    //新增資料到tad_web_cate中
    public function save_tad_web_cate($CateID = "", $newCateName = "")
    {
        global $xoopsDB, $xoopsUser;
        if (!empty($newCateName) and empty($CateID)) {

            $myts     = MyTextSanitizer::getInstance();
            $CateName = $myts->addSlashes($newCateName);
            $CateSort = $this->tad_web_cate_max_sort();

            $sql = "insert into `" . $xoopsDB->prefix("tad_web_cate") . "` (
              `WebID`,
              `CateName`,
              `ColName`,
              `ColSN`,
              `CateSort`,
              `CateEnable`,
              `CateCounter`
            ) values(
              '{$this->WebID}',
              '{$CateName}',
              '{$this->ColName}',
              '{$this->ColSN}',
              '{$CateSort}',
              '1',
              0
            )";
            $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);

            //取得最後新增資料的流水編號
            $CateID = $xoopsDB->getInsertId();
        }

        return $CateID;
    }

    //自動取得tad_web_cate的最新排序
    public function tad_web_cate_max_sort()
    {
        global $xoopsDB;
        $sql        = "select max(`CateSort`) from `" . $xoopsDB->prefix("tad_web_cate") . "` where WebID='{$this->WebID}' and  ColName='{$this->ColName}' and ColSN='{$this->ColSN}'";
        $result     = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
        list($sort) = $xoopsDB->fetchRow($result);
        return ++$sort;
    }

    //自動取得 tad_web_cate 的最新排序編號
    public function tad_web_cate_max_id()
    {
        global $xoopsDB;

        $sql        = "select max(`CateSort`) from `" . $xoopsDB->prefix("tad_web_cate") . "` where WebID='{$this->WebID}' and  ColName='{$this->ColName}' and ColSN='{$this->ColSN}'";
        $result     = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
        list($sort) = $xoopsDB->fetchRow($result);

        $sql          = "select `CateID` from `" . $xoopsDB->prefix("tad_web_cate") . "` where WebID='{$this->WebID}' and  ColName='{$this->ColName}' and ColSN='{$this->ColSN}' and CateSort='{$sort}'";
        $result       = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
        list($CateID) = $xoopsDB->fetchRow($result);

        return $CateID;
    }

    //更新tad_web_cate某一筆資料
    public function update_tad_web_cate($CateID = '', $newCateName = '')
    {
        global $xoopsDB, $isAdmin, $xoopsUser;

        $myts     = MyTextSanitizer::getInstance();
        $CateName = $myts->addSlashes($newCateName);

        $sql = "update `" . $xoopsDB->prefix("tad_web_cate") . "` set
       `CateName` = '{$CateName}' where `CateID`='{$CateID}'";
        $xoopsDB->queryF($sql) or web_error($sql, __FILE__, __LINE__);

        return $CateID;
    }

    //取得tad_web_cate資料陣列
    public function get_tad_web_cate($CateID = "")
    {
        global $xoopsDB;
        if (empty($CateID)) {
            return;
        }
        $sql    = "select * from `" . $xoopsDB->prefix("tad_web_cate") . "` where `CateID` = '{$CateID}'";
        $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
        $data   = $xoopsDB->fetchArray($result);
        return $data;
    }

    //取得tad_web_cate所有資料陣列
    public function get_tad_web_cate_arr($counter = true)
    {
        global $xoopsDB;

        include_once XOOPS_ROOT_PATH . "/modules/tad_web/function.php";

        $counter    = $counter ? $this->tad_web_cate_data_counter() : '';
        $arr        = array();
        $andWebID   = empty($this->WebID) ? '' : "and `WebID` = '{$this->WebID}'";
        $andColName = empty($this->ColName) ? '' : "and `ColName`='{$this->ColName}'";
        $sql        = "select * from `" . $xoopsDB->prefix("tad_web_cate") . "` where 1 $andWebID $andColName order by CateSort";
        // echo $sql . '<br>';
        $result = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
        while ($data = $xoopsDB->fetchArray($result)) {
            $CateID                    = $data['CateID'];
            $data['counter']           = isset($counter[$CateID]) ? $counter[$CateID] : 0;
            $arr[$CateID]              = $data;
            $arr[$CateID]['assistant'] = get_assistant($CateID);
            $arr[$CateID]['power']     = $this->power->get_power("read", "CateID", $CateID);
        }
        return $arr;
    }

    //搬移tad_web_cate某筆資料資料
    public function move_tad_web_cate($CateID = '', $move2CateID = 0)
    {
        global $xoopsDB;

        if (empty($CateID)) {
            return;
        }

        if (empty($this->table)) {
            $table = "tad_web_{$this->ColName}";
        } else {
            $table = $this->table;
        }

        $sql = "update `" . $xoopsDB->prefix($table) . "` set
       `CateID` = '{$move2CateID}' where `CateID`='{$CateID}'";
        $xoopsDB->queryF($sql) or web_error($sql, __FILE__, __LINE__);

    }

    //刪除tad_web_cate某筆資料資料
    public function delete_tad_web_cate($CateID = '', $move2CateID = 0)
    {
        global $xoopsDB;

        if (empty($CateID)) {
            return;
        }

        if (!empty($move2CateID)) {
            $this->move_tad_web_cate($CateID, $move2CateID);
        } else {
            $this->delete_tad_web_cate_data($CateID);
        }

        $sql = "delete from `" . $xoopsDB->prefix("tad_web_assistant_post") . "`
        where `CateID` = '{$CateID}'";
        $xoopsDB->queryF($sql) or web_error($sql, __FILE__, __LINE__);

        $sql = "delete from `" . $xoopsDB->prefix("tad_web_cate_assistant") . "`
        where `CateID` = '{$CateID}'";
        $xoopsDB->queryF($sql) or web_error($sql, __FILE__, __LINE__);

        $sql = "delete from `" . $xoopsDB->prefix("tad_web_cate") . "`
        where `CateID` = '{$CateID}'";
        $xoopsDB->queryF($sql) or web_error($sql, __FILE__, __LINE__);

    }

    //刪除tad_web_cate某筆資料資料
    public function delete_tad_web_cate_data($CateID = '')
    {
        global $xoopsDB;

        if (empty($CateID)) {
            return;
        }

        if (empty($this->table)) {
            $table = "tad_web_{$this->ColName}";
        } else {
            $table = $this->table;
        }

        $sql = "delete from `" . $xoopsDB->prefix($table) . "`
        where `CateID` = '{$CateID}'";
        $xoopsDB->queryF($sql) or web_error($sql, __FILE__, __LINE__);

    }

    //取得各分類下的檔案數
    public function tad_web_cate_data_counter()
    {
        global $xoopsDB;

        if (empty($this->table)) {
            $table = "tad_web_{$this->ColName}";
        } else {
            $table = $this->table;
        }
        $counter = array();
        $sql     = "select count(*),CateID from `" . $xoopsDB->prefix($table) . "` where `WebID` = '{$this->WebID}' group by CateID";
        $result  = $xoopsDB->query($sql) or web_error($sql, __FILE__, __LINE__);
        while (list($count, $CateID) = $xoopsDB->fetchRow($result)) {
            $counter[$CateID] = $count;
        }
        return $counter;
    }

    //改變分類啟用狀態
    public function enable_tad_web_cate($CateID = '', $enable = '1')
    {
        global $xoopsDB;

        if (empty($CateID)) {
            return;
        }

        $sql = "update `" . $xoopsDB->prefix('tad_web_cate') . "` set `CateEnable`='{$enable}' where `CateID` = '{$CateID}'";
        $xoopsDB->queryF($sql) or web_error($sql, __FILE__, __LINE__);

    }

}
