<?php
/**
 * @package redaxo\structure
 */
class rex_structure_article_move extends rex_structure_action_field
{
    /**
     * @return string
     * @throws rex_exception
     */
    public function get()
    {
        $article_id = $this->getVar('edit_id');
        $article = rex_article::get($article_id);
        $article_id = $article->getId();
        $category_id = $article->getCategoryId();
        /** @var rex_context $context */
        $context = $this->getVar('context');

        if ($article->isStartArticle() || !rex::getUser()->hasPerm('moveArticle[]') || $category_id == $article_id) {
            return '';
        }

        $url_params = array_merge($this->getVar('url_params'), [
            'form_article_move' => $article_id,
        ]);

        $button_params = [
            $this->hasVar('hide_label') && $this->getVar('hide_label') ? 'hidden_label' : 'label' => rex_i18n::msg('content_submitmovearticle'),
            'icon' => 'rex-icon fa-cut',
            'url' => $context->getUrl($url_params, false),
            'attributes' => [
                'class' => [
                    'btn btn-default',
                ],
            ],
        ];

        $return = $this->getButtonFragment($button_params);

        // Display form if necessary
        if (rex_request('form_article_move', 'int', -1) == $article_id) {
            $return .= $this->getModal();
        }

        return $return;
    }

    /**
     * @return string
     */
    protected function getModal()
    {
        $article_id = $this->getVar('edit_id');
        $article = rex_article::get($article_id);
        $article_id = $article->getId();
        $category_id = $article->getCategoryId();
        $user = rex::getUser();
        /** @var rex_context $context */
        $context = $this->getVar('context');

        if ($article->isStartArticle() || !$user->hasPerm('moveArticle[]') || $category_id == $article_id) {
            return '';
        }

        $category_select = new rex_category_select(false, false, true, !$user->getComplexPerm('structure')->hasMountPoints());
        $category_select->setId('new-category-id');
        $category_select->setName('new-category-id');
        $category_select->setSize('1');
        $category_select->setAttribute('class', 'form-control');
        $category_select->setSelected($category_id);

        return '  
            <div class="modal fade" id="article-move-'.$article_id.'">
                <div class="modal-dialog">
                    <form id="rex-form-content-article-move-'.$article_id.'" class="modal-content form-vertical" action="'.$context->getUrl().'" method="post" enctype="multipart/form-data" data-pjax-container="#rex-page-main">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title">'.rex_i18n::msg('content_submitmovearticle').'</h3>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="rex-api-call" value="article_move" />
                            <input type="hidden" name="article_id" value="'.$article_id.'" />
                            <div class="row">
                                <div class="col-xs-12">
                                    <dl class="rex-form-group form-group">
                                        <dt><label for="new-category-id">'.rex_i18n::msg('move_article').'</label></dt>
                                        <dd>'.$category_select->get().'</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-send" type="submit" data-confirm="'.rex_i18n::msg('content_submitmovearticle').'?">'.rex_i18n::msg('content_submitmovearticle').'</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">'.rex_i18n::msg('form_abort').'</button>
                        </div>
                    </form>
                </div>
            </div> 
            <script>
                $(document).ready(function() {
                    $("#article-move-'.$article_id.'").modal();
                });
            </script>        
        ';
    }
}
