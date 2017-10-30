<?php
/**
 * @package redaxo\structure
 */
class rex_structure_category_priority extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        return '<span class="btn" data-title="'.rex_i18n::msg('header_priority').'">'.htmlspecialchars($this->sql->getValue('catpriority')).'</span>';
    }
}
