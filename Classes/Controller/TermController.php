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
	 * initializeAction
	 *
	 * @return
	 */
	public function initializeAction() {

	}

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
			$terms = ($this->termRepository->findBySearchTerm($searchTerm)->count() > 0) ? $this->termRepository->findBySearchTerm($searchTerm) : $this->addFlashMessage('No terms found. You searched for: ' . $searchTerm, 'Search Result', \TYPO3\CMS\Core\Messaging\AbstractMessage::NOTICE);
			// Send entered searchTerm back to view
			$this->view->assign('searchTerm', $searchTerm);
		}

		// Filter by letter
		if (is_string($filterByLetter) && strlen($filterByLetter) == 1) {
			$terms = $this->termRepository->findByLeadingLetter($filterByLetter);
		}

		$this->view->assignMultiple(array(
			'terms' => $terms,
			'letterList' => $availableLetters,
			'debug' => $debug
		));
	}

	/**
	 * action show
	 *
	 * @param \Ecom\Ecomglossary\Domain\Model\Term $term
	 * @return void
	 */
	public function showAction(\Ecom\Ecomglossary\Domain\Model\Term $term) {
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
				if (!\TYPO3\CMS\Extbase\Reflection\ObjectAccess::isPropertyGettable($objectObject, $propertyKey) || (is_string($propertyValue) && !\TYPO3\CMS\Extbase\Reflection\ObjectAccess::isPropertyGettable($objectObject, $propertyValue))) continue; // Check if property chosen for key exists
				if (is_array($propertyValue)) {
					$values = array();
					foreach ($propertyValue as $singlePropertyValue) {
						if (!\TYPO3\CMS\Extbase\Reflection\ObjectAccess::isPropertyGettable($objectObject, $singlePropertyValue)) continue; // Check if property chosen for value exists
						$translation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($tableName . '.' . $singlePropertyValue . '.' . \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getPropertyPath($objectObject, $singlePropertyValue), $this->extensionName);
						$values[] = $translation ? $translation : \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getPropertyPath($objectObject, $singlePropertyValue);
					}
					$return[\TYPO3\CMS\Extbase\Reflection\ObjectAccess::getPropertyPath($objectObject, $propertyKey)] = implode(', ', $values);
				} else {
					$translation = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($tableName . '.' . $propertyValue . '.' . \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getPropertyPath($objectObject, $propertyValue), $this->extensionName);
					$return[\TYPO3\CMS\Extbase\Reflection\ObjectAccess::getPropertyPath($objectObject, $propertyKey)] = $translation ? $translation : \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getPropertyPath($objectObject, $propertyValue);
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

		if ($mergeWithEmptyLetters == true) {
			// Generate full alphabeth
			// and merge with array of already available letters
			$alphas = range('A', 'Z');
			foreach($alphas as $value) {
				if (array_key_exists($value, $letterList)) {
					continue;
				}
				$letterList[$value] = 'empty';
			}
		}
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