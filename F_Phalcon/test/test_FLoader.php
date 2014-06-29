<?php

require dirname(__FILE__) . '/../original/FLoader.php';

class Test_FLoader extends PHPUnit_Framework_Testcase
{
    public $loader;

    public function setUp()
    {

    }

    public function tearDown()
    {

    }

    public function testLoader()
    {
        $loader = new FLoader();

        $loader->registerClasses(
            array(
                'Class_A' => 'loader/Class_A.php',
                'Class_B' => 'loader/Class_Folder',
            )
        );

        $loader->registerNamespaces(
            array(
                'Na\\Na_A' => 'loader/Na/Na_A',
            )
        );

        $loader->registerPrefixes(
            array(
                'Prefix_' => 'loader/Prefix',
            )
        );

        $loader->registerDirs(
            array(
                'loader/dir_1',
                'loader/dir_2',
                'loader/custom',
            )
        );

        $loader->setExtensions(array('pp', 'ini'));

        $loader->setBasePath(dirname(__FILE__) . '/../test');

        $loader->setRule("return str_replace('_', '/', \$className) . '.php';");

        $loader->register();


        $obj1 = new Class_A();
        $obj2 = new Class_B();

        $obj3 = new Na\Na_A\Na_A_1();

        $obj4 = new Prefix_A();
        $obj5 = new Prefix_B();

        $obj6 = new Demo1();
        $obj7 = new Demo2();

        $obj8 = new A_B_C();

        $this->assertEquals(count($loader->getPools()), 8);
    }
}
