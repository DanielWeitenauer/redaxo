<?php
/**
 * @author Daniel Weitenauer
 * @copyright (c) 2017 studio ahoi
 */

class rex_structure_action_column
{
    /**
     * Traits
     */
    use rex_structure_trait_vars;
    /**
     * @var rex_structure_action_field
     */
    protected $head;
    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @param array $vars
     */
    public function __construct($vars = [])
    {
        $this->setVars($vars);
    }

    /**
     * @param rex_structure_action_field $head
     *
     * @return $this
     */
    public function setHead(rex_structure_action_field $head)
    {
        $this->head = $head;

        return $this;
    }

    /**
     * @return $this
     */
    public function unsetHead()
    {
        unset($this->head);

        return $this;
    }

    /**
     * @return rex_structure_action_field
     */
    public function getHead()
    {
        return $this->head;
    }

    /**
     * @return bool
     */
    public function hasHead()
    {
        return isset($this->head);
    }

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function setFields(array $fields = [])
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return bool
     */
    public function hasFields()
    {
        return !empty($this->fields);
    }

    /**
     * @param string $field_name
     * @param rex_structure_action_field $field
     *
     * @return $this
     */
    public function setField($field_name, rex_structure_action_field $field)
    {
        $this->fields[$field_name] = $field;

        return $this;
    }

    /**
     * @param string $field_name
     *
     * @return $this
     */
    public function unsetField($field_name)
    {
        unset($this->fields[$field_name]);

        return $this;
    }

    /**
     * @param string $field_name
     *
     * @return rex_structure_action_field|null
     */
    public function getField($field_name)
    {
        return isset($this->fields[$field_name]) ? $this->fields[$field_name] : null;
    }

    /**
     * @param string $field_name
     *
     * @return bool
     */
    public function hasField($field_name)
    {
        return isset($this->fields[$field_name]);
    }
}
