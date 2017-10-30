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

// --------------------- KATEGORIE LIST

$table_body = '';
$category_actions = [];
if ($KAT->getRows() > 0) {
    for ($i = 0; $i < $KAT->getRows(); ++$i) {
        $i_category_id = $KAT->getValue('id');

        // Show a category
        if ($KATPERM || rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($i_category_id)) {
            if ($KATPERM) {
                // These params are passed to the structure actions
                $action_params = [
                    'edit_id' => $i_category_id,
                    'sql' => $KAT,
                    'pager' => $catPager,
                    'clang' => $clang,
                    'context' => $context,
                    'url_params' => ['artstart' => $artstart, 'catstart' => $catstart],
                ];

                $category_actions = [
                    'icon' => [
                        'category_icon' => new rex_structure_category_icon($action_params),
                    ],
                    'id' => [
                        'category_id' => new rex_structure_category_id($action_params),
                    ],
                    'category' => [
                        'category_name' => new rex_structure_category_name($action_params),
                    ],
                    'priority' => [
                        'category_priority' => new rex_structure_category_priority($action_params),
                    ],
                    'status' => [
                        'category_edit' => new rex_structure_category_edit($action_params),
                        'category_delete' => new rex_structure_category_delete($action_params),
                        'category_status' => new rex_structure_category_status($action_params),
                    ],
                    'action' => [
                        'category2article' => new rex_structure_category2article($action_params),
                        'category_move' => new rex_structure_category_move($action_params),
                    ],
                ];

                // EXTENSION POINT to manipulate the $category_actions array
                $category_actions = rex_extension::registerPoint(new rex_extension_point('PAGE_STRUCTURE_CATEGORY_ACTIONS', $category_actions, $action_params));

                // Normalize array
                array_walk ($category_actions, function(&$item) {
                    if (!is_array($item)) {
                        $item = [$item]; // (array) would transform the object
                    }
                });
            }

            $fragment = new rex_fragment();
            $fragment->setVar('category_actions', $category_actions, false);
            $table_body .= $fragment->parse('structure/page/table_category_row_body.php');
        }

        $KAT->next();
    }
}

// Header
$structure_category_add = new rex_structure_category_add([
    'edit_id' => $category_id,
    'sql' => $KAT,
    'pager' => $catPager,
    'clang' => $clang,
    'context' => $context,
    'url_params' => ['artstart' => $artstart, 'catstart' => $catstart],
]);

$fragment = new rex_fragment();
$fragment->setVar('table_icon', $structure_category_add->get(), false);
$fragment->setVar('category_actions', $category_actions, false);
$table_head = $fragment->parse('structure/page/table_category_row_head.php');

// Link to parent category
if ($category_id != 0 && ($category = rex_category::get($category_id))) {
    $fragment = new rex_fragment();
    $fragment->setVar('parent_url', $context->getUrl(['category_id' => $category->getParentId()]));
    $fragment->setVar('category_actions', $category_actions, false);
    $table_body = $fragment->parse('structure/page/table_category_parent_row_body.php').$table_body;
}

// Table
$fragment = new rex_fragment();
$fragment->setVar('table_head', $table_head, false);
$fragment->setVar('table_body', $table_body, false);
$echo = $fragment->parse('structure/page/table.php');

// Section
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

    $table_body = '';

    // --------------------- ARTIKEL LIST
    $article_actions = [];
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

        // Get article actions
        $article_actions = [
            'icon' => [
                'article_icon' => new rex_structure_article_icon($action_params),
            ],
            'id' => [
                'article_id' => new rex_structure_article_id($action_params),
            ],
            'article_name' => [
                'article_name' => new rex_structure_article_name($action_params)
            ],
            'template' => [
                'article_template' => new rex_structure_article_template($action_params),
            ],
            'date' => [
                'article_create_date' => new rex_structure_article_create_date($action_params),
            ],
            'priority' => [
                'article_priority' => new rex_structure_article_priority($action_params),
            ],
            'status' => [
                'article_edit' => new rex_structure_article_edit($action_params),
                'article_delete' => new rex_structure_article_delete($action_params),
                'article_status' => new rex_structure_article_status($action_params),
            ],
            'action' => [
                'article2category' => new rex_structure_article2category($action_params),
                'article2startarticle' => new rex_structure_article2startarticle($action_params),
                'article_move' => new rex_structure_article_move($action_params),
                'article_copy' => new rex_structure_article_copy($action_params),
            ],
        ];

        // EXTENSION POINT to manipulate the $article_actions array
        $article_actions = rex_extension::registerPoint(new rex_extension_point('PAGE_STRUCTURE_ARTICLE_ACTIONS', $article_actions, $action_params));

        // Normalize array
        array_walk ($article_actions, function(&$item) {
            if (!is_array($item)) {
                $item = [$item]; // (array) would transform the object
            }
        });

        $fragment = new rex_fragment();
        $fragment->setVar('table_classes', $sql->getValue('startarticle') == 1 ? ' rex-startarticle' : '');
        $fragment->setVar('article_actions', $article_actions, false);
        $table_body .= $fragment->parse('structure/page/table_article_row_body.php');

        $sql->next();
    }

    // Header
    $structure_article_add = new rex_structure_article_add([
        'edit_id' => $category_id,
        'sql' => $sql,
        'pager' => $artPager,
        'clang' => $clang,
        'context' => $context,
        'url_params' => ['artstart' => $artstart, 'catstart' => $catstart],
    ]);

    $fragment = new rex_fragment();
    $fragment->setVar('table_icon', $structure_article_add->get(), false);
    $fragment->setVar('article_actions', $article_actions, false);
    $table_head = $fragment->parse('structure/page/table_article_row_head.php');
}

$fragment = new rex_fragment();
$fragment->setVar('table_head', $table_head, false);
$fragment->setVar('table_body', $table_body, false);
$echo = $fragment->parse('structure/page/table.php');

$fragment = new rex_fragment();
$fragment->setVar('heading', rex_i18n::msg('structure_articles_caption', $cat_name), false);
$fragment->setVar('content', $echo, false);
echo $fragment->parse('core/page/section.php');
