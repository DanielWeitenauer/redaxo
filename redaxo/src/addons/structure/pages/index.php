<?php
/**
 * @package redaxo5/structure
 */

// basic request vars
$category_id = rex_request('category_id', 'int');
$article_id = rex_request('article_id', 'int');
$clang = rex_request('clang', 'int');
$ctype = rex_request('ctype', 'int');

// additional request vars
$artstart = rex_request('artstart', 'int');
$catstart = rex_request('catstart', 'int');
$edit_id = rex_request('edit_id', 'int');
$function = rex_request('function', 'string');

$category_id = rex_category::get($category_id) ? $category_id : 0;
$article_id = rex_article::get($article_id) ? $article_id : 0;
$clang = rex_clang::exists($clang) ? $clang : rex_clang::getStartId();

// --------------------------------------------- Mountpoints
$mountpoints = rex::getUser()->getComplexPerm('structure')->getMountpoints();
if (count($mountpoints) == 1 && $category_id == 0) {
    // Nur ein Mointpoint -> Sprung in die Kategory
    $category_id = current($mountpoints);
}

// --------------------------------------------- Rechte prüfen
$KATPERM = rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id);

$stop = false;
if (rex_clang::count() > 1) {
    if (!rex::getUser()->getComplexPerm('clang')->hasPerm($clang)) {
        $stop = true;
        foreach (rex_clang::getAllIds() as $key) {
            if (rex::getUser()->getComplexPerm('clang')->hasPerm($key)) {
                $clang = $key;
                $stop = false;
                break;
            }
        }

        if ($stop) {
            echo rex_view::error('You have no permission to this area');
            exit;
        }
    }
} else {
    $clang = rex_clang::getStartId();
}

$context = new rex_context([
    'page' => 'structure',
    'category_id' => $category_id,
    'article_id' => $article_id,
    'clang' => $clang,
]);

// --------------------- Extension Point
echo rex_extension::registerPoint(new rex_extension_point('PAGE_STRUCTURE_HEADER_PRE', '', [
    'context' => $context,
]));

// --------------------------------------------- TITLE
echo rex_view::title(rex_i18n::msg('title_structure'));

// --------------------------------------------- Languages
echo rex_view::clangSwitchAsButtons($context);

// --------------------------------------------- Path
require __DIR__ . '/../functions/function_rex_category.php';

// --------------------------------------------- API MESSAGES
echo rex_api_function::getMessage();

/**
 * KATEGORIE LISTE
 */
$cat_name = 'Homepage';
$category = rex_category::get($category_id, $clang);
if ($category) {
    $cat_name = $category->getName();
}

// --------------------- Extension Point
echo rex_extension::registerPoint(new rex_extension_point('PAGE_STRUCTURE_HEADER', '', [
    'category_id' => $category_id,
    'clang' => $clang,
]));

// --------------------- SEARCH BAR
//require_once $this->getPath('functions/function_rex_searchbar.php');
//echo rex_structure_searchbar($context);

// --------------------- COUNT CATEGORY ROWS
$KAT = rex_sql::factory();
// $KAT->setDebug();
if (count($mountpoints) > 0 && $category_id == 0) {
    $parent_id = implode(',', $mountpoints);
    $KAT->setQuery('SELECT COUNT(*) as rowCount FROM ' . rex::getTablePrefix() . 'article WHERE id IN (' . $parent_id . ') AND startarticle=1 AND clang_id=' . $clang . ' ORDER BY catname');
} else {
    $KAT->setQuery('SELECT COUNT(*) as rowCount FROM ' . rex::getTablePrefix() . 'article WHERE parent_id=' . $category_id . ' AND startarticle=1 AND clang_id=' . $clang . ' ORDER BY catpriority');
}

// --------------------- ADD PAGINATION
$catPager = new rex_pager(30, 'catstart');
$catPager->setRowCount($KAT->getValue('rowCount'));
$catFragment = new rex_fragment();
$catFragment->setVar('urlprovider', $context);
$catFragment->setVar('pager', $catPager);
echo $catFragment->parse('core/navigations/pagination.php');

