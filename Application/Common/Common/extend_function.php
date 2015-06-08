<?php
/* 
 * +---------------------------------------------------------------
 * | 应用程序
 * +---------------------------------------------------------------
 * | 上海闪尖软件科技有限公司
 * +---------------------------------------------------------------
 * | Author: 张志勇 316235872@qq.com
 * +---------------------------------------------------------------
 */

/**
 * 友好的时间显示
 *
 * @param int    $sTime 待显示的时间
 * @param string $type  类型. normal | mohu | full | ymd | other
 * @param string $alt   已失效
 * @return string
 */
function friendlyDate($sTime,$type = 'normal',$alt = 'false') {
    if (!$sTime)
        return '';
    //sTime=源时间，cTime=当前时间，dTime=时间差
    $cTime      =   time();
    $dTime      =   $cTime - $sTime;
    $dDay       =   intval(date("z",$cTime)) - intval(date("z",$sTime));
    //$dDay     =   intval($dTime/3600/24);
    $dYear      =   intval(date("Y",$cTime)) - intval(date("Y",$sTime));
    //normal：n秒前，n分钟前，n小时前，日期
    if($type=='normal'){
        if( $dTime < 60 ){
            if($dTime < 10){
                return '刚刚';    //by yangjs
            }else{
                return intval(floor($dTime / 10) * 10)."秒前";
            }
        }elseif( $dTime < 3600 ){
            return intval($dTime/60)."分钟前";
            //今天的数据.年份相同.日期相同.
        }elseif( $dYear==0 && $dDay == 0  ){
            //return intval($dTime/3600)."小时前";
            return '今天'.date('H:i',$sTime);
        }elseif($dYear==0){
            return date("m月d日 H:i",$sTime);
        }else{
            return date("Y-m-d H:i",$sTime);
        }
    }elseif($type=='mohu'){
        if( $dTime < 60 ){
            return $dTime."秒前";
        }elseif( $dTime < 3600 ){
            return intval($dTime/60)."分钟前";
        }elseif( $dTime >= 3600 && $dDay == 0  ){
            return intval($dTime/3600)."小时前";
        }elseif( $dDay > 0 && $dDay<=7 ){
            return intval($dDay)."天前";
        }elseif( $dDay > 7 &&  $dDay <= 30 ){
            return intval($dDay/7) . '周前';
        }elseif( $dDay > 30 ){
            return intval($dDay/30) . '个月前';
        }
        //full: Y-m-d , H:i:s
    }elseif($type=='full'){
        return date("Y-m-d , H:i:s",$sTime);
    }elseif($type=='ymd'){
        return date("Y-m-d",$sTime);
    }else{
        if( $dTime < 60 ){
            return $dTime."秒前";
        }elseif( $dTime < 3600 ){
            return intval($dTime/60)."分钟前";
        }elseif( $dTime >= 3600 && $dDay == 0  ){
            return intval($dTime/3600)."小时前";
        }elseif($dYear==0){
            return date("Y-m-d H:i:s",$sTime);
        }else{
            return date("Y-m-d H:i:s",$sTime);
        }
    }
}
 

/**
 * 自动缓存
 */
function cache_facility($key, $func, $interval){
    $result = S($key);
    if (!$result) {
        $result = $func();
        S($key, $result, $interval);
    }
    return $result;
}

/**
 * 清理缓存
 */
function cache_clean($path='Runtime/'){
    $dirname = './'.$path;
    $dirs = array($dirname);
    foreach ($dirs as $value) {
        rmdirr($value);
    }
    @mkdir($dirname, 0777, true);
}


function rmdirr($dirname)
{
    if (!file_exists($dirname)) {
        return false;
    }
    if (is_file($dirname) || is_link($dirname)) {
        return unlink($dirname);
    }
    $dir = dir($dirname);
    if ($dir) {
        while (false !== $entry = $dir->read()) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
        }
    }
    $dir->close();
    return rmdir($dirname);
}


//读取SEO规则
function get_seo_meta($vars,$seo)
{
    //获取还没有经过变量替换的META信息
    $meta = D('Common/SeoRule')->getMetaOfCurrentPage($seo);
    //替换META中的变量
    foreach ($meta as $key => &$value) {
        $value = seo_replace_variables($value, $vars);
    }
    unset($value);

    //返回被替换的META信息
    return $meta;
}

function seo_replace_variables($string, $vars)
{
    //如果输入的文字是空的，那就直接返回空的字符串好了。
    if (!$string) {
        return '';
    }

    //调用ThinkPHP中的解析引擎解析变量
    $view = new Think\View();
    $view->assign($vars);
    $result = $view->fetch('', $string);

    //返回替换变量后的结果
    return $result;
}

//判断是否为二维数组
function is_two_array($data){
    foreach($data as $value){
         if(is_array($value)){
             return TRUE;
         }
    }
    return FALSE;
}

/**
 * 获取头像
 * 如果头像不存在，则传回默认头像 
 */
function get_user_head($uid){

	$cover_id = D('Member')->where( array('uid'=>$uid) )->getField('head_pic_id');
	return get_head($cover_id).'?random='.rand(10000,99999);	//加上随机数，防止手机端图片缓存问题而出现的图片压缩
}

function get_head($cover_id, $field = 'path'){
        $default_img='/Public/Home/images/user-head.jpg';
	$pic_img = get_cover($cover_id, $field);
	$pic_img = $cover_id==0?$default_img:$pic_img;
	return $pic_img;
}

//创建指定大小的
function create_zoom_os( $pic, $size=array( 'small'=>array(100,100),'middle'=>array(300,300) ) ){

	$image = new \Think\Image();

	$pic_img = '.'.$pic;

	$basename 	= basename($pic_img);
	$dirname 	= dirname($pic_img);
	
	//判断压缩目录是否存在，不存在则创建
	if( !is_dir($dirname) ){
		mkdir($dirname);
	}
	//小图
	$image->open( $pic_img );
	$image->thumb( $size['small'][0], $size['small'][1], \Think\Image::IMAGE_THUMB_SCALE)->save( $dirname .'/small_'. $basename );
	//中图
	$image->open( $pic_img );
	$image->thumb( $size['middle'][0], $size['middle'][1], \Think\Image::IMAGE_THUMB_SCALE)->save( $dirname .'/meddle_'. $basename );
}

function is_ie()
{
	$userAgent = $_SERVER['HTTP_USER_AGENT'];
	$pos = strpos($userAgent, ' MSIE ');
	if ($pos === false) {
		return false;
	} else {
		return true;
	}
}