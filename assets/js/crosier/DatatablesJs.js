'use strict';

import Utils from "./Utils";

class DatatablesJs {

    static makeDatatableJs(listId, columns) {
        $(document).ready(function () {

            $(listId).DataTable({
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

        });
    }

}

export default DatatablesJs;