<uses:layout layout="default" title="Slicehost Manager - Account Edit - {{$account->name}}" />

<php:form id="form" mode="edit" action="" model="{account}" redirect="/account/%s" allow_create="false" allow_update="true">
	<fields>
			<field label="Provider" id="provider_id" type="select" datasource="model://provider.provider" key="id" field="name" />
			<field label="Name" id="name" type="text" />
			<field label="Notes" id="notes" type="textarea" />
			<field label="Key" id="key" type="password" />
			<field label="Secret" id="secret" type="password" />
	</fields>
</php:form>
