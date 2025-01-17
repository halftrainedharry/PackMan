<?php
/**
 * Add package to packages grid
 *
 * @package packman
 */
use xPDO\Transport\xPDOTransport;
use MODX\Revolution\Transport\modTransportPackage;

$success = true;
if ($transport && $transport->xpdo) {
    $signature = '{signature}';
    $provider = '{provider}';
    $modx =& $transport->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            /* define version */
            $attributes = $modx->fromJSON('{attributes}');
            $metadata = $modx->fromJSON('{metadata}');
            $sig = explode('-', $signature);
            $versionSignature = explode('.', $sig[1]);

            /* add in the package as an object so it can be upgraded */
            $package = $modx->newObject(modTransportPackage::class);
            $package->set('signature', $signature);
            $package->fromArray([
                'created' => date('Y-m-d h:i:s'),
                'updated' => date('Y-m-d h:i:s'),
                'installed' => strftime('%Y-%m-%d %H:%M:%S'),
                'state' => 1,
                'workspace' => 1,
                'provider' => $provider,
                'disabled' => false,
                'source' => $signature . '.transport.zip',
                'manifest' => null,
                'attributes' => $attributes,
                'package_name' => $sig[0],
                'metadata' => $metadata,
                'version_major' => $versionSignature[0],
                'version_minor' => !empty($versionSignature[1]) ? $versionSignature[1] : 0,
                'version_patch' => !empty($versionSignature[2]) ? $versionSignature[2] : 0,
            ]);
            if (!empty($sig[2])) {
                $r = preg_split('/([0-9]+)/', $sig[2], -1, PREG_SPLIT_DELIM_CAPTURE);
                if (is_array($r) && !empty($r)) {
                    $package->set('release', $r[0]);
                    $package->set('release_index', (isset($r[1]) ? $r[1] : '0'));
                } else {
                    $package->set('release', $sig[2]);
                }
            }
            $success = $package->save();
            $modx->logManagerAction('package_install', modTransportPackage::class, $package->get('id'));
        break;

        case xPDOTransport::ACTION_UNINSTALL:
            /* remove the package on uninstall */
            $package = $modx->getObject(modTransportPackage::class, ['signature' => $signature]);
            if ($package) {
                if ($package->uninstall()) {
                    $cacheManager= $modx->getCacheManager();
                    $cacheManager->clearCache();
                    $modx->logManagerAction('package_uninstall', modTransportPackage::class, $package->get('id'));
                }
            }
        break;
    }
}

return $success;