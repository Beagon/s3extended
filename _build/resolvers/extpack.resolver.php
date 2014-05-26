<?php
/**
 * @package s3extended
 */
/**
 * Handles adding S3ExtendedMediaSource to Extension Packages
 *
 * @package s3extended
 * @subpackage build
 */

 if ($transport->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            /** @var modX $modx */
            $modx =& $transport->xpdo;
            $modelPath = $modx->getOption('s3extended.core_path');
            if (empty($modelPath)) {
                $modelPath = '[[++core_path]]components/s3extended/model/';
            }
            if ($modx instanceof modX) {
                $modx->addExtensionPackage('s3extended',$modelPath);
            }
            break;
        case xPDOTransport::ACTION_UNINSTALL:
            $modx =& $transport->xpdo;
            $modelPath = $modx->getOption('s3extended.core_path',null,$modx->getOption('core_path').'components/s3extended/').'model/';
            if ($modx instanceof modX) {
                $modx->removeExtensionPackage('s3extended');
            }
            break;
    }
}
return true;