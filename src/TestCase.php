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

    /**
     * @var mixed 生成的对应对象
     */
    protected $instance;

    protected function setUp(){
        $this->updateApi=false;
        $this->request=new Requests('');
    }
    protected function tearDown(){
        if($this->updateApi){
            //只有请求成功才会去更新api文档
            $markParam=new \liu\MarkParam();
            $markParam->setUrl($this->request->getUrl());
            $markParam->setMethod([$this->request->getMethod()]);
            $markParam->setFormData($this->request->getParam());
            $markParam->setResponse($this->instance);

            $request=new \liu\Requests('https://www.showdoc.cc/server/api/item/updateByApi');
            $param=[
                'api_key'=>'7fb6222f45ef6762bec0e153f7b16c58524841132',
                'api_token'=>'9322b2f5cf4a7e537d243174cbdcb5e01257330080',
                'page_title'=>'测试自动生成接口2',
                'page_content'=> $markParam->markdown(),
                's_number'=>1
            ];
            $request->setParam($param);
            $request->setMethod('post');
            $request->request();
        }
    }
}