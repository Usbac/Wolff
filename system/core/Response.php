<?php

namespace Core;

class Response
{

    /**
     * The HTTP status code.
     *
     * @var int
     */
    private $status_code;

    /**
     * The header location.
     *
     * @var string
     */
    private $url;

    /**
     * The header tags list.
     *
     * @var array
     */
    private $headers;

    const DEFAULT_STATUS = 200;


    public function __construct()
    {
        $this->status_code = self::DEFAULT_STATUS;
        $this->headers = [];
        $this->url = '';
    }


    /**
     * Return the HTTP headers
     *
     * @return array the HTTP headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }


    /**
     * Return the HTTP status code
     *
     * @return int the HTTP status code
     */
    public function getStatusCode()
    {
        return $this->status_code;
    }


    /**
     * Return the header location
     *
     * @return string the header location
     */
    public function getRedirect()
    {
        return $this->url;
    }


    /**
     * Add a new header
     *
     * @param  string  $key  the header key
     * @param  string  $value  the header value
     *
     * @return Response this
     */
    public function add(string $key, string $value)
    {
        if(!key_exists($key, $this->headers)) {
            $this->set($key, $value);
        }

        return $this;
    }


    /**
     * Set the value of an existent or new header
     *
     * @param  string  $key  the header key
     * @param  string  $value  the header value
     *
     * @return Response this
     */
    public function set(string $key, string $value)
    {
        $key = trim($key);
        $this->headers[$key] = $value;

        return $this;
    }


    /**
     * Unset an existent header
     *
     * @param  string  $key  the header key
     *
     * @return Response this
     */
    public function remove(string $key)
    {
        if(key_exists($key, $this->headers)) {
            unset($this->headers[$key]);
        }

        return $this;
    }


    /**
     * Set the HTTP status code
     *
     * @param  int  $status  the HTTP status code
     *
     * @return Response this
     */
    public function setStatusCode(int $status = self::DEFAULT_STATUS)
    {
        $this->status_code = $status;

        return $this;
    }


    /**
     * Set the header location and HTTP status code
     *
     * @param  string  $url  the header location
     * @param  int  $status  the HTTP status code
     *
     * @return Response this
     */
    public function redirect(string $url, int $status = self::DEFAULT_STATUS)
    {
        $this->setStatusCode($status);
        $this->url = $url;

        return $this;
    }


    /**
     * Execute the response with the available values
     */
    public function go()
    {
        foreach($this->headers as $key => $header) {
            header("$key: $header");
        }

        redirect($this->url, $this->status_code);
    }
}
