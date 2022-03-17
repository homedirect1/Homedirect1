/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Recurring
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
define(
    [
    'uiComponent'
    ],
    function (Component) {
    'use strict';
    return Component.extend({
       initialize: function (config, node) {
            node.value = 'test';
       }
    });
    }
);