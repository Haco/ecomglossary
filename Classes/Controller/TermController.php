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
use \TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * TermController
 */
class TermController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * termRepository
	 *
	 * @var \Ecom\Ecomglossary\Domain\Repository\TermRepository
	 * @inject
	 */
	protected $termRepository = NULL;

	/**
	 * action list
	 * @param string $filterByLetter
	 * @return void
	 */
	public function listAction($filterByLetter = '') {
		$terms = $this->termRepository->findAll();
		// All available letters (for letter navigation)
		$availableLetters = ($terms->count() > 0) ? $this->generateLetterArrayFromList($this->objectToArray($terms,'uid', 'title'), $this->settings['showEmptyLetters']) : '';

		// Search for term
		if($this->request->hasArgument('searchTerm')) {
			$searchTerm = $this->request->getArgument('searchTerm');
			// Delete non-word chars
			$searchTerm = preg_replace('/[^A-z0-9\-\/ßÄäÜüÖö]/', '', $searchTerm);
			// Get Results by searchTerm
			$terms = ($this->termRepository->findBySearchTerm($searchTerm)->count() > 0) ? $this->termRepository->findBySearchTerm($searchTerm) : $this->addFlashMessage(LocalizationUtility::translate('error.noTerms', 'ecomglossary') . ' ' . $searchTerm, LocalizationUtility::translate('error.searchResult', 'ecomglossary'), \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
			// Send entered searchTerm back to view
			$this->view->assign('searchTerm', $searchTerm);
		}
		// Filter by letter
		if (is_string($filterByLetter) && strlen($filterByLetter) == 1) {
			$terms = $this->termRepository->findByLeadingLetter($filterByLetter);
		}

		$this->view->assignMultiple(array(
			'terms' => $terms,
			'filterByLetter' => $filterByLetter,
			'letterList' => $availableLetters,
		));
	}

	/**
	 * action show
	 *
	 * @param \Ecom\Ecomglossary\Domain\Model\Term $term
	 * @return void
	 */
	public function showAction(\Ecom\Ecomglossary\Domain\Model\Term $term) {
		// Prevent access to a single term (show action) if
		// the term uses an external Link as description. Redirects directly to the external Link
		if ($term->getExternalLink() != '') {
			/**
			 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObjectRenderer
			 */
			$contentObjectRenderer = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer');
			$linkToExternalDescription = $contentObjectRenderer->typoLink_URL(array('parameter' => $term->getExternalLink()));

			$this->redirectToUri($linkToExternalDescription);
		}
		$this->view->assign('term', $term);
	}

	/**
	 * Transform object to array with specified key-value-pairs
	 *
	 * @param $object
	 * @param string $propertyKey
	 * @param string $propertyValue
	 * @return array
	 */
	public function objectToArray($object, $propertyKey = 'uid', $propertyValue = 'name') {
		// Return if object ist not instance of countable
		if (!$object instanceof \Countable) return FALSE;
		$return = array();
		if ($object->count()) {
			$tableName = preg_replace('/^ecom/i', 'tx', strtolower(str_ireplace('\\', '_', get_class($object->getFirst()))));
			foreach ($object as $objectObject) {
				if (!ObjectAccess::isPropertyGettable($objectObject, $propertyKey) || (is_string($propertyValue) && !ObjectAccess::isPropertyGettable($objectObject, $propertyValue))) continue; // Check if property chosen for key exists
				if (is_array($propertyValue)) {
					$values = array();
					foreach ($propertyValue as $singlePropertyValue) {
						if (!ObjectAccess::isPropertyGettable($objectObject, $singlePropertyValue)) continue; // Check if property chosen for value exists
						$translation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($tableName . '.' . $singlePropertyValue . '.' . ObjectAccess::getPropertyPath($objectObject, $singlePropertyValue), $this->extensionName);
						$values[] = $translation ? $translation : ObjectAccess::getPropertyPath($objectObject, $singlePropertyValue);
					}
					$return[ObjectAccess::getPropertyPath($objectObject, $propertyKey)] = implode(', ', $values);
				} else {
					$translation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($tableName . '.' . $propertyValue . '.' . ObjectAccess::getPropertyPath($objectObject, $propertyValue), $this->extensionName);
					$return[ObjectAccess::getPropertyPath($objectObject, $propertyKey)] = $translation ? $translation : ObjectAccess::getPropertyPath($objectObject, $propertyValue);
				}
			}
		}
		return $return;
	}

	/**
	 * Generates a letter list from A-Z 0-9 out of the term repository
	 *
	 * @param array $list
	 * @param bool $mergeWithEmptyLetters
	 * @return array
	 */
	public function generateLetterArrayFromList($list, $mergeWithEmptyLetters = true) {
		$letterList = array();
		// Generate list with already available letters
		foreach($list as $uid => $title) {
			$firstLetter = ucfirst(substr($this->toASCII($title),0,1));
			if(preg_match('/[^A-Za-z0-9]/', $firstLetter)) {
				continue;
			}
			if (array_key_exists($firstLetter, $letterList)) {
				continue;
			}
			$letterList[$firstLetter] = 'hasResult';
		}
		// Fill up array with the empty letters which are not currently used in DB
		// For different styling via CSS, it adds the "empty"-value. (Editable in FLUID Condition => List view)
		if ($mergeWithEmptyLetters == true) {
			// Generate the complete alphabeth
			// And merge with array of already available letters
			$alphas = range('A', 'Z');
			foreach($alphas as $value) {
				if (array_key_exists($value, $letterList)) {
					continue;
				}
				$letterList[$value] = 'empty';
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
	 *
	 */
	public function toASCII($str) {
		return strtr(utf8_decode($str),
			utf8_decode('ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ'),
			'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy');
	}
}