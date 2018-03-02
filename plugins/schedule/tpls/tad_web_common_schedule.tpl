<{if $web_display_mode=='index' and $schedule_data}>
  <{if "$xoops_rootpath/uploads/tad_web/0/image/`$dirname`.png"|file_exists}>
    <a href="<{$xoops_url}>/modules/tad_web/<{$dirname}>.php"><img src="<{$xoops_url}>/uploads/tad_web/0/image/<{$dirname}>.png" alt="<{$schedule.PluginTitle}>"></a>
  <{else}>
    <h3><a href="<{$xoops_url}>/modules/tad_web/schedule.php"><{$schedule.PluginTitle}></a></h3>
  <{/if}>
<{elseif $web_display_mode=='index_plugin'}>
  <h1><a href="<{$xoops_url}>/modules/tad_web/"><i class="fa fa-home"></i></a> <{$schedule.PluginTitle}></h1>
<{/if}>

<{if $isMyWeb}>
  <{$sweet_delete_schedule_func_code}>
<{/if}>

<{if $schedule_data}>
  <link href="<{$xoops_url}>/modules/tad_web/plugins/schedule/schedule.css" rel="stylesheet">
  <{if $WebID==""}>
    <{assign var="i" value=0}>
    <{assign var="total" value=1}>

    <{foreach item=act from=$schedule_data}>
      <{if $act.ScheduleDisplay=='1'}>
        <{if $i==0}>
          <div class="row">
          <{/if}>
            <div class="col-sm-3">
              <a href="schedule.php?WebID=<{$act.WebID}>&ScheduleID=<{$act.ScheduleID}>" class="btn btn-link btn-block"><i class="fa fa-table"></i> <{$act.ScheduleName}>
              </a>
            </div>
        <{assign var="i" value=$i+1}>
        <{if $i == 4 || $total==$schedule_amount}>
          </div>
          <{assign var="i" value=0}>
        <{/if}>
        <{assign var="total" value=$total+1}>
      <{/if}>
    <{/foreach}>
  <{else}>
    <{foreach item=act from=$schedule_data}>

      <{if $act.ScheduleDisplay=='1'}>
        <div style="margin: 8px auto;">
          <h2>
            <a href='schedule.php?WebID=<{$act.WebID}>&CateID=<{$act.CateID}>'><{$act.cate.CateName}>
            </a>
            <a href='schedule.php?WebID=<{$act.WebID}>&ScheduleID=<{$act.ScheduleID}>'><{$act.ScheduleName}>
            </a>
            <a href="<{$xoops_url}>/modules/tad_web/plugins/schedule/pdf.php?WebID=<{$WebID}>&ScheduleID=<{$act.ScheduleID}>"  class="text-success"><i class="fa fa-download "></i></a>
            <small>
              <{if $act.isCanEdit}>
                <a href="javascript:delete_schedule_func(<{$act.ScheduleID}>);" class="text-danger"><i class="fa fa-trash-o"></i></a>
                <a href="schedule.php?WebID=<{$WebID}>&op=edit_form&ScheduleID=<{$act.ScheduleID}>"  class="text-warning"><i class="fa fa-pencil"></i></a>
              <{/if}>
            </small>
          </h2>
          <{$act.schedule}>
        </div>
      <{else}>
        <h2><{$smarty.const._MD_TCW_SCHEDULE_OTHER_LIST}></h2>
        <div>
          <a href='schedule.php?WebID=<{$act.WebID}>&CateID=<{$act.CateID}>'><{$act.cate.CateName}>
          </a>
          <a href='schedule.php?WebID=<{$act.WebID}>&ScheduleID=<{$act.ScheduleID}>'><{$act.ScheduleName}>
          </a>
          <a href="<{$xoops_url}>/modules/tad_web/plugins/schedule/pdf.php?WebID=<{$WebID}>&ScheduleID=<{$act.ScheduleID}>"  class="text-success"><i class="fa fa-download "></i></a>
          <small>
            <{if $act.isCanEdit}>
              <a href="javascript:delete_schedule_func(<{$act.ScheduleID}>);" class="text-danger"><i class="fa fa-trash-o"></i></a>
              <a href="schedule.php?WebID=<{$WebID}>&op=edit_form&ScheduleID=<{$act.ScheduleID}>"  class="text-warning"><i class="fa fa-pencil"></i></a>
            <{/if}>
          </small>
        </div>
      <{/if}>
    <{/foreach}>
  <{/if}>
  <div style="clear: both;"></div>

  <div style="text-align:right; margin: 0px 0px 10px;">
    <{if $isMyWeb and $WebID}>
      <a href="schedule.php?WebID=<{$WebID}>&op=edit_form" class="btn btn-info"><i class="fa fa-plus"></i> <{$smarty.const._MD_TCW_ADD}><{$smarty.const._MD_TCW_SCHEDULE_SHORT}></a>
    <{/if}>
  </div>
<{/if}>

