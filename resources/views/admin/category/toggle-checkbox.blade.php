<label class="switch">
    <input type="checkbox" class="switch-input setCategory {{ $name }}-checkbox" data-name="{{ $name }}" id="{{ $name }}-{{ $id }}" data-id="{{@$id}}" {{@$status == '1' ? 'checked' : ''}}>
    <span class="switch-label" data-on="On" data-off="Off"></span>
    <span class="switch-handle"></span>
</label>