<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_template extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        $template_id = $this->sql->getValue('template_id');
        $category_id = rex_article::get($this->edit_id)->getCategoryId();
        $templates = $this->getTemplates($category_id);

        if (!isset($templates[$template_id])) {
            return '';
        }

        return '<span class="btn" data-title="'.rex_i18n::msg('header_template').'">'.$templates[$template_id].'</span>';
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
