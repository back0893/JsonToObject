<?php


//$json=new stdClass();
//$json->name='liu';
//$json->password='123456';
//$json->age=123;
//$json->double=1.234;
//$json->testB=new stdClass();
//$json->testC=new stdClass();
//$json->testB->lists=[1,3,4,5];
//$json->testC->lists=[1,3,4,5];
//$a=mapJson($json,\example\TestA::class);
//var_dump($a);
$request=new \liu\Requests("http://127.0.0.1:8080");
$request->setCookie([
    'XDEBUG_SESSION'=>'PHPSTORM'
]);
$response=$request->request();
var_dump($response);
var_dump($request->getResponseHttpCode());