// --------------------- GET THE DATA
if (count($mountpoints) > 0 && $category_id == 0) {
    $parent_id = implode(',', $mountpoints);
    $KAT->setQuery('SELECT * FROM ' . rex::getTablePrefix() . 'article WHERE id IN (' . $parent_id . ') AND startarticle=1 AND clang_id=' . $clang . ' ORDER BY catname LIMIT ' . $catPager->getCursor() . ',' . $catPager->getRowsPerPage());
} else {
    $KAT->setQuery('SELECT * FROM ' . rex::getTablePrefix() . 'article WHERE parent_id=' . $category_id . ' AND startarticle=1 AND clang_id=' . $clang . ' ORDER BY catpriority LIMIT ' . $catPager->getCursor() . ',' . $catPager->getRowsPerPage());
}

// --------------------- PRINT CATS/SUBCATS
$structure_category_add = new rex_structure_category_add([
    'edit_id' => $category_id,
    'sql' => $KAT,
    'pager' => $catPager,
    'clang' => $clang,
    'context' => $context,
    'url_params' => ['artstart' => $artstart, 'catstart' => $catstart],
]);

// Header
$fragment = new rex_fragment();
$fragment->setVar('table_icon', $structure_category_add->get(), false);
$table_head = $fragment->parse('structure/page/table_category_row_head.php');

$table_body = '';
// Link to parent category
if ($category_id != 0 && ($category = rex_category::get($category_id))) {
    $fragment = new rex_fragment();
    $fragment->setVar('table_icon', '<i class="rex-icon rex-icon-open-category"></i>', false);
    $fragment->setVar('table_id', '-');
    $fragment->setVar('table_name', '<a href="'.$context->getUrl(['category_id' => $category->getParentId()]).'">..</a>', false);
    $fragment->setVar('table_infos', '&nbsp;', false);
    $fragment->setVar('table_action', '&nbsp;', false);
    $table_body .= $fragment->parse('structure/page/table_category_row_body.php');
}

// --------------------- KATEGORIE LIST
if ($KAT->getRows() > 0) {
    for ($i = 0; $i < $KAT->getRows(); ++$i) {
        $i_category_id = $KAT->getValue('id');

        $kat_link = $context->getUrl(['category_id' => $i_category_id]);

        // Show a category
        if ($KATPERM || rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($i_category_id)) {
            // These params are passed to the structure actions
            $action_params = [
                'edit_id' => $i_category_id,
                'sql' => $KAT,
                'pager' => $catPager,
                'clang' => $clang,
                'context' => $context,
                'url_params' => ['artstart' => $artstart, 'catstart' => $catstart],
            ];

            $category_actions = [];
            if ($KATPERM) {
                $category_actions = [
                    [
                        'category_edit' => new rex_structure_category_edit($action_params),
                        'category_delete' => new rex_structure_category_delete($action_params),
                        'category_status' => new rex_structure_category_status($action_params),
                    ],
                    [
                        'category2article' => new rex_structure_category2article($action_params),
                        'category_move' => new rex_structure_category_move($action_params),
                    ],
                ];

                // EXTENSION POINT to manipulate the $category_actions array
                $category_actions = rex_extension::registerPoint(new rex_extension_point('PAGE_STRUCTURE_CATEGORY_ACTIONS', $category_actions, $action_params));
            }

            // Get article infos
            $category_infos = [
                [
                    'category_priority' => new rex_structure_category_priority($action_params),
                ]
            ];

            // EXTENSION POINT to manipulate the $category_infos array
            $category_infos = rex_extension::registerPoint(new rex_extension_point('PAGE_STRUCTURE_CATEGORY_INFOS', $category_infos, $action_params));

            $fragment = new rex_fragment();
            $fragment->setVar('table_icon', '<a href="'.$kat_link.'" title="'.htmlspecialchars($KAT->getValue('catname')).'"><i class="rex-icon rex-icon-category"></i></a>', false);
            $fragment->setVar('table_id', $i_category_id);
            $fragment->setVar('table_name', '<a href="'.$kat_link.'">'.htmlspecialchars($KAT->getValue('catname')).'</a>', false);

            // Add article infos
            $category_info_output = '';
            foreach ($category_infos as $category_info_group) {
                if (!is_array($category_info_group)) {
                    $category_info_group = [$category_info_group]; // (array) would transform the object
                }
                $category_info_output .= '<div class="btn-group">';
                foreach ($category_info_group as $category_info) {
                    if ($category_info instanceof rex_fragment && method_exists($category_info, 'get')) {
                        $category_info_output .= $category_info->get().PHP_EOL;
                    }
                }
                $category_info_output .= '</div>';
            }
            $fragment->setVar('table_infos', $category_info_output, false);

            // Add category actions
            // Each action must be an descendant of rex_fragment and implement the method get()
            // to return an action trigger which is collected in this loop
            $category_action_output = '';
            foreach ($category_actions as $category_action_group) {
                if (!is_array($category_action_group)) {
                    $category_action_group = [$category_action_group];  // (array) would transform the object
                }
                $category_action_output .= '<div class="btn-group">';
                foreach ($category_action_group as $category_action) {
                    if ($category_action instanceof rex_fragment && method_exists($category_action, 'get')) {
                        $category_action_output .= $category_action->get().PHP_EOL;
                    }
                }
                $category_action_output .= '</div>';
            }
            $fragment->setVar('table_action', $category_action_output, false);

            $table_body .= $fragment->parse('structure/page/table_category_row_body.php');
        }

        $KAT->next();
    }
} else {
    $fragment = new rex_fragment();
    $fragment->setVar('table_icon', '&nbsp;', false);
    $fragment->setVar('table_id', '');
    $fragment->setVar('table_name', '');
    $fragment->setVar('table_infos', '');
    $fragment->setVar('table_action', '');
    $table_body .= $fragment->parse('structure/page/table_category_row_body.php');
}

