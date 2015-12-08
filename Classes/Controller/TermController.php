<?php
namespace Ecom\Ecomglossary\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014 Nicolas Scheidler <nicolas.scheidler@ecom-ex.com>, ecom instruments GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use \TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use \TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * TermController
 */
class TermController extends ActionController {

	/**
	 * termRepository
	 *
	 * @var \Ecom\Ecomglossary\Domain\Repository\TermRepository
	 * @inject
	 */
	protected $termRepository = null;

	/**
	 * Is the current client IP-Address in the exclude list?
	 */
	protected $isExcludedIp = false;

	/**
	 * Pagination Items Per Page
	 * 		Holds the number of ItemsPerPage from session or postVars
	 */
	protected $itemsPerPage = null;

	/**
	 * Pagination Option Set
	 * 		Holds the options for the itemsPerPage select box
	 */
	protected $itemsPerPageOptionSets = null;

	/**
	 * feSession
	 *
	 * @var \Ecom\EcomToolbox\Domain\Session\FrontendSessionHandler
	 * @inject
	 */
	protected $feSession = null;

	/**
	 * Initializes the controller before invoking an action method.
	 *
	 * Override this method to solve tasks which all actions have in
	 * common.
	 * @return void
	 */
	protected function initializeAction() {
		$this->feSession->setStorageKey($this->extensionName);

		// Explodes the developer IPs.
		$excludedIpsArray = $this->settings['excludeIpsForVisits'] ? GeneralUtility::trimExplode(',', $this->settings['excludeIpsForVisits'], true) : array();
		if ( $GLOBALS['_SERVER']['REMOTE_ADDR']) $this->isExcludedIp = in_array($GLOBALS['_SERVER']['REMOTE_ADDR'], $excludedIpsArray ) ? true : false;

		// Generates OptionSet for ItemsPerPage Selector-Box
		if (!empty($this->settings['termsPerPageOptionSets'])) {
			$itemsPerPageOptionsArray = array();
			foreach(GeneralUtility::trimExplode(',', $this->settings['termsPerPageOptionSets'], true) as $option) {
				$itemsPerPageOptionsArray[GeneralUtility::trimExplode(':', $option)[0]] = GeneralUtility::trimExplode(':', $option)[1];
			}
			$this->itemsPerPageOptionSets = $itemsPerPageOptionsArray;
		} else {
			// Generates the default OptionSet for ItemsPerPage
			$this->itemsPerPageOptionSets = array(
				0 => LocalizationUtility::translate('label.default', 'ecomglossary'),
				5 => 5,
				15 => 15,
				25 => 25,
				50 => 50
			);
		}

		/**
		 * Get items per page by form select from paginator (Index View)
		 * @see \Ecom\Ecomglossary\ViewHelpers\Widget\Controller\PaginateController;
		 */
		if ( GeneralUtility::_GP('tx_ecomglossary_ecomglossary')['itemsPerPage'] != '' ) {
			$itemsPerPage = (int) GeneralUtility::_GP('tx_ecomglossary_ecomglossary')['itemsPerPage'];
			$this->feSession->store('itemsPerPage', $itemsPerPage);
		}
		if ( $this->feSession->get('itemsPerPage') != '' ) $itemsPerPage = $this->feSession->get('itemsPerPage');
		$this->itemsPerPage = $itemsPerPage;
	}

