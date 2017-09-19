<img src="{{$requestData["dod_image"]}}" width='700' height='1030' />
<div class="row">
	<div class="col-md-6">
		<img src="{{$requestData["dod_image"]}}" width='200' height='300' " />
	</div>
	<h1 style="color: #0091be;">
		IvoryOption Credit Card Transaction Authorisation (Single line)
	</h1>
	<table style="background-color: lightgray;width: 90%; height: 100px;font-size: 18px;margin-bottom: 50px;">
		<tr>
			<td>Created</td>
			<td>{{ Carbon\Carbon::now()->format('m-d-Y') }}</td>
		</tr>
		<tr>
			<td>By</td>
			<td>IvoryOption Documentation Department (documents@ivoryoption.com)</td>
		</tr>
		<tr>
			<td>Status</td>
			<td>Signed</td>
		</tr>
	</table>
	<ul>
		<li style="font-size: 16px;margin-bottom: 20px;">
			Document created by IvoryOption Documentation Department (documents@ivoryoption.com)<br/>
			{{ $form->created_at->format('m-d-Y H:i:s A T') }} - IP address: {{ $form->server_ip }}
		</li>
		<li style="font-size: 16px;margin-bottom: 20px;">
			Document emailed to {{ $customer->name }} ({{ $customer->email }}) for signature<br/>
			{{ $form->created_at->format('m-d-Y H:i:s A T') }} - IP address: {{ $form->server_ip }}
		</li>
		<li style="font-size: 16px;margin-bottom: 20px;">
			Document viewed by {{ $customer->name }} ({{ $customer->email }})<br/>
			{{ $form->viewed_at->format('m-d-Y H:i:s A T') }} - IP address: {{ $form->user_ip_on_view }}
		</li>
		<li style="font-size: 16px;margin-bottom: 20px;">
			Document e-signed by {{ $customer->name }} ({{ $customer->email }})<br/>
			{{ $form->signed_at->format('m-d-Y H:i:s A T') }} - IP address: {{ $form->user_ip_on_sign }}
		</li>
		<li style="font-size: 16px;margin-bottom: 20px;">
			Signed document emailed to IvoryOption Documentation Department (documents@ivoryoption.com) and {{ $customer->name }} ({{ $customer->email }})<br/>
			{{ $form->signed_at->format('m-d-Y H:i:s A T') }} - IP address: {{ $form->user_ip_on_sign }}
		</li>
	</ul>

</div>
