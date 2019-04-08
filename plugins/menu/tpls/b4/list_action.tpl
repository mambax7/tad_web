<{assign var="bc" value=$block.BlockContent}>

<{if $bc.main_data}>
  <{includeq file="$xoops_rootpath/modules/tad_web/templates/tad_web_block_title.tpl"}>
  <{foreach item=act from=$bc.main_data}>
    <div style="width: 156px; height: 240px; float:left; margin: 5px 2px; overflow: hidden;">
      <a href='menu.php?WebID=<{$act.WebID}>&MenuID=<{$act.MenuID}>'>
        <div style="width: 150px; height: 160px; background-color: #F1F7FF ; border:1px dotted green; margin: 0px auto;">
        <div style="width: 140px; height: 140px; background: #F1F7FF url('<{$act.MenuPic}>') center center no-repeat; border:8px solid #F1F7FF; margin: 0px auto;">
        </div>
        </div>
      </a>
      <div class="text-center" style="margin: 8px auto;">
        <a href='menu.php?WebID=<{$act.WebID}>&MenuID=<{$act.MenuID}>'><{$act.MenuName}></a>
        <{if $act.isMyWeb}>
          <a href="javascript:delete_menu_func(<{$act.MenuID}>);" class="text-danger"><i class="fa fa-trash-o"></i></a>
          <a href="menu.php?WebID=<{$WebID}>&op=edit_form&MenuID=<{$act.MenuID}>"  class="text-warning"><i class="fa fa-pencil"></i></a>
        <{/if}>
      </div>
    </div>
  <{/foreach}>
  <div style="clear: both;"></div>
<{/if}>
