<uses:layout layout="default" title="Slicehost Manager - {{($slice) ? $slice->name : 'Inventory'}}" />

<?	if ($slices): ?>
<render:view view="slices/list.html" />
<? 	else: ?>
<render:view view="slices/item.html" />
<?	endif; ?>