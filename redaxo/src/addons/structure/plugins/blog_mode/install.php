<?php
/**
 * @author Daniel Weitenauer
 * @copyright (c) 2017 studio ahoi
 */

rex_metainfo_add_field(
    'Artikel Sortierung',
    'cat_article_order',
    1,
    '',
    3,
    '',
    'priority, name|createdate DESC|createdate ASC|updatedate DESC|updatedate ASC|name ASC|name DESC',
    null,
    '',
    null
);

