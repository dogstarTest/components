<?php

require dirname(__FILE__) . '/../release/FLoader.php';

/** ------------------ 创建与设置 ------------------ **/

//创建加载器
$loader = new FLoader();

//注册类名
$loader->registerClasses(
    array(
        'Class_A' => 'loader/Class_A.php',
        'Class_B' => 'loader/Class_Folder',
    )
);

//注册命名空间
$loader->registerNamespaces(
    array(
        'Na\\Na_A' => 'loader/Na/Na_A',
    )
);

//注册类名前缀
$loader->registerPrefixes(
    array(
        'Prefix_' => 'loader/Prefix',
    )
);

//注册目录
$loader->registerDirs(
    array(
        'loader/dir_1',
        'loader/dir_2',
    )
);

//设置扩展名
$loader->setExtensions(array('pp', 'ini'));

//设置根路径
$loader->setBasePath(dirname(__FILE__) . '/../test');

//设置自定义规则
$loader->registerDirs('loader/custom')->setRule("return str_replace('_', '/', \$className) . '.php';");

//开启自动加载
$loader->register();


/** ------------------ 自动加载 ------------------ **/

$obj1 = new Class_A();
$obj2 = new Class_B();

$obj3 = new Na\Na_A\Na_A_1();

$obj4 = new Prefix_A();
$obj5 = new Prefix_B();

$obj6 = new Demo1();
$obj7 = new Demo2();

$obj8 = new A_B_C();

print_r($loader->getPools());
print_r($loader->getRegisterion());

echo "Finish to create all objects! \n\n";
