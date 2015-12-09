<?php
namespace Ecom\Ecomglossary\ViewHelpers\Widget\Controller;

/*                                                                        *
 * This script is backported from the TYPO3 Flow package "TYPO3.Fluid".   *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;

class PaginateController extends \TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetController
{
    /**
     * @var array
     */
    protected $configuration = array(
        'itemsPerPage' => 10,
        'insertAbove' => false,
        'insertBelow' => true,
        'maximumNumberOfLinks' => 99,
        'displayResultRange' => true,
        'addQueryStringMethod' => ''
    );

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    protected $objects;

    /**
     * Override ItemsPerPage Selectbox:
     * "NumberOfItems:Label" Option sets for the "Items per Page" Select-Box.
     *
     * ===========================
     * E.g:
     *        array(
     *            0 => 'Default',
     *            5 => 5,
     *            10 => 10,
     *        );
     *=============================
     *
     * "0" is always the default value.
     *
     * @var array $itemsPerPageOptionSets
     */
    protected $itemsPerPageOptionSets;

    /**
     * @var integer $totalAmountOfObjects
     */
    protected $totalAmountOfObjects;

    /**
     * @var integer
     */
    protected $currentPage = 1;

    /**
     * @var integer
     */
    protected $maximumNumberOfLinks = 99;

    /**
     * @var integer
     */
    protected $numberOfPages = 1;

    /**
     * @return void
     */
    public function initializeAction()
    {
        $this->objects = $this->widgetConfiguration['objects'];
        $this->itemsPerPageOptionSets = $this->widgetConfiguration['itemsPerPageOptionSets'];
        $this->totalAmountOfObjects = $this->objects->count();
        ArrayUtility::mergeRecursiveWithOverrule(
            $this->configuration,
            $this->widgetConfiguration['configuration'],
            false
        );
        $this->numberOfPages = ceil(count($this->objects) / (int)$this->configuration['itemsPerPage']);
        $this->maximumNumberOfLinks = (int)$this->configuration['maximumNumberOfLinks'];
    }

    /**
     * Index Action
     * @param integer $currentPage
     *
     * @return void
     */
    public function indexAction($currentPage = 1)
    {
        // Set current page
        $this->currentPage = ($currentPage > $this->numberOfPages || $currentPage < 1) ? 1 : $currentPage;

        if ($this->currentPage > $this->numberOfPages) {
            // set $modifiedObjects to NULL if the page does not exist
            //$modifiedObjects = null;
        } else {
            // modify query
            $itemsPerPage = (int)$this->configuration['itemsPerPage'];
            $query = $this->objects->getQuery();
            $query->setLimit($itemsPerPage);
            if ($this->currentPage > 1) {
                $query->setOffset((int)($itemsPerPage * ($this->currentPage - 1)));
            }
            $modifiedObjects = $query->execute();
        }
        $itemsPerPage = ($itemsPerPage != '') ? $itemsPerPage : (int)$this->configuration['itemsPerPage'];

        // Calculate Display Range
        if ((bool)$this->configuration['displayResultRange']) {
            $displayRangeResults = array(
                ($itemsPerPage * ($this->currentPage - 1)) + 1,
                $itemsPerPage ? (($itemsPerPage * $this->currentPage) < $this->totalAmountOfObjects ? ($itemsPerPage * $this->currentPage) : $this->totalAmountOfObjects) : $this->totalAmountOfObjects
            );
        } else {
            $displayRangeResults = false;
        }

        $this->view->assignMultiple(
            array(
                'itemsPerPageOptionSets' => $this->itemsPerPageOptionSets,
                'itemsPerPage' => $itemsPerPage,
                'displayRange' => $displayRangeResults,
                'totalAmount' => $this->totalAmountOfObjects,
                'configuration' => $this->configuration,
                'pagination' => $this->buildPagination(),
                'contentArguments' => array(
                    $this->widgetConfiguration['as'] => $modifiedObjects
                )
            )
        );
    }

    /**
     * If a certain number of links should be displayed, adjust before and after
     * amounts accordingly.
     *
     * @return void
     */
    protected function calculateDisplayRange()
    {
        $maximumNumberOfLinks = ($this->maximumNumberOfLinks > $this->numberOfPages) ? $this->numberOfPages : $this->maximumNumberOfLinks;

        $delta = floor($maximumNumberOfLinks / 2);
        $this->displayRangeStart = $this->currentPage - $delta;
        $this->displayRangeEnd = $this->currentPage + $delta - ($maximumNumberOfLinks % 2 === 0 ? 1 : 0);
        if ($this->displayRangeStart < 1) {
            $this->displayRangeEnd -= $this->displayRangeStart - 1;
        }
        if ($this->displayRangeEnd > $this->numberOfPages) {
            $this->displayRangeStart -= $this->displayRangeEnd - $this->numberOfPages;
        }
        $this->displayRangeStart = (int)max($this->displayRangeStart, 1);
        $this->displayRangeEnd = (int)min($this->displayRangeEnd, $this->numberOfPages);
    }

    /**
     * Returns an array with the keys "pages", "current", "numberOfPages", "nextPage" & "previousPage"
     *
     * @return array
     */
    protected function buildPagination()
    {
        $this->calculateDisplayRange();
        $pages = array();
        for ($i = $this->displayRangeStart; $i <= $this->displayRangeEnd; $i++) {
            $pages[] = array('number' => $i, 'isCurrent' => $i === $this->currentPage);
        }
        $pagination = array(
            'pages' => $pages,
            'current' => $this->currentPage,
            'numberOfPages' => $this->numberOfPages,
            'displayRangeStart' => $this->displayRangeStart,
            'displayRangeEnd' => $this->displayRangeEnd,
            'hasLessPages' => $this->displayRangeStart > 2,
            'hasMorePages' => $this->displayRangeEnd + 1 < $this->numberOfPages
        );
        if ($this->currentPage < $this->numberOfPages) {
            $pagination['nextPage'] = $this->currentPage + 1;
        }
        if ($this->currentPage > 1) {
            if ($this->currentPage > $this->numberOfPages) {
                $pagination['previousPage'] = 0;
            } else {
                $pagination['previousPage'] = $this->currentPage - 1;
            }
        }

        return $pagination;
    }
}
