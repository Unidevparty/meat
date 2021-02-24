<tr data-id="{{ $id or 1 }}" class="brand_row">
    <td>
        <img src="{{ $photo->photo or '' }}" width="70">
    </td>

    <td>
        <div class="input-group">
            <span class="input-group-btn">
            <a data-input="brand{{ $id or 1 }}" data-preview="holder" class="btn btn-primary lfm">
                <i class="fa fa-picture-o"></i>
            </a>
            </span>
            <input id="brand{{ $id or 1 }}" class="form-control brand_photo_input" type="text" name="brands[{{ $id or 1 }}][photo]" value="{{ $photo->photo or '' }}">
        </div>
    </td>

    <td>
        <input class="form-control brand_photo_name" type="text" name="brands[{{ $id or 1 }}][name]" value="{{ $photo->name or '' }}">
    </td>
    <td>
        <a href="#" class="btn btn-danger btn-sm delete_brand_row"><i class="fa fa-close"></i></a>
    </td>
</tr>
