var VersionX = function (config) {
    config = config || {};
    VersionX.superclass.constructor.call(this, config);
};
Ext.extend(VersionX, Ext.Component, {
    page: {},
    window: {},
    grid: {},
    tree: {},
    panel: {},
    combo: {},
    field: {},
    config: {},
    stats: {},
    inVersion: false,
});
Ext.reg('versionx', VersionX);
VersionX = new VersionX();

VersionX.panel.VersionHeader = function (config) {
    config = config || {};
    config.title = config.title || 'New Section';
    Ext.apply(config, {
        border: false,
        forceLayout: true,
        items: [{
            html: '<h3 style="border-bottom: 1px solid; padding-top: 1em;">' + config.title + '</h3>'
        }]
    });
    VersionX.panel.VersionHeader.superclass.constructor.call(this, config);
};
Ext.extend(VersionX.panel.VersionHeader, MODx.Panel);
Ext.reg('versionx-panel-versionheader', VersionX.panel.VersionHeader);

/**
 * @param config
 * @constructor
 */
VersionX.field.Search = function(config) {
    config = config || {};
    var grid = config.grid || null

    Ext.applyIf(config, {
        xtype: 'trigger',
        name: 'query',
        emptyText: _('versionx.search'),
        width: 250,
        ctCls: 'versionx-search',
        onTriggerClick: function() {
            this.reset();
            this.fireEvent('click');
        },
        listeners: {
            'render': {
                fn: function(cmp) {
                    new Ext.KeyMap(cmp.getEl(), {
                        key: Ext.EventObject.ENTER,
                        fn: function() {
                            grid.filter(this);
                            return true;
                        },
                        scope: cmp
                    });
                },
                scope:grid,
            },
            'click': {
                fn: function(trigger) {
                    grid.getStore().setBaseParam('query', '');
                    grid.getStore().load();
                },
                scope: grid,
            }
        }
    });
    VersionX.field.Search.superclass.constructor.call(this,config);
};
Ext.extend(VersionX.field.Search, Ext.form.TriggerField);
Ext.reg('versionx-field-search', VersionX.field.Search);

VersionX.combo.Filter = function(config) {
    Ext.applyIf(config, {
        url: VersionX.config.connector_url,
        showClearFilter: config.showClearFilter || 0,
        paging: false,
        editable: true,
        typeAhead: false,
    });
    VersionX.combo.Filter.superclass.constructor.call(this, config);
    var combo = this;
    this.on('beforeselect', function(combo, rec, index) {
        if (rec.data.id === 'clr' || rec.data[combo.valueField] === 'clr') {
            combo.clearValue();
            rec.data = {};
        }
    });
    this.getStore().on('load', function(store, rec, opts) {
        if (combo.showClearFilter) {
            combo.insertRecord(store, 0, _('versionx.filters.clear_filter'), 'clr');
        }
    });
};
Ext.extend(VersionX.combo.Filter, MODx.combo.ComboBox, {
    insertRecord: function(store, index, label, val) {
        var data = [],
            displayField = this.displayField || 'name',
            record = {id: val};
        record[displayField] = label;
        data.push(new Ext.data.Record(record));
        store.insert(index, data);
    }
});
Ext.reg('versionx-combo-filter', VersionX.combo.Filter);

VersionX.combo.Classes = function(config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'class',
        displayField: 'name',
        valueField: 'id',
        hiddenName: 'class',
        fields: ['id', 'name'],
        baseParams: {
            action: 'mgr/filters/classes',
            combo: true,
            limit: '0',
        },
        emptyText: _('versionx.filters.class'),
    });
    VersionX.combo.Classes.superclass.constructor.call(this, config);
};
Ext.extend(VersionX.combo.Classes, VersionX.combo.Filter);
Ext.reg('versionx-combo-classes', VersionX.combo.Classes);

VersionX.combo.Editors = function(config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'editor',
        displayField: 'name',
        valueField: 'id',
        hiddenName: 'editor',
        fields: ['id', 'name'],
        baseParams: {
            action: 'mgr/filters/editors',
            combo: true,
            limit: '0',
        },
        emptyText: _('versionx.filters.editor'),
    });
    VersionX.combo.Editors.superclass.constructor.call(this, config);
};
Ext.extend(VersionX.combo.Editors, VersionX.combo.Filter);
Ext.reg('versionx-combo-editors', VersionX.combo.Editors);

VersionX.combo.Packages = function(config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'package',
        displayField: 'name',
        valueField: 'id',
        hiddenName: 'package',
        fields: ['id', 'name'],
        baseParams: {
            action: 'mgr/filters/packages',
            combo: true,
            limit: '0',
        },
        emptyText: _('versionx.filters.package'),
    });
    VersionX.combo.Packages.superclass.constructor.call(this, config);
};
Ext.extend(VersionX.combo.Packages, VersionX.combo.Filter);
Ext.reg('versionx-combo-packages', VersionX.combo.Packages);