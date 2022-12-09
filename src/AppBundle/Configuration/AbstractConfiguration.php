<?php

namespace AppBundle\Configuration;

/**
 * Class AbstractConfiguration
 * @package AppBundle\Configuration
 */
abstract class AbstractConfiguration implements ConfigurationInterface
{

    /**
     * @var ConfigurationParameterInterface[]
     */
    private $map = [];

    /**
     * Array of changed parameters name.
     *
     * @var string[]
     */
    private $changed = [];

    /**
     * Array of removed parameters name.
     *
     * @var string[]
     */
    private $removed = [];

    /**
     * @var ConfigurationDefinitionMap
     */
    protected $definitions;

    /**
     * AbstractConfiguration constructor.
     *
     * @param ConfigurationDefinitionMap $definitions A ConfigurationDefinitionMap
     *                                                instance.
     */
    public function __construct(ConfigurationDefinitionMap $definitions)
    {
        $this->syncParameters();
        $this->definitions = $definitions;
    }

    /**
     * Get parameter value by name.
     *
     * @param string $name    Parameter name.
     * @param mixed  $default Default value if parameter not found.
     *
     * @return mixed
     */
    public function getParameter($name, $default = null)
    {
        if (! isset($this->map[$name]) || isset($this->removed[$name])) {
            return $default;
        }

        $param = $this->map[$name];
        if ($param === null) {
            return $default;
        }

        $value = $param->getValue();
        settype($value, $this->definitions->getDefinition($name)['type']);

        return $value;
    }

    /**
     * Sync current parameters with database.
     *
     * @return void
     */
    public function syncParameters()
    {
        $params = $this->loadData();

        foreach ($params as $param) {
            $this->map[$param->getName()] = $param;
        }
    }

    /**
     * Get all available parameters.
     *
     * @return ConfigurationParameterInterface[]
     */
    public function getParameters()
    {
        return $this->map;
    }

    /**
     * Get parameter value by name.
     *
     * @param string $name  Parameter name.
     * @param mixed  $value New parameter value.
     *
     * @return void
     */
    public function setParameter($name, $value)
    {
        $this->map[$name]->setValue($this->definitions->normalize($name, $value));
        $this->changed[$name] = true;
    }

    /**
     * Set parameters.
     *
     * @param array $params Array where key is parameter name and value is new
     *                      value.
     *
     * @return void
     */
    public function setParameters(array $params)
    {
        foreach ($params as $name => $newValue) {
            $this->setParameter($name, $newValue);
        }
    }

    /**
     * Sync configuration with storage.
     *
     * @return void
     */
    public function sync()
    {
        $changed = \nspl\a\filter(function (ConfigurationParameterInterface $parameter) {
            return isset($this->changed[$parameter->getName()]);
        }, $this->map);

        $removed = \nspl\a\filter(function (ConfigurationParameterInterface $parameter) {
            return isset($this->removed[$parameter->getName()]);
        }, $this->map);

        if ((count($changed) === 0) && (count($removed) === 0)) {
            return;
        }

        $this->doSync($changed, $removed);

        $this->map = \nspl\a\filter(function (ConfigurationParameterInterface $parameter) {
            return ! isset($this->removed[$parameter->getName()]);
        }, $this->map);

        $this->changed = [];
        $this->removed = [];
    }

    /**
     * Sync parameters with list of available.
     *
     * @return void
     */
    public function syncWithDefinitions()
    {
        $notExists = array_flip(ParametersName::getAvailables());

        /** @var ConfigurationParameterMutableInterface $parameter */
        foreach ($this->map as $name => $parameter) {
            if (! ParametersName::isExists($name)) {
                $this->removed[$name] = $parameter;
            } else {
                $definition = $this->definitions->getDefinition($name);
                $parameter
                    ->setTitle($definition['title'])
                    ->setSection($definition['section']);
                $this->changed[$name] = $parameter;
                unset($notExists[$name]);
            }
        }

        $notExists = array_keys($notExists);
        foreach ($notExists as $name) {
            $this->map[$name] = $this->createParameter($name);
            $this->changed[$name] = true;
        }

        $this->sync();
    }

    /**
     * Create default parameter from config.
     *
     * @param string $name Parameter name.
     *
     * @return ConfigurationParameterInterface
     */
    abstract protected function createParameter($name);

    /**
     * Load configuration from storage.
     *
     * @return ConfigurationParameterInterface[]
     */
    abstract protected function loadData();

    /**
     * @param ConfigurationParameterInterface[]|array $changed Array of changed
     *                                                         instances.
     * @param ConfigurationParameterInterface[]|array $removed Array of removed
     *                                                         parameter names.
     *
     * @return void
     */
    abstract protected function doSync(array $changed, array $removed);
}
