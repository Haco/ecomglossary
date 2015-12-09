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

use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * The repository for Terms
 */
class TermRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * Default Ordering
     *
     * @var array
     */
    protected $defaultOrderings = array(
        'title' => QueryInterface::ORDER_ASCENDING,
        'term_type' => QueryInterface::ORDER_DESCENDING
    );

    /**
     * @param string $letter
     *
     * @return array|null|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByLeadingLetter($letter = '')
    {
        if (!preg_match('/^\w$/is', $letter)) {
            return null;
        }

        $query = $this->createQuery();

        return $query->matching(
            $query->like('title', $letter . '%', false)
        )->execute();
    }

    /**
     * Find all terms starting with a leading number (Range: 0-9)
     *
     * @return array|null|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAllWithLeadingNumber()
    {
        $query = $this->createQuery();

        return $query->matching(
            $query->logicalOr(
                $query->like('title', '0%', false),
                $query->like('title', '1%', false),
                $query->like('title', '2%', false),
                $query->like('title', '3%', false),
                $query->like('title', '4%', false),
                $query->like('title', '5%', false),
                $query->like('title', '6%', false),
                $query->like('title', '7%', false),
                $query->like('title', '8%', false),
                $query->like('title', '9%', false)
            )
        )->execute();
    }

    /**
     * @param string $term
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findBySearchTerm($term = '')
    {
        if (!is_string($term) || trim($term) === '') {
            return $this->findAll();
        }

        $query = $this->createQuery();

        return $query->matching(
            $query->logicalOr(
                $query->like('title', $term, false),
                $query->like('title', '%' . $term, false),
                $query->like('title', $term . '%', false),
                $query->like('title', '%' . $term . '%', false),
                $query->equals('title', $term, false)
            )
        )->execute();
    }

    /**
     * @param string $term
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function containsInRelatedTerms($term = '')
    {
        $query = $this->createQuery();

        return $query->matching(
            $query->logicalOr(
                $query->contains('relatedTerms', $term)
            )
        )->execute();
    }
}
