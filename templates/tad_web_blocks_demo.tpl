<div class="tad_web_block">
    <!-- <{$block.BlockTitle}> -->
    <{if $block.plugin=="xoops"}>
        <{includeq file="$xoops_rootpath/modules/tad_web/templates/tad_web_block_title.tpl"}>
        <{block id=$block.BlockName}>
    <{elseif $block.plugin=="custom"}>
        <{includeq file="$xoops_rootpath/modules/tad_web/templates/tad_web_block_custom.tpl"}>
    <{elseif $block.plugin=="share"}>
        <{includeq file="$xoops_rootpath/modules/tad_web/templates/tad_web_block_custom.tpl"}>
        <div class="alert alert-info">from: <a href="<{$xoops_url}>/modules/tad_web/index.php?WebID=<{$share_info.WebID}>" target="_blank"><{$share_info.WebName}>(<{$share_info.WebTitle}>)</a></div>
    <{else}>
        <{if $block.BlockContent.main_data}>
            <{if "$xoops_rootpath/modules/tad_web/plugins/`$block.plugin.dirname`/tpls/`$block.tpl`"|file_exists}>
                <{includeq file="$xoops_rootpath/modules/tad_web/plugins/`$block.plugin.dirname`/tpls/`$block.tpl`"}>
            <{elseif "$xoops_rootpath/modules/tad_web/plugins/system/tpls/`$block.tpl`"|file_exists}>
                <{includeq file="$xoops_rootpath/modules/tad_web/plugins/system/tpls/`$block.tpl`"}>
            <{else}>
                no template<br>
                <{"$xoops_rootpath/modules/tad_web/plugins/`$block.plugin.dirname`/tpls/`$block.tpl`"}>
            <{/if}>
        <{else}>
            <{$smarty.const._MD_TCW_BLOCK_EMPTY}>
        <{/if}>
    <{/if}>
</div>