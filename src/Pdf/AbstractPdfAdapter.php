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

use Carbon\Carbon;
use UCSDMath\Functions\ServiceFunctions;
use UCSDMath\Functions\ServiceFunctionsInterface;

/**
 * AbstractPdfAdapter provides an abstract base class implementation of {@link PdfInterface}.
 * This service groups a common code base implementation that Pdf extends.
 *
 * This component library is an adapter to the mPDF library.
 *
 * Method list: (+) @api, (-) protected or private visibility.
 *
 * (+) PdfInterface __construct();
 * (+) void __destruct();
 * (+) PdfInterface setPageSizeLegal();
 * (+) PdfInterface setPageAsPortrait();
 * (+) PdfInterface setPageSizeLetter();
 * (+) PdfInterface setPageAsLandscape();
 * (+) PdfInterface setFooter(array $data);
 * (+) PdfInterface setHeader(array $data);
 * (+) PdfInterface setMetaTitle(string $str);
 * (+) PdfInterface appendPageCSS(string $str);
 * (+) PdfInterface setMargins(array $setting);
 * (+) PdfInterface setMetaAuthor(string $str);
 * (+) PdfInterface setMarginTop(int $marginTop);
 * (+) PdfInterface setMetaCreator(string $str);
 * (+) PdfInterface setMetaSubject(string $str);
 * (+) PdfInterface setPageSize(string $pageSize);
 * (+) PdfInterface setMetaKeywords(array $words);
 * (+) PdfInterface appendPageContent(string $str);
 * (+) PdfInterface setMarginLeft(int $marginLeft);
 * (+) PdfInterface setMarginRight(int $marginRight);
 * (+) PdfInterface setMarginBottom(int $marginBottom);
 * (+) PdfInterface setMarginHeader(int $marginHeader);
 * (+) PdfInterface setMarginFooter(int $marginFooter);
 * (+) PdfInterface setFontType(string $fontname = null);
 * (+) array setFooterContent(string $column, string $str);
 * (+) PdfInterface setPageOrientation(string $orientation);
 * (-) string getFontFamily(string $fontname = null);
 * (-) PdfInterface registerPageFormat(string $pageSize = null, string $orientation = null);
 *
 * @author Daryl Eisner <deisner@ucsd.edu>
 */
