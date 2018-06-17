<?php
/**
 * This class represents a single structure action column.
 * Structure action columns can contain multiple structure action fields
 * and an optional header.
 * Multiple structure action columns can be added to a structure action row.
 *
 * @see rex_structure_action_field
 * @see rex_structure_action_row
 * @package redaxo\structure
 */
class rex_structure_group
{
    /**
     * Traits
     */
    use rex_factory_trait;
    use rex_structure_trait_vars;

    /**
     * @var array
     */
    protected $header = [];
    /**
     * @var array
     */
    protected $body = [];

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
     * @param array $fields
     *
     * @return $this
     */
    public function setHeader(array $fields = [])
    {
        $this->header = $fields;

        return $this;
    }

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function setBody(array $fields = [])
    {
        $this->body = $fields;

        return $this;
    }

    /**
     * @return $this
     */
    public function clearHeader()
    {
        $this->header = [];

        return $this;
    }

    /**
     * @return $this
     */
    public function clearBody()
    {
        $this->body = [];

        return $this;
    }

    /**
     * @return array
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return array
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $key
     * @param rex_structure_field $field
     *
     * @return $this
     */
    public function setHeaderField($key, rex_structure_field $field)
    {
        $this->header[$key] = $field;

        return $this;
    }

    /**
     * @param string $key
     * @param rex_structure_field $field
     *
     * @return $this
     */
    public function setBodyField($key, rex_structure_field $field)
    {
        $this->body[$key] = $field;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function unsetHeaderField($key)
    {
        unset($this->header[$key]);

        return $this;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function unsetBodyField($key)
    {
        unset($this->body[$key]);

        return $this;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasHeaderField($key)
    {
        return isset($this->header[$key]);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasBodyField($key)
    {
        return isset($this->body[$key]);
    }

    /**
     * @param string $key
     *
     * @return rex_structure_action_field|null
     */
    public function getHeaderField($key)
    {
        return $this->hasHeaderField($key) ? $this->header[$key] : null;
    }

    /**
     * @param string $key
     *
     * @return rex_structure_action_field|null
     */
    public function getBodyField($key)
    {
        return $this->hasBodyField($key) ? $this->body[$key] : null;
    }
}
