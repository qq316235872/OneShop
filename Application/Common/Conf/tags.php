<?php
/**
 * 行为扩展
 */
return array(
    'app_init'     => array('Common\Behavior\InitModuleBehavior'),
    'app_begin'    => array('Common\Behavior\InitConfigBehavior'),
    'action_begin' => array('Common\Behavior\InitHookBehavior')
);
