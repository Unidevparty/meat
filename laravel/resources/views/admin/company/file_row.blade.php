<tr data-id="{{ $id or 1 }}" class="file_row">
    <td>
        <div class="input-group">
            <span class="input-group-btn">
            <a data-input="file{{ $id or 1 }}" data-preview="holder" class="btn btn-primary filelfm">
                <i class="fa fa-picture-o"></i>
            </a>
            </span>
            <input id="file{{ $id or 1 }}" class="form-control file_input" type="text" name="files[{{ $id or 1 }}][file]" value="{{ $file->file or '' }}">
        </div>
    </td>

    <td>
        <input class="form-control file_name" type="text" name="files[{{ $id or 1 }}][name]" value="{{ $file->name or '' }}">
    </td>
    <td>
        <a href="#" class="btn btn-danger btn-sm delete_file_row"><i class="fa fa-close"></i></a>
    </td>
</tr>
