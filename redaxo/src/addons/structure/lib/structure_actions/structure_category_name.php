<?php
/**
 * @package redaxo\structure
 */
class rex_structure_category_name extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        if (!$this->edit_id) {
            return '&nbsp;';
        }

        $category_name = rex_category::get($this->edit_id)->getName();

        $url_params = array_merge($this->url_params, [
            'category_id' => $this->edit_id,
        ]);

        return '<a href="'.$this->context->getUrl($url_params).'">'.htmlspecialchars($category_name).'</a>';
    }
}
