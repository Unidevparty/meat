{{-- <tr data-id="{{ $id or 1 }}" class="gallery_row">
    <td>
        <img src="{{ $photo->photo or '' }}" width="70">
    </td>

    <td>
        <div class="input-group">
            <span class="input-group-btn">
            <a data-input="photo{{ $id or 1 }}" data-preview="holder" class="btn btn-primary lfm">
                <i class="fa fa-picture-o"></i>
            </a>
            </span>
            <input id="photo{{ $id or 1 }}" class="form-control gallery_photo_input" type="text" name="gallery[{{ $id or 1 }}][photo]" value="{{ $photo->photo or '' }}">
        </div>
    </td>

    <td>
        <input class="form-control gallery_photo_name" type="text" name="gallery[{{ $id or 1 }}][name]" value="{{ $photo->name or '' }}">
    </td>
    <td>
        <a href="#" class="btn btn-danger btn-sm delete_gallery_row"><i class="fa fa-close"></i></a>
    </td>
</tr> --}}



<div data-id="{{ $id or 1 }}" class="photo_row">
    <div class="row">
        <div class="col-md-2">
            @if (!empty($photo) && $photo->photo)
                <img src="{{ resize($photo->photo, 150, 100) }}">

                <input type="hidden" name="photos[{{ $id or 1 }}][photo]" value="{{ $photo->photo }}">
            @else
                <img src="">
            @endif

            <input type="hidden" name="photos[{{ $id or 1 }}][sort]" value="{{ $id }}" class="sort_order">
        </div>

        <div class="col-md-8">
            <textarea name="photos[{{ $id or 1 }}][description]" class="form-control photo_description" id="photo_description_{{ $id or 1}}">{{ $photo->description or '' }}</textarea>
        </div>

        <div class="col-md-1">
            <div class="close_btn_wrap">
                <a href="#" class="btn btn-danger btn-sm delete_gallery_row"><i class="fa fa-close"></i></a>
            </div>
            <div class="btn btn-primary btn-sm drag_btn"><i class="fa fa-arrows"></i></div>
        </div>
    </div>
</div>
