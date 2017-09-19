$(document).ready(function() {

    $('.nice-select').select2();

    $('body').on('change', '.table-selector', function(){
        var employee_id = $(this).data('employee-id');
        var table_id = $(this).val();
        $.post('/sales/update-user-table', {
            table_id: table_id,
            employee_id : employee_id
        }, function(data){
            if(data == "error"){
                swal("Oops..", "Something went wrong, please refresh the page.", "error");
            }
        });
    });

    /**
     * Fill out all the hidden fields and open the split modal
     */
    $('.set_goal').on('click', function(){
        var employee_id = $(this).data('employee-id'),
            name = $(this).parent().parent().find('input[name="employee_name"]').val(),
            daily = $(this).data('daily-amount'),
            monthly = $(this).data('monthly-amount');

        $('#goal_employee_id').val(employee_id);
        $('#daily').val(daily);
        $('#monthly').val(monthly);
        $('.modal_worker_name').html(name);
        $('#updateGoalModal').modal('show');

    });


    $('.emloyee_name_input').on('focusin', function(){
        $(this).data('val', $(this).val());
    }).on('change', function(){
        var employee_id = $(this).data('employee-id');
        var name = $(this).val();

        if(name.length < 3){
            swal("Oops..", "Name must be 3 characters long", "error");
            $(this).val($(this).data('val'));
            return false;
        }

        $.post('/sales/update-employee-name', {
            employee_id: employee_id,
            name : name
        }, function(data){
            if(data == "error"){
                swal("Oops..", "Something went wrong, please refresh the page.", "error");
            }
        });
    });


});