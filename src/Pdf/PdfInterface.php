<?php

/*
 * This file is part of the UCSDMath package.
 *
 * (c) UCSD Mathematics | Math Computing Support <mathhelp@math.ucsd.edu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace UCSDMath\Pdf;

/**
 * PdfInterface is the interface implemented by all Pdf classes.
 *
 * @author Daryl Eisner <deisner@ucsd.edu>
 */
interface PdfInterface
{
    /**
     * Constants.
     *
     * @var string DEFAULT_CHARSET           A default charater setting
     * @var string DEFAULT_PAGE_SIZE         A default page size
     * @var string DEFAULT_PAGE_ORIENTATION  A default page orientation
     */
    const DEFAULT_CHARSET = 'UTF-8';
    const DEFAULT_PAGE_SIZE = 'Letter';
    const DEFAULT_FILENAME = 'document.pdf';
    const DEFAULT_PAGE_ORIENTATION = 'Portrait';
    const DEFAULT_OUTPUT_DESTINATION = 'B';

    // --------------------------------------------------------------------------

    /**
     * Initialize a new PDF document by specifying page size and orientation.
     *
     * @param string $pageSize     A page size ('Letter','Legal','A4')
     * @param string $orientation  A page orientation ('Portrait','Landscape')
     *
     * @return PdfInterface
     *
     * @api
     */
    public function initializePageSetup(string $pageSize = null, string $orientation = null): PdfInterface;

    // --------------------------------------------------------------------------

    /**
     * Set the page header.
     *
     * @param array $data  A list of header items ('left','right')
     *
     * @return PdfInterface
     *
     * @api
     */
    public function setHeader(array $data): PdfInterface;

    // --------------------------------------------------------------------------

    /**
     * Set the page footer.
     *
     * @param array $data  A list of footer items ('left','center','right')
     *
     * @return PdfInterface
     *
     * @api
     */
    public function setFooter(array $data): PdfInterface;

    // --------------------------------------------------------------------------

    /**
     * Set the output destination.
     *
     * @param string $destination  A destination to send the PDF
     *
     * @return PdfInterface
     *
     * @api
     */
    public function setOutputDestination(string $destination): PdfInterface;

    // --------------------------------------------------------------------------

    /**
     * Set the document filename.
     *
     * @param string $filename  A default document filename
     *
     * @return PdfInterface
     *
     * @api
     */
    public function setFilename(string $filename): PdfInterface;

    // --------------------------------------------------------------------------

    /**
     * Set the default document font.
     *
     * @param string $fontname  A font name ('Times','Helvetica','Courier')
     *
     * @return PdfInterface
     *
     * @api
     */
    public function setFontType(string $fontname = null): PdfInterface;

    // --------------------------------------------------------------------------

    /**
     * Append the HTML content.
     *
     * @param string $str  A string data used for render
     *
     * @return PdfInterface
     *
     * @api
     */
    public function appendPageContent(string $str): PdfInterface;

    // --------------------------------------------------------------------------

    /**
     * Render the PDF to output.
     *
     * @return string
     *
     * @api
     */
    public function render(): string;

    // --------------------------------------------------------------------------

    /**
     * Set the default font size.
     *
     * @param int $size  A font size (pt.)
     *
     * @return PdfInterface
     *
     * @api
     */
    public function setFontSize(int $size): PdfInterface;

    // --------------------------------------------------------------------------

    /**
     * Set the top page margin.
     *
     * @param int $marginTop  A top page margin
     *
     * @return PdfInterface
     *
     * @api
     */
    public function setMarginTop(int $marginTop): PdfInterface;

    // --------------------------------------------------------------------------

    /**
     * Set the right page margin.
     *
     * @param int $marginRight  A right page margin
     *
     * @return PdfInterface
     *
     * @api
     */
    public function setMarginRight(int $marginRight): PdfInterface;

    // --------------------------------------------------------------------------

    /**
     * Set the bottom page margin.
     *
     * @param int $marginBottom  A bottom page margin
     *
     * @return PdfInterface
     *
     * @api
     */
    public function setMarginBottom(int $marginBottom): PdfInterface;

    // --------------------------------------------------------------------------

    /**
     * Set the left page margin.
     *
     * @param int $marginLeft  A left page margin
     *
     * @return PdfInterface
     *
     * @api
     */
    public function setMarginLeft(int $marginLeft): PdfInterface;

    // --------------------------------------------------------------------------

    /**
     * Set the header page margin.
     *
     * @param int $marginHeader  A header page margin
     *
     * @return PdfInterface
     *
     * @api
     */
    public function setMarginHeader(int $marginHeader): PdfInterface;

    // --------------------------------------------------------------------------

    /**
     * Set the footer page margin.
     *
     * @param int $marginFooter  A footer page margin
     *
     * @return PdfInterface
     *
     * @api
     */
    public function setMarginFooter(int $marginFooter): PdfInterface;

    // --------------------------------------------------------------------------

    /**
     * Set the page margins.
     *
     * @param array $setting  A margin configiration setting
     *
     * @return PdfInterface
     *
     * @api
     */
    public function setMargins(array $setting): PdfInterface;

    // --------------------------------------------------------------------------

    /**
     * Set the page size.
     *
     * @param string $pageSize  A page format/size type ['Letter','Legal', etc.]
     *
     * @return PdfInterface
     *
     * @api
     */
    public function setPageSize(string $pageSize): PdfInterface;

    // --------------------------------------------------------------------------

    /**
     * Append a CSS style.
     *
     * @param string $str  A string data used for render
     *
     * @return PdfInterface
     *
     * @api
     */
    public function appendPageCSS(string $str): PdfInterface;

    // --------------------------------------------------------------------------
}
