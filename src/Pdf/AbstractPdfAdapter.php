<?php
declare(strict_types=1);

/*
 * This file is part of the UCSDMath package.
 *
 * (c) UCSD Mathematics | Math Computing Support <mathhelp@math.ucsd.edu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace UCSDMath\Pdf;

use Carbon\Carbon;
use UCSDMath\Functions\ServiceFunctions;
use UCSDMath\Functions\ServiceFunctionsInterface;

/**
 * AbstractPdfAdapter provides an abstract base class implementation of {@link PdfInterface}.
 * Primarily, this services the fundamental implementations for all Pdf classes.
 *
 * This component library is an adapter to the mPDF library.
 *
 * Method list: (+) @api, (-) protected or private visibility. (+) @api, (-) protected or private.
 *
 * (+) PdfInterface __construct();
 * (+) void __destruct();
 * (+) render();
 * (+) setPageSizeLegal();
 * (+) setPageAsPortrait();
 * (+) setPageSizeLetter();
 * (+) appendPageCSS($str);
 * (+) setPageAsLandscape();
 * (+) registerPageMargins();
 * (+) appendPageContent($str);
 * (+) setMetaTitle($str = null);
 * (+) setFontSize($size = null);
 * (+) setMetaAuthor($str = null);
 * (+) setMetaCreator($str = null);
 * (+) setMetaSubject($str = null);
 * (+) setHeader(array $data = null);
 * (+) setFooter(array $data = null);
 * (+) setFilename($filename = null);
 * (+) setFontType($fontname = null);
 * (+) setPageSize($pageSize = null);
 * (+) getFontFamily($fontname = null);
 * (+) setMarginTop($marginTop = null);
 * (+) setMargins(array $setting = null);
 * (+) setMarginLeft($marginLeft = null);
 * (+) setMarginRight($marginRight = null);
 * (+) setMetaKeywords(array $words = null);
 * (+) setMarginBottom($marginBottom = null);
 * (+) setMarginHeader($marginHeader = null);
 * (+) setMarginFooter($marginFooter = null);
 * (+) setOutputDestination($destination = null);
 * (+) registerPageFormat($pageSize = null, $orientation = null);
 * (+) initializePageSetup($pageSize = null, $orientation = null);
 *
 * @author Daryl Eisner <deisner@ucsd.edu>
 */
abstract class AbstractPdfAdapter implements PdfInterface, ServiceFunctionsInterface
{
    /**
     * Constants.
     *
     * @var string VERSION  A version number
     *
     * @api
     */
    const VERSION = '1.6.0';

    // --------------------------------------------------------------------------

