'use strict';

import $ from "jquery";

import CrosierMasks from './CrosierMasks';

class DatatablesJs {

    static makeDatatableJs(listId, columns) {
        $(document).ready(function () {

            let datatable = $(listId).DataTable({
                paging: true,
                serverSide: true,
                stateSave: true,
                ajax: {
                    'url': $(listId).data('listajaxurl'),
                    'type': 'POST',
                    'data': function (data) {
                        data.formPesquisar = $('#formPesquisar').serialize()
                    }

                },
                searching: false,
                columns: columns,
                "language": {
                    "url": "/build/static/datatables-Portuguese-Brasil.json"
                }
            });

            datatable.on('draw', function () {
                $('[data-toggle="tooltip"]').tooltip();
                CrosierMasks.maskAll();
            });


        });
    }

}

export default DatatablesJs;