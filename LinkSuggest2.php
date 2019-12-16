<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @ingroup Extensions
 */

$wgExtensionCredits['other'][] = array(
    'path' => __FILE__,
    'name' => 'LinkSuggest2',
    'author' => 'TK-999',
    'url' => 'https://www.mediawiki.org/wiki/Extension:LinkSuggest2',
    'descriptionmsg' => 'linksuggest2-desc',
    'version'  => '0.1',
    'license-name' => 'GPL-3.0-only'
);

$wgResourceModules['ext.LinkSuggest2'] = array(
	'scripts' => array(
		'modules/vendor/Caret.js/src/jquery.caret.js',
		'modules/ext.LinkSuggest2.js'
	),
	'dependencies' => array(
		'jquery.ui'
	),
	'localBasePath' => __DIR__,
	'remoteExtPath' => 'LinkSuggest2'
);

// Internationalization file
$wgMessagesDirs['LinkSuggest2'] = __DIR__ . '/i18n';

// Config variable - whether to load LinkSuggest on edit pages
$wgEnableEditFormLinkSuggest = true;

// Config variable - maximum amount of suggestions to show
$wgLinkSuggestMaxSuggestions = 3;

$wgAutoloadClasses['LinkSuggest2'] = __DIR__ . '/LinkSuggest2.class.php';
$wgHooks['GetPreferences'][] = 'LinkSuggest2::onGetPreferences';
$wgHooks['EditPage::showEditForm:initial'][] = 'LinkSuggest2::onEditPageShowEditFormInitial';
