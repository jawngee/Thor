{{str_pad('ID',8,' ')}}
 {{str_pad('Name',18,' ')}}
 {{str_pad('Status',12,' ')}}
 {{str_pad('IP Addy',32,' ')}}
 {{str_pad('Bandwidth',9,' ')}}

{{str_pad('',8,'=')}}
 {{str_pad('',18,'=')}}
 {{str_pad('',12,'=')}}
 {{str_pad('',32,'=')}}
 {{str_pad('',9,'=')}}

<? foreach($slices as $slice): $count++; ?>
{{str_pad($slice->id,8,' ')}}
 {{str_pad($slice->name,18,' ')}}
 {{str_pad($slice->status,12,' ')}}
 {{str_pad($slice->addresses[0].'/'.$slice->addresses[1],32,' ')}}
 {{str_pad($slice->bw_in+$slice->bw_out,9,' ')}}

<? endforeach; ?>
