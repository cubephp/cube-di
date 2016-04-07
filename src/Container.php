<?php

namespace Cube\Di;

class Container implements ContainerInterface
{
    /** @var ConfigInterface a ConfigInterface instance */
    protected $config;

    /** @var BuilderInterface a BuilderInterface instance */
    protected $builder;

    /** @var array an array of shared objects */
    protected $shared;

    public function __construct(ConfigInterface $config, BuilderInterface $builder)
    {
        $this->config = $config;
        $this->builder = $builder;
    }

    /**
     * returns a container instance with the provided definitions and parameters
     *
     * @param  array  $definitions an array of object definitions
     * @param  array  $parameters  an array of parameters
     *
     * @return ContainerInterface a ContainerInterface instance
     */
    public static function init(array $definitions, array $parameters)
    {
        return new Container(new Config($definitions, $parameters), new Builder);
    }

    /**
     * gets a definition from the config and builds it
     *
     * if the identifier is set in the shared array, that instance will be returned
     *
     * @param  string $id the definition identifier
     * @return object the built object
     */
    public function get(string $id)
    {
        if ($this->isShared($id)) {
            return $this->shared[$id];
        }

        return $this->builder->build($id, $this->config);
    }

    /**
     * share an object with the container
     *
     * @param  string $id       the object identifier
     * @param  object $instance the object to share
     *
     * @return self
     */
    public function share(string $id, $instance = null)
    {
        if ($instance !== null) {
            $this->shared[$id] = $instance;
        }

        $this->shared[$id] = $this->get($id);
        return $this;
    }

    /**
     * checks if an instance is shared
     *
     * @param  string  $id the shared object identifier
     *
     * @return boolean wether or not the shared instance exists
     */
    public function isShared(string $id)
    {
        return isset($this->shared[$id]);
    }
}
