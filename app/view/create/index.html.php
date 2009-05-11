<uses:layout layout="default" title="Slicehost Manager - Create Slice" />

<form method="post" action="/configs">
	<input type="hidden" name="real_method" value="put" />
	<div class="card">
		<div class="group">
			<ul>
				<li>Name</li>
				<li><input type="text" name="name" /></li>
			</ul>
		</div>
		<div class="group">
			<ul>
				<li>Flavor</li>
				<li>
					<select name="flavor_id">
						<? foreach($flavors as $flavor): ?>
						<option value="{{$flavor->id}}">{{$flavor->name}}</option>
						<? endforeach; ?>
					</select>
				</li>
			</ul>
		</div>
		<div class="group">
			<ul>
				<li>Image</li>
				<li>
					<select name="image_id">
						<option value="none">None</option>
						<? foreach($images as $image): ?>
						<option value="{{$image->id}}">{{$image->name}}</option>
						<? endforeach; ?>
					</select>
				</li>
			</ul>
		</div>
		<div class="group">
			<ul>
				<li>Back Up</li>
				<li>
					<select name="backup_id">
						<option value="none">None</option>
						<? foreach($backups as $backup): ?>
						<option value="{{$backup->id}}">{{$backup->name}}</option>
						<? endforeach; ?>
					</select>
				</li>
			</ul>
		</div>
		<div class="group">
			<ul>
				<li>Notes</li>
				<li><textarea name="notes" cols="30" rows="8"></textarea></li>
			</ul>
		</div>
		<div class="group">
			<input type="submit" value="Create Configuration" />
		</div>
	</div>
</form>