    /**
     * Properties.
     *
     * @var    mPDF         $mpdf               A mPDF Interface instance
     * @var    array        $pageHeader         A page header content to render
     * @var    array        $pageFooter         A page footer content to render
     * @var    string       $characterEncoding  A default character encoding
     * @var    int          $fontSize           A default font size specified in points (pt. [12], 14, 18, etc.)
     * @var    string       $fontType           A default font typeface ([Times], '','','')
     * @var    string       $filename           A default document or filename
     * @var    string       $outputDestination  A default destination where to send the document ([I], D, F, S)
     * @var    string       $pageCSS            A page style setting
     * @var    string       $pageContent        A page content to render
     * @var    string       $pageSize           A page size (['Letter'],'Legal','A4','Tabloid', etc.)
     * @var    string       $pageFormat         A page size and orientation scheme (['Letter'],'Legal-L') based in millimetres (mm)
     * @var    string       $pageOrientation    A setup orientation (['Portrait'],'Landscape')
     * @var    int          $marginTop          A top margin size specified as length in millimetres (mm)
     * @var    int          $marginRight        A right margin size specified as length in millimetres (mm)
     * @var    int          $marginBottom       A bottom margin size specified as length in millimetres (mm)
     * @var    int          $marginLeft         A left margin size specified as length in millimetres (mm)
     * @var    int          $marginHeader       A header margin size specified as length in millimetres (mm)
     * @var    int          $marginFooter       A footer margin size specified as length in millimetres (mm)
     * @var    string       $metaTitle          A document title (e.g., metadata)
     * @var    string       $metaAuthor         A document author (e.g., metadata)
     * @var    string       $metaSubject        A document subject (e.g., metadata)
     * @var    string       $metaCreator        A document creator (e.g., metadata)
     * @var    string       $outputTypes        A output type (e.g., 'I': inline, 'D': download, 'F': file, 'S': string)
     * @var    array        $metaKeywords       A document list of descriptive keywords (e.g., metadata)
     * @var    array        $storageRegister    A set of validation stored data elements
     * @static PdfInterface $instance           A PdfInterface instance
     * @static integer      $objectCount        A PdfInterface instance count
     */
    protected $mpdf               = null;
    protected $pageHeader         = array();
    protected $pageFooter         = array();
    protected $characterEncoding  = 'UTF-8';
    protected $fontSize           = 12;
    protected $fontType           = 'Times';
    protected $filename           = 'document.pdf';
    protected $outputDestination  = 'I';
    protected $pageCSS            = null;
    protected $pageContent        = null;
    protected $pageSize           = 'Letter';
    protected $pageOrientation    = 'Portrait';
    protected $pageFormat         = 'Letter';
    protected $marginTop          = 11;
    protected $marginRight        = 15;
    protected $marginBottom       = 14;
    protected $marginLeft         = 11;
    protected $marginHeader       = 5;
    protected $marginFooter       = 9;
    protected $metaTitle          = null;
    protected $metaAuthor         = null;
    protected $metaSubject        = null;
    protected $metaCreator        = null;
    protected $metaKeywords       = array();
    protected $storageRegister    = array();
    protected $pageTypes          = ['Letter','Legal','A4','Tabloid'];
    protected $outputTypes        = ['I', 'D', 'F', 'S'];

    protected $orientationTypes   = ['Portrait', 'Landscape'];
    protected $fontFamily         = [
            'arial' => "Arial, 'Helvetica Neue', Helvetica, sans-serif",
            'times' => "TimesNewRoman, 'Times New Roman', Times, Baskerville, Georgia, serif",
            'tahoma' => "Tahoma, Verdana, Segoe, Geneva, sans-serif",
            'georgia' => "Georgia, Times, 'Times New Roman', serif",
            'trebuchet' => "'Trebuchet MS', 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Helvetica, Tahoma, sans-serif",
            'courier' => "'Courier New', Courier, 'Lucida Sans Typewriter', 'Lucida Typewriter', monospace",
            'lucida' => "'Lucida Sans Typewriter', 'Lucida Console', monaco, 'Bitstream Vera Sans Mono', monospace",
            'lucida-bright' => "'Lucida Bright', Georgia, serif",
            'palatino' => "'Palatino Linotype', 'Palatino LT STD', 'Book Antiqua', Palatino, Georgia, serif",
            'garamond' => "Garamond, Baskerville, 'Baskerville Old Face', 'Hoefler Text', 'Times New Roman', serif",
            'verdana' => "Verdana, Geneva, sans-serif",
            'console' => "'Lucida Console', 'Lucida Sans Typewriter', Monaco, 'Bitstream Vera Sans Mono', monospace",
            'monaco' => "'Lucida Console', 'Lucida Sans Typewriter', Monaco, 'Bitstream Vera Sans Mono', monospace",
            'helvetica' => "'HelveticaNeue-Light', 'Helvetica Neue Light', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif",
            'calibri' => "Calibri, Candara, Segoe, 'Segoe UI', Optima, Arial, sans-serif",
            'avant-garde' => "'Avant Garde', Avantgarde, 'Century Gothic', CenturyGothic, AppleGothic, sans-serif",
            'cambria' => "Cambria, Georgia, serif",
            'default' => "Arial, 'Helvetica Neue', Helvetica, sans-serif"
        ];
    protected static $instance    = null;
    protected static $objectCount = 0;

    // --------------------------------------------------------------------------

    /**
     * Constructor.
     *
     * @api
     */
    public function __construct()
    {
        static::$instance = $this;
        static::$objectCount++;
    }

    // --------------------------------------------------------------------------

    /**
     * Destructor.
     *
     * @api
     */
    public function __destruct()
    {
        static::$objectCount--;
    }

    // --------------------------------------------------------------------------

