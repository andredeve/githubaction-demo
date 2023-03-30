$(function () {
    initDataTable();
    var col = $("#tabelaClassificacoes").children('tr').children('td').length;
    if (!$("#tabelaClassificacoes").hasClass('dataTable')) {
        $("#tabelaClassificacoes").dataTable({
            "sDom": "<'row'<'col'l'B><'col'f>r>t<'row'<'col'i><'col'p>>",
            //"sDom": "<'row'<'col-md-6 col-lg-6'i><'col-md-6 col-lg-6 col-xs-12 text-right'B>r>t<'row'<'col-md-6 hidden-xs'i><'col-md-6 col-xs-12'p>>",
            "aaSorting": [],
            "bStateSave": false,
            responsive: true,
            "deferRender": true,
            "autoWidth": false,
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    className: 'btn-success btn-sm',
                    exportOptions: {
                        page: 'all'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o"></i> PDF',
                    orientation: 'portrait',
                    messageBottom: null,
                    footer: true,
                    className: 'btn-info btn-sm',
                    exportOptions: {
                        page: 'all',
                        columns: [ 1, 2, 3, 4,5]
                    },
                   // download: 'open',
                    pageSize: 'A4', //A3 , A5 , A6 , legal , letter
                    customize: function (doc) {
                        let widths = [165,40,40,115,155];
                        let titulo = `CONARQ`;
                        customizeExportPDF(doc, titulo, widths);
                        let rowCount = doc.content[0].table.body.length;
//                        for (let i = 1; i < rowCount; i++) {
//                            doc.content[0].table.body[i][0].alignment = 'center';
//                            doc.content[0].table.body[i][5].alignment = 'center';
//                            doc.content[0].table.body[i][6].alignment = 'center';
//                        }
                    }
                }
            ],"initComplete": function (settings, json) {
                $(this).fadeIn('slow');
            },
            "aoColumnDefs": [
                {
                    "sClass": "text-center",
                    "aTargets": [0]
                },
                {
                    "sClass": "text-center",
                    bSortable: false,
                    "aTargets": [col - 1]
                }
            ]
        });
    }
});
