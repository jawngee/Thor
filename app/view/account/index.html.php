<uses:layout layout="default" title="Slicehost Manager - Account - {{$account->name}}" />

<a href="/account/edit/{{$account->id}}">Edit Account</a>

<php:form id="form" mode="view" action="" model="{account}">
	<fields>
			<field label="Provider" id="provider_id" type="select" datasource="model://provider.provider" key="id" field="name" format="%s" />
			<field label="Name" id="name" type="text" />
			<field label="Notes" id="notes" type="textarea" />
			<field label="Key" id="key" type="password" />
			<field label="Secret" id="secret" type="password" />
	</fields>
</php:form>