abstract class AbstractPdfAdapter implements PdfInterface, ServiceFunctionsInterface
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
     *
     * @var    mPDF         $mpdf              The mPDF Interface
     * @var    string       $pageHeader        The page header content to render
     * @var    array        $pageFooter        The page footer content to render
     * @var    string       $characterEncoding The default character encoding
     * @var    int          $fontSize          The default font size specified in points (pt. [12], 14, 18, etc.)
     * @var    string       $fontType          The default font typeface ([Times], '','','')
     * @var    string       $filename          The default document or filename
     * @var    string       $outputDestination The default destination where to send the document ([I], D, F, S)
     * @var    string       $pageCSS           The page style setting
     * @var    string       $pageContent       The page content to render
     * @var    string       $pageSize          The page size (['Letter'],'Legal','A4','Tabloid', etc.)
     * @var    string       $pageFormat        The page size and orientation scheme (['Letter'],'Legal-L') based in millimetres (mm)
     * @var    string       $pageOrientation   The setup orientation (['Portrait'],'Landscape')
     * @var    int          $marginTop         The top margin size specified as length in millimetres (mm)
     * @var    int          $marginRight       The right margin size specified as length in millimetres (mm)
     * @var    int          $marginBottom      The bottom margin size specified as length in millimetres (mm)
     * @var    int          $marginLeft        The left margin size specified as length in millimetres (mm)
     * @var    int          $marginHeader      The header margin size specified as length in millimetres (mm)
     * @var    int          $marginFooter      The footer margin size specified as length in millimetres (mm)
     * @var    string       $metaTitle         The document title (e.g., metadata)
     * @var    string       $metaAuthor        The document author (e.g., metadata)
     * @var    string       $metaSubject       The document subject (e.g., metadata)
     * @var    string       $metaCreator       The document creator (e.g., metadata)
     * @var    string       $outputTypes       The output type (e.g., 'I': inline, 'D': download, 'F': file, 'S': string)
     * @var    array        $metaKeywords      The document list of descriptive keywords (e.g., metadata)
     * @static PdfInterface $instance          The static instance PdfInterface
     * @static int          $objectCount       The static count of PdfInterface
     * @var    array        $storageRegister   The stored set of data structures used by this class
     */
    protected $mpdf              = null;
    protected $pageHeader        = null;
    protected $pageFooter        = [];
    protected $characterEncoding = 'UTF-8';
    protected $fontSize          = 12;
    protected $fontType          = 'Times';
    protected $filename          = 'document.pdf';
    protected $outputDestination = 'I';
    protected $pageCSS           = null;
    protected $pageContent       = null;
    protected $pageSize          = 'Letter';
    protected $pageOrientation   = 'Portrait';
    protected $pageFormat        = 'Letter';
    protected $marginTop         = 11;
    protected $marginRight       = 15;
    protected $marginBottom      = 14;
    protected $marginLeft        = 11;
    protected $marginHeader      = 5;
    protected $marginFooter      = 9;
    protected $metaTitle         = null;
    protected $metaAuthor        = null;
    protected $metaSubject       = null;
    protected $metaCreator       = null;
    protected $metaKeywords      = [];
    protected $pageTypes         = ['Letter', 'Legal', 'A4', 'Tabloid'];
    protected $outputTypes       = ['I', 'D', 'F', 'S'];
    protected $orientationTypes  = ['Portrait', 'Landscape'];
    protected $fontFamily        = [
        'arial'         => "Arial, 'Helvetica Neue', Helvetica, sans-serif",
        'times'         => "TimesNewRoman, 'Times New Roman', Times, Baskerville, Georgia, serif",
        'tahoma'        => "Tahoma, Verdana, Segoe, Geneva, sans-serif",
        'georgia'       => "Georgia, Times, 'Times New Roman', serif",
        'trebuchet'     => "'Trebuchet MS', 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Helvetica, Tahoma, sans-serif",
        'courier'       => "'Courier New', Courier, 'Lucida Sans Typewriter', 'Lucida Typewriter', monospace",
        'lucida'        => "'Lucida Sans Typewriter', 'Lucida Console', monaco, 'Bitstream Vera Sans Mono', monospace",
        'lucida-bright' => "'Lucida Bright', Georgia, serif",
        'palatino'      => "'Palatino Linotype', 'Palatino LT STD', 'Book Antiqua', Palatino, Georgia, serif",
        'garamond'      => "Garamond, Baskerville, 'Baskerville Old Face', 'Hoefler Text', 'Times New Roman', serif",
        'verdana'       => "Verdana, Geneva, sans-serif",
        'console'       => "'Lucida Console', 'Lucida Sans Typewriter', Monaco, 'Bitstream Vera Sans Mono', monospace",
        'monaco'        => "'Lucida Console', 'Lucida Sans Typewriter', Monaco, 'Bitstream Vera Sans Mono', monospace",
        'helvetica'     => "'HelveticaNeue-Light', 'Helvetica Neue Light', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif",
        'calibri'       => "Calibri, Candara, Segoe, 'Segoe UI', Optima, Arial, sans-serif",
        'avant-garde'   => "'Avant Garde', Avantgarde, 'Century Gothic', CenturyGothic, AppleGothic, sans-serif",
        'cambria'       => "Cambria, Georgia, serif",
        'default'       => "Arial, 'Helvetica Neue', Helvetica, sans-serif"
    ];
    protected static $instance    = null;
    protected static $objectCount = 0;
    protected $storageRegister    = [];

    //--------------------------------------------------------------------------

    /**
     * Constructor.
     *
     * @api
     */
    public function __construct()
    {
    }

    //--------------------------------------------------------------------------

    /**
     * Destructor.
     *
     * @api
     */
    public function __destruct()
    {
        static::$objectCount--;
    }

    //--------------------------------------------------------------------------

    /**
     * Set the page header.
     *
     * @param array $data The list of header items ('left','right')
     *
     * @return PdfInterface The current instance
     *
     * @api
     */
    public function setHeader(array $data): PdfInterface
    {
        $string_right = str_replace("{{date(\"n/d/Y g:i A\")}}", Carbon::now()->format('n/d/Y g:i A'), $data['right']);
        $string_left  = str_replace("|", '<br>', $data['left']);

        $html = "<table border='0' cellspacing='0' cellpadding='0' width='100%'><tr>
                     <td style='font-family:arial;font-size:14px;font-weight:bold;'>$string_left</td>
                     <td style='font-size:13px;font-family:arial;text-align:right;font-style:italic;'>$string_right</td>
                 </tr></table><br>";

        $this->setProperty('pageHeader', $html);
        $this->appendPageContent($this->pageHeader);

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Set the page footer.
     *
     * {@note: printing even/odd pages is not available yet.}
     *
     * @param array  $data The list of footer items ('left','center','right')
     * @param string $side The option for unique even/odd page printing ('both','even','odd')
     *
     * @return PdfInterface The current instance
     *
     * @api
     */
    public function setFooter(array $data, string $side = 'both'): PdfInterface
    {
        $data = array_change_key_case($data, CASE_LOWER);

        $footer = ('<table width="100%" style="vertical-align: bottom; font-family: arial; font-size: 9pt; color: #000000; font-weight: bold; font-style: italic;"><tr>'.
                        $this->setFooterContent('left', (string) $data['left']).
                        $this->setFooterContent('center', (string) $data['center']).
                        $this->setFooterContent('right', (string) $data['right']).
                    '</tr></table>');

        $this->setProperty('pageFooter', $footer, $side);
        $this->mpdf->mirrorMargins = false;  // if unique sides -> true
        $this->mpdf->SetHTMLFooter($this->getProperty('pageFooter', $side), 'O'); // Odd = 'O', Even = 'E'

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Set the content for the page footer.
     *
     * @param string $str       The footer content item
     * @param string $alignment The footer placement [right, center, left]
     *
     * @return string
     *
     * @api
     */
    public function setFooterContent(string $alignment, string $str): string
    {
        return '<td width="33%" align="'.$alignment.'">'.
               '<span style="font-weight: bold; font-style: italic;">'.
                   str_replace(['{{ page("# of #") }}', '{{page("# of #")}}'], ['{PAGENO} of {nb}','{PAGENO} of {nb}'], $str).
               '</span></td>';
    }

    //--------------------------------------------------------------------------

    /**
     * Set the default document font.
     *
     * @param string $fontname The font name ('Times','Helvetica','Courier')
     *
     * @return PdfInterface The current instance
     *
     * @api
     */
    public function setFontType(string $fontname = null): PdfInterface
    {
        /**
         * Font sets to be used for PDF documents:
         *
         *   - Arial           - Times             - Tahoma
         *   - Georgia         - Trebuchet         - Courier
         *   - Lucida          - Lucida-Bright     - Palatino
         *   - Garamond        - Verdana           - Console
         *   - Monaco          - Helvetica         - Calibri
         *   - Avant-Garde     - Cambria
         */
        $this->setProperty('fontType', $this->getFontFamily(strtolower($fontname)));
        $this->mpdf->SetDefaultBodyCSS('font-family', $this->getProperty('fontType'));

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Return a specific font-family.
     *
     * @param string $fontname The font name type
     *
     * @return string
     *
     * @api
     */
    protected function getFontFamily(string $fontname = null): string
    {
        /**
         * Font sets to be used for PDF documents:
         *
         *   - Arial           - Times             - Tahoma
         *   - Georgia         - Trebuchet         - Courier
         *   - Lucida          - Lucida-Bright     - Palatino
         *   - Garamond        - Verdana           - Console
         *   - Monaco          - Helvetica         - Calibri
         *   - Avant-Garde     - Cambria
         */
        return array_key_exists(strtolower($fontname), $this->fontFamily)
            ? $this->fontFamily[strtolower($fontname)]
            : $this->fontFamily['default'];
    }

    //--------------------------------------------------------------------------

    /**
     * Append the HTML content.
     *
     * @param string $str The string data used for render
     *
     * @return PdfInterface The current instance
     *
     * @api
     */
    public function appendPageContent(string $str): PdfInterface
    {
        $this->setProperty('pageContent', $str);
        $this->mpdf->WriteHTML($this->pageContent);

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Set the top page margin.
     *
     * @param int $marginTop The top page margin
     *
     * @return PdfInterface The current instance
     *
     * @api
     */
    public function setMarginTop(int $marginTop): PdfInterface
    {
        $this->setProperty('marginTop', $marginTop);

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Set the right page margin.
     *
     * @param int $marginRight The right page margin
     *
     * @return PdfInterface The current instance
     *
     * @api
     */
    public function setMarginRight(int $marginRight): PdfInterface
    {
        $this->setProperty('marginRight', $marginRight);

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Set the bottom page margin.
     *
     * @param int $marginBottom The bottom page margin
     *
     * @return PdfInterface The current instance
     *
     * @api
     */
    public function setMarginBottom(int $marginBottom): PdfInterface
    {
        $this->setProperty('marginBottom', $marginBottom);

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Set the left page margin.
     *
     * @param int $marginLeft The left page margin
     *
     * @return PdfInterface The current instance
     *
     * @api
     */
    public function setMarginLeft(int $marginLeft): PdfInterface
    {
        $this->setProperty('marginLeft', $marginLeft);

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Set the header page margin.
     *
     * @param int $marginHeader The header page margin
     *
     * @return PdfInterface The current instance
     *
     * @api
     */
    public function setMarginHeader(int $marginHeader): PdfInterface
    {
        $this->setProperty('marginHeader', $marginHeader);

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Set the footer page margin.
     *
     * @param int $marginFooter The footer page margin
     *
     * @return PdfInterface The current instance
     *
     * @api
     */
    public function setMarginFooter(int $marginFooter): PdfInterface
    {
        $this->setProperty('marginFooter', $marginFooter);

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Set the page margins.
     *
     * @param array $setting The margin configiration setting
     *
     * @return PdfInterface The current instance
     *
     * @api
     */
    public function setMargins(array $setting): PdfInterface
    {
        $this->setProperty('marginTop', (int) $setting['marginTop']);
        $this->setProperty('marginRight', (int) $setting['marginRight']);
        $this->setProperty('marginBottom', (int) $setting['marginBottom']);
        $this->setProperty('marginLeft', (int) $setting['marginLeft']);
        $this->setProperty('marginHeader', (int) $setting['marginHeader']);
        $this->setProperty('marginFooter', (int) $setting['marginFooter']);

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Set the page size.
     *
     * @param string $pageSize The page format/size type ['Letter','Legal', etc.]
     *
     * @return PdfInterface The current instance
     *
     * @api
     */
    public function setPageSize(string $pageSize): PdfInterface
    {
        $this->setProperty('pageSize', $pageSize);
        $this->registerPageFormat();

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Append a CSS style.
     *
     * @param string $str The string data used for render
     *
     * @return PdfInterface The current instance
     *
     * @api
     */
    public function appendPageCSS(string $str): PdfInterface
    {
        $this->setProperty('pageCSS', $str);
        $this->mpdf->WriteHTML($this->pageCSS, 1);

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Generate and store a defined PDF page format.
     *
     * @param string $pageSize    The page format type ['Letter','Legal', etc.]
     * @param string $orientation The page orientation ['Portrait','Landscape']
     *
     * @return PdfInterface The current instance
     */
    protected function registerPageFormat(string $pageSize = null, string $orientation = null): PdfInterface
    {
        in_array($pageSize, $this->pageTypes)
            ? $this->setProperty('pageSize', $pageSize)
            : $this->setProperty('pageSize', static::DEFAULT_PAGE_SIZE);

        $this->setPageOrientation($orientation);

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Set the page orientation.
     *
     * @param string $orientation The page orientation ['Portrait','Landscape']
     *
     * @return PdfInterface The current instance
     */
    public function setPageOrientation(string $orientation): PdfInterface
    {
        $this->setProperty('pageOrientation', strtoupper($orientation[0]));

        $this->pageOrientation === 'L'
            ? $this->setProperty('pageFormat', $this->pageSize . '-' . $this->pageOrientation)
            : $this->setProperty('pageFormat', $this->pageSize);

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Set PDF Meta Title.
     *
     * @param string $str  The page title
     *
     * @return PdfInterface The current instance
     */
    public function setMetaTitle(string $str): PdfInterface
    {
        $this->setProperty('metaTitle', $str);
        $this->mpdf->SetTitle($this->metaTitle);

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Set PDF Meta Author.
     *
     * @param string $str  The page author
     *
     * @return PdfInterface The current instance
     */
    public function setMetaAuthor(string $str): PdfInterface
    {
        $this->setProperty('metaAuthor', $str);
        $this->mpdf->SetAuthor($this->metaAuthor);

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Set PDF Meta Creator.
     *
     * @param string $str  The page creator
     *
     * @return PdfInterface The current instance
     */
    public function setMetaCreator(string $str): PdfInterface
    {
        $this->setProperty('metaCreator', $str);
        $this->mpdf->SetCreator($this->metaCreator);

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Set PDF Meta Subject.
     *
     * @param string $str  The page subject
     *
     * @return PdfInterface The current instance
     */
    public function setMetaSubject(string $str): PdfInterface
    {
        $this->setProperty('metaSubject', $str);
        $this->mpdf->SetSubject($this->metaSubject);

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Set PDF Meta Key Words.
     *
     * @param array $words  The page key words
     *
     * @return PdfInterface The current instance
     */
    public function setMetaKeywords(array $words): PdfInterface
    {
        $this->setProperty('metaKeywords', array_merge($this->metaKeywords, $words));
        $this->mpdf->SetKeywords(implode(', ', $this->metaKeywords));

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Set PDF to Letter Size.
     *
     * @return PdfInterface The current instance
     */
    public function setPageSizeLetter(): PdfInterface
    {
        $this->setProperty('pageSize', 'Letter');

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Set PDF to Legal Size.
     *
     * @return PdfInterface The current instance
     */
    public function setPageSizeLegal(): PdfInterface
    {
        $this->setProperty('pageSize', 'Legal');

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Set PDF to Landscape.
     *
     * @return PdfInterface The current instance
     */
    public function setPageAsLandscape(): PdfInterface
    {
        $this->setProperty('pageOrientation', 'Landscape');
        $this->registerPageFormat();

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Set PDF to Portrait.
     *
     * @return PdfInterface The current instance
     */
    public function setPageAsPortrait(): PdfInterface
    {
        $this->setProperty('pageOrientation', 'Portrait');
        $this->registerPageFormat();

        return $this;
    }

    //--------------------------------------------------------------------------

    /**
     * Method implementations inserted:
     *
     * Method list: (+) @api, (-) protected or private visibility.
     *
     * (+) array all();
     * (+) object init();
     * (+) string version();
     * (+) bool isString($str);
     * (+) bool has(string $key);
     * (+) string getClassName();
     * (+) int getInstanceCount();
     * (+) array getClassInterfaces();
     * (+) mixed getConst(string $key);
     * (+) bool isValidUuid(string $uuid);
     * (+) bool isValidEmail(string $email);
     * (+) bool isValidSHA512(string $hash);
     * (+) mixed __call($callback, $parameters);
     * (+) bool doesFunctionExist($functionName);
     * (+) bool isStringKey(string $str, array $keys);
     * (+) mixed get(string $key, string $subkey = null);
     * (+) mixed getProperty(string $name, string $key = null);
     * (+) object set(string $key, $value, string $subkey = null);
     * (+) object setProperty(string $name, $value, string $key = null);
     * (-) Exception throwExceptionError(array $error);
     * (-) InvalidArgumentException throwInvalidArgumentExceptionError(array $error);
     */
    use ServiceFunctions;

    //--------------------------------------------------------------------------
}
