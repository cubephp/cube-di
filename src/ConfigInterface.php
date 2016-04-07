<?php

namespace Cube\Di;

interface ConfigInterface
{
    /**
     * get a definition
     *
     * @param  string $id the definition identifier
     * @return array the definition
     */
    public function getDefinition(string $id):array;

    /**
     * checks if a definition exists
     *
     * @param  string  $id the definition identifier
     *
     * @return boolean wether or not the definition is set
     */
    public function hasDefinition(string $id):bool;

    /**
     * get a parameter
     *
     * implementations must provide array access using dot notation
     *
     * @param  string $key the parameter key to get
     *
     * @throws NotFoundException if the parameter does not exist
     *
     * @return mixed returns the parameter value
     */
    public function getParameter(string $key);

    /**
     * checks if the parameter exists using dot notation
     *
     * @param  string $key parameter key
     *
     * @return bool returns whether or not the parameter exists
     */
    public function hasParameter(string $key):bool;
}
