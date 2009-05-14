<div id="right">
	<h1>Actions</h1>
	<form method="post" action="/slices/{{$slice->id}}">
		<input type="hidden" name="action" value="put" />
		<input type="submit" value="Reboot" onclick="return confirm('Are you sure you want to reboot this slice?');" />
	</form>
	<form method="post" action="/slices/{{$slice->id}}">
		<input type="hidden" name="real_method" value="put" />
		<input type="hidden" name="action" value="hard_reboot" />
		<input type="submit" value="Hard Reboot" onclick="return confirm('Are you sure you want to reboot this slice?');"  />
	</form>
	
	<h1>Rebuild With Image</h1>
	<form method="post" action="/slices/{{$slice->id}}">
		<input type="hidden" name="real_method" value="put" />
		<input type="hidden" name="action" value="image_rebuild" />
		<select name="image_id">
			<? foreach($images as $image): ?>
			<option value="{{$image->id}}" {{ ($slice->image_id==$image->id) ? 'selected' : ''}} >{{$image->name}}</option>
			<? endforeach; ?>
		<input type="submit" value="Rebuild" />
	</form>
	
	<h1>Rebuild With Backup</h1>
	<form method="post" action="/slices/{{$slice->id}}">
		<input type="hidden" name="real_method" value="put" />
		<input type="hidden" name="action" value="backup_rebuild" />
		<select name="backup_id">
			<? foreach($backups as $backup): ?>
			<option value="{{$backup->id}}">{{$backup->name}}</option>
			<? endforeach; ?>
		<input type="submit" value="Rebuild" />
	</form>

	<h1>Delete</h1>
	<form method="post" action="/slices/{{$slice->id}}">
		<input type="hidden" name="real_method" value="delete" />
		<input type="submit" class="delete" value="Delete"  onclick="return confirm('Are you sure you want to delete this slice?');" />
	</form>
</div>

<div id="left">
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
				<li>Status</li>
				<li>{{$slice->status}}</li>
			</ul>
			<ul>
				<li>Progress</li>
				<li>{{$slice->progress}}%</li>
			</ul>
			<? if ($slice->root_password): ?>
			<ul>
				<li>Root Password</li>
				<li>{{$slice->root_password}}</li>
			</ul>
			<? endif; ?>
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
</div>
