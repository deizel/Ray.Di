<?php
/**
 * This file is part of the Ray package.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace Ray\Di;

use Ray\Di\Exception\Unbound;

final class SetterMethod
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var Parameters
     */
    private $parameters;

    /**
     * Is optional binding ?
     *
     * @var bool
     */
    private $isOptional = false;

    /**
     * @param \ReflectionMethod $method
     * @param Name              $name
     */
    public function __construct(\ReflectionMethod $method, Name $name)
    {
        $this->method = $method->name;
        $this->parameters = new Parameters($method, $name);
    }

    public function setOptional()
    {
        $this->isOptional = true;
    }

    /**
     * @param object    $instance
     * @param Container $container
     *
     * @throws Unbound
     * @throws \Exception
     */
    public function __invoke($instance, Container $container)
    {
        try {
            $parameters = $this->parameters->get($container);
        } catch (Unbound $e) {
            if ($this->isOptional) {
                return;
            }
            throw $e;
        }
        call_user_func_array([$instance, $this->method], $parameters);
    }
}