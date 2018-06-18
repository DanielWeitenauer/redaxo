<?php
/**
 * This class represents a structure group.
 * A structure group can contain multiple structure body and header fields.
 *
 * @see rex_structure_field
 * @package redaxo\structure
 */
class rex_structure_group
{
    use rex_factory_trait;

    /**
     * @var rex_structure_data_provider
     */
    protected $data_provider;
    /**
     * @var array
     */
    protected $group_head = [];
    /**
     * qvar array
     */
    protected $group_body = [];

    /**
     * @param rex_structure_data_provider $data_provider
     *
     * @return static
     */
    public static function factory(rex_structure_data_provider $data_provider)
    {
        $class = static::getFactoryClass();

        return new $class($data_provider);
    }

    /**
     * @param rex_structure_data_provider $data_provider
     */
    protected function __construct(rex_structure_data_provider $data_provider)
    {
        $this->setDataProvider($data_provider);
    }

    /**
     * @return rex_structure_data_provider
     */
    public function getDataProvider()
    {
        return $this->data_provider;
    }

    /**
     * @param rex_structure_data_provider $data_provider
     *
     * @return $this
     */
    public function setDataProvider($data_provider)
    {
        $this->data_provider = $data_provider;

        return $this;
    }

    /**
     * @return string
     */
    public function getGroupHead()
    {
        $fragment = new rex_fragment();
        $fragment->setVar('group', $this->group_head, false);

        return $fragment->parse('structure/group_head.php');
    }

    /**
     * @return string
     */
    public function getGroupBody()
    {
        $sql = $this->getDataProvider()->getSql();

        $css_class= '';
        // Result set may be empty
        if ($sql->getRows() && $sql->getValue('startarticle') == 1) {
            $css_class = ' rex-startarticle';
        }

        $fragment = new rex_fragment();
        $fragment->setVar('css_class', $css_class, false);
        $fragment->setVar('group', $this->group_body, false);

        return $fragment->parse('structure/group_body.php');
    }

    /**
     * @param string $key
     * @param rex_structure_field|array|null|string $field_body
     * @param rex_structure_field|array|null|string $field_head
     *
     * @return $this
     */
    public function setField($key, $field_body, $field_head = null)
    {
        $this->setFieldBody($key, $field_body);
        $this->setFieldHead($key, $field_head);

        return $this;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function unsetField($key)
    {
        unset($this->group_head[$key]);
        unset($this->group_body[$key]);

        return $this;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasField($key)
    {
        return isset($this->group_body[$key]) || isset($this->group_head[$key]);
    }

    /**
     * @param string $key
     *
     * @return array|null
     */
    public function getField($key)
    {
        return $this->hasField($key) ? [
            'head' => $this->group_head[$key],
            'body' => $this->group_body[$key],
        ] : [
            'head' => null,
            'body' => null,
        ];
    }

    /**
     * @param string $key
     * @param rex_structure_field|array|null|string $field_head
     *
     * @return $this
     */
    public function setFieldHead($key, $field_head)
    {
        if (!isset($field_head)) {
            $field_head = $key;
        }

        if (is_string($field_head)) {
            $field_head = rex_structure_field_string::factory($this->getDataProvider())->setString($field_head);
        }

        $this->group_head[$key] = $field_head;

        return $this;
    }

    /**
     * @param string $key
     * @param rex_structure_field|array|null|string $field_body
     *
     * @return $this
     */
    public function setFieldBody($key, $field_body)
    {
        if (!isset($field_body)) {
            $field_body = '';
        }
        if (is_string($field_body)) {
            $field_body = rex_structure_field_string::factory($this->getDataProvider())->setString($field_body);
        }

        $this->group_body[$key] = $field_body;

        if (!isset($this->group_head[$key])) {
            $this->setFieldHead($key, $key);
        }

        return $this;
    }

    /**
     * @param string $key
     *
     * @return rex_structure_field|null
     */
    public function getFieldHead($key)
    {
        return isset($this->group_head[$key]) ? $this->group_head[$key] : null;
    }

    /**
     * @param string $key
     *
     * @return rex_structure_field|null
     */
    public function getFieldBody($key)
    {
        return isset($this->group_body[$key]) ? $this->group_body[$key] : null;
    }
}
