<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_template extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        $article_id = $this->getVar('edit_id');
        /** @var rex_sql $sql */
        $sql = $this->getVar('sql');
        $template_id = $sql->getValue('template_id');
        $category_id = rex_article::get($article_id)->getCategoryId();
        $templates = $this->getTemplates($category_id);

        if (!isset($templates[$template_id])) {
            return '';
        }

        $button_params = [
            'label' => $templates[$template_id],
            'attributes' => [
                'title' => rex_i18n::msg('header_template'),
                'data-title' => rex_i18n::msg('header_template'),
            ],
        ];

        return $this->getButtonFragment($button_params);
    }

    /**
     * @param int $category_id
     * @return array
     */
    protected function getTemplates($category_id)
    {
        $return = [];

        $templates = rex_template::getTemplatesForCategory($category_id);
        if (count($templates) > 0) {
            foreach ($templates as $t_id => $t_name) {
                $return[$t_id] = rex_i18n::translate($t_name);
            }
        }

        $return[0] = rex_i18n::msg('template_default_name');

        return $return;
    }
}
