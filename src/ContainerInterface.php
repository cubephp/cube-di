<?php

namespace Cube\Di;

interface ContainerInterface
{
	public function get(string $id);
}