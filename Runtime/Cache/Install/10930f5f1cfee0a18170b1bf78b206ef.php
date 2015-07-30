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
    <li class="active"><a href="javascript:;">开始安装</a></li>
    <li class="active"><a href="javascript:;">完成安装</a></li>

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
            
    <div class="col-xs-12 col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                <span>恭喜您，<?php echo C('INSTALL_PRODUCT_NAME');?>安装完成！</span>
            </div>
            <div class="panel-body">
                <?php if(isset($info)): echo ($info); endif; ?>
                <a class="btn btn-primary" href="<?php echo U('Admin/Index/index');?>">登录后台</a>
                <a class="btn btn-success" href="/TestOS/">访问首页</a>
            </div>
            <div class="panel-footer">
                <span>版权所有 (c) 2014－<?php echo date("Y",time()); echo C('INSTALL_COMPANY_NAME');?> 保留所有权利。</span>
            </div>
        </div>
    </div>

        </div>
    </div>
</body>
</html>