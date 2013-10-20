<?php
$entry = ThinkInstance::D('InfoList');
$data  = $entry->getHpTopList();
//xdebug_max_depth(30);
//var_dump($data);
return $data;
