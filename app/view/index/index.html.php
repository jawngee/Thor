<uses:layout layout="default" title="Slicehost Manager - Server Inventory" />

<table>
	<thead>
		<th>Name</th>
		<th>Status</th>
		<th>IP Address</th>
		<th>Bandwidth</th>
		<th>In</th>
		<th>Out</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
	</thead>
	<tbody>
<? foreach($slices as $slice): $count++; ?>
		<tr class="{{((($count %2)!=0) ? 'odd' : '')}}">
			<td><a href="/slices/{{$slice->id}}">{{$slice->name}}</a></td>
			<td>{{$slice->status}}</td>
			<td>
				<? foreach($slice->addresses as $address):?>{{$address}}<br/><? endforeach; ?>
			</td>
			<td>{{$slice->bw_in+$slice->bw_out}} GB</td>
			<td>{{$slice->bw_in}} GB</td>
			<td>{{$slice->bw_out}} GB</td>
			<td>
				<form method="post" action="/slices/{{$slice->id}}">
					<input type="hidden" name="real_method" value="put" />
					<input type="hidden" name="action" value="reboot" />
					<input type="submit" value="Reboot" onclick="return confirm('Are you sure you want to reboot this slice?');" />
				</form>
			</td>
			<td>
				<form method="post" action="/slices/{{$slice->id}}">
					<input type="hidden" name="real_method" value="put" />
					<input type="hidden" name="action" value="hard_reboot" />
					<input type="submit" value="Hard Reboot" onclick="return confirm('Are you sure you want to reboot this slice?');"  />
				</form>
			</td>
			<td>
				<form method="post" action="/slices/{{$slice->id}}">
					<input type="hidden" name="real_method" value="delete" />
					<input type="submit" value="Delete Slice" class="delete"  onclick="return confirm('Are you sure you want to delete this slice?');" />
				</form>
			</td>
		</tr>			
<? endforeach; ?>
	</tbody>
</table>