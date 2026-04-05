<form method="GET" action="{{ isset($route) ? $route : '' }}" class="d-flex align-items-center">
  <div class="input-icon">
    <input type="text" name="search" class="form-control" placeholder="Pesquisar..."
      value="{{ request('search') }}">
    <span class="input-icon-addon">
      <i class="ti icon text-primary ti-search"></i>
    </span>
  </div>
</form>