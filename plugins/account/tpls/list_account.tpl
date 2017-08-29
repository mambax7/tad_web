<{assign var="bc" value=$block.BlockContent}>
<{if $bc.main_data}>
  <{includeq file="$xoops_rootpath/modules/tad_web/templates/tad_web_block_title.tpl"}>
  <ul class="list-group">
  <{foreach from=$bc.cate_menu item=account}>
    <li  class="list-group-item">
      <span class="badge"><{$account.PageCount}></span>
      <{if $isMyWeb or $account.isAssistant}>
      <a href='account.php?WebID=<{$account.WebID}>&CateID=<{$account.CateID}>'><{$account.CateName}></a>
      <{else}>
      <{$account.CateName}>
      <{/if}>
    </li>
  <{/foreach}>
  </ul>
<{/if}>