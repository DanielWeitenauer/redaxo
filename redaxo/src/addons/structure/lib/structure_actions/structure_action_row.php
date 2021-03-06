<?php
/**
 * This class represents a structure action row. Usually only one needed
 * is needed for every structure table.
 * A Structure action row can contain multiple structure action columns.
 */
class rex_structure_action_row
{
    /**
     * Traits
     */
    use rex_factory_trait;
    use rex_structure_trait_vars;
    /**
     * @var array
     */
    protected $columns = [];

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
     * @param array $columns
     *
     * @return $this
     */
    public function setColumns(array $columns = [])
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param string $column_name
     * @param rex_structure_action_column $column
     *
     * @return $this
     */
    public function setColumn($column_name, rex_structure_action_column $column)
    {
        $this->columns[$column_name] = $column;

        return $this;
    }

    /**
     * @param string $column_name
     *
     * @return $this
     */
    public function unsetColumn($column_name)
    {
        unset($this->columns[$column_name]);

        return $this;
    }

    /**
     * @param $column_name
     *
     * @return rex_structure_action_column|null
     */
    public function getColumn($column_name)
    {
        return isset($this->columns[$column_name]) ? $this->columns[$column_name] : null;
    }

    /**
     * @param $column_name
     *
     * @return bool
     */
    public function hasColumn($column_name)
    {
        return isset($this->columns[$column_name]);
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
        $fragment->setVar('columns', $this->getColumns());

        return $fragment->parse($filename);
    }
}
