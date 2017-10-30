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
        $category_name = $this->sql->getValue('catname');

        $url_params = array_merge($this->url_params, [
            'category_id' => $this->edit_id,
        ]);

        return '<a href="'.$this->context->getUrl($url_params).'">'.htmlspecialchars($category_name).'</a>';
    }
}
