$(document).ready(function () {
    var previous;

    /**
     * Initialize Select 2 for all select boxes
     */
    $('.nice-select').select2();


    /**
     * Save the previous value on focus so we can
     * return to it if necessary. when selecting
     * a table we update it on the DB
     */
    $(".deposit-table-selector").focus(function () {
        previous = this.value;
    }).change(function () {
        var employee = $(this).parent().parent().find('.deposit-worker-selector').val();
        if (employee == "0") {
            alert("You must select an employee first!");
            $(this).val(previous);
            return;
        }

        var deposit_id = $(this).data('deposit-id');
        var table_id = $(this).val();
        updateDepositTable(deposit_id, table_id, employee);
    });


    /**
     * Once a new employee get selected to a deposit
     * we update it on DB
     */
    $('body').on('change', '.deposit-worker-selector', function () {
        var thisSelect = $(this);
        var select = $(this).parent().parent().find('.deposit-table-selector');
        var split_btn = $(this).parent().parent().find('.split_deposit');
        var deposit_id = $(this).data('deposit-id');
        var employee_id = $(this).val();
        $.post('/sales/assign-deposit-to-employee', {
            deposit_id: deposit_id,
            employee_id: employee_id
        }, function (data) {
            if (data == "error") {
                alert("Something went wrong, please refresh the page.");
            } else {
                if (data != "no_table") {
                    $(thisSelect).parent().parent().find('input[type=radio][value="' + data.type + '"]').attr('checked', 'checked');
                    updateDepositTable(deposit_id, data.id, employee_id, select);
                }

                $(split_btn).show();
            }
        });
    });


    /**
     * Update the deposit type (FTD / RST)
     */
    $('.deposit_type_radio').on('change', function () {
        var deposit_id = $(this).data('deposit-id');
        var type = $(this).val();
        $.post('/sales/assign-deposit-to-type', {
            deposit_id: deposit_id,
            type: type
        }, function (data) {
            if (data == "error") {
                alert("Something went wrong, please refresh the page.");
            }
        });
    });

    /**
     * Update the deposit verification status (YES / NO)
     */
    $('.is_verified_radio').on('change', function () {
        var deposit_id = $(this).data('deposit-id');
        var is_verified = $(this).val();
        $.post('/sales/update-deposit-verification-status', {
            deposit_id: deposit_id,
            is_verified: is_verified
        }, function (data) {
            if (data == "error") {
                alert("Something went wrong, please refresh the page.");
            }
        });
    });

    /**
     * Fill out all the hidden fields and open the split modal
     */
    $('.split_deposit').on('click', function () {
        var employee_id = $(this).parent().parent().find('.deposit-worker-selector').val();
        var deposit_id = $(this).data('deposit-id');

        if (!employee_id) {
            alert("You must select an employee in order to split!");
            return;
        }
        $('#deposit_employee_id').val(deposit_id);
        $('#deposit_amount').val($(this).data('deposit-amount'));
        $('#createSplitModal').modal('show');

    });

    /**
     * On split form submit, we check if the amount
     * requested is not greater than the deposit amount
     */
    $('#split_form').on('submit', function (e) {
        var amount = parseInt($('#amount').val());
        var total = parseInt($('#deposit_amount').val());
        if (amount > total) {
            e.preventDefault();
            alert("Split amount cannot be greater then the entire deposit!");
        }
    });

    /**
     * Send request to remove an existing split
     */
    $('.remove_split').on('click', function () {
        var deposit_id = $(this).data('deposit-id');
        var split_id = $(this).data('split-id');

        if (confirm('Are you sure you want to delete this split?')) {
            $.post('/sales/remove-split', {
                deposit_id: deposit_id,
                split_id: split_id
            }, function (data) {
                if (data != "error") {
                    location.reload();
                } else {
                    alert("Something went wrong, The page will be refreshed");
                    location.reload();
                }
            });
        }
    });
});

/**
 * Update the current table the employee is in on the
 * moment we assigned the deposit to him
 *
 * @param  {integer} deposit_id
 * @param  {integer} table_id
 * @param  {integer} employee_id
 * @param  {jQuery Selector} selector
 */
function updateDepositTable(deposit_id, table_id, employee_id, selector = undefined) {
    $.post('/sales/assign-deposit-to-table', {
        employee_id: employee_id,
        deposit_id: deposit_id,
        table_id: table_id
    }, function (data) {
        console.log(data);
        if (data == "error") {
            alert("Something went wrong, please refresh the page.");
        } else {
            updateDepositType(data.table_id, data.id, data.deposit_type);

        }
    });

    if (selector) {
        $(selector).val(table_id).trigger('change.select2');
    }


    function updateDepositType(table_id, deposit_id, deposit_type) {

        if(!table_id) {
            $('.radio input[name=deposit_type_' +  deposit_id + ']').each(function() {
                $(this).prop("checked", false);
            });
        } else {
            $('.radio input[name=deposit_type_' +  deposit_id + ']').each(function() {
                if($(this).val() == deposit_type) {
                    $(this).prop("checked", true).trigger("click");
                }
            });
        }
    }
}

