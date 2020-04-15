<script type="text/javascript" src="<{$xoops_url}>/modules/tadtools/My97DatePicker/WdatePicker.js"></script>

<h2><{$smarty.const._MD_TCW_ACTION_ADD}></h2>
<div class="my-border">
    <form action="action.php" method="post" id="myForm" enctype="multipart/form-data" role="form" class="form-horizontal">

        <!--分類-->
        <{$cate_menu_form}>

        <!--活動名稱-->
        <input type="text" name="ActionName" value="<{$ActionName}>" id="ActionName" class="validate[required] form-control" placeholder="<{$smarty.const._MD_TCW_ACTIONNAME}>">


        <!--活動說明-->
        <textarea name="ActionDesc"  rows=4 id="ActionDesc"  class="form-control" placeholder="<{$smarty.const._MD_TCW_ACTIONDESC}>"><{$ActionDesc}></textarea>

        <!--活動日期-->
        <div class="form-group row">
            <label class="col-md-2 col-form-label text-sm-right control-label">
                <{$smarty.const._MD_TCW_ACTIONDATE}>
            </label>
            <div class="col-md-4">
                <input type="text" name="ActionDate" class="form-control" value="<{$ActionDate}>" id="ActionDate" onClick="WdatePicker({dateFmt:'yyyy-MM-dd' , startDate:'%y-%M-%d'})">
            </div>
            <!--活動地點-->
            <label class="col-md-2 col-form-label text-sm-right control-label">
                <{$smarty.const._MD_TCW_ACTIONPLACE}>
            </label>
            <div class="col-md-4">
                <input type="text" name="ActionPlace" class="form-control" value="<{$ActionPlace}>" id="ActionPlace" >
            </div>
        </div>

        <{$power_form}>

        <{$tags_form}>

        <!--上傳圖檔-->
        <div class="form-group row">
            <label class="col-md-2 col-form-label text-sm-right control-label">
                <{$smarty.const._MD_TCW_ACTION_UPLOAD}>
            </label>
            <div class="col-md-10">
                <{$upform}>
            </div>
        </div>

        <div class="text-center">
            <!--活動編號-->
            <input type="hidden" name="ActionID" value="<{$ActionID}>">
            <!--所屬團隊-->
            <input type="hidden" name="WebID" value="<{$WebID}>">
            <input type="hidden" name="op" value="<{$next_op}>">
            <button type="submit" class="btn btn-primary"><{$smarty.const._TAD_SAVE}></button>
        </div>
    </form>
</div>