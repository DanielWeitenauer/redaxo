<?php
/**
 * This class represents a single structure action field.
 * It must be overruled to implement the respective methods to generate
 * the representing html elements or a bootstrap modal.
 * Structure action fields are the lowest component of a structure table.
 * Multiple structure action fields can be added to a structure action column.
 *
 * @see rex_structure_action_column
 * @see rex_structure_action_row
 * @package redaxo\structure
 */
abstract class rex_structure_action_field
{
    /**
     * Traits
     */
    use rex_structure_trait_vars;

    /**
     * @param array $vars
     */
    public function __construct($vars = [])
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
    public function get()
    {
        throw new rex_exception('Method get() is yet to be implemeted!');
    }

    /**
     * This method should implement the generation and return of a bootstrap
     * modal presenting an additional form or information concerning the respective
     * structure element.
     *
     * @return string
     * @throws rex_exception
     */
    public function getModal()
    {
        throw new rex_exception('Method getModal() is yet to be implemeted!');
    }

    /**
     * @param array $button_params
     *
     * @return string
     * @throws rex_exception
     */
    protected function getButtonFragment(array $button_params)
    {
        $vars = array_merge($this->getVars(), [
            'buttons' => [
                'button' => $button_params,
            ],
        ]);

        $fragment = new rex_fragment($vars);

        return $fragment->parse('structure/structure_action_button.php');
    }

    /**
     * @param array $modal_params
     *
     * @return string
     * @throws rex_exception
     */
    protected function getModalFragment(array $modal_params = [])
    {
        $vars = array_merge($this->getVars(), $modal_params);

        $fragment = new rex_fragment($vars);

        return $fragment->parse('structure/structure_action_modal.php');
    }
}
