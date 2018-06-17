<?php
/**
 * This class represents a structure block.
 * A Structure block contains one or multiple structure groups.
 */
class rex_structure_block
{
    /**
     * Traits
     */
    use rex_factory_trait;
    use rex_structure_trait_vars;

    /**
     * @var array
     */
    protected $groups = [];

    /**
     * @param array $vars
     *
     * @return static
     */
    public static function factory($vars = [])
    {
        $class = static::getFactoryClass();

        return new $class($vars);
    }

    /**
     * @param array $vars
     */
    protected function __construct($vars = [])
    {
        $this->setVars($vars);
    }

    /**
     * @param array $groups
     *
     * @return $this
     */
    public function setGroups(array $groups = [])
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param string $key
     * @param rex_structure_group $group
     *
     * @return $this
     */
    public function setGroup($key, rex_structure_group $group)
    {
        $this->groups[$key] = $group;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function unsetGroup($key)
    {
        unset($this->groups[$key]);

        return $this;
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function hasGroup($key)
    {
        return isset($this->groups[$key]);
    }

    /**
     * @param $key
     *
     * @return rex_structure_group|null
     */
    public function getGroup($key)
    {
        return $this->hasGroup($key) ? $this->groups[$key] : null;
    }

    /**
     * @param string $filename
     *
     * @return string
     * @throws rex_exception
     */
    public function getFragment($filename)
    {
        $fragment = new rex_fragment($this->getVars());
        $fragment->setVar('columns', $this->getGroups());

        return $fragment->parse($filename);
    }
}
