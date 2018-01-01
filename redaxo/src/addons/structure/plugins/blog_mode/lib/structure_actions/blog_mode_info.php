<?php
/**
 * @package redaxo\structure
 */
class rex_blog_mode_info extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        /** @var rex_sql $sql */
        $sql = $this->getVar('sql');

        if ($sql->getValue('cat_article_order') != 'priority, name') {
            $type = rex_i18n::msg('blog_mode');
        } else {
            $type = rex_i18n::msg('standard_mode');
        }

        $button_params = [
            'label' => $type,
            'attributes' => [
                'class' => [
                    'btn',
                    'structure-blog-mode-info',
                ],
            ],
        ];

        return $this->getButtonFragment($button_params);
    }

    public function getModal()
    {
        return '';
    }
}