	/**
	 * action list
	 *
	 * @param string $filterByLetter
	 * @return void
	 */
	public function listAction($filterByLetter = null) {
		// Reset Search when filtered by letter
		$searchTermFromSession = $filterByLetter ? null : $this->feSession->get('searchTerm');

		// Reset Letter Filter after search request
		if ( $this->request->hasArgument('searchTerm') ) {
			$filterByLetter = null;
		}

		/**
		 * Repository Handling
		 */
		$terms = $this->termRepository->findAll();
		// Generate Letter Navigation (all available terms / not modified)
		$availableLetters = $terms->count() ? $this->generateLetterArrayFromList($terms, $this->settings['showEmptyLetters']) : null;

		/**
		 * Search Request Handling
		 */
		// Search by term
		if ( $this->request->hasArgument('searchTerm') || $searchTermFromSession ) {
			$searchTerm = $this->request->hasArgument('searchTerm') ? $this->request->getArgument('searchTerm') : $searchTermFromSession;
			// Delete non-word chars
			$searchTerm = preg_replace('/[^A-z0-9\-\/\s\.\,ßÄäÜüÖöŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ]/', '', $searchTerm);
			// Get Results by searchTerm
			$this->feSession->store('searchTerm', $searchTerm);
			$terms = $this->termRepository->findBySearchTerm($searchTerm)->count() ? $this->termRepository->findBySearchTerm($searchTerm) : $this->addFlashMessage(LocalizationUtility::translate('error.noTerms', 'ecomglossary') . ' ' . $searchTerm, LocalizationUtility::translate('error.searchResult', 'ecomglossary'), \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
			// Send entered searchTerm back to view
			$this->view->assign('searchTerm', $searchTerm);
		}

		/**
		 * Letter Filter Handling
		 */
		// Filter terms by letter
		if ( is_string($filterByLetter) && strlen($filterByLetter) === 1 && preg_match('/[A-Za-z0-9]/', $filterByLetter) ) {
			$filterByLetter = ucfirst($filterByLetter);
			// Find by single leading letter
			if($this->termRepository->findByLeadingLetter($filterByLetter)->count()) {
				$terms = $this->termRepository->findByLeadingLetter($filterByLetter);
			} else {
				$filterByLetter = null;
			}
		} elseif ( $filterByLetter === '0-9' ) {
			// Find terms in 0-9 range
			$terms = $this->termRepository->findAllWithLeadingNumber();
		} elseif ( $filterByLetter != null) {
			// Error if pattern matching fails
			$this->addFlashMessage(LocalizationUtility::translate('error.forbiddenFilter','ecomglossary'), LocalizationUtility::translate('error.forbiddenFilter.heading','ecomglossary'), \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
			$filterByLetter = null;
		}
        
		/**
		 * Pass Variables to view
		 */
		$this->view->assignMultiple(array(
			'terms' => $terms, // Can vary => By search or sorting
			'allTerms' => $this->termRepository->findAll(), // Always complete list
			'filterByLetter' => $filterByLetter,
			'letterList' => $availableLetters,
			'termsPerPage' => $itemsPerPage = $this->settings['forceTermsPerPage'] ? $itemsPerPage = $this->settings['termsPerPage'] : (($this->itemsPerPage) ? $this->itemsPerPage : $this->settings['termsPerPage']),
			'itemsPerPageOptionSets' => $this->itemsPerPageOptionSets,
		));
	}

	/**
	 * action show
	 *
	 * @param \Ecom\Ecomglossary\Domain\Model\Term $term
	 * @return void
	 */
	public function showAction(\Ecom\Ecomglossary\Domain\Model\Term $term) {
		// Handle term visits by session vars
		//		for unique visits
		//		if ip is not in exclude list
		$visitedTermsFromSession = $this->feSession->get('visitedTerms');
		if ( (!is_array($visitedTermsFromSession) || (is_array($visitedTermsFromSession) && !in_array($term->getUid(), $visitedTermsFromSession))) && !$this->isExcludedIp ) {
			$visitedTermsFromSession[] = $term->getUid();
			$this->feSession->store('visitedTerms', $visitedTermsFromSession);
			$term->setVisits($term->getVisits() + 1);
			$this->updateAction($term);
		}

		// Prevent access to a single term (show action) if
		// the term uses an external Link as description. Redirects directly to the external Link
		if ( is_string($term->getExternalLink()) && $term->getExternalLink() ) {
			/** @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObjectRenderer */
			$contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
			$linkToExternalDescription = $contentObjectRenderer->typoLink_URL(array('parameter' => $term->getExternalLink()));
			$this->redirectToUri($linkToExternalDescription);
			return;
		}

		/**
		 * Finds all terms where the current term is set as related term.
		 */
		$termsRelatedToThisTerm = $this->termRepository->containsInRelatedTerms($term);

		/**
		 * Merges the related terms of this term
		 * 		with the terms where the current term is set as related term.
		 */
		/** @var \Ecom\Ecomglossary\Domain\Model\Term $termObject */
		foreach($termsRelatedToThisTerm as $termObject) {
			if ( $term->getRelatedTerms()->contains($termObject) ) continue;
			$term->addRelatedTerm($termObject);
		}
		$this->view->assign('term', $term);
	}

	/**
	 * Resets Filters
	 *
	 * @return void
	 */
	public function resetAction() {
		$this->feSession->store('searchTerm', null);
		$this->redirect('list');
	}

	/**
	 * update action
	 *
	 * @param \Ecom\Ecomglossary\Domain\Model\Term $term
	 * @return void
	 */
	public function updateAction(\Ecom\Ecomglossary\Domain\Model\Term $term) {
		$this->termRepository->update($term);
	}

	/**
	 * Generates a letter list from A-Z 0-9 out of the term repository
	 *
	 * @param \Ecom\Ecomglossary\Domain\Model\Term $domainModelObject
	 * @param bool $mergeWithEmptyLetters
	 * @return array
	 */
	public function generateLetterArrayFromList($domainModelObject, $mergeWithEmptyLetters = true) {
		$letterList = array();

		// Generate list with already available letters
		foreach ( $domainModelObject as $object ) {
			/** @var \Ecom\Ecomglossary\Domain\Model\Term $object */
			$title = $object->getTitle();
			$firstLetter = ucfirst(substr($this->convertUTF8toASCII($title),0,1));
			if ( preg_match('/[^A-Za-z]/', $firstLetter) || array_key_exists($firstLetter, $letterList) ) {
				if ( preg_match('/[0-9]/', $firstLetter) && !array_key_exists('0-9', $letterList) ) {
					$letterList['0-9'] = 'hasResult';
				}
				continue;
			}
			$letterList[$firstLetter] = 'hasResult';
		}

		// Fill up array with the empty letters which are not currently used in DB
		// For different styling via CSS, it adds the "empty"-value. (Editable in FLUID Condition => List view)
		if ( $mergeWithEmptyLetters ) {
			// Generate the complete alphabeth
			// And merge with array of already available letters
			$alphas = range('A', 'Z');
			foreach ( $alphas as $value ) {
				if ( array_key_exists($value, $letterList) ) {
					continue;
				}
				$letterList[$value] = 'empty';
			}
			// Add the empty 0-9 range if not already exists
			if ( !array_key_exists('0-9', $letterList) ) {
				$letterList['0-9'] = 'empty';
			}
		}

		// Sort array A-Z
		ksort($letterList);
		return $letterList;
	}

	/**
	 * Convert Special Chars
	 *
	 * @param $str
	 * @return string
	 */
	public function convertUTF8toASCII($str) {
		return strtr(utf8_decode($str),
			utf8_decode('ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ'),
			'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy');
	}
}