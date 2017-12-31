<?php
/**
 * @package structure/custom_url
 */
class structure_custom_url_page extends structure_custom_url
{
    /**
     * @var int
     */
    protected $article_id;
    /**
     * @var int
     */
    protected $clang_id;
    /**
     * @var int
     */
    protected $ctype_id;

    /**
     * Set extension point
     */
    public static function init()
    {
        rex_extension::register('PACKAGES_INCLUDED', function () {
            // Metainfo js
            #if (rex_be_controller::getCurrentPagePart(1) == 'content') {
            #    rex_view::addJsFile(rex_url::addonAssets('metainfo', 'metainfo.js'));
            #}

            // Add page to sidebar
            rex_extension::register('STRUCTURE_CONTENT_SIDEBAR', [__CLASS__, '_getMetaPage']);
        });
    }

    /**
     * EP CALLBACK
     *
     * @param rex_extension_point $ep
     *
     * @return string
     * @throws rex_exception
     * @internal
     */
    public static function _getMetaPage(rex_extension_point $ep)
    {
        $params = $ep->getParams();
        $subject = $ep->getSubject();

        $panel = new self();

        $fragment = new rex_fragment();
        $fragment->setVar('title', '<i class="rex-icon rex-icon-info"></i> '.self::msg('title'), false);
        $fragment->setVar('body', $panel->getStructure(), false);
        $fragment->setVar('article_id', $params['article_id'], false);
        $fragment->setVar('clang', $params['clang'], false);
        $fragment->setVar('ctype', $params['ctype'], false);
        $fragment->setVar('collapse', true);
        $fragment->setVar('collapsed', false);
        $content = $fragment->parse('core/page/section.php');

        return $content.$subject;
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Instaciated via EP
     */
    protected function __construct()
    {
    }

    /**
     * Add sidebar panel
     *
     * @return string
     * @throws rex_exception
     * @throws rex_sql_exception
     */
    protected function getStructure()
    {

        $context = new rex_context([
            'page' => rex_be_controller::getCurrentPage(),
            'article_id' => $this->getArticleId(),
            'clang' => $this->getClangId(),
            'ctype' => $this->getCtypeId(),
        ]);

        // Message
        $message = self::updateData();

        // Url types and optional elements, depending on url type
        $url_type = rex_request('custom-url-type', 'int', self::URL_TYPE_DEFAULT);
        $url_type_options = $this->getUrlOptions();

        $url_type_select = new rex_select();
        $url_type_select->setId('custom-url-type');
        $url_type_select->setName('custom-url-type');
        $url_type_select->setSize('1');
        $url_type_select->setAttribute('class', 'form-control');
        foreach ($url_type_options as $key => $value) {
            $url_type_select->addOption($value, $key);
        }
        $url_type_select->setSelected($url_type);

        $form_elements = [
            [
                'label' => '<label for="custom-url-type">'.self::msg('url_type').'</label>',
                'field' => $url_type_select->get(),
            ],
        ];
        $form_elements = array_merge($form_elements, $this->getOptionalFormElements($url_type));
        $form_elements = array_merge($form_elements, $this->getCloneOption());

        $fragment = new rex_fragment();
        $fragment->setVar('elements', $form_elements, false);
        $form = $fragment->parse('core/form/form.php');

        return '
            <form class="custom-url" action="'.$context->getUrl().'" method="post" enctype="multipart/form-data">
                '.$message.'
                <fieldset>
                    '.$form.'
                    <button class="btn btn-primary pull-right" type="submit" name="custom-url-submit" value="1">'.self::msg('submit').'</button>
                </fieldset>
            </form>
        ';
    }

    /**
     * @param int $option
     *
     * @return array
     */
    protected function getOptionalFormElements($option)
    {
        switch ($option) {
            case self::URL_TYPE_LANG_SWITCH:
                return [
                    [
                        'label' => '<label for="custom-url-clang">'.self::msg('custom_url_clang').'</label>',
                        'field' => $this->getClangWidget('custom-url-clang'),
                    ],
                ];
                break;

            case self::URL_TYPE_ARTICLE:
                return [
                    [
                        'label' => '<label for="custom-url-article">'.self::msg('custom_url_article').'</label>',
                        'field' => $this->getLinkWidget('custom-url-article'),
                    ]
                ];
                break;

            case self::URL_TYPE_CLANG:
               return [
                    [
                        'label' => '<label for="custom-url-article">'.self::msg('custom_url_article').'</label>',
                        'field' => $this->getLinkWidget('custom-url-article'),
                    ],
                    [
                        'label' => '<label for="custom-url-clang">'.self::msg('custom_url_clang').'</label>',
                        'field' => $this->getClangWidget('custom-url-clang'),
                    ],
                ];
                break;

            case self::URL_TYPE_MEDIA:
                return  [
                    [
                        'label' => '<label for="custom-url-media">'.self::msg('custom_url_media').'</label>',
                        'field' => $this->getMediaWidget('custom-url-media'),
                    ],
                ];
                break;

            case self::URL_TYPE_INTERNAL:
                return [
                    [
                        'label' => '<label for="custom-url-internal">'.self::msg('custom_url_internal').'</label>',
                        'field' => $this->getCustomUrlWidget('custom-url-internal'),
                    ],
                ];
                break;

            case self::URL_TYPE_EXTERNAL:
                return [
                    [
                        'label' => '<label for="custom-url-external">'.self::msg('custom_url_external').'</label>',
                        'field' => $this->getCustomUrlWidget('custom-url-external'),
                    ]
                ];
                break;

            case self::URL_TYPE_FUNCTION:
                /*return [
                    [
                        'label' => '<label for="custom-url-media">'.self::msg('custom_url_remove_root_cat').'</label>',

            <label for="call_func"><?php echo $I18N->msg(\'seo42_urlpage_urltype_function\'); ?></label>
            <input type="text" value="<?php if ($urlType == SEO42_URL_TYPE_CALL_FUNC) { echo $jsonData[\'func\']; } ?>" name="call_func" id="call_func" class="rex-form-text" />
        </p>
    </div>
    <div class="rex-form-row">
        <p class="rex-form-col-a rex-form-checkbox">
            <input type="checkbox" value="<?php if (isset($jsonData[\'no_url\']) && $jsonData[\'no_url\']) { echo "1"; $check = \'checked = "checked"\'; } else { echo ""; $check = ""; } ?>" name="url_clone_no_url[]" class="rex-form-checkbox" <?php echo $check; ?> />
            <label for="url_clone_no_url"><?php echo $I18N->msg(\'seo42_urlpage_url_clone_no_url\') ?></label>
        </p>
    </div>
</div>
                ']];
                break;*/

            case self::URL_TYPE_REMOVE_ROOT_CAT:
            case self::URL_TYPE_DEFAULT:
            default:
                return [];
        }
    }

    /**
     * @return array
     * @throws rex_sql_exception
     */
    protected function getCloneOption()
    {
        if (rex_clang::count() < 1) {
            return [];
        }

        $article_id = $this->getArticleId();
        $data = self::getCustomUrlData($article_id);

        if (rex_clang::getCurrentId() == rex_clang::getStartId()) {
            $data_clone = rex_post('custom-url-clone', 'int', - 1);
            if (!$data_clone == - 1) {
                $data_clone = isset($data['url_clone']) ? $data['url_clone'] : '';
            }

            $data_checked = $data_clone > 0 ? 'checked="checked"' : '';

            return [
                [
                    'label' => '<label for="custom-url-clone">'.self::msg('custom_url_clone').'</label>',
                    'field' => '<input type="checkbox" name="custom-url-clone" value="1" class="form-control" '.$data_checked.' />',
                ],
            ];
        } else {
            $sql = rex_sql::factory();
            $start_clang = $sql->getArray("
                SELECT 
                  structure_custom_url 
                FROM 
                  ".rex::getTable('article')."
                WHERE 
                    id = ? AND clang = ?
            ", [$article_id, rex_clang::getStartId()]);
            $start_clang_data = isset($start_clang[0]['structure_custom_url']) ? json_decode($start_clang[0]['structure_custom_url']) : [];

            $has_no_type = isset($data['url_type']) ? $data['url_type'] == self::URL_TYPE_DEFAULT : true;
            $is_cloned   = isset($start_clang_data['url_clone']) ? $start_clang_data['url_clone'] == 1 : false;

            if ($has_no_type && $is_cloned) {
                $clang = rex_clang::getCurrent();

                return [
                    [
                        'label' => '<span>'.self::msg('custom_url_clone').'</span>',
                        'field' => '<span>'.self::msg('custom_url_clone_info').' '.$clang->getName().' ['.$clang->getId().']</span>',
                    ],
                ];
            }
        }
    }

    /**
     * @return string
     * @throws rex_sql_exception
     */
    protected function updateData()
    {
        if (!rex_post('custom-url-submit', 'int')) {
            return '';
        }

        $article_id = $this->getArticleId();
        $clang_id = $this->getClangId();

        $update = true;
        $data = [];

        $url_type = rex_post('custom-url-type', 'int');

        $data['url_type'] = $url_type;

        if (rex_post('custom-url-clone', 'int', 0)) {
            $data['url_clone'] = true;
        } else {
            $data['url_clone'] = false;
        }

        switch ($url_type) {
            case self::URL_TYPE_LANG_SWITCH:
                $data['clang_id'] = rex_post('custom-url-clang', 'int');
                break;

            case self::URL_TYPE_ARTICLE:
                $data['article_id'] = rex_post('custom-url-article', 'int');
                break;

            case self::URL_TYPE_CLANG:
                $data['article_id'] = rex_post('custom-url-article', 'int');
                $data['clang_id'] = rex_post('custom-url-clang', 'int');
                break;

            case self::URL_TYPE_MEDIA:
                $data['file'] = rex_post('custom-url-media');
                break;

            case self::URL_TYPE_INTERNAL:
                /*global $SEO42_URLS;

                $sanitizedUrl = seo42_utils::parseInternalUrl(rex_post('userdef_intern'));

                // check if url already exists
                if (isset($SEO42_URLS[$sanitizedUrl])) { // url already exists
                    $update = false;
                    echo rex_warning($I18N->msg('seo42_urlpage_url_already_exists', seo42_utils::getCustomUrl($sanitizedUrl)));
                } else {
                    $data['custom_url'] = $sanitizedUrl;
                } */
                break;

            case self::URL_TYPE_EXTERNAL:
                $data['custom_url'] = rex_post('custom-url-external');
                break;

            case self::URL_TYPE_FUNCTION:
                /*$data['func'] = rex_post('call_func');

                if (is_array(rex_post('url_clone_no_url'))) {
                    $data['no_url'] = true;
                } else {
                    $data['no_url'] = false;
                } */
                break;

            case self::URL_TYPE_REMOVE_ROOT_CAT:
                /*global $SEO42_URLS;

                $newUrl = seo42_utils::removeRootCatFromUrl(rex_getUrl($REX['ARTICLE_ID'], $REX['CUR_CLANG']), $REX['CUR_CLANG']);

                // check if url already exists
                if (isset($SEO42_URLS[$newUrl])) { // url already exists
                    $update = false;
                    echo rex_warning($I18N->msg('seo42_urlpage_url_already_exists', seo42_utils::getCustomUrl($newUrl)));
                }*/
                break;

            case self::URL_TYPE_DEFAULT:
            case self::URL_TYPE_NONE:
            default:
                // nothing
        }

        #dump($data);

        if ($url_type == self::URL_TYPE_DEFAULT && !$data['url_clone']) {
            $data = '';
        } else {
            $data = json_encode($data);
        }

        #dump($data);

        $sql = rex_sql::factory();
        $sql->setTable(rex::getTable('article'));
        $sql->setWhere("id = :article_id AND clang_id = :clang_id", [
            'article_id' => $article_id,
            'clang_id' => $clang_id,
        ]);
        $sql->setValue('structure_custom_url', $data);
        $sql->setValue('updatedate',  time());

        if ($update) {
            $sql->update();
            rex_article_cache::delete($article_id, $clang_id);
        }

        if ($sql->hasError()) {
            return rex_view::warning($sql->getError());
        }

        return rex_view::success(self::msg('url_type_updated'));
    }

    /**
     * @return int
     */
    protected function getArticleId()
    {
        if (!isset($this->article_id)) {
            $this->article_id = rex_request('article_id', 'int');
            $this->article_id = rex_article::get($this->article_id) instanceof rex_article ? $this->article_id : 0;
        }

        return $this->article_id;
    }

    /**
     * @return int
     */
    protected function getClangId()
    {
        if (!isset($this->clang_id)) {
            $this->clang_id = rex_request('clang', 'int');
            $this->clang_id = rex_clang::exists($this->clang_id) ? $this->clang_id : rex_clang::getStartId();
        }

        return $this->clang_id;
    }

    /**
     * @return int
     * @throws rex_sql_exception
     */
    protected function getCtypeId()
    {
        if (!isset($this->ctype_id)) {
            $article_id = $this->getArticleId();
            $clang_id = $this->getClangId();

            $article = rex_sql::factory();
            $article->setQuery("
                SELECT
                    article.*, template.attributes as template_attributes
                FROM
                    ".rex::getTable('article')." as article
                LEFT JOIN
                    ".rex::getTable('template')." as template
                ON
                    template.id = article.template_id
                WHERE
                    article.id = ? AND clang_id = ?
            ", [
                $article_id,
                $clang_id
            ]);

            $template_attributes = $article->getArrayValue('template_attributes');

            // Für Artikel ohne Template
            if (!is_array($template_attributes)) {
                $template_attributes = [];
            }

            $ctypes = isset($template_attributes['ctype']) ? $template_attributes['ctype'] : [];

            $this->ctype_id = rex_request('ctype', 'int', 1);
            if (!array_key_exists($this->ctype_id, $ctypes)) {
                $this->ctype_id = 1;
            }
        }

        return $this->ctype_id;
    }

    /**
     * @param string $var_name
     *
     * @return string
     */
    protected function getLinkWidget($var_name)
    {
        $article_id = $this->getArticleId();

        $data_article_id = rex_post($var_name, 'int', 0);
        if (!$data_article_id) {
            $data = self::getCustomUrlData($article_id);
            $data_article_id = isset($data['article_id']) ? $data['article_id'] : 0;
        }

        return rex_var_link::getWidget(1, $var_name, $data_article_id);
    }

    /**
     * @param string $var_name
     *
     * @return string
     */
    protected function getMediaWidget($var_name)
    {
        $article_id = $this->getArticleId();

        $data_media = rex_post($var_name, 'string', '');
        if (!$data_media) {
            $data = self::getCustomUrlData($article_id);
            $data_media = isset($data['file']) ? $data['file'] : '';
        }

        return rex_var_media::getWidget(1, $var_name, $data_media);
    }

    /**
     * @param string $var_name
     *
     * @return string
     */
    protected function getClangWidget($var_name)
    {
        $article_id = $this->getArticleId();

        $data_clang_id = rex_post($var_name, 'int', 0);
        if (!$data_clang_id) {
            $data = self::getCustomUrlData($article_id);
            $data_clang_id = isset($data['clang_id']) ? $data['clang_id'] : 1;
        }
        $clang_select = new rex_select();
        $clang_select->setId($var_name);
        $clang_select->setName($var_name);
        $clang_select->setSize('1');
        $clang_select->setAttribute('class', 'form-control');
        foreach (rex_clang::getAll(true) as $clang) {
            $clang_select->addOption($clang->getName(), $clang->getId());
        }
        $clang_select->setSelected($data_clang_id);

        return $clang_select->get();
    }

    /**
     * @param string $var_name
     *
     * @return string
     */
    protected function getCustomUrlWidget($var_name)
    {
        $data_url = rex_post($var_name, 'string', '');
        if (!$data_url) {
            $article_id = $this->getArticleId();
            $data = self::getCustomUrlData($article_id);
            $data_url = isset($data['custom_url']) ? $data['custom_url'] : '';
        }

        return '<input type="text" name="'.$var_name.'" value="'.rex_escape($data_url).'" class="form-control" />';
    }
}
