<?php
/**
 * @author 刘国君
 * @version 1.0
 */

namespace liu;


abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var bool 是否会去更新api文档
     */
    protected $updateApi=false;
    /**
     * @var Requests 请求
     */
    protected $request;

    protected function setUp(){
        $this->updateApi=false;
        $this->request=new Requests('');
    }
    protected function tearDown(){
        if($this->updateApi){
            //只有请求成功才会去更新api文档
        }
    }
}