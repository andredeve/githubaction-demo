$(function () {
   $("#tabelaAnexosPorPeriodo").dataTable({
            "sDom": "<'row'<'col-md-6 col-lg-6'i><'col-md-6 col-lg-6 col-xs-12 text-right'B>r>t<'row'<'col-md-6 hidden-xs'i><'col-md-6 col-xs-12'p>>",
            "aaSorting": [],
            "autoWidth": false,
            "bStateSave": false,
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
                        columns: [0, 1, 2, 3, 4,5,6]
                    },
                   // download: 'open',
                    pageSize: 'A4', //A3 , A5 , A6 , legal , letter
                    customize: function (doc) {
//                        let widths = [300,110,110];
                        let widths = [50, 80, 75, 45, 95,95,45];
                        let titulo =`Relatório de Anexo por Período (${$('#data_periodo_ini').val()} - ${$('#data_periodo_fim').val()})`;
                       // titulo =``;
                        customizeExportPDF(doc, titulo, widths);
                        let rowCount = doc.content[0].table.body.length;
                        doc.content[0].table.widths = widths;
//                        debugger;
                        for (let i = 1; i < rowCount; i++) {
                            doc.content[0].table.body[i][0].alignment = 'center';
                            doc.content[0].table.body[i][2].alignment = 'center';
                            doc.content[0].table.body[i][3].alignment = 'center';
                            doc.content[0].table.body[i][6].alignment = 'center';
//                            doc.content[0].table.body[i][7].alignment = 'center';
                        }
                    }
                }
            ],
            "aoColumnDefs": [
                {
                    type: 'date-uk',
                    "aTargets": [2]
                },
                {
                    type: 'date-uk',
                    "aTargets": [3]
                },
                {
                    "sClass": "col-actions vertical-middle",
                    bSortable: false,
                    "aTargets": [7]
                }

            ]
        }
    );
    $("#tabelaAnexoQuantitativo").dataTable({
        "sDom": "<'row'<'col-md-6 col-lg-6'i><'col-md-6 col-lg-6 col-xs-12 text-right'B>r>t<'row'<'col-md-6 hidden-xs'i><'col-md-6 col-xs-12'p>>",
        "aaSorting": [],
        "autoWidth": false,
        "bStateSave": false,
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
                    page: 'all'
                },
               // download: 'open',
                pageSize: 'A4', //A3 , A5 , A6 , legal , letter
                customize: function (doc) {
                    let widths = [300,110,110];
//                    let widths = [];
                    let titulo = `Relatório de Quantitativo de Anexo por Período (${$('#data_periodo_ini').val()} - ${$('#data_periodo_fim').val()})`;
                    customizeExportPDF(doc, titulo, widths);
                    let rowCount = doc.content[0].table.body.length;
                    doc.content[0].table.widths = widths;
                    for (let i = 1; i < rowCount; i++) {
                        doc.content[0].table.body[i][1].alignment = 'center';
                        doc.content[0].table.body[i][2].alignment = 'center';
                    }
                }
            }
        ]
    });
});
