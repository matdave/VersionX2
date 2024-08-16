<?php

use modmore\VersionX\VersionX;
use modmore\VersionX\Types\Chunk;
use modmore\VersionX\Types\Plugin;
use modmore\VersionX\Types\Resource;
use modmore\VersionX\Types\Snippet;
use modmore\VersionX\Types\Template;
use modmore\VersionX\Types\TV;
use modmore\VersionX\Types\Type;

/**
 * @var \modX|\MODX\Revolution\modX $modx
 * @var \xPDOTransport|\xPDO\Transport\xPDOTransport $object
 * @var array $options
 */
if (isset($object->xpdo)) {
    $modx = $object->xpdo;
}
if (!isset($modx)) {
    $options = [
        'vx_snapshot_resources' => true,
        'vx_snapshot_templates' => true,
        'vx_snapshot_chunks' => true,
        'vx_snapshot_snippets' => true,
        'vx_snapshot_plugins' => true,
        'vx_snapshot_tmplvars' => true,
    ];
}

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_UPGRADE:
    case xPDOTransport::ACTION_INSTALL:
        @set_time_limit(0);

        /* @var VersionX $versionX */
        $path = MODX_CORE_PATH . 'components/versionx/';

        require_once $path . '/vendor/autoload.php';
        $versionX = new VersionX($modx);

        $modx->log(modX::LOG_LEVEL_INFO,'Starting snapshot process for selected objects...');

        if (!empty($options['vx_snapshot_resources'])) {
            createInitialDelta($versionX, modResource::class, new Resource($versionX), 'Resource');
        }

        if (!empty($options['vx_snapshot_templates'])) {
            createInitialDelta($versionX, modTemplate::class, new Template($versionX), 'Template');
        }

        if (!empty($options['vx_snapshot_chunks'])) {
            createInitialDelta($versionX, modChunk::class, new Chunk($versionX), 'Chunk');
        }

        if (!empty($options['vx_snapshot_snippets'])) {
            createInitialDelta($versionX, modSnippet::class, new Snippet($versionX), 'Snippet');
        }

        if (!empty($options['vx_snapshot_plugins'])) {
            createInitialDelta($versionX, modPlugin::class, new Plugin($versionX),'Plugin');
        }

        if (!empty($options['vx_snapshot_tmplvars'])) {
            createInitialDelta($versionX, modTemplateVar::class, new TV($versionX), 'TV');
        }

    break;
}

/**
 * Attempts to create an initial delta for each object of the given type (resource, snippet etc.)
 * If a delta already exists from a previous install, skip and continue.
 *
 * @param VersionX $versionX
 * @param string $class
 * @param Type $type
 * @param string $name
 * @return void
 */
function createInitialDelta(VersionX $versionX, string $class, Type $type, string $name): void
{
    global $modx;

    $modx->log(modX::LOG_LEVEL_INFO,"Iterating over {$name}s and creating initial deltas..");

    $count = 0;
    foreach ($modx->getIterator($class) as $object) {
        // Only create a delta if none currently exist for the given object
        if (!$modx->getObject(vxDelta::class, [
            'principal_package' => 'core',
            'principal_class' => $class,
            'principal' => $object->get('id'),
        ])) {
            // Create an initial delta for this object
            if (!$versionX->deltas()->createDelta($object->get('id'), $type)) {
                $modx->log(modX::LOG_LEVEL_WARN,"Error creating snapshot for {$name} {$object->get('id')}");
                continue;
            }
        }

        // Add to count if a delta was created or one already exists
        $count++;

        if (is_int($count / 25)) {
            $modx->log(modX::LOG_LEVEL_INFO,"Checked {$count} {$name}s so far.");
        }
    }

    $modx->log(modX::LOG_LEVEL_WARN,"Iterated over {$count} {$name}s.");
}
    
return true;