    /**
     * Initialize a new PDF document by specifying page size and orientation.
     *
     * @param array $pageSize     A page size
     * @param array $orientation  A page orientation
     *
     * @return PdfInterface
     *
     * @api
     */
    public function initializePageSetup($pageSize = null, $orientation = null): PdfInterface
    {
        in_array($pageSize, $this->pageTypes)
            ? $this->setProperty(
                'mpdf',
                new \mPDF(
                    'UTF-8',
                    $pageSize.'-'.$orientation[0],
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
    public function setFooter(array $data): PdfInterface
    {

        $string_right  = str_replace("{{page(\"# of #\")}}", '{PAGENO} of {nb}', $data['right']);
        $string_left   = str_replace("|", '<br>', $data['left']);
        $string_center = str_replace("|", '<br>', $data['center']);

        $footer = [
            'odd' => [
                'L' => [
                    'content'     => "<strong>$string_left</strong>",
                    'font-size'   => 9,
                    'font-style'  => '',
                    'font-family' => 'Arial',
                    'color'       => '#000000'
                ],

                'C' => [
                    'content'     => "<strong>$string_center</strong>",
                    'font-size'   => 9,
                    'font-style'  => 'I',
                    'font-family' => 'Arial',
                    'color'       => '#000000'
                ],

                'R' => [
                    'content'     => "<strong>$string_right</strong>",
                    'font-size'   => 9,
                    'font-style'  => '',
                    'font-family' => 'Arial',
                    'color'       => '#000000'
                ],

                'line' => true,
            ],
            'even' => []
        ];

        $this->setProperty('pageFooter', $footer);
        $this->mpdf->SetFooter($this->pageFooter);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the output destination.
     *
     * @param array $destination  A destination to send the PDF
     *
     * @return PdfInterface
     *
     * @api
     */
    public function setOutputDestination($destination): PdfInterface
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

    // --------------------------------------------------------------------------

    /**
     * Set the document filename.
     *
     * @param array $filename  A default document filename
     *
     * @return PdfInterface
     *
     * @api
     */
    public function setFilename($filename): PdfInterface
    {
        $this->setProperty('filename', $filename);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setFontType($fontname = null): PdfInterface
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

    // --------------------------------------------------------------------------

    /**
     * Return a specific font-family.
     *
     * @param array $fontname  A font name type
     *
     * @return string
     *
     * @api
     */
    protected function getFontFamily($fontname = null): string
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

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function appendPageContent($str): PdfInterface
    {
        $this->setProperty('pageContent', $str);
        $this->mpdf->WriteHTML($this->pageContent);

        return $this;
    }

    // --------------------------------------------------------------------------

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

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setFontSize($size): PdfInterface
    {
        $this->fontSize = (int) $size;
        $this->mpdf->SetDefaultFontSize($this->fontSize);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setMarginTop($marginTop): PdfInterface
    {
        $this->setProperty('marginTop', $marginTop);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setMarginRight($marginRight): PdfInterface
    {
        $this->setProperty('marginRight', $marginRight);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setMarginBottom($marginBottom): PdfInterface
    {
        $this->setProperty('marginBottom', $marginBottom);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setMarginLeft($marginLeft): PdfInterface
    {
        $this->setProperty('marginLeft', $marginLeft);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setMarginHeader($marginHeader): PdfInterface
    {
        $this->setProperty('marginHeader', $marginHeader);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setMarginFooter($marginFooter): PdfInterface
    {
        $this->setProperty('marginFooter', $marginFooter);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
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

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setPageSize($pageSize): PdfInterface
    {
        $this->setProperty('pageSize', $pageSize);
        $this->registerPageFormat();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function appendPageCSS($str): PdfInterface
    {
        $this->setProperty('pageCSS', $str);
        $this->mpdf->WriteCSS($this->pageCSS, 1);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Generate and store a defined PDF page format.
     *
     * @param string $pageSize     A page format type ['Letter','Legal', etc.]
     * @param string $orientation  A page orientation ['Portrait','Landscape']
     *
     * @return PdfInterface
     */
    protected function registerPageFormat($pageSize = null, $orientation = null): PdfInterface
    {
        in_array($pageSize, $this->pageTypes)
            ? $this->setProperty('pageSize', $pageSize)
            : $this->setProperty('pageSize', static::DEFAULT_PAGE_SIZE);

        $this->setPageOrientation($orientation);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setPageOrientation($orientation): PdfInterface
    {
        $this->setProperty('pageOrientation', strtoupper($orientation[0]));

        $this->pageOrientation === 'L'
            ? $this->setProperty('pageFormat', $this->pageSize.'-'.$this->pageOrientation)
            : $this->setProperty('pageFormat', $this->pageSize);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Registering the page size and margins.
     *
     * @return PdfInterface
     *
     * @api
     */
    public function registerPageMargins(): PdfInterface
    {
        $mpdf = $this->mpdf;

        /* Set the margins and page current page width */
        $mpdf->SetLeftMargin($this->marginLeft);            /* Sets the Left page margin for the new document   */
        $mpdf->SetTopMargin($this->marginTop);              /* Sets the Top page margin for the new document    */
        $mpdf->SetRightMargin($this->marginRight);          /* Sets the Right page margin for the new document  */
        $mpdf->SetAutoPageBreak(true, $this->marginBottom); /* Sets the Bottom page margin for the new document */
        $mpdf->margin_header = $this->marginHeader;         /* Sets the Header page margin for the new document */
        $mpdf->margin_footer = $this->marginFooter;         /* Sets the Footer page margin for the new document */
        $mpdf->orig_lMargin = $mpdf->DeflMargin = $mpdf->lMargin = $this->marginLeft;
        $mpdf->orig_tMargin = $mpdf->tMargin = $this->marginTop;
        $mpdf->orig_rMargin = $mpdf->DefrMargin = $mpdf->rMargin = $this->marginRight;
        $mpdf->orig_bMargin = $mpdf->bMargin = $this->marginBottom;
        $mpdf->orig_hMargin = $mpdf->margin_header = $this->marginHeader;
        $mpdf->orig_fMargin = $mpdf->margin_footer = $this->marginFooter;
        $mpdf->pgwidth = $mpdf->w - $mpdf->lMargin - $mpdf->rMargin;

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setMetaTitle($str): PdfInterface
    {
        $this->setProperty('metaTitle', $str);
        $this->mpdf->SetTitle($this->metaTitle);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setMetaAuthor($str): PdfInterface
    {
        $this->setProperty('metaAuthor', $str);
        $this->mpdf->SetAuthor($this->metaAuthor);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setMetaCreator($str): PdfInterface
    {
        $this->setProperty('metaCreator', $str);
        $this->mpdf->SetCreator($this->metaCreator);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setMetaSubject($str): PdfInterface
    {
        $this->setProperty('metaSubject', $str);
        $this->mpdf->SetSubject($this->metaSubject);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setMetaKeywords(array $words): PdfInterface
    {
        $this->setProperty('metaKeywords', array_merge($this->metaKeywords, $words));
        $this->mpdf->SetKeywords(implode(', ', $this->metaKeywords));

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setPageSizeLetter(): PdfInterface
    {
        $this->setProperty('pageSize', 'Letter');

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setPageSizeLegal(): PdfInterface
    {
        $this->setProperty('pageSize', 'Legal');

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setPageAsLandscape(): PdfInterface
    {
        $this->setProperty('pageOrientation', 'Landscape');
        $this->registerPageFormat();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setPageAsPortrait(): PdfInterface
    {
        $this->setProperty('pageOrientation', 'Portrait');
        $this->registerPageFormat();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Method implementations inserted:
     *
     * (+) all();
     * (+) init();
     * (+) get($key);
     * (+) has($key);
     * (+) version();
     * (+) getClassName();
     * (+) getConst($key);
     * (+) set($key, $value);
     * (+) isString($str);
     * (+) getInstanceCount();
     * (+) getClassInterfaces();
     * (+) __call($callback, $parameters);
     * (+) getProperty($name, $key = null);
     * (+) doesFunctionExist($functionName);
     * (+) isStringKey($str, array $keys);
     * (+) throwExceptionError(array $error);
     * (+) setProperty($name, $value, $key = null);
     * (+) throwInvalidArgumentExceptionError(array $error);
     */
    use ServiceFunctions;
}
