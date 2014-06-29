<?php
/**
 * Dependency Injection
 *
 * 参考自Phcalcon的DI类(@link: http://docs.phalconphp.com/en/latest/reference/di.html)，实现统一的资源设置、获取与管理，支持延时加载
 *
 * 调用的方式有：set/get函数、魔法方法setX/getX、类变量$fdi->X、数组$fdi['X]
 * 初始化的途径：直接赋值、类名、匿名函数
 *
 * @author: chanzonghuang@gmail.com 20140122
 */ 

class FDI implements ArrayAccess
{
    /**
     * @var object 单体实例对象句柄
     */ 
    protected static $_instance = null;

    /**
     * @var array 容器内存放全部变量与资源的数组
     */ 
    protected $_data = array();

    /** ------------------ 构造操作 ------------------ **/

    /**
     * 构造函数
     *
     */ 
    public function __construct()
    {

    }

    /**
     * 获取FDI单体实例
     *
     * 1、将进行service级的构造与初始化
     * 2、也可以通过new创建，但不能实现service的共享
     *
     * @return FDI
     */ 
    public static function getInstance()
    {
        if(self::$_instance == null){
            self::$_instance = new FDI();
            self::$_instance->onConstruct();
        }

        self::$_instance->onInitialize();

        return self::$_instance;
    }

    /**
     * service级的构造函数
     *
     * 1、可实现一些自定义业务的操作，如内置默认service
     * 2、首次创建时将会调用
     *
     * @return null
     */ 
    public function onConstruct()
    {
        //TODO
    }

    /**
     * service级的初始化函数
     *
     * 1、每次获取实例时将被调用
     *
     * @return null
     */ 
    public function onInitialize()
    {
        //TODO
        //自定义业务操作
    }

    /** ------------------ 统一Setter和Getter ------------------ **/

    /**
     * 统一setter
     *
     * 1、设置保存service的构造原型，延时创建
     *
     * @param string $key service注册名称，要求唯一，区分大小写
     * @parms mixed $value service的值，可以是具体的值或实例、类名、匿名函数、数组配置
     * @return null
     */ 
    public function set($key, $value)
    {
        $this->checkKey($key);

        $this->_data[$key] = $value;
    }

    /**
     * 统一getter
     *
     * 1、获取指定service的值，并根据其原型分不同情况创建
     * 2、首次创建时，如果service级的构造函数可调用，则调用
     * 3、每次获取时，如果非共享且service级的初始化函数可调用，则调用
     *
     * @param string $key service注册名称，要求唯一，区分大小写
     * @param mixed $default service不存在时的默认值
     * @param boolean $isShare 是否获取共享service
     * @return mixed
     */ 
    public function get($key, $default = null, $isShare = false)
    {
        $this->checkKey($key);

        if(!isset($this->_data[$key]))
            return $default;

        $value = $this->_data[$key];
        if($value instanceOf Closure){
            $value = $value();
        }elseif(is_string($value) && class_exists($value)){
            $value = new $value();
            if(is_callable(array($value, 'onConstruct')))
                call_user_func(array($value, 'onConstruct'));
            $isShare = false;
        }elseif(is_array($value)){
            //TODO
            //根据数组配置创建所需对象
        }

        if(!$isShare && is_object($value) && is_callable(array($value, 'onInitialize'))){
            call_user_func(array($value, 'onInitialize'));
        }

        $this->_data[$key] = $value;
        return $value;
    }

    protected function checkKey($key)
    {
        if(empty($key) || (!is_string($key) && !is_numeric($key)))
            throw new Exception('Unvalid service key(' . gettype($key). '), expect to string or numeric.');
    }

    /** ------------------ 魔法方法 ------------------ **/

    public function __call($name, $params)
    {
        if(substr($name, 0, 3) == 'set'){
            $key = lcfirst(substr($name, 3));
            return $this->set($key, isset($params[0]) ? $params[0] : null);
        }elseif(substr($name, 0, 3) == 'get'){
            $key = lcfirst(substr($name, 3));
            return $this->get($key, isset($params[0]) ? $params[0] : null);
        }

        throw new Exception('Call to  undefined method FDI::' . $name . '() .');
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function __get($name)
    {
        return $this->get($name, null, true);
    }

    /** ------------------ ArrayAccess（数组式访问）接口 ------------------ **/

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
    }

    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }
}

