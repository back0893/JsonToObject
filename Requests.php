<?php


class Requests
{
    protected $method;
    protected $url;
    protected $param;
    protected $cookie;
    protected $response_body;
    protected $response_http_code;

    /**
     * Requests constructor.
     * @param $method
     * @param $url
     * @param $param
     * @param $file
     * @param $cookie
     */
    public function __construct($url, $method='get', $param=[], $cookie=[])
    {
        $this->method = $method;
        $this->url = $url;
        $this->param = $param;
        $this->cookie = $cookie;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url): void
    {
        $this->url = $url;
    }

    /**
     * @return array
     */
    public function getParam(): array
    {
        return $this->param;
    }

    /**
     * @param array $param
     */
    public function setParam(array $param): void
    {
        $this->param = $param;
    }

    /**
     * @return array
     */
    public function getCookie(): array
    {
        return $this->cookie;
    }

    /**
     * @param array $cookie
     */
    public function setCookie(array $cookie): void
    {
        $this->cookie = $cookie;
    }

    public function request($timeout=5){
        $ch=curl_init();
        $opt=[
            CURLOPT_URL=>$this->url,
            CURLOPT_RETURNTRANSFER=>1,
            CURLOPT_TIMEOUT=>$timeout,
            CURLOPT_HEADER=>0,
            CURLOPT_REFERER=>'',
            CURLOPT_POST=>$this->method=='get'?0:1,
            CURLOPT_CUSTOMREQUEST=>strtoupper($this->method),
            CURLOPT_POSTFIELDS=>http_build_query($this->param),
        ];
        curl_setopt_array($ch,$opt);
        if(count($this->cookie)){
            $cookies=[];
            foreach ($this->cookie as $key=>$value){
                $cookies[]=sprintf("%s=%s",$key,$value);
            }
            curl_setopt($ch,CURLOPT_COOKIE,implode($cookies,';'));
        }
        $this->response_body=curl_exec($ch);
        $this->response_http_code=curl_getinfo($ch,CURLINFO_HTTP_CODE);
        if($this->response_http_code>=500){
            throw new Exception("请求出现".$this->response_http_code.'错误!');
        }
        curl_close($ch);
        return $this->response_body;
    }

    /**
     * @return string
     */
    public function getResponseBody():string
    {
        return $this->response_body;
    }

    /**
     * @return mixed
     */
    public function getResponseHttpCode()
    {
        return $this->response_http_code;
    }

}