<uses:layout layout="default" title="Slicehost Manager - Create Configuration" />
<uses:helper helper="form" />
<form method="post" action="/configs/create">
	<input type="hidden" name="real_method" value="put" />
	<div class="card">
		<div class="group">
			<ul>
				<li>Name</li>
				<li><input type="text" name="name" value="{{$controller->post->name}}"/><?if ($errors['name']): ?><span class="error">{{$errors['name']}}</span><? endif; ?></li>
			</ul>
		</div>
		<div class="group">
			<ul>
				<li>Flavor</li>
				<li>
					{{select("flavor_id","id","name",$flavors,$controller->post->flavor_id)}}
					<?if ($errors['flavor_id']): ?><span class="error">{{$errors['flavor_id']}}</span><? endif; ?>
				</li>
			</ul>
		</div>
		<div class="group">
			<ul>
				<li>Image</li>
				<li>
					{{select("image_id","id","name",$images,$controller->post->image_id,"No Image")}}
					<?if ($errors['image_id']): ?><span class="error">{{$errors['image_id']}}</span><? endif; ?>
				</li>
			</ul>
		</div>
		<div class="group">
			<ul>
				<li>Back Up</li>
				<li>
					{{select("backup_id","id","name",$backups,$controller->post->backup_id,"No Backup")}}
					<?if ($errors['backup_id']): ?><span class="error">{{$errors['backup_id']}}</span><? endif; ?>
				</li>
			</ul>
		</div>
		<div class="group">
			<ul>
				<li>Notes</li>
				<li><textarea name="notes" cols="30" rows="8">{{$controller->post->notes}}</textarea></li>
			</ul>
		</div>
		<div class="group">
			<input type="submit" value="Create Configuration" />
		</div>
	</div>
</form>