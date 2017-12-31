<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_priority extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        /** @var rex_sql $sql */
        $sql = $this->getVar('sql');

        $button_params = [
            'label' => htmlspecialchars($sql->getValue('priority')),
            'attributes' => [
                'class' => [
                    'btn',
                ],
                'data-title' => rex_i18n::msg('header_priority'),
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
