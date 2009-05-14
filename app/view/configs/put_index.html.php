 <uses:layout layout="default" title="Slicehost Manager - Create Configuration" />
 
 	<div class="card">
		<div class="group">
			<ul>
				<li>Name</li>
				<li>{{$slice->name}}</li>
			</ul>
			<ul>
				<li>Flavor</li>
				<li>{{$slice->flavor->name}}</li>
			</ul>
			<ul>
				<li>Image</li>
				<li>{{$slice->image->name}}</li>
			</ul>
		</div>
		<div class="group">
			<ul>
				<li>Root</li>
				<li>{{$slice->root_password}}</li>
			</ul>
		</div>
		<div class="group">
			<ul>
				<li>Status</li>
				<li>{{$slice->status}}</li>
			</ul>
			<ul>
				<li>Progress</li>
				<li>{{$slice->progress}}%</li>
			</ul>
		</div>
		<div class="group">
			<ul>
				<li>Addresses</li>
				<? foreach($slice->addresses as $address):?>
				<li>{{$address}}</li>
				<? endforeach; ?>
			</ul>
		</div>
		<div class="group">
			<ul>
				<li>Bandwidth</li>
				<li>{{$slice->bw_in+$slice->bw_out}}GB</li>
			</ul>
			<ul>
				<li>Incoming</li>
				<li>{{$slice->bw_in}}GB</li>
			</ul>
			<ul>
				<li>Outgoing</li>
				<li>{{$slice->bw_out}}GB</li>
			</ul>
		</div>
	</div>
