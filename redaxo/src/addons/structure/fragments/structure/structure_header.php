<?php
/**
 * Regelt die Rechte an den einzelnen Kategorien und gibt den Pfad aus
 * Kategorien = Startartikel und Bezüge.
 *
 * @package redaxo5
 */

$structure_data = rex_structure_data::getInstance();

echo $this->structure_header_pre;

echo $this->structure_title;

echo $this->structure_language;

echo $this->structure_breadcrumb;

echo $this->structure_message;

echo $this->structure_header;
