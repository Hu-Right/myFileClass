<?php 

namespace Myself;
/**
* ============================================================================
* 文件操作类
* ============================================================================
* @author  Zx
* @version 1.0
*/
class File{
      
        /**
         * 创建文件夹
         *
         * @param string $path 文件夹路径
         */
        public static  function createFolder($path)
      {
         if (!is_dir($path))
         {
            mkdir($path, 0777);
         }
      }

        /**
         * 获取指定目录里的文件夹
         *
         * @param string $path 文件夹路径
         */
		public static  function GetFile($path){
			$aFileArr = array();
			if (is_dir($path) && $handle=opendir($path)) {
				
				while (false !== ($file = readdir($handle))) {
					if( $file == "." || $file == ".." || is_file($path . '/' . $file) ){
						continue;
					}
					$aFileArr[] = $file;
				}
				
				closedir($handle);
			}
			return $aFileArr;
		}


      /**
       * 得到指定目录里的信息
       *
       * @return unknown
       */
      public  static  function getFolder($path , &$array)
      {
			
             if (!is_dir($path))
               return null;
             $path = rtrim($path,'/').'/';  

           $dir_obj = opendir($path);

           while ($dir = readdir($dir_obj))
           {         
                if ($dir != '.' && $dir != '..')
                {
                    $file = $path.$dir;
                    if (is_dir($file)){
						self::getFolder($file , $array);  
						   
					}else{
						$array[] = $file;
					}
                   				
                    
                }       
           }    
           closedir($dir_obj);
		   
		 return $array;
      }

      
      /**
       * 删除文件
       *
       * @param string $path 文件路径
       */
      public  static function  delFile($path)
      {
           if (file_exists($path))
           {
                  unlink($path);
           }
      }
     
      /**
       * 删除目录
       *
       * @param string $dir 目录路径
       */
     public static function deleteDir($path) 
     {             
             if (!is_dir($path))
               return ;
             
             $path = rtrim($path,'/').'/';  

           $dir_obj = opendir($path);

           while ($dir = readdir($dir_obj))
           {         
                if ($dir != '.' && $dir != '..')
                {
                    $file = $path.$dir;
                    if (is_dir($file))
                       self::deleteDir($file);
                    elseif (is_file($file)){                         
                        unlink($file);
                    }    
                        
                }       
           }           
           closedir($dir_obj);    
           rmdir($path);     
     }
     
     /**
      * 复制目录
      *
      * @param string $source  要复制的目录地址
      * @param string $destination  目标目录地址
      * @param int $child  是否复制子目录
      * @return bool
      */
     public static  function xCopy($source, $destination, $child=1)
     { 
        if(!is_dir($source))
        { 
           echo("Error:the $source is not a direction!"); 
           return 0; 
        } 
        if(!is_dir($destination))
        { 
           mkdir($destination,0777); 
        } 
        $handle=dir($source); 
        while($entry=$handle->read()) 
        { 
           if(($entry!=".")&&($entry!=".."))
           { 
            if(is_dir($source."/".$entry))
            { 
             if($child) 
               self::xCopy($source."/".$entry,$destination."/".$entry,$child);            
            } 
            else
            { 
               copy($source."/".$entry,$destination."/".$entry); 
            
            }
        
           } 
        } 
        return 1; 
     }
     
     /**
      * 复制文件
      *
      * @param string $source  要复制的目录地址
      * @param string $destination  目标目录地址
      * @return null
      */
     public static  function copyFile($source,$path)
     {
	 	  $source = iconv("UTF-8","GBK",$source );
          $path  = str_replace('\\','/',$path);
          $arr = explode('/',$path);
          array_pop($arr);
          $folder = iconv("UTF-8","GBK",join('/',$arr));
          if (!is_dir($folder)){
              self::createFolder($folder);           
          }
           if (is_file($source)){
               copy($source,$path); 
           }
     }
      
     
     /**
      * 创建文件
      * 
      * @param unknown_type $file
      */
     public static function createFile($file,$content='')
     {
         $folder = dirname($file);
         if (!is_dir($folder)){
             self::createFolder($folder);
         }
         
         file_put_contents($file,$content);
     }
  }
  
  <?php
namespace Admin\Controller;
use Think\Controller;
use Myself\File;
class BuildController extends BaseController {


	 protected $m = NULL;
	 protected $m_config = NULL;
	 protected $m_send = NULL;
	 
	 public function _initialize(){
	 		parent::_initialize();
			$this->m = M('admin_language');	
			$this->m_config = M('admin_config');	
			$this->m_send = M('admin_emailcontent');	
	 }
	
  
	
	public function clearCache(){
		if($this->_clearRuntime('Cache')){
			$this->success('清理成功！',U('Index/addMain'));
		}else{
			$this->error('没有可清除的缓存！',U('Index/addMain'));
		}
	}
	
	public function clearLogs(){
		if($this->_clearRuntime('Logs')){
			$this->success('清理成功！',U('Index/addMain'));
		}else{
			$this->error('没有可清除的日志！',U('Index/addMain'));
		}
	}
	
	public function _clearRuntime($CPath='Cache'){
		$fileArr=File::GetFile(__PHYSICS__.'/Runtime/'.$CPath);
		//var_dump($fileArr);
		$this->checkUser();
		if($fileArr){
			foreach($fileArr as $item){
				File::deleteDir(__PHYSICS__.'/Runtime/'.$CPath.'/'.$item);
			}
			return true;
		}else{
			return false;
		}
	}
	
}


?>
