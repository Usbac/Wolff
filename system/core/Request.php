<?php

namespace Core;

class Request
{

    /**
     * List of parameters
     *
     * @var array
     */
    private $params;

    /**
     * List of body parameters
     *
     * @var array
     */
    private $body;

    /**
     * List of files
     *
     * @var array
     */
    private $files;

    /**
     * Current server superglobal
     *
     * @var array
     */
    private $server;


    /**
     * Default constructor
     *
     * @param  array  $params  the url parameters
     * @param  array  $body  the body parameters
     * @param  array  $files  the files
     * @param  array  $server  the superglobal server
     */
    public function __construct(array $params,
                                array $body,
                                array $files,
                                array $server)
    {
        $this->params = $_GET;
        $this->body = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;
    }


    /**
     * Returns the specified parameter.
     * The key parameter accepts dot notation
     *
     * @param  string  $key  the parameter key
     *
     * @return mixed The specified parameter.
     */
    public function param(string $key)
    {
        return val($this->params, $key);
    }


    /**
     * Returns true if the specified parameter is set,
     * false otherwise.
     *
     * @param  string  $key  the parameter key
     *
     * @return bool True if the specified parameter is set,
     * false otherwise.
     */
    public function hasParam(string $key)
    {
        return val($this->params, $key) !== null;
    }


    /**
     * Returns the specified body parameter.
     * The key parameter accepts dot notation
     *
     * @param  string  $key  the body parameter key
     *
     * @return mixed The specified body parameter.
     */
    public function body(string $key)
    {
        return val($this->body, $key);
    }


    /**
     * Returns true if the specified body parameter is set,
     * false otherwise.
     *
     * @param  string  $key  the parameter key
     *
     * @return bool True if the specified body parameter is set,
     * false otherwise.
     */
    public function has(string $key)
    {
        return val($this->body, $key) !== null;
    }


    /**
     * Returns the specified file.
     *
     * @param  string  $key  the file key
     *
     * @return mixed The specified file.
     */
    public function file(string $key)
    {
        return $this->files[$key];
    }


    /**
     * Returns true if the specified file is set,
     * false otherwise.
     *
     * @param  string  $key  the parameter key
     *
     * @return bool True if the specified file is set,
     * false otherwise.
     */
    public function hasFile(string $key)
    {
        return array_key_exists($key, $this->files);
    }


    /**
     * Returns the request method
     *
     * @return string The request method
     */
    public function getMethod()
    {
        return $this->server['REQUEST_METHOD'];
    }


    /**
     * Returns the request uri
     *
     * @return string The request uri
     */
    public function getUrl()
    {
        return $this->server['REQUEST_URI'];
    }

}
