'use strict';

import $ from 'jquery';
global.$ = $; // manter isso até remover todos os <script>'s dos templates

import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap';

import 'popper.js';

import 'pace-progress/themes/black/pace-theme-barber-shop.css';
import 'pace';

import 'perfect-scrollbar';

import 'font-awesome/css/font-awesome.css';

import 'flag-icon-css/css/flag-icon.css';


import 'simple-line-icons/css/simple-line-icons.css';


import '@coreui/coreui';
import '@coreui/coreui/dist/css/coreui.css';


import 'datatables/media/css/jquery.dataTables.css';

import 'select2/select2.css';
import 'select2';

import 'jquery-mask-plugin';
import 'jquery-maskmoney/dist/jquery.maskMoney.js';

// como adicionar o crosier.js e crosier.css ???

import '../../css/crosier/crosier.css';


$(document).ready(function() {

    $(document).ajaxStart(function() {
        Pace.restart();
    });

});

