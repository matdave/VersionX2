<?php

$widgets = [];

// Use different base paths if we're bootstrapping rather than building.
if (isset($componentPath)) {
    $basePath = $componentPath . '/core/';
} else {
    $basePath = '[[++core_path]]';
}

/** @var \modX $modx */
$widgets[0] = $modx->newObject('modDashboardWidget');
$widgets[0]->fromArray([
    'name' => 'versionx.widget.resources',
    'description' => 'versionx.widget.resources.desc',
    'type' => 'file',
    'size' => 'half',
    'content' => $basePath . 'components/versionx/elements/widgets/resources.dashboardwidget.php',
    'namespace' => 'versionx',
    'lexicon' => 'versionx:default',
], '', true, true);

return $widgets;