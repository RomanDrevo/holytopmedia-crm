<!DOCTYPE html>
<html>
<head>
	<title>DOD</title>

	<link href="/public/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="/public/bower_components/sweetalert2/dist/sweetalert2.min.css">
	<link rel="stylesheet" href="/public/pdf/css/dod.css">

</head>
<body>
	<div id="signature_area">
		<h2>Sign Here</h2>
		<div id="signature_container">
			<canvas id="canvas_signature">E-sign is not supported in your browser</canvas>
			<div class="col-xs-12 text-right">
				<button class="btn btn-xs btn-default" onclick="init_Sign_Canvas()" id="signature_clear_btn">Clear</button>
				<button class="btn btn-xs btn-primary" onclick="signature_done()" id="signature_success_btn">Insert signature to document</button>
			</div>
		</div>
		<hr>
		<h2>Initials Here</h2>
		<div id="initials_container">
			<canvas id="canvas_initials">E-sign is not supported in your browser</canvas>
			<div class="col-xs-12 text-right">
				<button class="btn btn-xs btn-default" onclick="init_Sign_initials_Canvas()" class="signature_clear_btn">Clear</button>
				<button class="btn btn-xs btn-primary" onclick="initials_done()" class="signature_success_btn">Insert initials to document</button>
			</div>
		</div>
		<hr>
		<h2>Full Name</h2>
		<input type="text" class="form-control" name="name" id="name" value="{{ $customer->name }}" placeholder="Full Name" />
		<div class="col-xs-12 text-right" style="margin-top: 5px;">
			<button class="btn btn-xs btn-primary" id="name_done_btn">Insert name to document</button>
		</div>
		<hr>
		<div id="done_btn_wrapper">
			<button class="btn btn-block btn-success" id="done_form">All Done!</button>
		</div>
	</div> 
	<div id="page-wrap">
		<div class="row">
			<div class="col-xs-6">
				<img id="logo" class="img-responsive" src="/public/img/ivory_logo.png" alt="logo" />
			</div>
			<div class="col-xs-6">
				<p id="right_line">
					Credit Card Transaction Authorisation Form
				</p>
			</div>
			<div class="row">
				<h1 id="main_header" class="text-center">
					Credit Card Transaction Authorisation Form
				</h1>
				<p class="explenation">
					Please complete this form directly on your computer via E-sign, following the instructions. Do

					not print and scan it. Please then return it together with your compliance documents. If it is not

					returned in this manner it cannot be accepted.
				</p>
				<p class="explenation">
					The following form confirms that you made a deposit on the date stated below. This gives

					permission for the company to debit your account for the listed transaction(s) only.
				</p>
			</div>
			<div class="row">
				<h3 class="text-center sub_header">
					This does not authorise any other debits to your account.
				</h3>
				<table border="1">
					<tr>
						<th>Date</th>
						<th>Card No.</th>
						<th>Amount</th>
						<th>Currency</th>
						<th>Signature</th>
					</tr>
					@foreach($deposits as $deposit)
					<tr>
						<td>{{ $deposit->confirm_time->format('m-d-Y') }}</td>
						<td>{{ $deposit->last4 }}</td>
						<td>{{ $deposit->amount }}</td>
						<td>{{ $deposit->currency }}</td>
						<td class="signature_area"></td>
					</tr>
					@endforeach
				</table>
			</div>
			<div class="row">
				<p class="explenation">
					I,................<span class="full_name_wrapper"></span>.......................................................(FullName) AcNo......{{ $customer->id }}.....

					Authorised IvoryOptionTM (the company) to charge my credit card for the above stated

					amount. By doing so I agreed to all Terms and Conditions of the Company. I also agreed that

					by signing this authorisation form I will not receive any goods but have transferred the above-
					stated funds into an online account.

					I certify that I am the authorised credit card holder and that I have made this transaction.
				</p>
			</div>
			<div class="row" id="signs_area">
				<p>
					Print Name: ............<span class="full_name_wrapper"></span>..........................
				</p>
				<p>
					<span class="pull-left">Signature: .......<span class="signature_area"></span>.....</span>
					<span class="pull-right">Date: ....<span class="date_wrapper">{{ Carbon\Carbon::now()->format('m/d/Y') }}</span>....</span>
				</p>
				<p style="margin-top: 100px;">
					<span class="pull-left">Initials .....<span class="initials_area"></span>..... Date ...<span class="date_wrapper">{{ Carbon\Carbon::now()->format('m/d/Y') }}</span>....
				</p>
			</div>
		</div>
	</div>
	<div style="display:none;">
		<form action="/store-dod" method="POST" id="image_form">
			{{ csrf_field() }}
			<input type="hidden" name="customer_id" value="{{ $customer->id }}">
			<input type="hidden" name="form_id" value="{{ $form->id }}">
			<input type="hidden" name="access_code" value="{{ $form->access_code }}">
			<input type="hidden" name="dod_image" id="img_val" value="">
		</form>
	</div>
	<script src="/public/js/jquery.min.js"></script>
	<script src="/public/bower_components/jspdf/dist/jspdf.min.js"></script>
	<script src="/public/js/html2canvas.min.js"></script>
	<script src="/public/js/jquery.plugin.html2canvas.js"></script>
	<script src="/public/bower_components/sweetalert2/dist/sweetalert2.min.js"></script>
	<script src="/public/pdf/js/dod.js"></script>
</body>
</html>
