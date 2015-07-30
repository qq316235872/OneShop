<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Common\Model;
use Think\Model;
use Think\Upload;

/**
 * 图片模型
 * 负责图片的上传
 */

class PictureModel extends Model{
    /**
     * 自动完成
     * @var array
     */
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
    );

    /**
     * 文件上传
     * @param  array  $files   要上传的文件列表（通常是$_FILES数组）
     * @param  array  $setting 文件上传配置
     * @param  string $driver  上传驱动名称
     * @param  array  $config  上传驱动配置
     * @return array           文件上传成功后的信息
     */
    public function upload($files, $setting, $driver = 'Local', $config = null){
        /* 上传文件 */
        $setting['callback'] = array($this, 'isFile');
	$setting['removeTrash'] = array($this, 'removeTrash');
        $Upload = new Upload($setting, $driver, $config);
        $info   = $Upload->upload($files);
        //生成缩略图
        foreach($info as $pic){
        	if( !isset($pic['path']) ){
        		$pic['path'] = '/Uploads/Picture/'.$pic['savepath'].$pic['savename'];
        		//解决头像偏移
        		//-----------------------------------------------------------------------------------------------
        		$src = '.'.$pic['path'];
        		$exif = exif_read_data ($src,"IFD0");
        		if($exif===false){
        			
        		}else{
        			$exif=exif_read_data($src);
        			if(!empty($exif['Orientation'])) {
        				if(($exif['Orientation']==6)||($exif['Orientation']==3)||($exif['Orientation']==8))
        				{
        					switch ($exif['FileType']) {
        						case 1:
        							$src_f = imagecreatefromgif($src);break;
        						case 2:
        							$src_f = imagecreatefromjpeg($src);break;
        						case 3:
        							$src_f = imagecreatefrompng($src);break;
        					}
        					if($src_f=="")return false;
        					if($exif['Orientation']==6)
        					{
        						$rotate = imagerotate($src_f, 270, 0);
        					}
        					else if($exif['Orientation']==3)
        					{
        						$rotate = imagerotate($src_f, 180, 0);
        					}
        					else if($exif['Orientation']==8)
        					{
        						$rotate = imagerotate($src_f, 90, 0);
        					}
        					imagedestroy($src_f);
        					switch ($exif['FileType']) {
        						case IMAGETYPE_GIF:
        							$result = imagegif($rotate, $src);
        							$src_img = imagecreatefromgif($src);
        							break;
        		
        						case IMAGETYPE_JPEG:
        							$result = imagejpeg($rotate, $src);
        							$src_img = imagecreatefromjpeg($src);
        							break;
        		
        						case IMAGETYPE_PNG:
        							$result = imagepng($rotate, $src);
        							$src_img = imagecreatefrompng($src);
        							break;
        					}
        					imagedestroy($rotate);
        				}
        			}
        		}
        		//-----------------------------------------------------------------------------------------------
        	}
        	create_zoom_os( $pic['path'] );
        }
        if($info){ //文件上传成功，记录文件信息
            foreach ($info as $key => &$value) {
                /* 已经存在文件记录 */
                if(isset($value['id']) && is_numeric($value['id'])){
                    continue;
                }
                /* 记录文件信息 */
                $value['path'] = substr($setting['rootPath'], 1).$value['savepath'].$value['savename'];	//在模板里的url路径
                if($this->create($value) && ($id = $this->add())){
                    $value['id'] = $id;
                } else {
                    //TODO: 文件上传成功，但是记录文件信息失败，需记录日志
                    unset($info[$key]);
                }
            }
            return $info; //文件上传成功
        } else {
            $this->error = $Upload->getError();
            return false;
        }
    }

    /**
     * 下载指定文件
     * @param  number  $root 文件存储根目录
     * @param  integer $id   文件ID
     * @param  string   $args     回调函数参数
     * @return boolean       false-下载失败，否则输出下载文件
     */
    public function download($root, $id, $callback = null, $args = null){
        /* 获取下载文件信息 */
        $file = $this->find($id);
        if(!$file){
            $this->error = '不存在该文件！';
            return false;
        }

        /* 下载文件 */
        switch ($file['location']) {
            case 0: //下载本地文件
                $file['rootpath'] = $root;
                return $this->downLocalFile($file, $callback, $args);
            case 1: //TODO: 下载远程FTP文件
                break;
            default:
                $this->error = '不支持的文件存储类型！';
                return false;

        }

    }

    /**
     * 检测当前上传的文件是否已经存在
     * @param  array   $file 文件上传数组
     * @return boolean       文件信息， false - 不存在该文件
     */
    public function isFile($file){
        if(empty($file['md5'])){
            throw new \Exception('缺少参数:md5');
        }
        /* 查找文件 */
		$map = array('md5' => $file['md5'],'sha1'=>$file['sha1'],);
        return $this->field(true)->where($map)->find();
    }

    /**
     * 下载本地文件
     * @param  array    $file     文件信息数组
     * @param  callable $callback 下载回调函数，一般用于增加下载次数
     * @param  string   $args     回调函数参数
     * @return boolean            下载失败返回false
     */
    private function downLocalFile($file, $callback = null, $args = null){
        if(is_file($file['rootpath'].$file['savepath'].$file['savename'])){
            /* 调用回调函数新增下载数 */
            is_callable($callback) && call_user_func($callback, $args);

            /* 执行下载 */ //TODO: 大文件断点续传
            header("Content-Description: File Transfer");
            header('Content-type: ' . $file['type']);
            header('Content-Length:' . $file['size']);
            if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
                header('Content-Disposition: attachment; filename="' . rawurlencode($file['name']) . '"');
            } else {
                header('Content-Disposition: attachment; filename="' . $file['name'] . '"');
            }
            readfile($file['rootpath'].$file['savepath'].$file['savename']);
            exit;
        } else {
            $this->error = '文件已被删除！';
            return false;
        }
    }

	/**
	 * 清除数据库存在但本地不存在的数据
	 * @param $data
	 */
	public function removeTrash($data){
		$this->where(array('id'=>$data['id'],))->delete();
	}

}
