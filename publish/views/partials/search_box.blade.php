
<form class="col-md-8 float-right" method="GET" action="{{ $page }}" accept-charset="UTF-8" role="search">

	<div class="form-group row d-flex justify-content-end">

		{{-- search input text field --}}
		<input type="text" id="search_box" class="form-control col-md-6" name="search" placeholder="Search..." value="{{ request('search') }}" />
		&nbsp

		{{-- Search button --}}
		<button class="btn btn-primary col-md-2" type="submit">
		Search
		</button>

	</div>

</form>
