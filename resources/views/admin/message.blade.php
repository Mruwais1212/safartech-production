<div>
	@if (Session::has('success'))
	<div class="col-12 alert-class" style="position: absolute;top: 10px;">
		<div class="alert-click-hide alert alert-success alert alert-success col-9 col-xl-3 rounded-0 mb-1"
			style="z-index: 11;opacity:.9;cursor:pointer;" onclick="$(this).fadeOut();">{{
			Session::get('success') }}</div>
	</div>
	@endif


	@if (Session::has('error'))
	<div class="col-12 alert-class" style="position: absolute;top: 10px;">
		<div class="alert-click-hide alert alert-danger alert alert-danger col-9 col-xl-3 rounded-0 mb-1"
			style="z-index: 11;opacity:.9;cursor:pointer;" onclick="$(this).fadeOut();">{{
			Session::get('error') }}</div>
	</div>
	@endif

	@if ($errors->all())
	<div class="col-12 alert-class" style="position: absolute;top: 10px;">
		{!! implode('', $errors->all('<div
			class="alert-click-hide alert alert-danger alert alert-danger col-9 col-xl-3 rounded-0 mb-1"
			style="z-index: 11;opacity:.9;cursor:pointer;" onclick="$(this).fadeOut();">:message</div>')) !!}
	</div>
	@endif
</div>