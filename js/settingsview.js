/* global Backbone, Handlebars, OC */

(function (OC, Handlebars, $) {
    'use strict';

    OC.Settings = OC.Settings || {};
    OC.Settings.TwoFactorBackup = OC.Settings.TwoFactorBackup || {};

    var TEMPLATE = '<div>'
            + '<span>' + t('twofactor_backup_codes', 'Backup codes let you access your account if your other factors are not available.') + '</span><br>'
            + '{{#unless remaining}}'
            + '	   <span>' + t('twofactor_backup_codes', 'You can generate second factor backup codes below.') + '</span><br>'
            + '	   <button id="backup-generate-backup-codes" class="button">' + t('twofactor_backup_codes', 'Generate codes') + '</button>'
            + '{{else}}'
            +      '{{#if codes}}'
            +          '<ul>'
            +          '{{#each codes}}'
            +          '<li class="backup-code">{{this}}</li>'
            +          '{{/each}}'
            +          '</ul>'
            +          '<span>' + t('twofactor_backup_codes', 'Keep them somewhere accessible, like your wallet. Each code can be used only once.') + '</span><br>'
            + '    {{else}}'
            + '	       <span>' + t('twofactor_backup_codes', 'You have {{remaining}} backup codes left can be used.') + '</span><br>'
            + '    {{/if}}'
            + '	   <button id="backup-generate-backup-codes" class="button">' + t('twofactor_backup_codes', 'Regenerate codes') + '</button>'
            + '{{/unless}}'
            + '</div>';

    var View = OC.Backbone.View.extend({
        template: Handlebars.compile(TEMPLATE),

        /**
         * @type {boolean}
         */
        _loading: undefined,

        /**
         * @type {Number}
         */
        _remaining: undefined,

        /**
         * @type {Array}
         */
        _codes: undefined,

        events: {
            'click #backup-generate-backup-codes': '_clickGenerateBackupCodes'
        },

        /**
         * @returns {undefined}
         */
        initialize: function () {
            this._load();
        },

        /**
         * @returns {self}
         */
        render: function() {
            this.$el.html(this.template({
                remaining: this._remaining,
                codes: this._codes
            }));

            return this;
        },

        /**
         * @returns {undefined}
         */
        _load: function () {
            this._loading = true;

            var url = OC.generateUrl('/apps/twofactor_backup_codes/settings/state');
            var loading = $.ajax(url, {
                method: 'GET'
            });

            var _this = this;
            $.when(loading).done(function (data) {
                _this._remaining = data.remaining;
            });
            $.when(loading).always(function () {
                _this._loading = false;
                this.render();
            }.bind(this));
        },
        _clickGenerateBackupCodes: function () {
            // Hide old codes
            this._remaining = 0;
            this.render();
            $('#generate-backup-codes').addClass('icon-loading-small');
            var url = OC.generateUrl('/apps/twofactor_backup_codes/settings/generateBackupCodes');
            $.ajax(url, {
                method: 'POST'
            }).done(function(data) {
                this._remaining = data.remaining;
                this._codes = data.codes;
                this.render();
            }.bind(this));
        },
    });
    OC.Settings.TwoFactorBackup.View = View;

})(OC, Handlebars, $);
