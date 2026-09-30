<div class="mb-3 {{ isset($class) ? $class : '' }}">
    <label class="form-label {{ isset($required) && $required == 'true' ? 'required' : '' }}" for="{{ isset($id) ? $id : '' }}">{{ $title }} <span
            class="form-label-description"> {{ isset($description) ? $description : '' }}</span></label>
    <textarea class="form-control" name="{{ $name }}" rows="{{ isset($rows) ? $rows : '' }}" id="{{ isset($id) ? $id : '' }}" placeholder="{{ isset($placeholder) ? $placeholder : '' }}" {{ isset($required) && $required == 'true' ? 'required' : '' }} {{ isset($disabled) && $disabled == 'true' ? 'readonly' : '' }}>{{ isset($value) ? $value : '' }}</textarea>
</div>
