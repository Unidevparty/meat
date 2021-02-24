<tr data-id="{{ $id or 1 }}" class="video_row">
    <td>
        <img src="{{ $video->photo or '' }}" width="70">
    </td>

    <td>
        <div class="input-group">
            <span class="input-group-btn">
            <a data-input="video{{ $id or 1 }}" data-preview="holder" class="btn btn-primary lfm">
                <i class="fa fa-picture-o"></i>
            </a>
            </span>
            <input id="video{{ $id or 1 }}" class="form-control video_photo_input" type="text" name="videos[{{ $id or 1 }}][photo]" value="{{ $video->photo or '' }}">
        </div>
    </td>
    <td>
        <input class="form-control video_url" type="text" name="videos[{{ $id or 1 }}][url]" value="{{ $video->url or '' }}">
    </td>
    <td>
        <input class="form-control video_name" type="text" name="videos[{{ $id or 1 }}][name]" value="{{ $video->name or '' }}">
    </td>
    <td>
        <a href="#" class="btn btn-danger btn-sm delete_video_row"><i class="fa fa-close"></i></a>
    </td>
</tr>
