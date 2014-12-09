<?php
namespace Ecom\Ecomglossary\Domain\Repository;


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
 * The repository for Terms
 */
class TermRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	/**
	 * Default Ordering
	 *
	 * @var array
	 */
	protected $defaultOrderings = array(
		'title' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
		'term_type' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
	);

	/**
	 * @param string $letter
	 *
	 * @return array|null|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByLeadingLetter($letter = '') {
		if ( !preg_match('/^\w$/is', $letter) ) {
			return NULL;
		}

		$query = $this->createQuery();

		return $query->matching(
			$query->like('title', $letter . '%', FALSE)
		)->execute();
	}

	/**
	 * @param string $term
	 *
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findBySearchTerm($term = '') {
		if ( !is_string($term) || trim($term) === '' ) {
			return $this->findAll();
		}

		$query = $this->createQuery();

		return $query->matching(
			$query->logicalOr(
				$query->like('title', $term, FALSE),
				$query->like('title', '%' . $term, FALSE),
				$query->like('title', $term . '%', FALSE),
				$query->equals('title', $term, FALSE)
			)
		)->execute();
	}
}