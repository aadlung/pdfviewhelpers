<?php

namespace Bithost\Pdfviewhelpers\ViewHelpers;

/* * *
 *
 * This file is part of the "PDF ViewHelpers" Extension for TYPO3 CMS.
 *
 *  (c) 2016 Markus Mächler <markus.maechler@bithost.ch>, Bithost GmbH
 *           Esteban Marin <esteban.marin@bithost.ch>, Bithost GmbH
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
 * * */

use Bithost\Pdfviewhelpers\Exception\Exception;
use Bithost\Pdfviewhelpers\Exception\ValidationException;
use Bithost\Pdfviewhelpers\Model\BasePDF;

/**
 * AbstractContentElementViewHelper
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
abstract class AbstractContentElementViewHelper extends AbstractPDFViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('posX', 'integer', '', false, null);
        $this->registerArgument('posY', 'integer', '', false, null);
        $this->registerArgument('width', 'integer', '', false, null);
        $this->registerArgument('height', 'integer', '', false, null);
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function initialize()
    {
        parent::initialize();

        if (!is_null($this->arguments['width'])) {
            $this->validationService->validateWidth($this->arguments['width']);
        }

        if (!is_null($this->arguments['height'])) {
            $this->validationService->validateHeight($this->arguments['height']);
        }

        if (is_null($this->arguments['posX'])) {
            $this->arguments['posX'] = $this->getPDF()->GetX();
        }

        if (is_null($this->arguments['posY'])) {
            $this->arguments['posY'] = $this->getPDF()->GetY();
        }

        $this->initializeHeaderAndFooter();
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function initializeHeaderAndFooter()
    {
        if ($this->viewHelperVariableContainer->get('DocumentViewHelper', 'pageNeedsHeader')) {
            $this->viewHelperVariableContainer->addOrUpdate('DocumentViewHelper', 'pageNeedsHeader', false);

            $this->getPDF()->renderHeader();
            $this->getPDF()->renderFooter();
        }
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    protected function initializeMultiColumnSupport()
    {
        $multiColumnContext = $this->getMultiColumnContext();

        if ($multiColumnContext !== null && $multiColumnContext['isInAColumn']) {
            $this->arguments['width'] = $multiColumnContext['columnWidth'];
            $this->arguments['posX'] = $multiColumnContext['currentPosX'];
        }
    }
}