$fragment = new rex_fragment();
$fragment->setVar('table_head', $table_head, false);
$fragment->setVar('table_body', $table_body, false);
$echo = $fragment->parse('structure/page/table.php');

$fragment = new rex_fragment();
$fragment->setVar('heading', rex_i18n::msg('structure_categories_caption', $cat_name), false);
$fragment->setVar('content', $echo, false);
echo $fragment->parse('core/page/section.php');

/**
 * ARTIKEL LISTE
 */
if ($category_id > 0 || ($category_id == 0 && !rex::getUser()->getComplexPerm('structure')->hasMountpoints())) {
    // ---------- COUNT DATA
    $sql = rex_sql::factory();
    // $sql->setDebug();
    $sql->setQuery('SELECT COUNT(*) as artCount
                FROM
                    ' . rex::getTablePrefix() . 'article
                WHERE
                    ((parent_id=' . $category_id . ' AND startarticle=0) OR (id=' . $category_id . ' AND startarticle=1))
                    AND clang_id=' . $clang . '
                ORDER BY
                    priority, name');

    // --------------------- ADD PAGINATION
    $artPager = new rex_pager(30, 'artstart');
    $artPager->setRowCount($sql->getValue('artCount'));
    $artFragment = new rex_fragment();
    $artFragment->setVar('urlprovider', $context);
    $artFragment->setVar('pager', $artPager);
    echo $artFragment->parse('core/navigations/pagination.php');

    // ---------- READ DATA
    $article_order = rex_structure_service::getArticleOrder($category_id); //@todo must be escaped

    $sql->setQuery('SELECT *
                FROM
                    ' . rex::getTablePrefix() . 'article
                WHERE
                    ((parent_id=' . $category_id . ' AND startarticle=0) OR (id=' . $category_id . ' AND startarticle=1))
                    AND clang_id=' . $clang . '
                ORDER BY
                    '.$article_order.'
                LIMIT ' . $artPager->getCursor() . ',' . $artPager->getRowsPerPage());

    // ----------- PRINT OUT THE ARTICLES
    $structure_article_add = new rex_structure_article_add([
        'edit_id' => $category_id,
        'sql' => $sql,
        'pager' => $artPager,
        'clang' => $clang,
        'context' => $context,
        'url_params' => ['artstart' => $artstart, 'catstart' => $catstart],
    ]);

    // Header
    $fragment = new rex_fragment();
    $fragment->setVar('table_icon', $structure_article_add->get(), false);
    $table_head = $fragment->parse('structure/page/table_article_row_head.php');

    $table_body = '';

    // --------------------- ARTIKEL LIST
    for ($i = 0; $i < $sql->getRows(); ++$i) {
        // --------------------- ARTIKEL NORMAL VIEW | EDIT AND ENTER

        // These params are passed to the structure actions and infos
        $action_params = [
            'edit_id' => $sql->getValue('id'),
            'sql' => $sql,
            'pager' => $artPager,
            'clang' => $clang,
            'context' => $context,
            'url_params' => ['artstart' => $artstart, 'catstart' => $catstart],
        ];

        // Actions with dedicated rows
        $structure_article_icon = new rex_structure_article_icon($action_params);
        $structure_article_name = new rex_structure_article_name($action_params);

        // Get article actions
        $article_actions = [
            [
                'article_edit' => new rex_structure_article_edit($action_params),
                'article_delete' => new rex_structure_article_delete($action_params),
                'article_status' => new rex_structure_article_status($action_params),
            ],
            [
                'article2category' => new rex_structure_article2category($action_params),
                'article2startarticle' => new rex_structure_article2startarticle($action_params),
                'article_move' => new rex_structure_article_move($action_params),
                'article_copy' => new rex_structure_article_copy($action_params),
            ],
        ];

        // EXTENSION POINT to manipulate the $article_actions array
        $article_actions = rex_extension::registerPoint(new rex_extension_point('PAGE_STRUCTURE_ARTICLE_ACTIONS', $article_actions, $action_params));

        // Get article infos
        $article_infos = [
            [
                'article_template' => new rex_structure_article_template($action_params),
            ],
            [
                'article_create_date' => new rex_structure_article_create_date($action_params),
            ],
            [
                'article_priority' => new rex_structure_article_priority($action_params),
            ],
        ];

        // EXTENSION POINT to manipulate the $article_infos array
        $article_infos = rex_extension::registerPoint(new rex_extension_point('PAGE_STRUCTURE_ARTICLE_INFOS', $article_infos, $action_params));

        $fragment = new rex_fragment();
        $fragment->setVar('table_classes', $sql->getValue('startarticle') == 1 ? ' rex-startarticle' : '');
        $fragment->setVar('table_icon', $structure_article_icon->get(), false);
        $fragment->setVar('table_id', $sql->getValue('id'));
        $fragment->setVar('table_name', $structure_article_name->get(), false);

        // Add article infos
        $article_info_output = '';
        foreach ($article_infos as $article_info_group) {
            if (!is_array($article_info_group)) {
                $article_info_group = [$article_info_group]; // (array) would transform the object
            }
            $article_info_output .= '<div class="btn-group">';
            foreach ($article_info_group as $article_info) {
                if ($article_info instanceof rex_fragment && method_exists($article_info, 'get')) {
                    $article_info_output .= $article_info->get().PHP_EOL;
                }
            }
            $article_info_output .= '</div>';
        }
        $fragment->setVar('table_infos', $article_info_output, false);

        // Add article actions
        // Each action must be an descendant of rex_fragment and implement the method get()
        // to return an action trigger which is collected in this loop
        $article_action_output = '';
        foreach ($article_actions as $article_action_group) {
            if (!is_array($article_action_group)) {
                $article_action_group = [$article_action_group];
            }
            $article_action_output .= '<div class="btn-group">';
            foreach ($article_action_group as $article_action) {
                if ($article_action instanceof rex_fragment && method_exists($article_action, 'get')) {
                    $article_action_output .= $article_action->get().PHP_EOL;
                }
            }
            $article_action_output .= '</div>';
        }
        $fragment->setVar('table_action', $article_action_output, false);

        $table_body .= $fragment->parse('structure/page/table_article_row_body.php');

        $sql->next();
    }
}

$fragment = new rex_fragment();
$fragment->setVar('table_head', $table_head, false);
$fragment->setVar('table_body', $table_body, false);
$echo = $fragment->parse('structure/page/table.php');

$fragment = new rex_fragment();
$fragment->setVar('heading', rex_i18n::msg('structure_articles_caption', $cat_name), false);
$fragment->setVar('content', $echo, false);
echo $fragment->parse('core/page/section.php');
