<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_update_user extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        $article_id = $this->getVar('edit_id');
        $article = rex_article::get($article_id);

        $button_params = [
            'label' => htmlspecialchars($article->getValue('updateuser')),
            'attributes' => [
                'data-title' => rex_i18n::msg('header_update_user'),
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
