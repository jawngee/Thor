
{{str_pad('ID',8,' ')}}
 {{str_pad('Name',18,' ')}}
 {{str_pad('Flavor',12,' ')}}
 {{str_pad('Image',32,' ')}}
 {{str_pad('Status',12,' ')}}
 {{str_pad('Progress',8,' ')}}
 {{str_pad('Root Password',18,' ')}}
 {{str_pad('Addresses',32,' ')}}
 {{str_pad('Bandwidth',9,' ')}}
 {{str_pad('In',9,' ')}}
 {{str_pad('Out',9,' ')}}

{{str_pad('',8,'=')}}
 {{str_pad('',18,'=')}}
 {{str_pad('',12,'=')}}
 {{str_pad('',32,'=')}}
 {{str_pad('',12,'=')}}
 {{str_pad('',8,'=')}}
 {{str_pad('',18,'=')}}
 {{str_pad('',32,'=')}}
 {{str_pad('',9,'=')}}
 {{str_pad('',9,'=')}}
 {{str_pad('',9,'=')}}

{{str_pad($slice->id,8,' ')}}
 {{str_pad($slice->name,18,' ')}}
 {{str_pad($slice->flavor->name,12,' ')}}
 {{str_pad($slice->image->name,32,' ')}}
 {{str_pad($slice->status,12,' ')}}
 {{str_pad($slice->progress.'%',8,' ')}}
 {{str_pad($slice->root_password,18,' ')}}
 {{str_pad($slice->addresses[0].'/'.$slice->addresses[1],32,' ')}}
 {{str_pad(($slice->bw_in+$slice->bw_out).'GB',9,' ')}}
 {{str_pad($slice->bw_in.'GB',9,' ')}}
 {{str_pad($slice->bw_out.'GB',9,' ')}}

