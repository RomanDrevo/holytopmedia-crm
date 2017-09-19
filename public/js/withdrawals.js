$(document).ready(function() {
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
    $(".withdrawal-table-selector").focus(function () {
        previous = this.value;
    }).change(function(){
        var employee = $(this).parent().parent().find('.withdrawal-worker-selector').val();
        if(employee == "Not Managed"){
            alert("You must select an employee first!");
            $(this).val(previous);
            return;
        }

        var withdrawal_id = $(this).data('withdrawal-id');
        var table_id = $(this).val();
        updateWithdrawalTable(withdrawal_id, table_id, employee);
    });



    /**
     * Once a new employee get selected to a withdrawal
     * we update it on DB
     */
    $('body').on('change', '.withdrawal-worker-selector', function(){
        var thisSelect = $(this);
        var select = $(this).parent().parent().find('.deposit-table-selector');
        var split_btn = $(this).parent().parent().find('.split_withdrawal');
        var withdrawal_id = $(this).data('withdrawal-id');
        var employee_id = $(this).val();
        $.post('/sales/assign-withdrawal-to-employee', {
            withdrawal_id: withdrawal_id,
            employee_id : employee_id
        }, function(data){
            if(data == "error"){
                alert("Something went wrong, please refresh the page.");
            }else{
                $(split_btn).show();
            }
        });
    });


    /**
     * Update the withdrawal type (FTD / RST)
     */
    $('.withdrawal_type_radio').on('change', function(){
        var withdrawal_id = $(this).data('withdrawal-id');
        var type = $(this).val();
        $.post('/sales/assign-withdrawal-to-type', {
            withdrawal_id: withdrawal_id,
            type : type
        }, function(data){
            if(data == "error"){
                alert("Something went wrong, please refresh the page.");
            }
        });
    });

    /**
     * Update the withdrawal verification status (YES / NO)
     */
    $('.is_verified_radio').on('change', function(){
        var withdrawal_id = $(this).data('withdrawal-id');
        var is_verified = $(this).val();
        $.post('/sales/update-withdrawal-verification-status', {
            withdrawal_id: withdrawal_id,
            is_verified : is_verified
        }, function(data){
            if(data == "error"){
                alert("Something went wrong, please refresh the page.");
            }
        });
    });

    /**
     * Fill out all the hidden fields and open the split modal
     */
    $('.split_withdrawal').on('click', function(){
        var employee_id = $(this).parent().parent().find('.withdrawal-worker-selector').val();
        var withdrawal_id = $(this).data('withdrawal-id');

        if(!employee_id){
            alert("You must select an employee in order to split!");
            return;
        }
        $('#withdrawal_employee_id').val(withdrawal_id);
        $('#withdrawal_amount').val($(this).data('withdrawal-amount'));
        $('#createWithdrawalSplitModal').modal('show');

    });

    /**
     * On split form submit, we check if the amount
     * requested is not greater than the withdrawal amount
     */
    $('#split_form').on('submit', function(e){
        var amount = parseInt($('#amount').val());
        var total = parseInt($('#withdrawal_amount').val());
        if(amount > total){
            e.preventDefault();
            alert("Split amount cannot be greater then the entire withdrawal!");
        }
    });

    /**
     * Send request to remove an existing split
     */
    $('.remove_split').on('click', function(){
        var withdrawal_id = $(this).data('withdrawal-id');
        var split_id = $(this).data('split-id');

        if(confirm('Are you sure you want to delete this split?')){
            $.post('/sales/remove-withdrawal-split', {
                withdrawal_id: withdrawal_id,
                split_id: split_id
            }, function(data){
                if(data != "error"){
                    location.reload();
                }else{
                    alert("Something went wrong, The page will be refreshed");
                    location.reload();
                }
            });
        }
    });
});

/**
 * Update the current table the employee is in on the
 * moment we assigned the withdrawal to him
 * 
 * @param  {integer} withdrawal_id
 * @param  {integer} table_id
 * @param  {integer} employee_id
 * @param  {jQuery Selector} selector
 */
function updateWithdrawalTable(withdrawal_id, table_id, employee_id, selector = undefined){
    $.post('/sales/assign-withdrawal-to-table', {
        employee_id: employee_id,
        withdrawal_id: withdrawal_id,
        table_id: table_id
    }, function(data){
        if(data == "error"){
            alert("Something went wrong, please refresh the page.");
        } else {
            updateWithdrawalType(data.table_id, data.id, data.withdrawal_type)
        }
    });

    if(selector){
        $(selector).val(table_id).trigger('change.select2');
    }


    function updateWithdrawalType(table_id, withdrawal_id, withdrawal_type) {

        if(!table_id) {
            $('.radio input[name=withdrawal_type_' +  withdrawal_id + ']').each(function() {
                $(this).prop("checked", false);
            });
        } else {
            $('.radio input[name=withdrawal_type_' +  withdrawal_id + ']').each(function() {
                if($(this).val() == withdrawal_type) {
                    $(this).prop("checked", true).trigger("click");
                }
            });
        }
    }
}