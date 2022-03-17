define([
    'Magento_Ui/js/grid/columns/column',
    'jquery',
    'Magento_Ui/js/modal/modal'
], function (Column, $, mageTemplate, sendmailPreviewTemplate) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'ui/grid/cells/html',
            fieldClass: {
                'data-grid-html-cell': true
            }
        },
        /* gethtml: function (row) {
         return row[this.index + '_html'];
         },

         getCustomerid: function (row) {
         return row[this.index + '_id'];
         },*/
        getLabel: function (row) {
            return row[this.index + '_htmltext']
        },
        /* getTitle: function (row) {
         return row[this.index + '_title']
         },*/


    });
});