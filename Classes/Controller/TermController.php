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

require \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('ecom_toolbox') . 'vendor/autoload.php';

use Jaybizzle\CrawlerDetect\CrawlerDetect;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Messaging\AbstractMessage;

/**
 * TermController
 */
class TermController extends ActionController
{
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
     * Holds the number of ItemsPerPage from session or postVars
     */
    protected $itemsPerPage = null;

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
    protected function initializeAction()
    {
        $this->feSession->setStorageKey($this->extensionName);

        // Explodes the developer IPs.
        $excludedIpsArray = $this->settings['excludeIpsForVisits'] ? GeneralUtility::trimExplode(',', $this->settings['excludeIpsForVisits'], true) : [];
        if ($GLOBALS['_SERVER']['REMOTE_ADDR']) {
            $this->isExcludedIp = in_array($GLOBALS['_SERVER']['REMOTE_ADDR'], $excludedIpsArray) ? true : false;
        }

        // Items per Page
        if ($this->request->hasArgument('termsPerPage') && ($itemsPerPage = (int)$this->request->getArgument('termsPerPage'))) {
            $this->feSession->store('itemsPerPage', $itemsPerPage);
        } elseif ($this->feSession->get('itemsPerPage')) {
            $itemsPerPage = $this->feSession->get('itemsPerPage');
        } else {
            $itemsPerPage = $this->settings['termsPerPage'];
        }

        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * action list
     *
     * @param string $filterByLetter
     * @return void
     */
    public function listAction($filterByLetter = null)
    {
        // Reset Search when filtered by letter
        $searchTermFromSession = $filterByLetter ? null : $this->feSession->get('searchTerm');

        // Reset Letter Filter after search request
        if ($this->request->hasArgument('searchTerm')) {
            $filterByLetter = null;
        }

        /**
         * Repository Handling
         */
        $terms = $this->termRepository->findAll();
        // Generate Letter Navigation (all available terms / not modified)
        $availableLetters = $terms->count() ? $this->generateLetterArrayFromList($this->settings['showEmptyLetters']) : null;

        /**
         * Search Request Handling
         */
        // Search by term
        if ($this->request->hasArgument('searchTerm') || $searchTermFromSession) {
            $searchTerm = $this->request->hasArgument('searchTerm') ? $this->request->getArgument('searchTerm') : $searchTermFromSession;
            // Delete non-word chars
            $searchTerm = preg_replace('/[^A-z0-9\-\/\s\.\,ßÄäÜüÖöŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ]/', '', $searchTerm);

            // Get Results by searchTerm
            $this->feSession->store('searchTerm', $searchTerm);
            if ($this->termRepository->findBySearchTerm($searchTerm)->count()) {
                $terms = $this->termRepository->findBySearchTerm($searchTerm);
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate('error.noTerms', 'ecomglossary') . ' ' . $searchTerm,
                    LocalizationUtility::translate('error.searchResult', 'ecomglossary'),
                    AbstractMessage::WARNING
                );
            }

            // Send entered searchTerm back to view
            $this->view->assign('searchTerm', $searchTerm);
        }

        /**
         * Letter Filter Handling
         */
        // Filter terms by letter
        if (is_string($filterByLetter) && strlen($filterByLetter) === 1 && preg_match('/[A-Za-z0-9]/', $filterByLetter)) {
            $filterByLetter = ucfirst($filterByLetter);
            // Find by single leading letter
            if ($this->termRepository->findByLeadingLetter($filterByLetter)->count()) {
                $terms = $this->termRepository->findByLeadingLetter($filterByLetter);
            } else {
                $filterByLetter = null;
            }
        } elseif ($filterByLetter === '0-9') {
            // Find terms in 0-9 range
            $terms = $this->termRepository->findAllWithLeadingNumber();
        } elseif ($filterByLetter != null) {
            // Error if pattern matching fails
            $this->addFlashMessage(
                LocalizationUtility::translate('error.forbiddenFilter', 'ecomglossary'),
                LocalizationUtility::translate('error.forbiddenFilter.heading', 'ecomglossary'),
                \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
            );
            $filterByLetter = null;
        }

        /**
         * Pass Variables to view
         */
        $this->view->assignMultiple(
            array(
                'terms' => $terms, // Can vary => By search or sorting
                'allTerms' => $this->termRepository->findAll(), // Always complete list
                'filterByLetter' => $filterByLetter,
                'letterList' => $availableLetters,
                'termsPerPage' => $this->itemsPerPage
            )
        );
    }

