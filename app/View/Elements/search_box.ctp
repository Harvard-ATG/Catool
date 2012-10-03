
<form class="form-search" action="<?php echo $search_url?>" method="get">
  <input type="text" name="search" class="input-medium search-query" value="<?php echo isset($this->request->query['search']) ? $this->request->query['search'] : ''; ?>">
  <button type="submit" class="btn">Search</button>
</form>