@csrf

<div class="form-group">
    <label for="category_id">Category</label>
    <select id="category_id" name="category_id" required>
        <option value="">-- Select category --</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? null) == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="name">Name</label>
    <input id="name" name="name" type="text" value="{{ old('name', $product->name ?? '') }}" required>
    @error('name') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="slug">Slug</label>
    <input id="slug" name="slug" type="text" value="{{ old('slug', $product->slug ?? '') }}" required>
    @error('slug') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="description">Description</label>
    <textarea id="description" name="description" rows="4" required>{{ old('description', $product->description ?? '') }}</textarea>
    @error('description') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="base_price">Base Price</label>
    <input id="base_price" name="base_price" type="number" min="0" step="0.01" value="{{ old('base_price', $product->base_price ?? '') }}" required>
    @error('base_price') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="thumbnail">
        @isset($product)
            Thumbnail (optional — leave empty to keep current)
        @else
            Thumbnail
        @endisset
    </label>
    @isset($product)
        @if($product->thumbnail)
            <div style="margin-bottom: 8px;">
                <img src="{{ \App\Support\ProductMedia::publicUrl($product->thumbnail) }}" alt="" style="max-height: 120px; border-radius: 6px; border: 1px solid #ddd;">
            </div>
        @endif
    @endisset
    <input id="thumbnail" name="thumbnail" type="file" accept="image/*" @if(! isset($product)) required @endif>
    @error('thumbnail') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label>
        <input type="checkbox" name="status" value="1" @checked(old('status', $product->status ?? true))>
        Active
    </label>
</div>

<button class="btn btn-primary" type="submit">Save Product</button>
