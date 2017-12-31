<?php
/**
 * @author Daniel Weitenauer
 * @copyright (c) 2017 studio ahoi
 */

rex_sql_table::get(rex::getTable('article'))
     ->ensureColumn(new rex_sql_column('structure_custom_url', 'varchar(255)', false))
     ->alter();
