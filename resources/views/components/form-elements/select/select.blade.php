<div class="mb-3 {{ isset($class) ? $class : '' }}">
    <label class="form-label {{ isset($required) && $required == 'true' ? 'required' : '' }} "
        for="{{ isset($id) ? $id : '' }}">{{ $title }}</label>
    <select class="form-select" {{ isset($multiple) && $multiple=='true' ? 'multiple' : '' }}
        id="{{ isset($id) ? $id : '' }}" name="{{ isset($name) ? $name : '' }}" {{ isset($required) && $required=='true'
        ? $required : '' }}>
        {{$options}}
    </select>
</div>