<{if $op=="edit_form"}>
  <script type="text/javascript">
    $(document).ready(function(){
      $('#LinkUrl').change(function() {
        $('#LinkTitle').val($('#LinkUrl').val());
        $.post("link_ajax.php", { url: $('#LinkUrl').val()},
         function(data) {
          var obj = $.parseJSON(data);
           $('#LinkTitle').val(obj.title);
           $('#LinkDesc').val(obj.description);
         });
      });


      $('#LinkGet').click(function() {
        $.post("link_ajax.php", { url: $('#LinkUrl').val()},
         function(data) {
          var obj = $.parseJSON(data);
           $('#LinkTitle').val(obj.title);
           $('#LinkDesc').val(obj.description);
         });
      });
    });

  </script>


  <h2><{$smarty.const._MD_TCW_LINK}></h2>
  <div class="card card-body bg-light m-1">
    <form action="link.php" method="post" id="myForm" enctype="multipart/form-data" role="form">

      <!--分類-->
      <{$cate_menu_form}>

      <!--網站連結-->
      <div class="form-group row">
        <label class="col-md-2 col-form-label text-sm-right">
          <{$smarty.const._MD_TCW_LINKURL}>
        </label>
        <div class="col-md-8">
          <input type="text" name="LinkUrl" value="<{$LinkUrl}>" id="LinkUrl" class="form-control validate[required]" placeholder="<{$smarty.const._MD_TCW_LINKURL}>">
        </div>
        <div class="col-md-2">
          <button type="button" class="btn" id="LinkGet"><{$smarty.const._MD_TCW_LINK_AUTO_GET}></button>
        </div>
      </div>

      <!--網站名稱-->
      <div class="form-group row">
        <label class="col-md-2 col-form-label text-sm-right">
          <{$smarty.const._MD_TCW_LINKTITLE}>
        </label>
        <div class="col-md-10">
          <input type="text" name="LinkTitle" value="<{$LinkTitle}>" id="LinkTitle" class="validate[required] form-control" placeholder="<{$smarty.const._MD_TCW_LINKTITLE}>">
        </div>
      </div>

      <!--說明-->
      <div class="form-group row">
        <label class="col-md-2 col-form-label text-sm-right">
          <{$smarty.const._MD_TCW_LINKDESC}>
        </label>
        <div class="col-md-10">
          <textarea name="LinkDesc" class="form-control" rows=3 id="LinkDesc" placehold="<{$smarty.const._MD_TCW_LINKDESC}>"><{$LinkDesc}></textarea>
        </div>
      </div>

      <{$tags_form}>


      <div class="form-group row">
        <div class="col-md-12 text-center">
          <!--排序-->
          <input type="hidden" name="LinkSort" value="<{$LinkSort}>">
          <!--所屬團隊-->
          <input type="hidden" name="WebID" value="<{$WebID}>">

          <!--編號-->
          <input type="hidden" name="LinkID" value="<{$LinkID}>">

          <input type="hidden" name="op" value="<{$next_op}>">
          <button type="submit" class="btn btn-primary"><{$smarty.const._TAD_SAVE}></button>
        </div>
      </div>
    </form>
  </div>
<{elseif $op=="list_all"}>
  <{if $WebID}>
    <div class="row">
      <div class="col-md-8">
        <{$cate_menu}>
      </div>
      <div class="col-md-4 text-right">
        <{if $isMyWeb and $WebID}>
          <a href="cate.php?WebID=<{$WebID}>&ColName=link&table=tad_web_link" class="btn btn-warning <{if $web_display_mode=='index'}>btn-sm<{/if}>"><i class="fa fa-folder-open"></i> <{$smarty.const._MD_TCW_CATE_TOOLS}></a>
          <a href="link.php?WebID=<{$WebID}>&op=edit_form" class="btn btn-info <{if $web_display_mode=='index'}>btn-sm<{/if}>"><i class="fa fa-plus"></i> <{$smarty.const._MD_TCW_ADD}><{$smarty.const._MD_TCW_LINK_SHORT}></a>
        <{/if}>
      </div>
    </div>
  <{/if}>
  <{if $link_data}>

    <{includeq file="$xoops_rootpath/modules/tad_web/plugins/link/tpls/b4/tad_web_common_link.tpl"}>
  <{else}>
    <h2><a href="index.php?WebID=<{$WebID}>"><i class="fa fa-home"></i></a> <{$link.PluginTitle}></h2>
    <div class="alert alert-info"><{$smarty.const._MD_TCW_EMPTY}></div>
  <{/if}>
<{elseif $op=="setup"}>

  <{includeq file="$xoops_rootpath/modules/tad_web/templates/tad_web_plugin_setup.tpl"}>
<{else}>
    <h2><a href="index.php?WebID=<{$WebID}>"><i class="fa fa-home"></i></a> <{$link.PluginTitle}></h2>
    <{if $isMyWeb or $isAssistant}>
      <a href="link.php?WebID=<{$WebID}>&op=edit_form" class="btn btn-info"><i class="fa fa-plus"></i> <{$smarty.const._MD_TCW_ADD}><{$smarty.const._MD_TCW_LINK_SHORT}></a>
    <{else}>
      <div class="text-center">
        <img src="images/empty.png" alt="<{$smarty.const._MD_TCW_EMPTY}>" title="<{$smarty.const._MD_TCW_EMPTY}>">
      </div>
    <{/if}>
<{/if}>