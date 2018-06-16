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
    /**
     * Traits
     */
    use rex_factory_trait;
    use rex_structure_trait_vars;

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
     * This method should implement the generation and return of an html element
     * representing a single structure action field, e.g. an icon, button,
     * link or informational text.
     *
     * @return string
     * @throws rex_exception
     */
    abstract public function getField();

    /**
     * @param array $field_params
     *
     * @return string
     * @throws rex_exception
     */
    protected function getFragment(array $field_params = [])
    {
        $fragment = new rex_fragment(array_merge($this->getVars(), [
            'field' => $field_params,
        ]));

        return $fragment->parse('structure/field.php');
    }
}
