<?php
require dirname(__FILE__) . '/../release/FDI.php';

/** ------------------ 创建与设置 ------------------ **/
//获取FDI
$fdi = FDI::getInstance();

//演示的key
$key = 'demoKey';

/** ------------------ 设置 ------------------ **/

//可赋值的类型：直接赋值、类名赋值、匿名函数
$fdi->set($key, 'Hello FDI!');
$fdi->set($key, 'Simple');
$fdi->set($key, function(){
    return new Simple();
});

//设置途径：除了上面的set()，你还可以这样赋值
$fdi->setDemoKey('Hello FDI!');
$fdi->demoKey = 'Hello FDI!';
$fdi['demoKey'] = 'Hello FDI!';


/** ------------------ 获取 ------------------ **/

//所以你可以这样取值
echo $fdi->get('demoKey'), "\n";
echo $fdi->getDemoKey(), "\n";
echo $fdi->demoKey, "\n";
echo $fdi['demoKey']. "\n";


/**
 * 演示类
 */ 
class Simple
{
    public function __construct()
    {

    }

    public function onConstruct()
    {

    }

    public function onInitialize()
    {

    }
}
