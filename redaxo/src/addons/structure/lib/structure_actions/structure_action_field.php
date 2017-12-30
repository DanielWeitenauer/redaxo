<?php
/**
 * @author Daniel Weitenauer
 * @copyright (c) 2017 studio ahoi
 */

abstract class rex_structure_action_field
{
    use rex_structure_trait_vars;

    public function __construct($vars = [])
    {
        $this->setVars($vars);
    }

    /**
     * @return array
     */
    abstract public function get();

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
     * @return string
     * @throws rex_exception
     */
    protected function getModalFragment()
    {
        $fragment = new rex_fragment($this->vars);

        return $fragment->parse('structure/structure_action_modal.php');
    }
}
