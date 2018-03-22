<?php

namespace TCG\Component\Kernel;


use TCG\Component\Util\StringUtil;

class ContainerBuilder
{

    protected $kernelNS;

    public function __construct($kernelNS)
    {
        $this->kernelNS = $kernelNS;
    }

    /**
     * @param Config|null $configuration
     * @return string
     */
    public function dumpServices(Config $configuration = null)
    {
        if (!$configuration) {
            $configuration = new Config(array());
        }
        $content = array();
        foreach ($configuration['services'] as $service_key => $service_configuration) {

            $body = '';
            $class_name = $service_configuration->class;
            $class_name = $this->resolveString($class_name, $configuration->toArray());
            $not_share = (bool) $service_configuration->not_share;
            // 创建对象
            if (!$service_configuration->factory) {
                // 非工厂类
                if (!$not_share) {
                    $service_getter_prepend = <<<PREPEND
/**
 * @return \\{$service_configuration->class}
 * @throws
 */
public function get{$this->camelize($service_key)}Service()
{
if (!isset(\$this->services['$service_key'])) {

PREPEND;
                } else {
                    $service_getter_prepend = <<<PREPEND
/**
 * @return \\{$service_configuration->class}
 * @throws
 */
public function get{$this->camelize($service_key)}Service()
{

PREPEND;
                }
                $instance_create_expr = '$instance = new ' . $class_name . "(\n";
                $arguments = $service_configuration->arguments ? $service_configuration->arguments->toArray() : array();
                $args = $this->resolveArguments($arguments, $configuration->toArray());
                $args = new Config($args);
                $params = array();
                foreach ($args as $key => $val) {
                    $params[$key] = (string) $val;
                }
                $instance_create_expr .= implode(", \n", $params) . "\n);\n";
                $body .= $instance_create_expr;
                // interface
                if ($service_configuration->interfaces) {
                    $interfaces = $service_configuration->interfaces->toArray();
                    $interfaces = $this->resolveValue($interfaces, $configuration->toArray());
                    foreach ($interfaces as $interface) {
                        $interface_check = <<<PREPEND
if (!\$instance instanceof $interface) {
throw new \Exception('"$service_key" of "$class_name" instance must implement interface "$interface"');
}

PREPEND;
                        $body .= $interface_check;
                    }
                }
                // setter call
                if ($service_configuration->calls) {
                    foreach ($service_configuration->calls->toArray() as $setter) {
                        $setter_method = $setter[0];
                        if (!isset($setter[1])) {
                            $setter[1] = array();
                        }
                        $setter_arguments = $this->resolveArguments($setter[1], $configuration->toArray());
                        $setter_arguments = new Config($setter_arguments);
                        $params = array();
                        foreach ($setter_arguments as $key => $val) {
                            $params[$key] = (string) $val;
                        }
                        $setter_call_expr = '$instance->' . $setter_method . '(' . implode(', ', $params) . ");\n";
                        $body .= $setter_call_expr;
                    }
                }
                if (!$not_share) {
                    $service_getter_append = <<<APPEND
\$this->services['$service_key'] = \$instance;
}
return \$this->services['$service_key'];
}
APPEND;
                } else {
                    $service_getter_append = <<<APPEND
return \$instance;
}
APPEND;
                }
                $service_getter = $service_getter_prepend . $body . $service_getter_append;
            } else {
                // 工厂生产
                $service_getter_prepend = <<<PREPEND
/**
 * @return \\{$service_configuration->class}
 * @throws
 */
public function get{$this->camelize($service_key)}Service()
{

PREPEND;
                $factory = $service_configuration->factory->toArray();
                $callable = array($factory[0], $factory[1]);
                if (!isset($factory[2])) {
                    $factory[2] = array();
                }
                $arguments = $factory[2];

                $callable = $this->resolveArguments($callable, $configuration->toArray());
                $arguments = $this->resolveArguments($arguments, $configuration->toArray());

                $arguments = 'array(' . implode(', ', $arguments) . ')';
                $callable = 'array(' . implode(', ', $callable) . ')';

                if (!$not_share) {
                    $body = <<<FACTORY
if (!isset(\$this->services['$service_key'])) {
\$this->services['$service_key'] = call_user_func_array($callable, $arguments);
}

FACTORY;
                    // interface
                    if ($service_configuration->interfaces) {
                        $interfaces = $service_configuration->interfaces->toArray();
                        $interfaces = $this->resolveValue($interfaces, $configuration->toArray());
                        foreach ($interfaces as $interface) {
                            $interface_check = <<<PREPEND
if (!\$this->services['$service_key'] instanceof $interface) {
throw new \Exception('"$service_key" of "$class_name" instance must implement interface "$interface"');
}

PREPEND;
                            $body .= $interface_check;
                        }
                    }
                    // setter call
                    if ($service_configuration->calls) {
                        foreach ($service_configuration->calls->toArray() as $setter) {
                            $setter_method = $setter[0];
                            if (!isset($setter[1])) {
                                $setter[1] = array();
                            }
                            $setter_arguments = $this->resolveArguments($setter[1], $configuration->toArray());
                            $setter_arguments = new Config($setter_arguments);
                            $params = array();
                            foreach ($setter_arguments as $key => $val) {
                                $params[$key] = (string) $val;
                            }
                            $setter_call_expr = "\$this->services['$service_key']->" . $setter_method . '(' . implode(', ', $params) . ");\n";
                            $body .= $setter_call_expr;
                        }
                    }
                    $body .= <<<FACTORY
return \$this->services['$service_key'];
}
FACTORY;
                } else {
                    $body = <<<FACTORY
\$instance = call_user_func_array($callable, $arguments);
FACTORY;
                    // interface
                    if ($service_configuration->interfaces) {
                        $interfaces = $service_configuration->interfaces->toArray();
                        $interfaces = $this->resolveValue($interfaces, $configuration->toArray());
                        foreach ($interfaces as $interface) {
                            $interface_check = <<<PREPEND
if (!\$instance instanceof $interface) {
throw new \Exception('"$service_key" of "$class_name" instance must implement interface "$interface"');
}

PREPEND;
                            $body .= $interface_check;
                        }
                    }
                    // setter call
                    if ($service_configuration->calls) {
                        foreach ($service_configuration->calls->toArray() as $setter) {
                            $setter_method = $setter[0];
                            if (!isset($setter[1])) {
                                $setter[1] = array();
                            }
                            $setter_arguments = $this->resolveArguments($setter[1], $configuration->toArray());
                            $setter_arguments = new Config($setter_arguments);
                            $params = array();
                            foreach ($setter_arguments as $key => $val) {
                                $params[$key] = (string) $val;
                            }
                            $setter_call_expr = "\$instance->" . $setter_method . '(' . implode(', ', $params) . ");\n";
                            $body .= $setter_call_expr;
                        }
                    }
                    $body .= <<<FACTORY
return \$instance;
}
FACTORY;
                }

                $service_getter = $service_getter_prepend . $body;
            }
            $content[] = $service_getter;
        }
        $body = implode("\n", $content);
        return $body;
    }

    /**
     * @param Config|null $configuration
     * @return string
     */
    public function dumpTags(Config $configuration = null)
    {
        if (!$configuration) {
            $configuration = new Config(array());
        }
        $content = array();
        $tags = array();
        foreach ($configuration['services'] as $service_key => $service_configuration) {
            if ($service_configuration->tags) {
                foreach (array_unique($service_configuration->tags->toArray()) as $tag_name) {
                    /*if (!isset($tags[$tag_name])) {
                        $tags[$tag_name] = array();
                    }
                    $tags[$tag_name][] = array('service' => $service_key, 'class' => $service_configuration->class);*/
                    $tags[$tag_name] = array(
                        array('service' => $service_key, 'class' => $service_configuration->class)
                    );
                }
            }
        }
        foreach ($tags as $tag_name => $services) {
            foreach ($services as $i => $service) {
                $service_key = $service['service'];
                $service_class = $service['class'];
                // tag
                $suffix = '';
                if ($i > 0) {
                    $suffix = '__' . $this->camelize($service_key);
                }
                $tagged_getter = <<<TAG
/**
 * @return \\{$service_class}
 */
public function get{$this->camelize($tag_name)}Tag{$suffix}()
{
return \$this->getService('$service_key');
}
TAG;
                $content[] = $tagged_getter;

            }
        }
        $body = implode("\n", $content);
        return $body;
    }

    /**
     * @param Config|null $configuration
     * @return string
     */
    public function dumpEvents(Config $configuration = null)
    {
        if (!$configuration) {
            $configuration = new Config(array());
        }
        $content = [];

        foreach ($configuration['events'] as $event_name => $event_configuration) {
            $event_key = $this->camelize($event_name);
            $event_class = $this->resolveString($event_configuration->class, $configuration->toArray());
            $event_dispatcher_prepend = <<<PREPEND
/**
 * @return \\{$event_class}
 */
public function dispatch{$event_key}Event(\\{$event_class} \$event)
{
PREPEND;
            $body = <<<BODY

\$dispatcher = new Symfony\Component\EventDispatcher\EventDispatcher();
BODY;
            foreach ($event_configuration->handlers as $handler) {
                $handler = $handler->toArray();
                $handler = $this->resolveArguments($handler, $configuration->toArray());
                $handler = new Config($handler);
                $body .= <<<BODY

\$dispatcher->addListener('$event_name', [$handler[0], $handler[1]]);
BODY;

            }
            $event_dispatcher_append = <<<APPEND

\$dispatcher->dispatch('$event_name', \$event);
return \$event;
}
APPEND;
            $content[] = $event_dispatcher_prepend . $body . $event_dispatcher_append;
        }
        $body = implode("\n", $content);
        return $body;
    }

    /**
     * @param Config|null $configuration
     */
    public function dump(Config $configuration = null)
    {
        $id = $this->kernelNS;
        if (!file_exists(CACHE_ROOT . '/' . $id)) {
            mkdir(CACHE_ROOT . '/' . $id, 0777, true);
        }
        $container_prepend = <<<PREPEND
<?php
namespace {
class {$this->kernelNS}ServiceContainer extends \TCG\Component\Kernel\Container
{
public function getKernel()
{
return \TCG\Component\Kernel\AppKernel::getInstance('$this->kernelNS');
}

PREPEND;
        $container_append = <<<APPEND
}
}
APPEND;
        $service_body = $this->dumpServices($configuration);
        $tags_body = $this->dumpTags($configuration);
        $events_body = $this->dumpEvents($configuration);
        $body = $service_body . "\n" . $tags_body . "\n" . $events_body;
        $container_content = $container_prepend . $body . $container_append;
        $service_container_filename = CACHE_ROOT . '/' . $id . '/' . $id . 'ServiceContainer.php';


        $tmp_file = tempnam(CACHE_ROOT . '/' . $id, basename($service_container_filename));
        if (false !== @file_put_contents($tmp_file, $container_content) && @rename($tmp_file, $service_container_filename)) {
            @chmod($service_container_filename, 0666 & ~umask());
        }

        unset($configuration['services'], $configuration['events']);
        $configuration_prepend = <<<PREPEND
<?php
return new \TCG\Component\Kernel\Config(array(
PREPEND;
        $configuration_append = <<<APPEND

), true);
APPEND;
        $configuration_body = $this->dumpConfig($configuration, 1);
        $configuration_content = $configuration_prepend . $configuration_body . $configuration_append;
        $configuration_filename = CACHE_ROOT . '/' . $id . '/Config.php';
        $tmp_file = tempnam(CACHE_ROOT . '/' . $id, basename($configuration_filename));
        if (false !== @file_put_contents($tmp_file, $configuration_content) && @rename($tmp_file, $configuration_filename)) {
            @chmod($service_container_filename, 0666 & ~umask());
        }
    }

    /**
     * @param Config|null $configuration
     * @param int $indent
     * @return string
     */
    public function dumpConfig(Config $configuration = null, $indent = 1)
    {
        if (!$configuration) {
            $configuration = new Config(array());
        }
        $content = '';
        $indent_content = str_repeat("  ", $indent * 2);
        if (ENV == 'online') {
            $indent_content = '';
        }
        foreach ($configuration as $key => $value) {
            if ($value instanceof Config) {
                if (is_string($key)) {
                    $key_raw = "'{$key}'";
                } else {
                    $key_raw = "{$key}";
                }
                $content .= "\n{$indent_content}{$key_raw}=>array(";
                $content .= $this->dumpConfig($value, $indent + 1);
                $content .= "\n{$indent_content}),";
            } else {
                if (is_string($value)) {
                    $value_raw = "'{$value}'";
                } elseif (is_numeric($value)) {
                    $value_raw = "{$value}";
                } elseif (is_bool($value)) {
                    if ($value) {
                        $value_raw = "true";
                    } else {
                        $value_raw = 'false';
                    }
                } else {
                    $value_raw = "null";
                }
                if (is_string($key)) {
                    $key_raw = "'{$key}'";
                } else {
                    $key_raw = "{$key}";
                }
                $content .= "\n{$indent_content}{$key_raw}=>{$value_raw},";
            }
        }
        return $content;
    }

    /**
     * 处理参数部分
     * @param array $arguments
     * @param array $configuration
     * @return array
     */
    public function resolveArguments(array $arguments, array $configuration)
    {
        $args = array();
        foreach ($arguments as $key => $argument) {
            $arg = 'null';
            if (is_array($argument)) {
                $arg = $this->resolveArguments($argument, $configuration);
            } elseif (strpos($argument, '@') === 0) {
                // 引用其他服务对象
                $start = 1;
                if (strpos($argument, '?') === 1) {
                    // 可以为null
                    $start = 2;
                }
                $sub_service_key = substr($argument, $start, strlen($argument));
                if ($sub_service_key == 'service_container') {
                    $arg = '$this';
                } else {
                    if ($start === 1) {
                        $arg = '$this->getService(' . "'$sub_service_key'" . ')';
                    } elseif ($start === 2) {
                        $arg = <<<ARG
\$this->hasService('$sub_service_key') ? \$this->getService('$sub_service_key') : null
ARG;
                    }
                }

            } elseif (strpos($argument, '$') === 0) {
                // tag
                // 引用其他服务对象
                $start = 1;
                if (strpos($argument, '?') === 1) {
                    // 可以为null
                    $start = 2;
                }
                $sub_service_key = substr($argument, $start, strlen($argument));
                if ($start === 1) {
                    $arg = '$this->getTagService(' . "'$sub_service_key'" . ')';
                } elseif ($start === 2) {
                    $arg = <<<ARG
\$this->hasTagService('$sub_service_key') ? \$this->getTagService('$sub_service_key') : null
ARG;
                }
            } else {
                $arg = $this->resolveValue($argument, $configuration);
                if (is_string($arg)) {
                    $arg = "'$arg'";
                }
            }
            $args[$key] = $arg;
        }
        return $args;
    }

    /**
     * @param $value
     * @param array $configuration
     * @return array|mixed
     */
    public function resolveValue($value, array $configuration)
    {
        if (is_array($value)) {
            $args = array();
            foreach ($value as $k => $v) {
                $args[$this->resolveValue($k, $configuration)] = $this->resolveValue($v, $configuration);
            }
            return $args;
        }
        if (!is_string($value)) {
            return $value;
        }
        return $this->resolveString($value, $configuration);
    }

    /**
     * @param $value
     * @param array $configuration
     * @return array|mixed
     */
    public function resolveString($value, array $configuration)
    {
        // we do this to deal with non string values (Boolean, integer, ...)
        // as the preg_replace_callback throw an exception when trying
        // a non-string in a parameter value
        if (preg_match('/^%([^%\s]+)%$/', $value, $match)) {
            $key = strtolower($match[1]);
            $val = $configuration['parameters'][$key];
            $return = $this->resolveValue($val, $configuration);
            return $return;
        }
        $self = $this;
        return preg_replace_callback('/%%|%([^%\s]+)%/', function ($match) use ($self, $configuration, $value) {
            // skip %%
            if (!isset($match[1])) {
                return '%%';
            }
            $key = strtolower($match[1]);
            $resolved = $configuration['parameters'][$key];
            if (!is_string($resolved) && !is_numeric($resolved)) {
                throw new \RuntimeException(sprintf('A string value must be composed of strings and/or numbers, but found parameter "%s" of type %s inside string value "%s".', $key, gettype($resolved), $value));
            }
            $resolved = (string) $resolved;
            return $self->resolveString($resolved, $configuration);
        }, $value);
    }

    /**
     * @param $value
     * @return array|mixed
     */
    protected function escapeValue($value)
    {
        if (is_string($value)) {
            return str_replace('%', '%%', $value);
        }
        if (is_array($value)) {
            $result = array();
            foreach ($value as $k => $v) {
                $result[$k] = $this->escapeValue($v);
            }
            return $result;
        }
        return $value;
    }

    /**
     * @param $value
     * @return array|mixed
     */
    protected function unescapeValue($value)
    {
        if (is_string($value)) {
            return str_replace('%%', '%', $value);
        }
        if (is_array($value)) {
            $result = array();
            foreach ($value as $k => $v) {
                $result[$k] = $this->unescapeValue($v);
            }
            return $result;
        }
        return $value;
    }


    /**
     * Camelizes a string.
     *
     * @param string $id A string to camelize
     *
     * @return string The camelized string
     */
    public static function camelize($id)
    {
        return StringUtil::camelcase($id);
    }
    /**
     * A string to underscore.
     *
     * @param string $id The string to underscore
     *
     * @return string The underscored string
     */
    public static function underscore($id)
    {
        return StringUtil::underscore($id);
    }
}