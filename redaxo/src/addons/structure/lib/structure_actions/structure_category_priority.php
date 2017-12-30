<?php
/**
 * @package redaxo\structure
 */
class rex_structure_category_priority extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        $button_params = [
            'label' => htmlspecialchars($this->getVar('sql')->getValue('catpriority')),
            'attributes' => [
                'class' => [
                    'btn',
                ],
                'data-title' => rex_i18n::msg('header_priority'),
            ],
        ];

        return $this->getButtonFragment($button_params);
    }
}
