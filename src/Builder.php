<?php

namespace Cube\Di;

use \Cube\Di\Exception\{BuilderException, NotFoundException};

class Builder implements BuilderInterface
{
    /** @var string parameter reference identifier */
    protected $parameterIdentifier = '&';

    /** @var string definition reference identifier */
    protected $definitionIdentifier = '@';

	/**
     * builds a new object from a definition in the config
     *
     * @param  string $id     the definition identifier
     * @param  Config $config a Config instance
     *
     * @throws NotFoundException if the definition is not defined
     *
     * @return object The object built from the definition
     */
	public function build(string $id, ConfigInterface $config)
	{
        if (!$config->hasDefinition($id)) {
            throw new NotFoundException('The config does not contain the definition');
        }

        $instance = $this->newInstance($config->getDefinition($id), $config);

        if (!isset($definition['calls'])) {
            return $instance;
        } else {
            return $this->init($instance, $definition['calls'], $config);
        }
	}

    /**
     * creates a new instance from a definition and a Config instance
     *
     * constructor arguments will be resolved from the config instance
     *
     * @param  array  $definition the definition
     * @param  Config $config     a Config instance
     *
     * @throws NotFoundException if the definition is not defined
     *
     * @return object The object built from the definition
     */
	public function newInstance(array $definition, ConfigInterface $config)
	{
		if(!class_exists($definition['class'])) {
			throw new BuilderException('class does not exist');
		}

		if (!isset($definition['args'])) {
			return new $definition['class'];
		} else {
			return (new \ReflectionClass($definition['class']))->newInstanceArgs(
				$this->resolveArgs(
					$definition['args'],
                    $config
				)
			);
		}
	}

	/**
     * calls methods on an object
     *
     * method arguments will be resolved from the config instance
     *
     * @param  object          $instance an object
     * @param  array           $calls    an array of calls
     * @param  ConfigInterface $config   a ConfigInterface instance
     *
     * @throws  BuilderException if a method does not exist
     *
     * @return object the object given with the provided methods called
     */
	public function init($instance, $calls, ConfigInterface $config)
	{
		foreach ($definition['calls'] as $call) {
			if (isset($call['method'])) {
                if (!method_exists($instance, $call['method'])) {
                    throw new BuilderException(
                        $call['method'] .
                        'does not exist in ' .
                        get_class($instance)
                    );
                }

				if (isset($call['arguments'])) {
					call_user_func_array(
						[$instance, $call['method']],
						$this->resolveArgs($call['arguments'])
					);
				} else {
					call_user_func([$instance, $call['method']]);
				}
			}
		}

		return $instance;
	}

	/**
     * Resolves method arguments from the config
     *
     * @param array           $arguments an array of arguments to resolve
     * @param ConfigInterface $config    a ConfigInterface instance
     *
     * @return array an array of resolved arguments
     */
	public function resolveArgs(array $arguments, ConfigInterface $config):array
	{
		$resolved = [];
		foreach ($arguments as $value) {
			if (strpos($value, $this->definitionIdentifier) === 0) {
				$resolved[] = $this->build(substr($value, 1), $config);
			} elseif (strpos($value, $this->parameterIdentifier) === 0) {
				$resolved[] = $this->getParameter($value, $config);
			} else {
				$resolved[] = $value;
			}
		}

		return $resolved;
	}

    /**
     * Resolves a parameter reference from the config
     *
     * @param array           $key       the parameter key
     * @param ConfigInterface $config    a ConfigInterface instance
     *
     * @throws NotFoundException if the parameter does not exist
     *
     * @return mixed the parameter value
     */
    protected function getParameter(string $key, ConfigInterface $config)
    {
        if (!$config->hasParameter($key)) {
            throw new NotFoundException('Invalid parameter reference');
        }

        return $config->getParameter($key);
    }
}
