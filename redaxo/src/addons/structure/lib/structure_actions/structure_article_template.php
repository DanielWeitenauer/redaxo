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
        if (!isset($this->temple_name[$this->sql->getValue('template_id')])) {
            return '';
        }

        return '<span class="btn" data-title="'.rex_i18n::msg('header_template').'">'.$this->template_name[$this->sql->getValue('template_id')].'</span>';
    }
}
