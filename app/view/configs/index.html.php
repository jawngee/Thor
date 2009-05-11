<uses:layout layout="default" title="Slicehost Manager - Server Configurations" />

<?	if (count($configs)==0): ?>
<div id="message">
	You don't have any configurations.  <a href="/configs/create">Create your first!</a>
</div>
<?	else: ?>
<table>
	<thead>
		<th>Name</th>
		<th>Image</th>
		<th>Backup</th>
		<th>Flavor</th>
		<th>Notes</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
	</thead>
	<tbody>
<? 		foreach($configs as $config): $count++; ?>
		<tr class="{{((($count %2)!=0) ? 'odd' : '')}}">
			<td>{{$config->name}}</td>
			<td>{{$config->image_desc}}</td>
			<td>{{$config->backup_desc}}</td>
			<td>{{$config->flavor_desc}}</td>
			<td>{{$config->notes}}</td>
			<td>
				<form method="post" action="/configs/{{$config->id}}">
					<input type="hidden" name="real_method" value="put" />
					<input type="hidden" name="action" value="instantiate" />
					<input type="submit" value="Create Instance" onclick="return confirm('Are you sure you want to create an instance of this configuration?');"  />
				</form>
			</td>
			<td>
				<form method="post" action="/configs/{{$config->id}}">
					<input type="hidden" name="real_method" value="delete" />
					<input type="submit" value="Delete Config" class="delete"  onclick="return confirm('Are you sure you want to delete this configuration?');" />
				</form>
			</td>
		</tr>			
<? 		endforeach; ?>
	</tbody>
</table>
<? 	endif; ?>