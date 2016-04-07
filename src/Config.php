<?php

namespace Cube\Di;

class Config implements ConfigInterface
{
    protected $definitions;
    protected $parameters;

    public function __construct($definitions, $parameters)
    {
        $this->setDefinitions($definitions);
        $this->setParameters($parameters);
    }

    /**
     *
     */
    public function getDefinition(string $id):array
    {
        if ($this->hasDefinition($id)){
            return $this->definitions[$id];
        }
    }

    public function setDefinition(string $id, array $definition)
    {
        $this->definitions[$id] = $definition;
        return $this;
    }

    public function getDefinitions()
    {
        return $this->definitions;
    }

    public function setDefinitions(array $definitions)
    {
        foreach ($definitions as $id => $definition) {
            $this->setDefinition($id, $definition);
        }

        return $this;
    }

    /**
     *
     */
    public function hasDefinition(string $id):bool
    {
        return isset($this->definitions[$id]);
    }

    /**
     *
     */
    public function getParameter(string $key)
    {
        $current = $this->parameters;
        $p = strtok($key, '.');

        while ($p !== false) {
            $current = $current[$p] ?? null;
            $p = strtok('.');
        }

        return $current;
    }

    /**
     *
     */
    public function hasParameter(string $key):bool
    {
        return is_null($this->getParameter($key)) ? false : true;
    }

    /**
     * Sets a parameter
     *
     * @param string $name the parameter name
     * @param mixed $value the parameter value
     *
     * @return self
     */
    public function setParameter(string $name, $value)
    {
        $this->parameters[$name] = $value;
        return $this;
    }

    /**
     * sets parameters recursively
     *
     * @param array $parameters an array of parameters
     *
     * @return self
     */
    public function setParameters(array $parameters)
    {
        foreach ($parameters as $name => $value) {
            $this->setParameter($name, $value);
        }

        return $this;
    }
}
