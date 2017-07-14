(function (OC) {
    'use strict';

    OC.Settings = OC.Settings || {};
    OC.Settings.TwoFactorBackup = OC.Settings.TwoFactorBackup || {};

    $(function () {
        var view = new OC.Settings.TwoFactorBackup.View({
            el: $('#twofactor-backup-codes-settings')
        });
        view.render();
    });
})(OC);

