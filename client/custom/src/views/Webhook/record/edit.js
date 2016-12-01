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

Espo.define('custom:views/Webhook/record/edit', 'views/record/edit', function (Dep) {

    return Dep.extend({

    	  quickDetailDisabled: true,

          quickEditDisabled: true,

          massActionList: ['remove'],

       // checkAllResultDisabled: true,
		
        template: 'record/edit',

        type: 'edit',

        name: 'edit',

        fieldsMode: 'edit',

        mode: 'edit',
		
        buttonList: [
            {
                name: 'save',
                label: 'Save',
                style: 'primary',
            },
            {
                name: 'cancel',
                label: 'Cancel',
            },
            {
                name: 'generate',
                label: 'Generate',
            }
        ],
	//sideView: 'views/record/edit-side',

       // bottomView: 'views/record/edit-bottom',

        duplicateAction: false,
		
	actionGenerate: function () {
			
			if( '' === $("input[name='campaignName']").val() ){
				this.notify('Please select campaign.', 'error');
				return;
			} 
			if( 0 == $(".field div > div").length ) {
				this.notify('Please select parameters.', 'error');
				return;
			}	
			
			var url = 'Webhook/action/generate';
			var strUrl = window.location.protocol+'//'+window.location.host+window.location.pathname+'api/v1/Webhook/action/generate';
			for (var name in this.getFieldViews() ) {
				if( name == 'campaign' ){
					strUrl +='?'+name+'='+$("input[name='campaignName']").val();
					url    +='?'+name+'='+$("input[name='campaignName']").val();
					//break;
				}
				
			}
			
			var campname = $("input[name='campaignId']").attr('name');
			strUrl +='&'+campname+'='+$("input[name='campaignId']").val();
			url    +='&'+campname+'='+$("input[name='campaignId']").val();
			
			$('.panel-body .row div .field div div').each(function() {
				var strName = $(this).text()
				strName = $.trim(strName);
				
				strUrl += '&'+strName+'=';
				url    += '&'+strName+'=';
			});
			
			$(".panel-body .row div .field textarea[name='code']").val( strUrl);

        },
        actionSave: function () {
          
			var url = 'Webhook/action/validate';

			var hookname = $("input[name='name']").val();
			hookname = hookname.trim();
			hookname = hookname.replace(/\s+/g, " ");
			var flag = true;
			var resultData = '';
			 
			if( '' != hookname ){
				  var  hookId = '';
				 
				  var arrLocationData = $(location).attr('href').split('/');
				  if( -1 !==  arrLocationData.indexOf("edit" ) ) { 
						hookId = arrLocationData[arrLocationData.length-1];
				  } 
				 
				  $.ajax({
					  url: url,
					  async: false,
					  type: 'POST',
					  data: JSON.stringify({
							  hook: hookname,
							  hookId : hookId
					  }),
					  success: function ( data, flag ) { 
							  if( 'true' ==  data.result ) {
								 flag = false;
								 resultData = data.result;
								  $("input[name='name']").val( '' );
								  this.notify('Webhook Name already exists.', 'error');
								 
							  } 
					  }.bind(this),
					  error: function () {
						 this.notify('Error occurred', 'error');
					  }.bind(this)
				  });
			} 
			
			if( 'true' !== resultData ) {
				this.save();	
			}
        },
         save: function (callback) {    
             
            this.beforeBeforeSave();

            var data = this.fetch();

            var self = this;
            var model = this.model;

            var initialAttributes = this.attributes;

            var beforeSaveAttributes = this.model.getClonedAttributes();

            data = _.extend(Espo.Utils.cloneDeep(beforeSaveAttributes), data);

            var attrs = false;
            if (model.isNew()) { 
                attrs = data;
				
            } else { 
                for (var name in data) {
					
                    if (_.isEqual(initialAttributes[name], data[name])) {
                        continue;
                    }
                    (attrs || (attrs = {}))[name] = data[name];
				
                }
            }
			
            if (!attrs) {
                this.trigger('cancel:save');
                this.afterNotModified();
                return true;
            }

            model.set(attrs, {silent: true});

            if (this.validate()) {
                model.attributes = beforeSaveAttributes;
                this.trigger('cancel:save');
                this.afterNotValid();
                return;
            }

            this.beforeSave();

            this.trigger('before:save');
            model.trigger('before:save');

            model.save(attrs, {
                success: function () {
                    this.afterSave();
                    if (self.isNew) {
                        self.isNew = false;
                    }
                    this.trigger('after:save');
                    model.trigger('after:save');
                    if (!callback) {
                        this.exit('save');
                    } else {
                        callback(this);
                    }
                }.bind(this),
                error: function (e, xhr) {
                    var r = xhr.getAllResponseHeaders();
                    var response = null;

                    if (xhr.status == 409) {
                        var header = xhr.getResponseHeader('X-Status-Reason');
                        try {
                            var response = JSON.parse(header);
                        } catch (e) {
                            console.error('Error while parsing response');
                        }
                    }

                    if (xhr.status == 400) {
                        if (!this.isNew) {
                            this.model.set(this.attributes);
                        }
                    }

                    if (response) {
                        if (response.reason == 'Duplicate') {
                            xhr.errorIsHandled = true;
                            self.showDuplicate(response.data);
                        }
                    }

                    this.afterSaveError();

                    model.attributes = beforeSaveAttributes;
                    self.trigger('cancel:save');

                }.bind(this),
                patch: !model.isNew()
            });
            return true;
        },
        validate: function () {
            var notValid = false;
            var fields = this.getFields();
            
            for (var i in fields) {
                if (fields[i].mode == 'edit') {
                    if (!fields[i].disabled && !fields[i].readOnly) {
                        notValid = fields[i].validate() || notValid;
                    }
                }
            };
            return notValid
        },
        actionCancel: function () {
            this.cancel();
        },

        cancel: function () {
            if (this.isChanged) {
                this.model.set(this.attributes);
            }
            this.setIsNotChanged();
            this.exit('cancel');
        },

        setupFinal: function () {
            if (this.model.isNew()) {
                this.populateDefaults();
            }
            Dep.prototype.setupFinal.call(this);
        }

    });
});
