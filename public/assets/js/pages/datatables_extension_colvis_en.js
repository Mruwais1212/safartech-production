/* ------------------------------------------------------------------------------
*
*  # Columns Visibility (Buttons) extension for Datatables
*
*  Specific JS code additions for datatable_extension_colvis.html page
*
*  Version: 1.1
*  Latest update: Nov 10, 2015
*
* ---------------------------------------------------------------------------- */

$(function() {


    // Table setup
    // ------------------------------

    // Setting datatable defaults
    $.extend( $.fn.dataTable.defaults, {
        autoWidth: false,
        dom: '<"datatable-header"fBl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
        language: {
            // search: '<span>ابحث :</span> _INPUT_',
            // lengthMenu: '<span>اظهر :</span> _MENU_',
            // paginate: { 'first': 'First', 'last': 'Last', 'next': '&larr;', 'previous': '&rarr;' }
                "emptyTable": "No data available in table",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "Showing 0 to 0 of 0 entries",
                "infoFiltered": "(filtered from _MAX_ total entries)",
                "infoThousands": ",",
                "lengthMenu": "Show _MENU_ entries",
                "loadingRecords": "Loading...",
                "processing": "Processing...",
                "search": "Search:",
                "zeroRecords": "No matching records found",
                "thousands": ",",
                "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
                },
                "aria": {
                "sortAscending": ": activate to sort column ascending",
                "sortDescending": ": activate to sort column descending"
                },
                "autoFill": {
                "cancel": "Cancel",
                "fill": "Fill all cells with <i>%d</i>",
                "fillHorizontal": "Fill cells horizontally",
                "fillVertical": "Fill cells vertically"
                },
                "buttons": {
                "collection": "Collection <span class='ui-button-icon-primary ui-icon ui-icon-triangle-1-s'/>",
                "colvis": "Column Visibility",
                "colvisRestore": "Restore visibility",
                "copy": "Copy",
                "copyKeys": "Press ctrl or u2318 + C to copy the table data to your system clipboard.<br><br>To cancel, click this message or press escape.",
                "copySuccess": {
                "1": "Copied 1 row to clipboard",
                "_": "Copied %d rows to clipboard"
                },
                "copyTitle": "Copy to Clipboard",
                "csv": "CSV",
                "excel": "Excel",
                "pageLength": {
                "-1": "Show all rows",
                "_": "Show %d rows"
                },
                "pdf": "PDF",
                "print": "Print",
                "updateState": "Update",
                "stateRestore": "State %d",
                "savedStates": "Saved States",
                "renameState": "Rename",
                "removeState": "Remove",
                "removeAllStates": "Remove All States",
                "createState": "Create State"
                },
                "searchBuilder": {
                "add": "Add Condition",
                "button": {
                "0": "Search Builder",
                "_": "Search Builder (%d)"
                },
                "clearAll": "Clear All",
                "condition": "Condition",
                "conditions": {
                "date": {
                "after": "After",
                "before": "Before",
                "between": "Between",
                "empty": "Empty",
                "equals": "Equals",
                "not": "Not",
                "notBetween": "Not Between",
                "notEmpty": "Not Empty"
                },
                "number": {
                "between": "Between",
                "empty": "Empty",
                "equals": "Equals",
                "gt": "Greater Than",
                "gte": "Greater Than Equal To",
                "lt": "Less Than",
                "lte": "Less Than Equal To",
                "not": "Not",
                "notBetween": "Not Between",
                "notEmpty": "Not Empty"
                },
                "string": {
                "contains": "Contains",
                "empty": "Empty",
                "endsWith": "Ends With",
                "equals": "Equals",
                "not": "Not",
                "notEmpty": "Not Empty",
                "startsWith": "Starts With",
                "notContains": "Does Not Contain",
                "notStartsWith": "Does Not Start With",
                "notEndsWith": "Does Not End With"
                },
                "array": {
                "without": "Without",
                "notEmpty": "Not Empty",
                "not": "Not",
                "contains": "Contains",
                "empty": "Empty",
                "equals": "Equals"
                }
                },
                "data": "Data",
                "deleteTitle": "Delete filtering rule",
                "leftTitle": "Outdent Criteria",
                "logicAnd": "And",
                "logicOr": "Or",
                "rightTitle": "Indent Criteria",
                "title": {
                "0": "Search Builder",
                "_": "Search Builder (%d)"
                },
                "value": "Value"
                },
                "searchPanes": {
                "clearMessage": "Clear All",
                "collapse": {
                "0": "SearchPanes",
                "_": "SearchPanes (%d)"
                },
                "count": "{total}",
                "countFiltered": "{shown} ({total})",
                "emptyPanes": "No SearchPanes",
                "loadMessage": "Loading SearchPanes",
                "title": "Filters Active - %d",
                "showMessage": "Show All",
                "collapseMessage": "Collapse All"
                },
                "select": {
                "cells": {
                "1": "1 cell selected",
                "_": "%d cells selected"
                },
                "columns": {
                "1": "1 column selected",
                "_": "%d columns selected"
                },
                "rows": {
                "1": "1 row selected",
                "_": "%d rows selected"
                }
                },
                "datetime": {
                "previous": "Previous",
                "next": "Next",
                "hours": "Hour",
                "minutes": "Minute",
                "seconds": "Second",
                "unknown": "-",
                "amPm": [
                "am",
                "pm"
                ],
                "weekdays": [
                "Sun",
                "Mon",
                "Tue",
                "Wed",
                "Thu",
                "Fri",
                "Sat"
                ],
                "months": [
                "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December"
                ]
                },
                "editor": {
                "close": "Close",
                "create": {
                "button": "New",
                "title": "Create new entry",
                "submit": "Create"
                },
                "edit": {
                "button": "Edit",
                "title": "Edit Entry",
                "submit": "Update"
                },
                "remove": {
                "button": "Delete",
                "title": "Delete",
                "submit": "Delete",
                "confirm": {
                "1": "Are you sure you wish to delete 1 row?",
                "_": "Are you sure you wish to delete %d rows?"
                }
                },
                "error": {
                "system": "A system error has occurred (<a target=\"\\\" rel=\"nofollow\" href=\"\\\">More information</a>)."
                },
                "multi": {
                "title": "Multiple Values",
                "info": "The selected items contain different values for this input. To edit and set all items for this input to the same value, click or tap here, otherwise they will retain their individual values.",
                "restore": "Undo Changes",
                "noMulti": "This input can be edited individually, but not part of a group. "
                }
                },
                "stateRestore": {
                "renameTitle": "Rename State",
                "renameLabel": "New Name for %s:",
                "renameButton": "Rename",
                "removeTitle": "Remove State",
                "removeSubmit": "Remove",
                "removeJoiner": " and ",
                "removeError": "Failed to remove state.",
                "removeConfirm": "Are you sure you want to remove %s?",
                "emptyStates": "No saved states",
                "emptyError": "Name cannot be empty.",
                "duplicateError": "A state with this name already exists.",
                "creationModal": {
                "toggleLabel": "Includes:",
                "title": "Create New State",
                "select": "Select",
                "searchBuilder": "SearchBuilder",
                "search": "Search",
                "scroller": "Scroll Position",
                "paging": "Paging",
                "order": "Sorting",
                "name": "Name:",
                "columns": {
                "visible": "Column Visibility",
                "search": "Column Search"
                },
                "button": "Create"
                }
                }
        }
    });

    
    // Basic example
    $('.datatable-colvis-basic').DataTable({
        buttons: [
            {
                extend: 'colvis',
                className: 'btn btn-default'
            }
        ]
    });


    // Multi-column layout
    $('.datatable-colvis-multi').DataTable({
        buttons: [
            {
                extend: 'colvis',
                text: '<i class="icon-three-bars"></i> <span class="caret"></span>',
                className: 'btn bg-blue btn-icon',
                collectionLayout: 'fixed two-column'
            }
        ]
    });


    // Restore column visibility
    $('.datatable-colvis-restore').DataTable({
        buttons: [
            {
                extend: 'colvis',
                text: '<i class="icon-grid7"></i> <span class="caret"></span>',
                className: 'btn bg-teal-400 btn-icon',
                postfixButtons: [ 'colvisRestore' ]
            }
        ],
        columnDefs: [
            {
                targets: -1,
                visible: false
            }
        ]
    });


    // State saving
    $('.datatable-colvis-state').DataTable({
        buttons: [
            {
                extend: 'colvis',
                text: '<i class="icon-grid3"></i> <span class="caret"></span>',
                className: 'btn bg-indigo-400 btn-icon'
            }
        ],
        stateSave: true,
        columnDefs: [
            {
                targets: -1,
                visible: false
            }
        ]
    });


    // Column groups
    $('.datatable-colvis-group').DataTable({
        buttons: {
            dom: {
                button: {
                    className: 'btn btn-default'
                }
            },
            buttons: [
                {
                    extend: 'colvisGroup',
                    text: 'Office info',
                    show: [0, 1, 2],
                    hide: [3, 4, 5]
                },
                {
                    extend: 'colvisGroup',
                    text: 'HR info',
                    show: [3, 4, 5],
                    hide: [0, 1, 2]
                },
                {
                    extend: 'colvisGroup',
                    text: 'Show all',
                    show: ':hidden'
                }
            ]
        }
    });



    // External table additions
    // ------------------------------

    // Launch Uniform styling for checkboxes
    $('.ColVis_Button').addClass('btn btn-primary btn-icon').on('click mouseover', function() {
        $('.ColVis_collection input').uniform();
    });


    // Add placeholder to the datatable filter option
    $('.dataTables_filter input[type=search]').attr('placeholder', 'Search');


    // Enable Select2 select for the length option
    $('.dataTables_length select').select2({
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });
    
});
