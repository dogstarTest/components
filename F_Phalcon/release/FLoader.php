<?php
/**
 * Universal Class Loader
 *
 * 参考自参考自Phcalcon的Loader类(@link: http://docs.phalconphp.com/en/latest/reference/loader.html)，实现统一的类加载
 *
 * 支持路径注册的方式有：命名空间、类名、前缀名、目录，同时支持自定义的扩展名
 *
 * @author: chanzonghuang@gmail.com 20140128
 */ 

class FLoader
{
    //四种类型的注册方式
    const TYPE_NAMESPACE    = 10;   //命名空间注册方式
    const TYPE_PREFIX       = 11;   //前缀名注册方式
    const TYPE_DIR          = 12;   //目录注册方式
    const TYPE_CLASS        = 13;   //类名注册方式

    /**
     * @var array 加载池
     */ 
    protected $_pools = array();

    /**
     * @var array 注册表
     */ 
    protected $_registerion = array();

    /**
     * @var array 自定义扩展名
     */ 
    protected $_extensions = array();

    /**
     * @var string 项目根路径 
     */ 
    protected $_basePath = '';

    /**
     * @var string 自定义的类名-路径规则，将被eval，类名用$className
     */ 
    protected $_rule = '';

    /** ------------------ 构造操作 ------------------ **/

    /**
     * 构造函数
     *
     */ 
    public function __construct()
    {
        $this->_registerion[self::TYPE_NAMESPACE] = array();
        $this->_registerion[self::TYPE_PREFIX] = array();
        $this->_registerion[self::TYPE_DIR] = array();
        $this->_registerion[self::TYPE_CLASS] = array();

        $this->_extensions['php'] = '.php';
    }

    /** ------------------ 外部调用 ------------------ **/

    /**
     * 注册自动加载
     *
     * 1、在定义各种加载方式后，必须调用此函数才能实现由此类提供的自动加载
     *
     * @return null
     */ 
    public function register()
    {
        spl_autoload_register(array($this, 'loader'));
    }

    /**
     * 注册命名空间加载方式
     *
     * @param array $namespaces 新注册的命名空间
     * @return $this
     */ 
    public function registerNamespaces($namespaces)
    {
        $this->_registerion[self::TYPE_NAMESPACE] = array_merge($this->_registerion[self::TYPE_NAMESPACE], $namespaces);

        return $this;
    }

    /**
     * 注册前缀名加载方式
     *
     * @param array $prefixes 新注册的类前缀名
     * @return $this
     */ 
    public function registerPrefixes($prefixes)
    {
        $this->_registerion[self::TYPE_PREFIX] = array_merge($this->_registerion[self::TYPE_PREFIX], $prefixes);

        return $this;
    }

    /**
     * 注册目录加载方式
     *
     * @param string/array $dirs 新注册的目录
     * @return $this
     */ 
    public function registerDirs($dirs)
    {
        if(is_string($dirs))
            $dirs = array($dirs);

        $this->_registerion[self::TYPE_DIR] = array_merge($this->_registerion[self::TYPE_DIR], $dirs);

        return $this;
    }

    /**
     * 注册类名加载方式
     *
     * @param array $classes 新注册的类名
     * @return $this
     */ 
    public function registerClasses($classes)
    {
        $this->_registerion[self::TYPE_CLASS] = array_merge($this->_registerion[self::TYPE_CLASS], $classes); 

        return $this;
    }

    /**
     * 设置（添加）加载文件的扩展名
     *
     * @param string/array $exts 扩展名，默认已支持php
     * @return $this
     */ 
    public function setExtensions($exts)
    {
        if(is_string($exts))
            $exts = array($exts);

        foreach($exts as $ext){
            $this->_extensions[$ext] = '.' . $ext;
        }

        return $this;
    }

    /**
     * 设置项目根路径
     *
     * @param string $path 项目根路径
     * @return $this
     */ 
    public function setBasePath($path)
    {
        $this->_basePath = $path;

        return $this;
    }

    public function setRule($rule)
    {
        $this->_rule = $rule;

        return $this;
    }

    /** ------------------ 内部实现 ------------------ **/

    /**
     * 自动加载
     *
     * 1、按命名空间、类名、类前缀、目录顺序依次加载
     *
     * @param string $className 等待加载的类名
     * @return boolean
     */ 
    public function loader($className)
    {
        $ns = substr($className, 0, strrpos($className, '\\'));
        if(!empty($ns)){
            $className = strrchr($className, '\\');
            $className = substr($className, 1);
            foreach($this->_registerion[self::TYPE_NAMESPACE] as $namespace => $path){
                if($ns == $namespace && $this->_loadClassByFile($path, $className))
                    return true;
            }
            return false;
        }

        foreach($this->_registerion[self::TYPE_CLASS] as $class => $path){
            if($class == $className && $this->_loadClassByFile($path, $className))
                return true;
        }

        foreach($this->_registerion[self::TYPE_PREFIX] as $prefix => $path){
            if(stripos($className, $prefix) === 0 && $this->_loadClassByFile($path, $className))
                return true;    
        }

        foreach($this->_registerion[self::TYPE_DIR] as $dir){
            if($this->_loadClassByFile($dir, $className))
                return true;
        }

        //throw new Exception("Class '$className' not found .");
        return false;
    }

    /**
     * 加载目录或文件
     *
     * 1、优先加载文件，其次加载目录
     * 2、优先加载相对路径，其次加载绝对路径
     * 3、优先加载.php扩展，其次加载自定义扩展
     * 4、优先使用默认匹配，其次使用自定义匹配
     *
     * @param string $filePath 等待加载的文件或目录
     * @param string $className 类名
     * @return boolean
     */ 
    protected function _loadClassByFile($filePath, $className)
    {
        if(isset($this->_pools[$className]))
            return true;

        $isFound = false;

        if(file_exists($filePath))
            $isFound = true;

        if(!$isFound && !empty($this->_basePath) && substr($filePath, 0, 1) != DIRECTORY_SEPARATOR){
            $filePath = $this->_basePath . DIRECTORY_SEPARATOR . $filePath;
            if(file_exists($filePath))
                $isFound = true;
        }

        if(!$isFound)
            return false;

        if(is_file($filePath)){
            require $filePath;

            /**
            if(!class_exists($className))
                throw new Exception("Failed to autoload class '$className' in file '$filePath' .");
             */

            $this->_pools[$className] = $filePath;

            return true;
        }

        foreach($this->_extensions as $ext => $extName){
            if($this->_loadClassByFile($filePath . DIRECTORY_SEPARATOR . $className . $extName, $className))
                return true;
        }

        if(!empty($this->_rule) && $this->_loadClassByFile($filePath . DIRECTORY_SEPARATOR . eval($this->_rule), $className))
            return true;
        
        return false;
    }

    /** ------------------ 调试测试相关 ------------------ **/

    public function getPools()
    {
        return $this->_pools;
    }

    public function getRegisterion($type = null)
    {
        return ($type != null && isset($this->_registerion[$type])) ? $this->_registerion[$type] : $this->_registerion;
    }
}
