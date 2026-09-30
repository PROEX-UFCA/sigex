<form action="{{ $route ?? '' }}" method="GET">
    <div class="input-icon">
        <input type="text" 
        name="search" 
        class="form-control text-brown fw-bold" 
        placeholder="Pesquisar..." 
        value="{{ request('search') }}">
        <span class="input-icon-addon">
            <i class="ti ti-search text-yellow"></i>
        </span>
    </div>
</form>