    /**
     * action show
     *
     * @param \Ecom\Ecomglossary\Domain\Model\Term $term
     * @return void
     */
    public function showAction(\Ecom\Ecomglossary\Domain\Model\Term $term = null)
    {
        // Redirect to default action if no valid object is given
        if (!$term instanceof \Ecom\Ecomglossary\Domain\Model\Term) {
            $this->redirectToUri($this->uriBuilder->setUseCacheHash(false)->reset()->build());
        }

        // Increase Views / Visits
        $this->updateViews($term);

        // Prevent access to a single term (show action)
        // if the term uses an external link as description. Redirects directly to the external link
        if (is_string($term->getExternalLink()) && $term->getExternalLink()) {
            /** @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObjectRenderer */
            $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            $linkToExternalDescription = $contentObjectRenderer->typoLink_URL(array('parameter' => $term->getExternalLink()));
            $this->redirectToUri($linkToExternalDescription);
            return;
        }

        // Finds all terms where the current term is set as related term.
        $termsRelatedToThisTerm = $this->termRepository->containsInRelatedTerms($term);

        /**
         * Merges the related terms of this term
         * with the terms where the current term is set as related term.
         */
        /** @var \Ecom\Ecomglossary\Domain\Model\Term $termObject */
        foreach ($termsRelatedToThisTerm as $termObject) {
            if ($term->getRelatedTerms()->contains($termObject)) continue;
            $term->addRelatedTerm($termObject);
        }
        $this->view->assign('term', $term);
    }

    /**
     * Resets Filters
     *
     * @return void
     */
    public function resetAction()
    {
        $this->feSession->store('searchTerm', null);
        $this->redirectToUri($this->uriBuilder->setUseCacheHash(false)->uriFor('list'));
    }

    /**
     * Update Term Views/Visits
     *
     * @param \Ecom\Ecomglossary\Domain\Model\Term $term
     * @return void
     */
    public function updateViews(\Ecom\Ecomglossary\Domain\Model\Term $term)
    {
        $crawlerDetect = new CrawlerDetect();
        if (!$crawlerDetect->isCrawler()) {
            //  Handle term visits by session (unique & not excluded IPs)
            $visitedTermsFromSession = $this->feSession->get('visitedTerms');

            if ((!is_array($visitedTermsFromSession) || (is_array($visitedTermsFromSession) && !in_array($term->getUid(), $visitedTermsFromSession))) && !$this->isExcludedIp) {
                $visitedTermsFromSession[] = $term->getUid();
                $this->feSession->store('visitedTerms', $visitedTermsFromSession);
                $term->setVisits($term->getVisits() + 1);
                $this->termRepository->update($term);

                /** @var PersistenceManager $persistenceManager */
                $persistenceManager = $this->objectManager->get(PersistenceManager::class);
                $persistenceManager->persistAll();
            }
        } else {
            $this->feSession->delete('visitedTerms');
        }
    }

    /**
     * Generates a letter list from A-Z 0-9 out of the term repository
     *
     * @param bool $showEmptyLetters
     * @return array
     */
    public function generateLetterArrayFromList($showEmptyLetters = true)
    {
        // Umlaute are automatically respected by RepositoryQuery
        $letterList = array();
        $alphas = range('A', 'Z');

        foreach ($alphas as $alpha) {
            if (($amount = $this->termRepository->findByLeadingLetter($alpha)->count())) {
                $letterList[$alpha] = 'hasResult';
            } else {
                if ($showEmptyLetters) {
                    $letterList[$alpha] = 'empty';
                }
            }
        }

        if ($this->termRepository->findAllWithLeadingNumber()->count()) {
            $letterList['0-9'] = 'hasResult';
        } else {
            if ($showEmptyLetters) {
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
    public function convertUTF8toASCII($str)
    {
        return strtr(
            utf8_decode($str),
            utf8_decode('ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ'),
            'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy'
        );
    }
}
