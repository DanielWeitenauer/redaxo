<?php
/**
 * @package redaxo\structure
 */
class rex_structure_category_move extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        $article_id = $this->getVar('edit_id');
        $article = rex_article::get($article_id);
        $user = rex::getUser();
        /** @var rex_context $context */
        $context = $this->getVar('context');

        if (!$article->isStartArticle() || !$user->hasPerm('moveCategory[]') || !$user->getComplexPerm('structure')->hasCategoryPerm($article_id)) {
            return '';
        }

        $url_params = array_merge($this->getVar('url_params'), [
            'form_category_move' => $article_id,
        ]);

        $button_params = [
            $this->hasVar('hide_label') && $this->getVar('hide_label') ? 'hidden_label' : 'label' => rex_i18n::msg('content_submitmovecategory'),
            'icon' => 'rex-icon fa-cut',
            'url' => $context->getUrl($url_params, false),
            'attributes' => [
                'class' => [
                    'btn btn-default',
                ],
            ],
        ];

        return $this->getButtonFragment($button_params);
    }

    /**
     * @return string
     */
    public function getModal()
    {
        $article_id = $this->getVar('edit_id');
        $article = rex_article::get($article_id);
        $user = rex::getUser();
        /** @var rex_context $context */
        $context = $this->getVar('context');

        if (!$article->isStartArticle() || !$user->hasPerm('moveCategory[]') || !$user->getComplexPerm('structure')->hasCategoryPerm($article_id)) {
            return '';
        }
        // Display form only if requested
        if (rex_request('form_category_move', 'int', -1) != $article_id) {
            return '';
        }

        $category_select = new rex_category_select(false, false, true, !$user->getComplexPerm('structure')->hasMountPoints());
        $category_select->setId('category_id_new');
        $category_select->setName('category_id_new');
        $category_select->setSize('1');
        $category_select->setAttribute('class', 'form-control');
        $category_select->setSelected($article_id);

        return '  
            <div class="modal fade" id="category-move-'.$article_id.'">
                <div class="modal-dialog">
                    <form id="rex-form-category-move-'.$article_id.'" class="modal-content form-vertical" action="'.$context->getUrl().'" method="post" enctype="multipart/form-data" data-pjax-container="#rex-page-main">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title">'.rex_i18n::msg('content_submitmovecategory').'</h3>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="rex-api-call" value="category_move" />
                            <input type="hidden" name="category_id" value="'.$article_id.'" />
                            <div class="row">
                                <div class="col-xs-12">
                                   <dl class="rex-form-group form-group">
                                        <dt><label for="category_id_new">'.rex_i18n::msg('move_category').'</label></dt>
                                        <dd>'.$category_select->get().'</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-send" type="submit" data-confirm="'.rex_i18n::msg('content_submitmovecategory').'?">'.rex_i18n::msg('content_submitmovecategory').'</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">'.rex_i18n::msg('form_abort').'</button>
                        </div>
                    </form>
                </div>
            </div> 
            <script>
                $(document).ready(function() {
                    $("#category-move-'.$article_id.'").modal();
                });
            </script>        
        ';
    }
}
