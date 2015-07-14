<?php/* * This file is part of the UCSDMath package. * * (c) UCSD Mathematics | Math Computing Support <mathhelp@math.ucsd.edu> * * For the full copyright and license information, please view the LICENSE * file that was distributed with this source code. */namespace UCSDMath\Pdf;use mPDF;use Carbon\Carbon;use UCSDMath\Functions\ServiceFunctions;use UCSDMath\Functions\ServiceFunctionsInterface;/** * AbstractPdfAdapter provides an abstract base class implementation of {@link PdfInterface}. * Primarily, this services the fundamental implementations for all Pdf classes. * * This component library is an adapter to the mPDF library. * * Method list: * * @see (+) __construct(); * @see (+) __destruct(); * @see (+) render(); * @see (+) setPageSizeLegal(); * @see (+) setPageAsPortrait(); * @see (+) setPageSizeLetter(); * @see (+) appendPageCSS($str); * @see (+) setPageAsLandscape(); * @see (+) registerPageMargins(); * @see (+) appendPageContent($str); * @see (+) setMetaTitle($str = null); * @see (+) setFontSize($size = null); * @see (+) setMetaAuthor($str = null); * @see (+) setMetaCreator($str = null); * @see (+) setMetaSubject($str = null); * @see (+) setHeader(array $data = null); * @see (+) setFooter(array $data = null); * @see (+) setFilename($filename = null); * @see (+) setFontType($fontname = null); * @see (+) setPageSize($pageSize = null); * @see (-) getFontFamily($fontname = null); * @see (+) setMarginTop($marginTop = null); * @see (+) setMarginLeft($marginLeft = null); * @see (+) setMarginRight($marginRight = null); * @see (+) setMetaKeywords(array $words = null); * @see (+) setMarginBottom($marginBottom = null); * @see (+) setMarginHeader($marginHeader = null); * @see (+) setMarginFooter($marginFooter = null); * @see (+) setOutputDestination($destination = null); * @see (-) registerPageFormat($pageSize = null, $orientation = null); * @see (+) initializePageSetup($pageSize = null, $orientation = null); * @see (+) setMargins($marginTop, $marginRight, $marginBottom, $marginLeft, $marginHeader, $marginFooter); * * @author Daryl Eisner <deisner@ucsd.edu> */abstract class AbstractPdfAdapter implements PdfInterface, ServiceFunctionsInterface{    /**     * Constants.     */    const VERSION = '1.0.4';    /**     * Properties.     *     * @var    mPDF         $mpdf               A mPDF Interface instance     * @var    array        $pageHeader         A page header content to render     * @var    array        $pageFooter         A page footer content to render     * @var    string       $characterEncoding  A default character encoding     * @var    int          $fontSize           A default font size specified in points (pt. [12], 14, 18, etc.)     * @var    string       $fontType           A default font typeface ([Times], '','','')     * @var    string       $filename           A default document or filename     * @var    string       $outputDestination  A default destination where to send the document ([I], D, F, S)     * @var    string       $pageCSS            A page style setting     * @var    string       $pageContent        A page content to render     * @var    string       $pageSize           A page size (['Letter'],'Legal','A4','Tabloid', etc.)     * @var    string       $pageFormat         A page size and orientation scheme (['Letter'],'Legal-L') based in millimetres (mm)     * @var    string       $pageOrientation    A setup orientation (['Portrait'],'Landscape')     * @var    int          $marginTop          A top margin size specified as length in millimetres (mm)     * @var    int          $marginRight        A right margin size specified as length in millimetres (mm)     * @var    int          $marginBottom       A bottom margin size specified as length in millimetres (mm)     * @var    int          $marginLeft         A left margin size specified as length in millimetres (mm)     * @var    int          $marginHeader       A header margin size specified as length in millimetres (mm)     * @var    int          $marginFooter       A footer margin size specified as length in millimetres (mm)     * @var    string       $metaTitle          A document title (e.g., metadata)     * @var    string       $metaAuthor         A document author (e.g., metadata)     * @var    string       $metaSubject        A document subject (e.g., metadata)     * @var    string       $metaCreator        A document creator (e.g., metadata)     * @var    array        $metaKeywords       A document list of descriptive keywords (e.g., metadata)     * @var    array        $storageRegister    A set of validation stored data elements     * @static PdfInterface $instance           A PdfInterface instance     * @static integer      $objectCount        A PdfInterface instance count     */    protected $mpdf               = null;    protected $pageHeader         = array();    protected $pageFooter         = array();    protected $characterEncoding  = 'utf-8';    protected $fontSize           = 12;    protected $fontType           = 'Times';    protected $filename           = 'document.pdf';    protected $outputDestination  = 'I';    protected $pageCSS            = null;    protected $pageContent        = null;    protected $pageSize           = 'Letter';    protected $pageOrientation    = 'Portrait';    protected $pageFormat         = 'Letter';    protected $marginTop          = 11;    protected $marginRight        = 15;    protected $marginBottom       = 14;    protected $marginLeft         = 11;    protected $marginHeader       = 5;    protected $marginFooter       = 9;    protected $metaTitle          = null;    protected $metaAuthor         = null;    protected $metaSubject        = null;    protected $metaCreator        = null;    protected $metaKeywords       = array();    protected $storageRegister    = array();    protected static $instance    = null;    protected static $objectCount = 0;    /**     * Constructor.     *     * @api     */    public function __construct()    {        static::$instance = $this;        static::$objectCount++;    }    /**     * Destructor.     */    public function __destruct()    {        static::$objectCount--;    }    /**     * Initialize a new PDF document by specifying page size and orientation.     *     * @param array $pageSize     A page size     * @param array $orientation  A page orientation     *     * @return PdfInterface     *     * @api     */    public function initializePageSetup($pageSize = null, $orientation = null)    {        $this->isString($pageSize)            && in_array($pageSize , ['Letter', 'Legal'])            && in_array($orientation , ['Portrait', 'Landscape'])            ? $this->setProperty('mpdf', new mPDF(                    'utf-8',                    $pageSize.'-'.strtoupper($orientation[0]),                    $this->fontSize,                    $this->fontType,                    $this->marginLeft,                    $this->marginRight,                    $this->marginTop,                    $this->marginBottom,                    $this->marginHeader,                    $this->marginFooter,                    strtoupper($this->pageOrientation[0])                    )              )            : $this->setProperty('mpdf', new mPDF('utf-8', 'Letter-P'));        return $this;    }    /**     * Set the page header.     *     * @param array $data  A list of header items ('left','right')     *     * @return PdfInterface     *     * @api     */    public function setHeader(array $data = null)    {        $string_right = str_replace("{{date(\"n/d/Y g:i A\")}}", Carbon::now()->format('n/d/Y g:i A'), $data['right']);        $string_left  = str_replace("|", '<br>', $data['left']);        $html = "<table border='0' cellspacing='0' cellpadding='0' width='100%'><tr>                     <td style='font-family:arial;font-size:14px;font-weight:bold;'>$string_left</td>                     <td style='font-size:13px;font-family:arial;text-align:right;font-style:italic;'>$string_right</td>                 </tr></table><br>";        $this->setProperty('pageHeader', $html);        $this->appendPageContent($this->pageHeader);        return $this;    }    /**     * Set the page footer.     *     * @param array $data  A list of footer items ('left','center','right')     *     * @return PdfInterface     *     * @api     */    public function setFooter(array $data = null)    {        $string_right  = str_replace("{{page(\"# of #\")}}", '{PAGENO} of {nb}', $data['right']);        $string_left   = str_replace("|", '<br>', $data['left']);        $string_center = str_replace("|", '<br>', $data['center']);        $footer = [            'odd' => [                'L' => [                    'content'     => "<strong>$string_left</strong>",                    'font-size'   => 9,                    'font-style'  => '',                    'font-family' => 'Arial',                    'color'       => '#000000'                ],                'C' => [                    'content'     => "<strong>$string_center</strong>",                    'font-size'   => 9,                    'font-style'  => 'I',                    'font-family' => 'Arial',                    'color'       => '#000000'                ],                'R' => [                    'content'     => "<strong>$string_right</strong>",                    'font-size'   => 9,                    'font-style'  => '',                    'font-family' => 'Arial',                    'color'       => '#000000'                ],                'line' => true,            ],            'even' => []        ];        $this->setProperty('pageFooter', $footer);        $this->mpdf->SetFooter($this->pageFooter);        return $this;    }    /**     * Set the output destination.     *     * @param array $destination  A destination to send the PDF     *     * @return PdfInterface     *     * @api     */    public function setOutputDestination($destination = null)    {        /**         * Destinations can be sent to the following:         *    - I/B [Inline]   - Sends output to browser (browser plug-in is used if avaialble)         *                       If a $filename is given, the browser's "Save as..." option is provided         *    - D   [Download] - Forces browser to download the file         *    - F   [File]     - Saves the file to the server's filesystem cache         *    - S   [String]   - Returns the PDF as a string         */        $destination = strtoupper($destination[0]) === 'B' ? 'I' : $destination ;        $this->isString($destination) && in_array(strtoupper($destination[0]) , ['I', 'D', 'F', 'S'])            ? $this->setProperty('outputDestination', $destination)            : $this->setProperty('outputDestination', static::DEFAULT_OUTPUT_DESTINATION);        return $this;    }    /**     * Set the document filename.     *     * @param array $filename  A default document filename     *     * @return PdfInterface     *     * @api     */    public function setFilename($filename = null)    {        $this->isString($filename)            ? $this->setProperty('filename', $filename)            : $this->setProperty('filename', static::DEFAULT_FILENAME);        return $this;    }    /**     * {@inheritdoc}     */    public function setFontType($fontname = null)    {        /**         * Font sets to be used for PDF documents:         *         *   - Arial           - Times             - Tahoma         *   - Georgia         - Trebuchet         - Courier         *   - Lucida          - Lucida-Bright     - Palatino         *   - Garamond        - Verdana           - Console         *   - Monaco          - Helvetica         - Calibri         *   - Avant-Garde     - Cambria         */        $this->isString($fontname)            && in_array(                strtolower($fontname),                ['arial', 'times', 'tahoma', 'georgia',                 'monaco', 'courier', 'lucida', 'calibri',                 'cambria', 'garamond', 'verdana', 'console',                 'trebuchet', 'helvetica', 'palatino', 'avant-garde', 'lucida-bright']            )            ? $this->setProperty('fontType', $this->getFontFamily($fontname))            : $this->setProperty('fontType', $this->getFontFamily());        $this->mpdf->SetDefaultBodyCSS('font-family', $this->getProperty('fontType'));        return $this;    }    /**     * Return a specific font-family.     *     * @param array $fontname  A font name (key)     *     * @return string     *     * @api     */    protected function getFontFamily($fontname = null)    {        /** return the CSS font-family */        switch (strtolower($fontname)) {            case 'arial':                return "Arial, 'Helvetica Neue', Helvetica, sans-serif";                break;            case 'times':                return "TimesNewRoman, 'Times New Roman', Times, Baskerville, Georgia, serif";                break;            case 'tahoma':                return "Tahoma, Verdana, Segoe, Geneva, sans-serif";                break;            case 'georgia':                return "Georgia, Times, 'Times New Roman', serif";                break;            case 'trebuchet':                return "'Trebuchet MS', 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Helvetica, Tahoma, sans-serif";                break;            case 'courier':                return "'Courier New', Courier, 'Lucida Sans Typewriter', 'Lucida Typewriter', monospace";                break;            case 'lucida':                return "'Lucida Sans Typewriter', 'Lucida Console', monaco, 'Bitstream Vera Sans Mono', monospace";                break;            case 'lucida-bright':                return "'Lucida Bright', Georgia, serif";                break;            case 'palatino':                return "'Palatino Linotype', 'Palatino LT STD', 'Book Antiqua', Palatino, Georgia, serif";                break;            case 'garamond':                return "Garamond, Baskerville, 'Baskerville Old Face', 'Hoefler Text', 'Times New Roman', serif";                break;            case 'verdana':                return "Verdana, Geneva, sans-serif";                break;            case 'console':                return "'Lucida Console', 'Lucida Sans Typewriter', Monaco, 'Bitstream Vera Sans Mono', monospace";                break;            case 'monaco':                return "'Lucida Console', 'Lucida Sans Typewriter', Monaco, 'Bitstream Vera Sans Mono', monospace";                break;            case 'helvetica':                return "'HelveticaNeue-Light', 'Helvetica Neue Light', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif";                break;            case 'calibri':                return "Calibri, Candara, Segoe, 'Segoe UI', Optima, Arial, sans-serif";                break;            case 'avant-garde':                return "'Avant Garde', Avantgarde, 'Century Gothic', CenturyGothic, AppleGothic, sans-serif";                break;            case 'cambria':                return "Cambria, Georgia, serif";                break;            default:                return "Arial, 'Helvetica Neue', Helvetica, sans-serif";                break;        }    }    /**     * {@inheritdoc}     */    public function appendPageContent($str)    {        $this->setProperty('pageContent', $str);        $this->mpdf->WriteHTML($this->pageContent);        return $this;    }    /**     * Render the PDF to output.     *     * @return PdfInterface     *     * @api     */    public function render()    {        /** finally render document */        return $this->mpdf->Output($this->filename, $this->outputDestination);    }    /**     * {@inheritdoc}     */    public function setMetaTitle($str = null)    {        isset($str) && $this->isString($str)            ? $this->setProperty('metaTitle', $str)            : $this->setProperty('metaTitle', null);        $this->mpdf->SetTitle($this->metaTitle);        return $this;    }    /**     * {@inheritdoc}     */    public function setMetaAuthor($str = null)    {        isset($str) && $this->isString($str)            ? $this->setProperty('metaAuthor', $str)            : $this->setProperty('metaAuthor', null);        $this->mpdf->SetAuthor($this->metaAuthor);        return $this;    }    /**     * {@inheritdoc}     */    public function setMetaCreator($str = null)    {        isset($str) && $this->isString($str)            ? $this->setProperty('metaCreator', $str)            : $this->setProperty('metaCreator', null);        $this->mpdf->SetCreator($this->metaCreator);        return $this;    }    /**     * {@inheritdoc}     */    public function setMetaSubject($str = null)    {        isset($str) && $this->isString($str)            ? $this->setProperty('metaSubject', $str)            : $this->setProperty('metaSubject', null);        $this->mpdf->SetSubject($this->metaSubject);        return $this;    }    /**     * {@inheritdoc}     */    public function setMetaKeywords(array $words = null)    {        empty($words)            ? null            : $this->setProperty('metaKeywords', array_merge($this->metaKeywords, $words));        $this->mpdf->SetKeywords(implode(', ', $this->metaKeywords));        return $this;    }    /**     * {@inheritdoc}     */    public function setFontSize($size = null)    {        isset($size) && is_int($size)            ? $this->fontSize = $size            : null;        $this->mpdf->SetDefaultFontSize($this->fontSize);        return $this;    }    /**     * {@inheritdoc}     */    public function setMarginTop($marginTop = null)    {        isset($marginTop) && is_int($marginTop)            ? $this->setProperty('marginTop', $marginTop)            : null;        return $this;    }    /**     * {@inheritdoc}     */    public function setMarginRight($marginRight = null)    {        isset($marginRight) && is_int($marginRight)            ? $this->setProperty('marginRight', $marginRight)            : null;        return $this;    }    /**     * {@inheritdoc}     */    public function setMarginBottom($marginBottom = null)    {        isset($marginBottom) && is_int($marginBottom)            ? $this->setProperty('marginBottom', $marginBottom)            : null;        return $this;    }    /**     * {@inheritdoc}     */    public function setMarginLeft($marginLeft = null)    {        isset($marginLeft) && is_int($marginLeft)            ? $this->setProperty('marginLeft', $marginLeft)            : null;        return $this;    }    /**     * {@inheritdoc}     */    public function setMarginHeader($marginHeader = null)    {        isset($marginHeader) && is_int($marginHeader)            ? $this->setProperty('marginHeader', $marginHeader)            : null;        return $this;    }    /**     * {@inheritdoc}     */    public function setMarginFooter($marginFooter = null)    {        isset($marginFooter) && is_int($marginFooter)            ? $this->setProperty('marginFooter', $marginFooter)            : null;        return $this;    }    /**     * {@inheritdoc}     */    public function setMargins(        $marginTop    = null,        $marginRight  = null,        $marginBottom = null,        $marginLeft   = null,        $marginHeader = null,        $marginFooter = null    ) {        isset($marginTop) && is_int($marginTop)            ? $this->setProperty('marginTop', $marginTop)            : null;        isset($marginRight) && is_int($marginRight)            ? $this->setProperty('marginRight', $marginRight)            : null;        isset($marginBottom) && is_int($marginBottom)            ? $this->setProperty('marginBottom', $marginBottom)            : null;        isset($marginLeft) && is_int($marginLeft)            ? $this->setProperty('marginLeft', $marginLeft)            : null;        isset($marginHeader) && is_int($marginHeader)            ? $this->setProperty('marginHeader', $marginHeader)            : null;        isset($marginFooter) && is_int($marginFooter)            ? $this->setProperty('marginFooter', $marginFooter)            : null;        return $this;    }    /**     * {@inheritdoc}     */    public function setPageSizeLetter()    {        $this->setProperty('pageSize', 'Letter');        return $this;    }    /**     * {@inheritdoc}     */    public function setPageSizeLegal()    {        $this->setProperty('pageSize', 'Legal');        return $this;    }    /**     * {@inheritdoc}     */    public function setPageSize($pageSize = null)    {        if ($this->isString($pageSize)) {            switch ($pageSize) {                case 'Letter':                    $this->setProperty('pageSize', $pageSize);                    break;                case 'Legal':                    $this->setProperty('pageSize', $pageSize);                    break;                case 'A4':                    $this->setProperty('pageSize', $pageSize);                    break;                case 'Tabloid':                    $this->setProperty('pageSize', $pageSize);                    break;                default:                    $this->setProperty('pageSize', static::DEFAULT_PAGE_SIZE);            }        }        $this->registerPageFormat();        return $this;    }    /**     * {@inheritdoc}     */    public function appendPageCSS($str)    {        $this->setProperty('pageCSS', $str);        $this->mpdf->WriteCSS($this->pageCSS, 1);        return $this;    }    /**     * {@inheritdoc}     */    public function setPageAsLandscape()    {        $this->setProperty('pageOrientation', 'Landscape');        $this->registerPageFormat();        return $this;    }    /**     * {@inheritdoc}     */    public function setPageAsPortrait()    {        $this->setProperty('pageOrientation', 'Portrait');        $this->registerPageFormat();        return $this;    }    /**     * Generate and store a defined PDF page format.     *     * @param string $pageSize     A page format type ['Letter','Legal', etc.]     * @param string $orientation  A page orientation ['Portrait','Landscape']     *     * @return PdfInterface     */    protected function registerPageFormat($pageSize = null, $orientation = null)    {        if ($this->isString($pageSize)) {            switch ($pageSize) {                case 'Letter':                    $this->setProperty('pageSize', $pageSize);                    break;                case 'Legal':                    $this->setProperty('pageSize', $pageSize);                    break;                case 'A4':                    $this->setProperty('pageSize', $pageSize);                    break;                case 'Tabloid':                    $this->setProperty('pageSize', $pageSize);                    break;                default:                    $this->setProperty('pageSize', static::DEFAULT_PAGE_SIZE);            }        }        if (null !== $orientation && $this->isString($orientation)) {            $this->setProperty('pageOrientation',                $orientation === 'Landscape'                    ? 'Landscape'                    : 'Portrait'            );        }        $orientation = $this->pageOrientation[0];        $this->setProperty('pageFormat',            $this->pageOrientation[0] === 'L'                ? $this->pageSize.'-'.$this->pageOrientation[0]                : $this->pageSize        );        return $this;    }    /**     * Registering the page size and margins.     *     * @return PdfInterface     *     * @api     */    public function registerPageMargins()    {        $mpdf = $this->mpdf;        /** Set the margins and page current page width */        $mpdf->SetLeftMargin($this->marginLeft);            /** Sets the Left page margin for the new document   */        $mpdf->SetTopMargin($this->marginTop);              /** Sets the Top page margin for the new document    */        $mpdf->SetRightMargin($this->marginRight);          /** Sets the Right page margin for the new document  */        $mpdf->SetAutoPageBreak(true, $this->marginBottom); /** Sets the Bottom page margin for the new document */        $mpdf->margin_header = $this->marginHeader;         /** Sets the Header page margin for the new document */        $mpdf->margin_footer = $this->marginFooter;         /** Sets the Footer page margin for the new document */        $mpdf->orig_lMargin = $mpdf->DeflMargin = $mpdf->lMargin = $this->marginLeft;        $mpdf->orig_tMargin = $mpdf->tMargin = $this->marginTop;        $mpdf->orig_rMargin = $mpdf->DefrMargin = $mpdf->rMargin = $this->marginRight;        $mpdf->orig_bMargin = $mpdf->bMargin = $this->marginBottom;        $mpdf->orig_hMargin = $mpdf->margin_header = $this->marginHeader;        $mpdf->orig_fMargin = $mpdf->margin_footer = $this->marginFooter;        $mpdf->pgwidth = $mpdf->w - $mpdf->lMargin - $mpdf->rMargin;        return $this;    }    /**     * Method implementations inserted.     *     * The notation below illustrates visibility: (+) @api, (-) protected or private.     *     * @see (+) all();     * @see (+) init();     * @see (+) get($key);     * @see (+) has($key);     * @see (+) version();     * @see (+) getClassName();     * @see (+) getConst($key);     * @see (+) set($key, $value);     * @see (+) isString($string);     * @see (+) getInstanceCount();     * @see (+) getClassInterfaces();     * @see (+) getProperty($name, $key = null);     * @see (-) doesFunctionExist($functionName);     * @see (+) isStringKey($string, array $keys);     * @see (-) throwExceptionError(array $error);     * @see (+) setProperty($name, $value, $key = null);     * @see (-) throwInvalidArgumentExceptionError(array $error);     */    use ServiceFunctions;}