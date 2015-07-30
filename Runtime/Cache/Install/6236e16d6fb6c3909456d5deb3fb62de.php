<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>安装<?php echo C('INSTALL_PRODUCT_NAME');?></title>
    <link rel="stylesheet" type="text/css" href="/TestOS/Public/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/TestOS/Public/bootstrapextend/css/bootstrap.extend.css">
    <script type="text/javascript" src="/TestOS/Public/jquery/jquery-1.11.1.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="/TestOS/Public/bootstrap/js/bootstrap.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="/TestOS/Public/bootstrapextend/js/bootstrap.extend.js" charset="utf-8"></script>
    
</head>
<body style="background-color: #f6f6f6;">
    <div class="navbar navbar-inverse border-radius-none">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="sr-only">导航开关</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" target="_blank" href="/TestOS/"><?php echo C('INSTALL_PRODUCT_NAME');?></a>
            </div>
            <div class="collapse navbar-collapse nav-collapse">
                <ul class="nav navbar-nav" id="step">
                    
    <li class="active"><a href="javascript:;">安装协议</a></li>
    <li class="active"><a href="javascript:;">环境检测</a></li>
    <li class="active"><a href="javascript:;">参数设置</a></li>
    <li><a href="javascript:;">开始安装</a></li>
    <li><a href="javascript:;">完成安装</a></li>

                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="<?php echo C('INSTALL_WEBSITE_DOMAIN');?>" target="_blank">官网</a></li>
                    <li><a href="<?php echo C('INSTALL_WEBSITE_DOMAIN');?>" target="_blank">授权</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            
    <?php
 defined('SAE_MYSQL_HOST_M') or define('SAE_MYSQL_HOST_M', '127.0.0.1'); defined('SAE_MYSQL_HOST_M') or define('SAE_MYSQL_PORT', '3306'); ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <span>参数设置</span>
        </div>
        <div class="panel-body">
            <form class="form" action="/TestOS/index.php?s=/Install/step2.html" method="post" target="_self">
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <h2>数据库连接信息</h2>
                        <div class="form-group">
                            <label class="control-label">数据库连接类型</label>
                            <div class="control-group">
                                <select name="db[]" class="form-control">
                                    <option>mysql</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">数据库服务器，数据库服务器IP，一般为127.0.0.1</label>
                            <div class="control-group">
                                <input class="form-control" type="text" name="db[]" value="<?php if(defined("SAE_MYSQL_HOST_M")): echo (SAE_MYSQL_HOST_M); else: ?>127.0.0.1<?php endif; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">数据库名</label>
                            <div class="control-group">
                                <input class="form-control" type="text" name="db[]" value="<?php if(defined("SAE_MYSQL_DB")): echo (SAE_MYSQL_DB); else: ?>oneshop<?php endif; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">数据库用户名</label>
                            <div class="control-group">
                                <input class="form-control" type="text" name="db[]" value="<?php if(defined("SAE_MYSQL_USER")): echo (SAE_MYSQL_USER); else: ?>root<?php endif; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">数据库密码</label>
                            <div class="control-group">
                                <input class="form-control" type="password" name="db[]" value="<?php if(defined("SAE_MYSQL_PASS")): echo (SAE_MYSQL_PASS); endif; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">数据库端口，数据库服务连接端口，一般为3306</label>
                            <div class="control-group">
                                <input class="form-control" type="text" name="db[]" value="<?php if(defined("SAE_MYSQL_PORT")): echo (SAE_MYSQL_PORT); else: ?>3306<?php endif; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">数据表前缀，同一个数据库运行多个系统时请修改为不同的前缀</label>
                            <div class="control-group">
                                <input class="form-control" type="text" name="db[]" value="<?php echo (C("ORIGINAL_TABLE_PREFIX")); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <h2>创始人帐号信息</h2>
                        <div class="form-group">
                            <label class="control-label">用户名</label>
                            <div class="control-group">
                                <input class="form-control" type="text" name="admin[]" value="admin">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">密码</label>
                            <div class="control-group">
                                <input class="form-control" type="password" name="admin[]" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">确认密码</label>
                            <div class="control-group">
                                <input class="form-control" type="password" name="admin[]" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">邮箱，请填写正确的邮箱便于收取提醒邮件</label>
                            <div class="control-group">
                                <input class="form-control" type="text" name="admin[]" value="admin@example.com">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">手机号</label>
                            <div class="control-group">
                                <input class="form-control" type="text" name="admin[]" value="">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <input type="submit" class="btn btn-primary btn-block ajax-post" target-form="form" value="下一步">
            <a class="btn btn btn-default btn-block" href="<?php echo U('Install/step1');?>">上一步</a>
        </div>
        <div class="panel-footer">
            <span>版权所有 (c) 2014－<?php echo date("Y",time()); echo C('INSTALL_COMPANY_NAME');?> 保留所有权利。</span>
        </div>
    </div> 

        </div>
    </div>
</body>
</html>