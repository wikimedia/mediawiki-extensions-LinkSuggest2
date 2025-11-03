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
 */

use MediaWiki\EditPage\EditPage;
use MediaWiki\MediaWikiServices;

class LinkSuggest2 {

	/**
	 * @var LinkSuggest2
	 */
	private static $instance = null;

	/**
	 * @var array Array of jQuery-style selectors which will receive LinkSuggest2
	 */
	private $selectors = [];

	/**
	 * @var bool Whether the current user has enabled LinkSuggest
	 */
	private $userWantsLinkSuggest = false;

	private function __construct() {
		$user = RequestContext::getMain()->getUser();
		$userOptionsLookup = MediaWikiServices::getInstance()->getUserOptionsLookup();
		$this->userWantsLinkSuggest = !$userOptionsLookup->getOption( $user, 'disablelinksuggest' );

		// only register hook if the user has enabled LinkSuggest
		if ( $this->userWantsLinkSuggest ) {
			$hookContainer = MediaWikiServices::getInstance()->getHookContainer();
			$hookContainer->register( 'BeforePageDisplay', [ $this, 'onBeforePageDisplay' ] );
		}
	}

	public static function singleton() {
		if ( self::$instance == null ) {
			self::$instance = new LinkSuggest2();
		}
		return self::$instance;
	}

	/**
	 * Add one or more jQuery-style selectors which will receive LinkSuggest
	 * @param mixed $selectors a single jQuery-style selector or an array of them
	 */
	public function addSelectors( $selectors ) {
		if ( is_array( $selectors ) ) {
			$this->selectors = array_merge( $this->selectors, $selectors );
		} else {
			$this->selectors[] = $selectors;
		}
	}

	/**
	 * Add LinkSuggest module to output if enabled & requested
	 * @param OutputPage &$out
	 * @param Skin &$skin
	 * @return bool true because it's a hook
	 */
	public function onBeforePageDisplay( OutputPage &$out, Skin &$skin ) {
		// only add LinkSuggest if there are elements that need it
		if ( count( $this->selectors ) ) {
			global $wgLinkSuggestMaxSuggestions;

			$out->addJsConfigVars( [
				'wgLinkSuggestElements' => $this->selectors,
				'wgLinkSuggestMaxSuggestions' => $wgLinkSuggestMaxSuggestions,
			] );
			$out->addModules( 'ext.LinkSuggest2' );
		}
		return true;
	}

	/**
	 * Allow the user to enable/disable LinkSuggest2
	 * @param User $user
	 * @param array &$preferences
	 * @return bool true because it's a hook
	 */
	public static function onGetPreferences( User $user, array &$preferences ) {
		$preferences['disablelinksuggest'] = [
			'type' => 'toggle',
			'section' => 'editing/advancedediting',
			'label-message' => 'tog-disablelinksuggest2'
		];
		return true;
	}

	/**
	 * Add LinkSuggest to edit form
	 * Configurable with $wgEnableEditFormLinkSuggest
	 * @param EditPage $editPage
	 * @param OutputPage $output
	 * @return bool true because it's a hook
	 */
	public static function onEditPageShowEditFormInitial( EditPage $editPage, OutputPage $output ) {
		global $wgEnableEditFormLinkSuggest;

		if ( $wgEnableEditFormLinkSuggest ) {
			self::singleton()->addSelectors( '#wpTextbox1' );
		}

		return true;
	}
}
