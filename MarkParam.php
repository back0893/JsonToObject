<?php
/**
 * @author 刘国君
 * @version 1.0
 *  ['参数名','必选','类型','说明'];
 */

require_once __DIR__.'/TestA.php';
interface export
{
    function markdown(): string;
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
        $markdown = sprintf("**%s:**\n", $this->title);
        $markdown .= $this->value->markdown();
        return $markdown;
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
        $this->value = $value;
        $this->filed=$filed;
    }

    function markdown(): string
    {
        $markdown = [];
        $markdown[] = implode('|', $this->filed);
        $markdown[] = implode('|', array_fill(0, count($this->filed), ':----'));
        $reflection=new ReflectionObject($this->value);
        $properties=$reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property){
            /**
             * 注释就是的php的注释 var type name 说明
             */
            $doc=explode(' ',trim(str_replace(['*','\\','/'],'',$property->getDocComment())));
            $doc=array_slice($doc,1);
            $markdown[]=implode('|',[$property->getName(),'是',getValueType($property->getValue($this->value)),$doc[2]]);
        }
        return implode("\n", $markdown);
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
        $markdown[] = (new Mark('请求方法', new MarkList([$this->url])));
        $markdown[] = (new Mark('参数', new MarkForm($this->formData)));
        $markdown[] = (new Mark('返回参数', new MarkForm($this->response)));
        $markdown[] = (new Mark('返回实例', new MarkCode($this->response)));
    }
}




