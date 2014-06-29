<?php
require dirname(__FILE__) . '/../original/FDI.php';
require dirname(__FILE__) . '/fdi/Demo2.php';

class FDI_Test extends PHPUnit_Framework_Testcase
{
    public $fdi;

    public function setUp()
    {
        echo "\nsetUp ...\n";

        //$this->fdi = new FDI();
        $this->fdi = FDI::getInstance();

    }

    public function tearDown()
    {
        echo "\ntearDown ...\n";
    }

    public function testSetterAndGetter()
    {
        $this->fdi->set('name', 'dogstar');
        $this->assertEquals($this->fdi->get('name'), 'dogstar');

        $arr = array(1, 5, 7);
        $this->fdi->set('nameArr', $arr);
        $this->assertEquals($this->fdi->get('nameArr'), $arr);
    }

    public function testMagicFunction()
    {
        $this->fdi->setName('dogstar');
        $this->assertEquals($this->fdi->getName(), 'dogstar');

        $this->assertEquals($this->fdi->getNameDefault('2013'), '2013');

        $this->assertEquals($this->fdi->getNameNull(), null);

        $this->fdi->setNameSetNull();
        $this->assertEquals($this->fdi->getNameSetNull(), null);
    }

    public function testClassSettterAndGetter()
    {
        $this->fdi->name2 = 'dogstar';
        $this->assertEquals($this->fdi->name2, 'dogstar');

        $this->fdi->nameAgain = 'dogstarAgain';
        $this->assertEquals($this->fdi->nameAgain, 'dogstarAgain');

        $this->assertEquals($this->fdi->nameNull, null);

    }

    public function testMixed()
    {
        $this->fdi->name1 = 'dogstar1';
        $this->assertEquals($this->fdi->name1, 'dogstar1');
        $this->assertEquals($this->fdi->getName1('2013'), 'dogstar1');
        $this->assertEquals($this->fdi->name1, 'dogstar1');

        $this->fdi->setName1('dogstar2');
        $this->assertEquals($this->fdi->name1, 'dogstar2');
        $this->assertEquals($this->fdi->getName1('2013'), 'dogstar2');
        $this->assertEquals($this->fdi->name1, 'dogstar2');

        $this->fdi->set('name1', 'dogstar3');
        $this->assertEquals($this->fdi->name1, 'dogstar3');
        $this->assertEquals($this->fdi->getName1('2013'), 'dogstar3');
        $this->assertEquals($this->fdi->name1, 'dogstar3');

    }

    public function testAnonymousFunction()
    {
        $this->fdi->set('name', function(){
           return new Demo(2014);   
        });

        $this->assertEquals($this->fdi->name->mark, 2014);

        $mark = 2015;
        $this->fdi->set('name1', function() use ($mark){
            return new Demo($mark);
        });
        $this->assertEquals($this->fdi->name1->mark, $mark);

        $this->fdi->name3 = function(){
            return new Demo(2015);
        };
        $this->assertEquals($this->fdi->getName3()->mark, 2015);
    }

    public function testLazyLoadClass()
    {
        $this->fdi->setName('Demo2');
        $this->assertEquals($this->fdi->name instanceof Demo2, true);
        $this->assertEquals($this->fdi->name->number, 3);
        $this->assertEquals($this->fdi->name->number, 3);
        $this->fdi->name->number = 9;
        $this->assertEquals($this->fdi->name->number, 9);
        $this->assertEquals($this->fdi->getName()->number, 3);
    }

    public function testArrayIndex()
    {
        $this->fdi['name'] = 'dogstar';
        $this->assertEquals($this->fdi->name, 'dogstar');

        $this->fdi[2014] = 'horse';
        $this->assertEquals($this->fdi->get2014(), 'horse');
        $this->assertEquals($this->fdi[2014], 'horse');
        $this->assertEquals($this->fdi->get(2014), 'horse');
    }

    public function testException()
    {
        /*
        $this->fdi[array(1)] = 1;
        $this->fdi->set(array(1), array(1));
        $this->fdi->get(array(1), array(1));
         */
    }
}


class Demo
{
    public $mark = null;

    public function __construct($mark)
    {
        echo "Demo::__construct()\n";

        $this->mark = $mark;
    }

    public function onConstruct()
    {
        echo "Demo::onConstruct()\n";
    }

    public function onInitialize()
    {  
        echo "Demo:: onInitialize()\n";
    }
}
