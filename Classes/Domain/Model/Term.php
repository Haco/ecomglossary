<?php
namespace Ecom\Ecomglossary\Domain\Model;


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
 * Glossary Word / Term
 */
class Term extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * title
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * termType
	 *
	 * @var integer
	 */
	protected $termType = 0;

	/**
	 * shortDescription
	 *
	 * @var string
	 */
	protected $shortDescription = '';

	/**
	 * description
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * externalLink
	 *
	 * @var string
	 */
	protected $externalLink = '';

	/**
	 * sources
	 *
	 * @var string
	 */
	protected $sources = '';

	/**
	 * Related terms.
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Ecom\Ecomglossary\Domain\Model\Term>
	 */
	protected $relatedTerms = NULL;

	/**
	 * __construct
	 */
	public function __construct() {
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
	}

	/**
	 * Initializes all ObjectStorage properties
	 * Do not modify this method!
	 * It will be rewritten on each save in the extension builder
	 * You may modify the constructor of this class instead
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		$this->relatedTerms = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * Returns the title
	 *
	 * @return string $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the title
	 *
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Returns the termType
	 *
	 * @return integer $termType
	 */
	public function getTermType() {
		return $this->termType;
	}

	/**
	 * Sets the termType
	 *
	 * @param integer $termType
	 * @return void
	 */
	public function setTermType($termType) {
		$this->termType = $termType;
	}

	/**
	 * Returns the shortDescription
	 *
	 * @return string $shortDescription
	 */
	public function getShortDescription() {
		return $this->shortDescription;
	}

	/**
	 * Sets the shortDescription
	 *
	 * @param string $shortDescription
	 * @return void
	 */
	public function setShortDescription($shortDescription) {
		$this->shortDescription = $shortDescription;
	}

	/**
	 * Returns the description
	 *
	 * @return string $description
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Sets the description
	 *
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * Returns the externalLink
	 *
	 * @return string $externalLink
	 */
	public function getExternalLink() {
		return $this->externalLink;
	}

	/**
	 * Sets the externalLink
	 *
	 * @param string $externalLink
	 * @return void
	 */
	public function setExternalLink($externalLink) {
		$this->externalLink = $externalLink;
	}

	/**
	 * Returns the sources
	 *
	 * @return string $sources
	 */
	public function getSources() {
		return $this->sources;
	}

	/**
	 * Sets the sources
	 *
	 * @param string $sources
	 * @return void
	 */
	public function setSources($sources) {
		$this->sources = $sources;
	}

	/**
	 * Adds a Term
	 *
	 * @param \Ecom\Ecomglossary\Domain\Model\Term $relatedTerm
	 * @return void
	 */
	public function addRelatedTerm(\Ecom\Ecomglossary\Domain\Model\Term $relatedTerm) {
		$this->relatedTerms->attach($relatedTerm);
	}

	/**
	 * Removes a Term
	 *
	 * @param \Ecom\Ecomglossary\Domain\Model\Term $relatedTermToRemove The Term to be removed
	 * @return void
	 */
	public function removeRelatedTerm(\Ecom\Ecomglossary\Domain\Model\Term $relatedTermToRemove) {
		$this->relatedTerms->detach($relatedTermToRemove);
	}

	/**
	 * Returns the relatedTerms
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Ecom\Ecomglossary\Domain\Model\Term> $relatedTerms
	 */
	public function getRelatedTerms() {
		return $this->relatedTerms;
	}

	/**
	 * Sets the relatedTerms
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Ecom\Ecomglossary\Domain\Model\Term> $relatedTerms
	 * @return void
	 */
	public function setRelatedTerms(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $relatedTerms) {
		$this->relatedTerms = $relatedTerms;
	}
}