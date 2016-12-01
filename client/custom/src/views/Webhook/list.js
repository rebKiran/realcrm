/************************************************************************
 * This file is part of EspoCRM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2015 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: http://www.espocrm.com
 *
 * EspoCRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EspoCRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EspoCRM. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "EspoCRM" word.
 ************************************************************************/

Espo.define('custom:views/Webhook/list', 'views/list', function (Dep) {

    return Dep.extend({  
	
	template: 'list',
	
	scope: null,

	 name: 'List',
	
	recordView: 'views/record/list',
		
    	quickDetailDisabled: true,

        quickEditDisabled: true,

        massActionList: ['remove'],

        checkAllResultDisabled: true,
		
		getRecordViewName: function () {
            return this.getMetadata().get('clientDefs.' + this.scope + '.recordViews.list') || this.recordView;
        },

        afterRender: function () {
            if (!this.hasView('list')) {
                this.loadList();
            }
        },

        loadList: function () {
            this.notify('Loading...');
            if (this.collection.isFetched) {
                this.createListRecordView(false);
            } else {
                this.listenToOnce(this.collection, 'sync', function () {
                    this.createListRecordView();
                }, this);
                this.collection.fetch();
            }
        },

        createListRecordView: function (fetch) {
            var o = {
                collection: this.collection,
                el: this.options.el + ' .list-container'
            };
            this.optionsToPass.forEach(function (option) {
                o[option] = this.options[option];
            }, this);
            var listViewName = this.getRecordViewName();

            this.createView('list', listViewName, o, function (view) {
                if (!this.hasParentView()) return;

                view.render();
                view.notify(false);
                if (this.searchPanel) {
                    this.listenTo(view, 'sort', function (obj) {
                        this.getStorage().set('listSorting', this.collection.name, obj);
                    }, this);
                }
                if (fetch) {
                    setTimeout(function () {
                        this.collection.fetch();
                    }.bind(this), 2000);
                }
            });
        },

    });

});
