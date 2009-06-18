<uses:layout layout="default" title="Slicehost Manager - Create Account" />

<php:form id="form" mode="create" action="" model="provider.account" redirect="/account/%s" allow_create="true" allow_update="false">
	<fields>
			<field label="Provider" id="provider_id" type="select" datasource="model://provider.provider" key="id" field="name" />
			<field label="Name" id="name" type="text" />
			<field label="Notes" id="notes" type="textarea" />
			<field label="Key" id="key" type="password" />
			<field label="Secret" id="secret" type="password" />
	</fields>
</php:form>
