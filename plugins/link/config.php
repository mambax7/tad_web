<?php
global $xoopsConfig;
require_once XOOPS_ROOT_PATH . "/modules/tad_web/plugins/link/langs/{$xoopsConfig['language']}.php";
$pluginConfig['name'] = _MD_TCW_LINK;
$pluginConfig['short'] = _MD_TCW_LINK_SHORT;
$pluginConfig['icon'] = 'fa-globe';
$pluginConfig['limit'] = '5';
$pluginConfig['cate'] = true;
$pluginConfig['cate_table'] = 'tad_web_link';
$pluginConfig['top_table'] = 'tad_web_link';
$pluginConfig['common'] = true;
$pluginConfig['sql'] = ['tad_web_link'];
$pluginConfig['setup'] = false;
$pluginConfig['add'] = true;
$pluginConfig['menu'] = true;
$pluginConfig['export'] = false;
$pluginConfig['tag'] = true;
$pluginConfig['top_score'] = 2;
$pluginConfig['assistant'] = true;
