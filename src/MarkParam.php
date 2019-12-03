<?php
/**
 * @author 刘国君
 * @version 1.0
 *  ['参数名','必选','类型','说明'];
 */

namespace liu;

interface export
{
    function markdown(): string;
    public function __toString();
}

class Mark implements export
{
    protected $title;
    protected $value;

    /**
     * Mark constructor.
     * @param string $title
     * @param export $value
     */
    public function __construct(string $title, export $value)
    {
        $this->title = $title;
        $this->value = $value;
    }

    function markdown(): string
    {
        $markdown = sprintf("**%s:**\n\n", $this->title);
        $markdown .= $this->value->markdown();
        return $markdown;
    }

    public function __toString()
    {
        return $this->markdown();
    }


}

/**
 * Class MarkTitle
 *队列
 */
class MarkList implements export
{
    protected $list;

    /**
     * MarkList constructor.
     * @param array $list
     */
    public function __construct(array $list)
    {
        $this->list = $list;
    }

    function markdown(): string
    {
        $makrdown = [];
        foreach ($this->list as $item) {
            $makrdown[] = sprintf("- %s", $item);
        }
        return implode("\n", $makrdown);
    }

    public function __toString()
    {
        return $this->markdown();
    }

}

class MarkFormRow implements export{
    protected $value;
    protected $is_request;
    protected $level=0;
    public function __construct($value,$is_request,$level=0)
    {
        if(is_array($value)){
            $value=(object)$value;
        }
        $this->value=$value;
        $this->is_request=$is_request;
        $this->level=$level;
    }

    function markdown(): string
    {
        $reflection=new \ReflectionObject($this->value);
        $properties=$reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        $markdown=[];
        foreach ($properties as $property){
            $type=getValueType($property->getValue($this->value));
            /**
             * 注释就是的php的注释 var type name 说明
             */
            $doc=explode(' ',trim(str_replace(['*','\\','/'],'',$property->getDocComment())));
            $doc=array_slice($doc,1);
            if($this->is_request){
                $markdown[]=implode('|',[$property->getName(),'是',$type,$doc[2]??'待补充']);
            }
            else{
                $markdown[]=implode('|',[str_repeat('--',$this->level).$property->getName(),$type,$doc[2]??'待补充']);
            }
            if($type=='object'){
                $markdown[]=(new MarkFormRow($property->getValue($this->value),$this->is_request,$this->level+1));
            }
        }
        return implode("\n", $markdown);
    }

    public function __toString()
    {
        return $this->markdown();
    }

}
/**
 * Class MarkForm
 * 表格
 */
class MarkForm implements export
{
    protected $filed;
    protected $value;

    /**
     * MarkForm constructor.
     * @param $value
     * @param $filed
     */
    public function __construct($filed,$value)
    {
        $this->value =new MarkFormRow($value,count($filed)==4);
        $this->filed=$filed;
    }

    function markdown(): string
    {
        $markdown = [];
        $markdown[] = implode('|', $this->filed);
        $markdown[] = implode('|', array_fill(0, count($this->filed), ':----'));
        $markdown[]=$this->value->markdown();
        return implode("\n", $markdown);
    }

    public function __toString()
    {
        return $this->markdown();
    }


}

/**
 * Class MarkCode
 * 代码
 */
class MarkCode implements export
{
    protected $code;

    /**
     * MarkCode constructor.
     * @param $code
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    function markdown(): string
    {
        $markdown = '```';
        $markdown .= json_encode($this->code,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        $markdown .= '```';
        return $markdown;
    }

    public function __toString()
    {
        return $this->markdown();
    }


}


/**
 * Class MarkParam
 * @property string $url
 * @property array method
 * @property array $formData
 */
class MarkParam implements export
{
    protected $url; //请求的url
    protected $method; //请求的方法
    protected $formData; //请求的内容
    protected $response;

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response
     */
    public function setResponse($response): void
    {
        $this->response = $response;
    }//返回值


    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return array
     */
    public function getMethod(): array
    {
        return $this->method;
    }

    /**
     * @param array $method
     */
    public function setMethod(array $method): void
    {
        $this->method = $method;
    }

    /**
     * @return array
     */
    public function getFormData(): array
    {
        return $this->formData;
    }

    /**
     * @param array $formData
     */
    public function setFormData(array $formData): void
    {
        $this->formData = $formData;
    }

    function markdown(): string
    {
        $markdown = [];
        $markdown[] = (new Mark('请求url', new MarkList([$this->url])));
        $markdown[] = (new Mark('请求方法', new MarkList($this->method)));
        $markdown[] = (new Mark('参数', new MarkForm(['参数名','必选','类型','说明'],$this->formData)));
        $markdown[] = (new Mark('返回参数', new MarkForm(['参数名','类型','说明'],$this->response)));
        $markdown[] = (new Mark('返回实例', new MarkCode($this->response)));
        return implode("\n\n",$markdown);
    }

    public function __toString()
    {
        return $this->markdown();
    }

}




