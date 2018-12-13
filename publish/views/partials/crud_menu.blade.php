
<div class="d-flex" style="color: grey;">

	<a href="{{ url($page.'/'.$model->id) }}">View</a>
	&nbsp;|&nbsp;

	<a href="{{ url($page.'/'.$model->id.'/edit') }}">Edit</a>
	&nbsp;|&nbsp;

	<form method="POST" action="{{ url($page.'/'.$model->id) }}">
	    @method('DELETE')
	    @csrf

	    <button class="btn btn-link m-0 p-0" onclick="return confirm('Delete Drum Permanently ?')" >Delete</button>

	</form>

</div>
