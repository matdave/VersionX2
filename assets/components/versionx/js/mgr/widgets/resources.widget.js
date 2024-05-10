VersionX.grid.ResourcesWidget = function(config) {
    this.config = config || {};
    Ext.applyIf(this.config, {
        id: 'versionx-grid-resources-widget',
        baseParams: {
            action: 'mgr/objects/getlist',
            class: 'modResource',
        },
        params: [],
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            emptyText: _('versionx.error.noresults')
        },
        tbar: [],
        columns: this.getColumns(),


    });
    VersionX.grid.ResourcesWidget.superclass.constructor.call(this,config);
};
Ext.extend(VersionX.grid.ResourcesWidget, VersionX.grid.Objects, {
    getColumns: function() {
        let columns = this.superclass().getColumns(),
            newColumns = [];

        columns.forEach(function(column) {
            if (['id','time_end','name','username'].includes(column.dataIndex)) {
                newColumns.push(column);
            }
        });

        return newColumns;
    }
});
Ext.reg('versionx-grid-resources-widget', VersionX.grid.ResourcesWidget);