@csrf

<div class="form-group">
    <label for="name">Name</label>
    <input id="name" name="name" type="text" value="{{ old('name', $category?->name ?? '') }}" required>
    @error('name') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="slug">Slug</label>
    <input id="slug" name="slug" type="text" value="{{ old('slug', $category?->slug ?? '') }}" required>
    @error('slug') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="parent_id">Parent category</label>
    <select id="parent_id" name="parent_id">
        <option value="">— None —</option>
        @foreach($parentCategories as $parentCategory)
            <option value="{{ $parentCategory->id }}" @selected(old('parent_id', $category?->parent_id) == $parentCategory->id)>
                {{ $parentCategory->name }}
            </option>
        @endforeach
    </select>
    @error('parent_id') <div class="error">{{ $message }}</div> @enderror
</div>

<button class="btn btn-primary" type="submit">Save Category</button>
