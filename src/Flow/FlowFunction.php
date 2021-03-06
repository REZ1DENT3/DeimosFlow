<?php

namespace Deimos\Flow;

abstract class FlowFunction
{

    const REGEXP_CALLBACK = '[\w"\[\]\(\)\$-\>]+';
    const REGEXP_VARIABLE = '\$[\w"\'\[\]\(\)\$-\>]+';

    /**
     * @var array
     */
    public $data;

    /**
     * @var Flow
     */
    public $flow;

    /**
     * @var Configure
     */
    public $configure;

    /**
     * FlowFunction constructor.
     *
     * @param Flow      $flow
     * @param Configure $configure
     * @param array     $data
     */
    public function __construct(Flow $flow, Configure $configure, array $data)
    {
        $this->flow      = $flow;
        $this->configure = $configure;
        $this->data      = $data;
    }

    /**
     * @param $variable
     *
     * @return string
     */
    protected function variable($variable)
    {
        $variable = preg_replace('~(\$)(.*)\.([\w-_]+)~', '$1$2[\'$3\']', $variable);

        return trim($variable, '"\' ');
    }

    /**
     * @return int
     */
    public function random()
    {
        return $this->flow->random();
    }

    abstract public function view();

}