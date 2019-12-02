<?php
/**
 * @author 刘国君
 * @version 1.0
 */

namespace example;
class TestA{

    /**
     * @var string $name 用户名
     */
    public $name;


    /**
     * @var string $password 密码
     */
    public $password;
    /**
     * @var integer age 年龄
     */
    public $age;
    /**
     * @var float $double mm
     */
    public $double;
    /**
     * @var TestB $testB 子
     */
    public $testB;

    /**
     * TestA constructor.
     * @param string $name
     * @param string $password
     * @param int $age
     * @param float $double
     * @param TestB $testB
     */
    public function __construct(string $name, string $password, int $age, float $double, TestB $testB)
    {
        $this->name = $name;
        $this->password = $password;
        $this->age = $age;
        $this->double = $double;
        $this->testB = $testB;
    }
    public static function instance(){
        return New TestA('','',1,0,new TestB([]));
    }
}
class TestB{
    /**
     * @var array $lists;
     */
    public $lists;

    /**
     * TestB constructor.
     * @param array $lists
     */
    public function __construct(array $lists)
    {
        $this->lists = $lists;
    }


}
