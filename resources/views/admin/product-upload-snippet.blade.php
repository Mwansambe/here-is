<form id="productForm" enctype="multipart/form-data">
  <input name="name" placeholder="Product name" />
  <input type="number" name="price" />
  <input type="number" name="stock" />
  <input type="file" name="images[]" accept="image/*" multiple />
  <input type="file" name="images[]" accept="image/*" capture="environment" />
</form>