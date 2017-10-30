<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_name extends rex_fragment
{
    /**
     * @return string
     */
    public function get()
    {
        $article_title = htmlspecialchars($this->sql->getValue('name'));
        $category_id = rex_article::get($this->edit_id)->getCategoryId();

        if (rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id)) {
            $edit_url = $this->context->getUrl([
                'page' => 'content/edit',
                'article_id' => $this->edit_id,
                'mode' => 'edit'
            ]);

            $article_title = '<a href="'.$edit_url.'">'.$article_title.'</a>';
        }

        return $article_title;
    }
}
