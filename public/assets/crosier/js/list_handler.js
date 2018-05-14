
$(document).ready(function () {

    $('.crsr-datatable').each(function () {
        if ($.fn.dataTable.isDataTable(this)) {
            table = this.DataTable();
        } else {
            $(this).DataTable({
                paging: false,
                searching: false
            });
        }
    });


});