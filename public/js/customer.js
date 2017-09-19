$(function(){
	Customer.init();
});


var Customer = {
	init: function(){
		this.catchDom();
		this.bindElements();
		this.initDatatables();
		this.initFancyBox();
	},

	catchDom: function(){
		this.$dodBtn = $('#send_dod');
        this.$approveDeposit = $(".approve_deposit");
		this.$verificationSelect = $('#verification_status');	
		this.$depositsList = $('#deposits_list');
		this.$dodForm = $('#send_dod_form');
		this.$customerId = $('#customer_id').val();
		this.$approveDenyBtn = $('.approve_deny_file');
		this.$editFileCommentsBtn = $('.edit_comments');
	},

	bindElements: function(){
		this.$dodBtn.on('click', this.sendDod.bind(this));
		this.$dodBtn.on('click', this.sendDod.bind(this));
		this.$approveDenyBtn.on('click', this.approveDenyFile);
		this.$editFileCommentsBtn.on('click', this.editComments);
		this.$dodForm.on('submit', this.sendDodSubmitted.bind(this));
        this.$approveDeposit.on("click", this.approveDepositClicked)
	},

	initDatatables: function(){

		if($('#deposits_table tbody tr').size() > 10){
			$('#deposits_table').DataTable( {
		        "order": [[ 4, "desc" ]]
		    } );
		}else{
			$('#deposits_table').addClass('table table-striped');
			$('#deposits_table tfoot').remove();
		}

	    if($('#bonuses_table tbody tr').size() > 10){
	    	$('#bonuses_table').DataTable( {
		        "order": [[ 4, "desc" ]]
		    } );
	    }else{
			$('#bonuses_table').addClass('table table-striped');
			$('#bonuses_table tfoot').remove();
		}

	    if($('#withdrawals_table tbody tr').size() > 10){
			$('#withdrawals_table').DataTable( {
		        "order": [[ 4, "desc" ]]
		    } );
		}else{
			$('#withdrawals_table').addClass('table table-striped');
			$('#withdrawals_table tfoot').remove();
		}

	},

	initFancyBox: function(){

		$('.fancybox').fancybox({
		    width  : 800,
		    height : 600,
		    type   :'iframe'
		});

	},

    approveDepositClicked: function(e){
        e.preventDefault();
        var deposit_id = $(this).data("deposit-id");
        var button = $(this);
        $.post('/compliance/customer/toggle-deposit', {
            deposit_id: deposit_id
        }, function(data){
            if(data == 'error'){
                toastr.error('There was an error trying to update the deposit status.', 'Error!');
                return false;
            }
            toastr.success('The deposit status has been updated successfully.', 'Success!');

            if($(button).hasClass('btn-success')){
                $(button).removeClass('btn-success');
                $(button).addClass('btn-danger');
                $(button).html('<i class="fa fa-times"></i>');
            }else{
                $(button).removeClass('btn-danger');
                $(button).addClass('btn-success');
                $(button).html('<i class="fa fa-check"></i>');
            }
        });
    },

	sendDod: function(e){
		e.preventDefault();
		var deposits = $('.dodDeposits:checkbox:checked').map(function() {
		    return $(this).data('deposit-id');
		}).get();
		
		if(!deposits.length){
			swal('Error', 'Please select at least 1 deposit', 'error');
			return false;
		}	

		$.post('/get-deposits', {
			deposit_ids: JSON.stringify(deposits)
		}, this.generateDepositsModal.bind(this));

	},

	generateDepositsModal: function(deposits){

		var html = '';

		$.each(deposits, function(i, deposit){
			html += '<div class="row">';
			html += 	'<div class="col-md-4">Transaction ID: ' + deposit.transaction_id + '</div>';
			html += 	'<div class="col-md-3">Amount: ' + deposit.amount + '</div>';
			html += 	'<div class="col-md-3">Currency: ' + deposit.currency + '</div>';
			html += 	'<input type="hidden" name="deposits[' + i + '][id]" value="' + deposit.id + '" />';
			html += 	'<div class="col-md-2"><input type="text" placeholder="Last 4" name="deposits[' + i + '][last4]" class="form-control deposit_input" /></div>';
			html += '</div>';
			html += '<hr />';
		});

		var primaryEmail = $('#primary_email').val();
		var secondaryEmail = $('#secondary_email').data('email').trim();

		html += '<input type="hidden" name="customer_id" value="' + this.$customerId + '" />';

		if(secondaryEmail){
			html += '<select name="email" class="form-control">';
			html += '<option value="' + primaryEmail + '">' + primaryEmail + '</option>';
			html += '<option value="' + secondaryEmail + '">' + secondaryEmail + '</option>';
			html += '</select>';
		}else{
			html += '<input type="hidden" name="email" value="' + primaryEmail + '" />';
		}

		this.$depositsList.html(html);

		$('#sendDodModal').modal('show');
	},

	editComments: function(e){

		e.preventDefault();

		var file_id = $(this).data('file-id');
		var file_name = $(this).data('file-name');

		$.post('/get-comments-for-file', {
			file_id: file_id
		}, function(data){
			if(data == 'error'){
				swal('Error', 'Something went wrong, please refresh the page abd try again', 'error');
				return false;
			}

			$('.file_name').html(file_name);
			$('#comments').val(data);
			$('#file_id').val(file_id);


			$('#fileCommentsModal').modal('show');
		});
	},

	sendDodSubmitted: function(e){

		var valid = true;

		$.each($('.deposit_input'), function(i, input){
			if($(input).val() == "" || !$.isNumeric($(input).val()) || $(input).val().length != 4){
				valid = false;
			}
		});

		if(!valid){
			e.preventDefault();
			swal('Error', 'Please make sure all fields have 4 digits numbers', 'error');
		}
		
	},

	approveDenyFile: function(e){

		e.preventDefault();

		var fileId = $(this).data('file-id');
		var action = $(this).data('action');
		
		if(!fileId)
			return false;

		$.post('/approve-deny-file', {
			file_id: fileId,
			action: action
		}, function(data){
			if(data != 'ok'){
				console.log(data);
			}else{
				location.reload();
			}
		});
	}

};