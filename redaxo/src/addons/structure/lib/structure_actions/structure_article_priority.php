<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_priority extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        return '<span data-title="'.rex_i18n::msg('header_priority').'">'.htmlspecialchars($this->sql->getValue('priority')).'</span>';
    }
}
