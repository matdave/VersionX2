<?php

/**
 * @package versionx
 * @subpackage dashboard
 */
class vxResourceHistoryWidget extends modDashboardWidgetInterface
{
    public function render(): string
    {
        $corePath = $this->modx->getOption(
            'versionx.core_path',
            null,
            $this->modx->getOption('core_path') . 'components/versionx/'
        );

        require_once $corePath . 'model/versionx.class.php';
        $versionx = new VersionX($this->modx);

        $langs = $versionx->_getLangs();

        $vxUrl = $versionx->config['assets_url'];
        $this->modx->regClientCSS($vxUrl . 'css/mgr/mgr.css');
        $this->modx->regClientStartupHTMLBlock(<<<HTML
            <script type="text/javascript" src="{$vxUrl}js/mgr/versionx.class.js" ></script>
            <script type="text/javascript" src="{$vxUrl}js/mgr/window.milestone.js" ></script>
            <script type="text/javascript" src="{$vxUrl}js/mgr/grid.deltas.js" ></script>
            <script type="text/javascript" src="{$vxUrl}js/mgr/window.deltas.js" ></script>
            <script type="text/javascript" src="{$vxUrl}js/mgr/grid.objects.js" ></script>
            <script type="text/javascript" src="{$vxUrl}js/mgr/widgets/resources.widget.js" ></script>
            <script type="text/javascript" src="{$vxUrl}js/mgr/index.js" ></script>
            <script type="text/javascript" >
            Ext.onReady(function() {
                {$langs}
                VersionX.config = {$this->modx->toJSON($versionx->config)};
                MODx.load({
                    xtype: "versionx-grid-resources-widget"
                    ,renderTo: "versionx-widget-resource-div"
                });
            });
            </script>
        HTML);

        return '<div id="versionx-widget-resource-div"></div>';
    }
}

return 'vxResourceHistoryWidget';
