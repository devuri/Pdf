<?php

/*
 * This file is part of the UCSDMath package.
 *
 * (c) 2015-2017 UCSD Mathematics | Math Computing Support <mathhelp@math.ucsd.edu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace UCSDMath\Pdf;

/**
 * Pdf is the default implementation of {@link PdfInterface} which
 * provides routine Pdf methods that are commonly used in the framework.
 *
 * {@link AbstractPdfAdapter} is basically a adapter for the mPDF library
 * which this class extends.
 *
 * Method list: (+) @api, (-) protected or private visibility.
 *
 * (+) PdfInterface __construct();
 * (+) void __destruct();
 * (+) string render();
 * (+) PdfInterface registerPageMargins();
 * (+) PdfInterface setFontSize(int $size);
 * (+) PdfInterface importPages(string $filePath);
 * (+) PdfInterface setFilename(string $filename);
 * (+) PdfInterface setOutputDestination(string $destination);
 * (+) PdfInterface initializePageSetup(string $pageSize = null, string $orientation = null);
 *
 * @author Daryl Eisner <deisner@ucsd.edu>
 */
class Pdf extends AbstractPdfAdapter implements PdfInterface
{
    /**
     * Constants.
     *
     * @var string VERSION The version number
     *
     * @api
     */
    const VERSION = '1.13.0';

    //--------------------------------------------------------------------------

    /**
     * Properties.
     */

    //--------------------------------------------------------------------------

    /**
     * Constructor.
     *
     * @api
     */
    public function __construct()
    {
        parent::__construct();
    }

    //--------------------------------------------------------------------------

    /**
     * Destructor.
     *
     * @api
     */
    public function __destruct()
    {
        parent::__destruct();
    }

    //--------------------------------------------------------------------------

    /**
     * Render the PDF to output.
     *
     * @return string
     *
     * @api
     */
    public function render(): string
    {
        /* finally render document */
        return $this->mpdf->Output($this->filename, $this->outputDestination);
    }

    //--------------------------------------------------------------------------

    /**
     * Registering the page size and margins.
     *
     * @return PdfInterface The current interface
     *
     * @api
     */
    public function registerPageMargins(): PdfInterface
    {
        $mpdf = $this->mpdf;

        /* Set the margins and page current page width */
        $mpdf->SetLeftMargin($this->marginLeft);
        $mpdf->SetTopMargin($this->marginTop);
        $mpdf->SetRightMargin($this->marginRight);
        $mpdf->SetAutoPageBreak(true, $this->marginBottom);
        $mpdf->margin_header = $this->marginHeader;
        $mpdf->margin_footer = $this->marginFooter;
        $mpdf->orig_lMargin = $mpdf->DeflMargin = $mpdf->lMargin = $this->marginLeft;
        $mpdf->orig_tMargin = $mpdf->tMargin = $this->marginTop;
        $mpdf->orig_rMargin = $mpdf->DefrMargin = $mpdf->rMargin = $this->marginRight;
        $mpdf->orig_bMargin = $mpdf->bMargin = $this->marginBottom;
        $mpdf->orig_hMargin = $mpdf->margin_header = $this->marginHeader;
        $mpdf->orig_fMargin = $mpdf->margin_footer = $this->marginFooter;
        $mpdf->pgwidth = $mpdf->w - $mpdf->lMargin - $mpdf->rMargin;

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Set the default font size.
     *
     * @param int $size The font size (pt.)
     *
     * @return PdfInterface The current interface
     *
     * @api
     */
    public function setFontSize(int $size): PdfInterface
    {
        $this->fontSize = (int) $size;
        $this->mpdf->SetDefaultFontSize($this->fontSize);

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Import external file pages and merge with current.
     *
     * @param string $filePath The path of the file to merge
     *
     * @return PdfInterface The current interface
     *
     * @throws \InvalidArgumentException if the parameter file does not exist
     *
     * @api
     */
    public function importPages(string $filePath): PdfInterface
    {
        if (!is_file($filePath)) {
            throw new \InvalidArgumentException(sprintf('The file "%s" does not exist.', $filePath));
        }
        $numberPagesCurrentFile = count($this->mpdf->pages);
        $numberPagesExternalFile = $this->mpdf->setSourceFile($filePath);
        $numberTotalPages = $numberPagesCurrentFile + $numberPagesExternalFile;
        $this->mpdf->setImportUse();

        for ($i = 1; $i <= $numberPagesExternalFile; $i++) {
            if ($i < $numberTotalPages) {
                $this->mpdf->addPage();
            }
            $this->mpdf->useTemplate($this->mpdf->importPage($i));
        }

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Set the document filename.
     *
     * @param string $filename The default document filename
     *
     * @return PdfInterface The current interface
     *
     * @api
     */
    public function setFilename(string $filename): PdfInterface
    {
        $this->setProperty('filename', $filename);

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Set the output destination.
     *
     * @param string $destination The destination to send the PDF
     *
     * @return PdfInterface The current interface
     *
     * @api
     */
    public function setOutputDestination(string $destination): PdfInterface
    {
        /**
         * Destinations can be sent to the following:
         *    - I/B [Inline]   - Sends output to browser (browser plug-in is used if avaialble)
         *                       If a $filename is given, the browser's "Save as..." option is provided
         *    - D   [Download] - Forces browser to download the file
         *    - F   [File]     - Saves the file to the server's filesystem cache
         *    - S   [String]   - Returns the PDF as a string
         */
        $this->setProperty(
            'outputDestination',
            strtoupper($destination[0]) === 'B' ? 'I' : strtoupper($destination[0])
        );

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Initialize a new PDF document by specifying page size and orientation.
     *
     * @param string $pageSize    The page size ('Letter','Legal','A4')
     * @param string $orientation The page orientation ('Portrait','Landscape')
     *
     * @return PdfInterface The current interface
     *
     * @api
     */
    public function initializePageSetup(string $pageSize = null, string $orientation = null): PdfInterface
    {
        in_array($pageSize, $this->pageTypes)
            ? $this->setProperty(
                'mpdf',
                new \mPDF(
                    'utf-8',
                    $pageSize . '-' . $orientation[0],
                    $this->fontSize,
                    $this->fontType,
                    $this->marginLeft,
                    $this->marginRight,
                    $this->marginTop,
                    $this->marginBottom,
                    $this->marginHeader,
                    $this->marginFooter,
                    $orientation[0]
                )
            )
            : $this->setProperty('mpdf', new \mPDF('UTF-8', 'Letter-P'));

        return $this;
    }

    //--------------------------------------------------------------------------
}
