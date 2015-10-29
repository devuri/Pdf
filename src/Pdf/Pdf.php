<?php

/*
 * This file is part of the UCSDMath package.
 *
 * (c) UCSD Mathematics | Math Computing Support <mathhelp@math.ucsd.edu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace UCSDMath\Pdf;

/**
 * Pdf is the default implementation of {@link PdfInterface} which
 * provides routine pdf methods that are commonly used throughout the framework.
 *
 * This is a adapter for the mPDF library.
 *
 * @author Daryl Eisner <deisner@ucsd.edu>
 *
 * @api
 */
class Pdf extends AbstractPdfAdapter implements PdfInterface
{
    /**
     * Constants.
     *
     * @var string VERSION  A version number
     *
     * @api
     */
    const VERSION = '1.4.0';

    // --------------------------------------------------------------------------

    /**
     * Properties.
     */

    // --------------------------------------------------------------------------

    /**
     * Constructor.
     *
     * @api
     */
    public function __construct()
    {
        parent::__construct();
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setMetaTitle($str)
    {
        $this->setProperty('metaTitle', $str);
        $this->mpdf->SetTitle($this->metaTitle);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setMetaAuthor($str)
    {
        $this->setProperty('metaAuthor', $str);
        $this->mpdf->SetAuthor($this->metaAuthor);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setMetaCreator($str)
    {
        $this->setProperty('metaCreator', $str);
        $this->mpdf->SetCreator($this->metaCreator);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setMetaSubject($str)
    {
        $this->setProperty('metaSubject', $str);
        $this->mpdf->SetSubject($this->metaSubject);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setMetaKeywords(array $words)
    {
        $this->setProperty('metaKeywords', array_merge($this->metaKeywords, $words));
        $this->mpdf->SetKeywords(implode(', ', $this->metaKeywords));

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setPageSizeLetter()
    {
        $this->setProperty('pageSize', 'Letter');

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setPageSizeLegal()
    {
        $this->setProperty('pageSize', 'Legal');

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setPageAsLandscape()
    {
        $this->setProperty('pageOrientation', 'Landscape');
        $this->registerPageFormat();

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * {@inheritdoc}
     */
    public function setPageAsPortrait()
    {
        $this->setProperty('pageOrientation', 'Portrait');
        $this->registerPageFormat();

        return $this;
    }
}
