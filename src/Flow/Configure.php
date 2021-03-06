<?php

namespace Deimos\Flow;

use Deimos\DI\DI;
use Deimos\Helper\Exceptions\NotFound;

class Configure
{

    use Traits\FileSystem;

    /**
     * @var string
     */
    protected $compile;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var array
     */
    protected $resources = [];

    /**
     * @var Tokenizer
     */
    protected $tokenizer;

    /**
     * @var DI
     */
    protected $block;

    /**
     * @var DefaultContainer
     */
    protected $di;

    /**
     * @var string
     */
    protected $ext = 'tpl';

    /**
     * @var array
     */
    protected $extends = [];

    /**
     * @var array
     */
    protected $extendsAll = [];

    /**
     * @var bool
     */
    protected $phpEnable = false;

    /**
     * @var bool
     */
    protected $isDebug = false;

    /**
     * @var array
     */
    protected $variable = [];

    /**
     * Configure constructor.
     */
    public function __construct()
    {
        $this->tokenizer = new Tokenizer();
    }

    /**
     * @param DefaultContainer $di
     *
     * @return DI
     */
    public function di(DefaultContainer $di = null)
    {
        if (!$this->di)
        {
            if (!$di)
            {
                $di = new DefaultContainer();
            }

            $this->di = $di;
        }

        return $this->di;
    }

    /**
     * @return Block
     */
    public function block()
    {
        if (!$this->block)
        {
            $this->block = new Block($this);
        }

        return $this->block;
    }

    /**
     * @return Tokenizer
     */
    public function tokenizer()
    {
        return $this->tokenizer;
    }

    /**
     * @return bool
     */
    public function isPhpEnable()
    {
        return $this->phpEnable;
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->isDebug;
    }

    /**
     * set php enable
     */
    public function phpEnable()
    {
        $this->phpEnable = true;
    }

    /**
     * set php enable
     */
    public function debugEnable()
    {
        $this->isDebug = true;
    }

    /**
     * @param string $path
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function compile($path = null)
    {
        if ($path)
        {
            $this->compile = $this->createDirectory($path);
        }

        if (!$this->compile)
        {
            throw new \InvalidArgumentException('Set compile directory!');
        }

        return $this->compile;
    }

    /**
     * @param $path
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function getFile($path)
    {
        $result   = explode(':', $path, 2);
        $template = $this->template();

        if (count($result) === 2)
        {
            $path     = $this->getResource($result[0]) . $result[1];
            $template = null;
        }

        $fullPath = $template . $path;

        if (file_exists($fullPath))
        {
            return file_get_contents($fullPath);
        }

        return file_get_contents($path);
    }

    /**
     * @param $path
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function requireFile($path)
    {
        $flow = new Flow($this);

        return $flow->render($path);
    }


    /**
     * @param string $view
     * @param string $path
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function extendsFile($view, $path)
    {
        if (!isset($this->extends[$view]))
        {
            $this->extends[$view] = [];
        }

        $this->extends[$view][]  = $path;
        $this->extendsAll[$view] = false;
        $this->extendsAll[$path] = true;

        return $this->extends[$view];
    }

    /**
     * @param $view
     *
     * @return bool
     */
    public function getExtendsAll($view)
    {
        if (isset($this->extendsAll[$view]))
        {
            return $this->extendsAll[$view];
        }

        return true;
    }

    /**
     * @param string $view
     * @param bool   $remove
     *
     * @return array
     */
    public function getExtendsFile($view, $remove = false)
    {
        if (isset($this->extends[$view]))
        {
            $data = $this->extends[$view];

            if ($remove)
            {
                unset($this->extends[$view]);
            }

            return $data;
        }

        return [];
    }

    /**
     * @param string $path
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function template($path = null)
    {
        if ($path)
        {
            $this->template = $this->createDirectory($path);
        }

        if (!$this->template)
        {
            throw new \InvalidArgumentException('Set template directory!');
        }

        return $this->template;
    }

    public function addResource($name, $path)
    {
        $this->resources[$name] = realpath($path) . '/';

        if (!realpath($path))
        {
            throw new NotFound('Path `' . $path . '` not found');
        }

        return $this;
    }

    public function getResource($name)
    {
        if (!isset($this->resources[$name]))
        {
            throw new NotFound('Resource not found');
        }

        return $this->resources[$name];
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function ext($value = null)
    {
        if ($value)
        {
            $this->ext = $value;
        }

        return '.' . $this->ext;
    }

    public function getVariables()
    {
        return $this->variable;
    }

    public function __get($name)
    {
        return $this->getVariable($name);
    }

    public function __isset($name)
    {
        return $this->issetVariable($name);
    }

    public function __set($name, $value)
    {
        $this->setVariable($name, $value);
    }

    public function getVariable($name)
    {
        return $this->variable[$name];
    }

    public function issetVariable($name)
    {
        return isset($this->variable[$name]);
    }

    public function setVariable($name, $value)
    {
        $this->variable[$name] = $value;
    }

}