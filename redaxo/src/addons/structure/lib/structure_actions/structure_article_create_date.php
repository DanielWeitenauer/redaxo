<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_create_date extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        return '<span class="btn" data-title="'.rex_i18n::msg('header_date').'">'.rex_formatter::strftime($this->sql->getDateTimeValue('createdate'), 'date').'</span>';
    }
}
