
<div class="pagination-wrapper">

	{{ $model->appends(['search' => Request::get('search')])->onEachSide(3)->links() }}

</div>
