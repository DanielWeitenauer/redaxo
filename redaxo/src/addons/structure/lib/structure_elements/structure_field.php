<?php
/**
 * This class represents a single structure action field.
 * It must be overruled to implement the respective methods to generate
 * the representing html elements or a bootstrap modal.
 * Structure action fields are the lowest component of a structure table.
 * Multiple structure action fields can be added to a structure action column.
 *
 * @package redaxo
 */
abstract class rex_structure_field
{
    use rex_factory_trait;

    /**
     * @var rex_structure_data_provider
     */
    protected $data_provider;
    /**
     * @var bool
     */
    protected $hidden_label = false;

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
     * This method should implement the generation and return of an html element
     * representing a single structure action field, e.g. an icon, button,
     * link or informational text.
     *
     * @return string
     */
    abstract public function getField();

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
     * @param bool $hidden_label
     *
     * @return $this
     */
    public function setHiddenLabel($hidden_label)
    {
        $this->hidden_label = (bool) $hidden_label;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHiddenLabel()
    {
        return $this->hidden_label;
    }

    /**
     * @param array $field_params
     *
     * @return string
     */
    protected function getFragment(array $field_params = [])
    {
        $fragment = new rex_fragment([
            'field_params' => $field_params,
        ]);

        return $fragment->parse('structure/field.php');
    }
}
