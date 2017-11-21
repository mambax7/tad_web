<{if $web_display_mode=='index' and $file_data}>
  <{if "$xoops_rootpath/uploads/tad_web/0/image/`$dirname`.png"|file_exists}>
    <a href="<{$xoops_url}>/modules/tad_web/<{$dirname}>.php"><img src="<{$xoops_url}>/uploads/tad_web/0/image/<{$dirname}>.png" alt="<{$files.PluginTitle}>"></a>
  <{else}>
    <h3><a href="<{$xoops_url}>/modules/tad_web/files.php"><{$files.PluginTitle}></a></h3>
  <{/if}>
<{elseif $web_display_mode=='index_plugin'}>
  <h1><a href="<{$xoops_url}>/modules/tad_web/"><i class="fa fa-home"></i></a> <{$files.PluginTitle}></h1>
<{elseif $web_display_mode=='home_plugin'}>
  <h1><a href="index.php?WebID=<{$WebID}>"><i class="fa fa-home"></i></a> <{$files.PluginTitle}></h1>
<{/if}>

<{if $file_data}>
  <{if $isMyWeb}>
    <{$sweet_delete_files_func_code}>
  <{/if}>

  <table class="footable table common_table">
    <thead>
      <tr>
        <th data-class="expand">
          <{$smarty.const._MD_TCW_FILENAME}>
        </th>
        <th data-hide="phone" nowrap style="width:60px;">
          <{$smarty.const._MD_TCW_FILES_UID}>
        </th>
        <{if $web_display_mode=="index" or $web_display_mode=="index_plugin"}>
          <th data-hide="phone"  class="common_team" style="text-align: center;">
            <{$smarty.const._MD_TCW_TEAMID}>
          </th>
        <{/if}>
      </tr>
    </thead>
    <{foreach item=file from=$file_data}>
      <tr>
        <td>
          <div style="word-wrap:break-word;">
          <{if isset($file.cate.CateID)}>
            <span class="label label-info"><a href="files.php?WebID=<{$file.WebID}>&CateID=<{$file.cate.CateID}>" style="color: #FFFFFF;"><{$file.cate.CateName}></a></span>
          <{/if}>
          <{$file.showurl}>
          <{if $file.isMyWeb or $file.isAssistant}>
            <a href="javascript:delete_files_func(<{$file.fsn}>);" class="text-danger"><i class="fa fa-trash-o"></i></a>
            <a href="files.php?WebID=<{$file.WebID}>&op=edit_form&fsn=<{$file.fsn}>" class="text-warning"><i class="fa fa-pencil"></i></a>
          <{/if}>
          </div>
        </td>
        <td style="text-align:center;" nowrap>
          <{$file.uid_name}>
        </td>

        <{if $web_display_mode=="index" or $web_display_mode=="index_plugin"}>
          <td style="text-align:center;" class="common_team_content">
            <{$file.WebTitle}>
          </td>
        <{/if}>
      </tr>
    <{/foreach}>
  </table>

  <{if $file_data}>
    <{if $web_display_mode=='index_plugin' or $web_display_mode=='home_plugin'}>
      <{$bar}>
    <{/if}>
  <{/if}>

  <div style="text-align:right; margin: 0px 0px 10px;">
    <{if $web_display_mode=='index'}>
      <a href="files.php" class="btn btn-primary <{if $web_display_mode=='index'}>btn-xs<{/if}>"><i class="fa fa-info-circle"></i> <{$smarty.const._MD_TCW_MORE}><{$smarty.const._MD_TCW_FILES_SHORT}></a>
    <{elseif $web_display_mode=='home' or $FilesDefCateID}>
      <a href="files.php?WebID=<{$WebID}>" class="btn btn-primary <{if $web_display_mode=='index'}>btn-xs<{/if}>"><i class="fa fa-info-circle"></i> <{$smarty.const._MD_TCW_MORE}><{$smarty.const._MD_TCW_FILES_SHORT}></a>
    <{/if}>


    <{if $isMyWeb and $WebID}>
      <a href="files.php?WebID=<{$WebID}>&op=edit_form" class="btn btn-info <{if $web_display_mode=='index'}>btn-xs<{/if}>"><i class="fa fa-plus"></i> <{$smarty.const._MD_TCW_ADD}><{$smarty.const._MD_TCW_FILES_SHORT}></a>
    <{/if}>
  </div>
<{/if}>