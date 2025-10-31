<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2023 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Esto renderiza únicamente el campo <input ...> sin ningún contenedor adicional.
// La variable $this->input contiene el HTML del <input> ya preparado por Joomla.
echo $this->input;
?>