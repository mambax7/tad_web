<?php
global $xoopsConfig;
require_once XOOPS_ROOT_PATH . "/modules/tad_web/plugins/video/langs/{$xoopsConfig['language']}.php";
$pluginConfig['name'] = _MD_TCW_VIDEO;
$pluginConfig['short'] = _MD_TCW_VIDEO_SHORT;
$pluginConfig['icon'] = 'fa-film';
$pluginConfig['limit'] = '5';
$pluginConfig['cate'] = true;
$pluginConfig['cate_table'] = 'tad_web_video';
$pluginConfig['top_table'] = 'tad_web_video';
$pluginConfig['common'] = true;
$pluginConfig['sql'] = ['tad_web_video'];
$pluginConfig['setup'] = true;
$pluginConfig['add'] = true;
$pluginConfig['menu'] = true;
$pluginConfig['export'] = true;
$pluginConfig['tag'] = true;
$pluginConfig['top_score'] = 2;
$pluginConfig['assistant'] = true;
