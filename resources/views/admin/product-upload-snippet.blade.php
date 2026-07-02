<form id="productForm" method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="w-full max-w-3xl mx-auto p-4 sm:p-6 space-y-4">
  @csrf

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
      <label for="name" class="block text-sm font-medium mb-1">Product name</label>
      <input id="name" name="name" placeholder="Product name" required class="w-full border rounded px-3 py-2" />
    </div>

    <div>
      <label for="price" class="block text-sm font-medium mb-1">Price</label>
      <input id="price" type="number" step="0.01" min="0" name="price" required class="w-full border rounded px-3 py-2" />
    </div>

    <div>
      <label for="stock" class="block text-sm font-medium mb-1">Stock</label>
      <input id="stock" type="number" min="0" name="stock" required class="w-full border rounded px-3 py-2" />
    </div>

    <div class="md:col-span-2">
      <label for="images" class="block text-sm font-medium mb-1">Product images</label>
      <input id="images" type="file" name="images[]" accept="image/*" multiple class="block w-full text-sm" />
      <p class="text-xs text-gray-500 mt-1">You can select one or many images. Camera capture is available on supported devices after opening file picker.</p>
    </div>
  </div>

  <div class="pt-2">
    <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center rounded px-4 py-2 bg-blue-600 text-white hover:bg-blue-700">
      Save Product
    </button>
  </div>
</form>
