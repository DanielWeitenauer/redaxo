<?php
/**
 * @package redaxo\structure
 */
class rex_structure_category_id extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        $button_params = [
            'label' => htmlspecialchars($this->getVar('sql')->getValue('id')),
            'attributes' => [
                'class' => [
                    'btn',
                ],
            ],
        ];

        return $this->getButtonFragment($button_params);
    }

    /**
     * @return string
     */
    public function getModal()
    {
        return '';
    }
}
