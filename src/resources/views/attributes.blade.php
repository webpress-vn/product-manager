<section>
@if($attributeType($attribute_value) === "color")
<div class="custom-color">
    @foreach($attribute_value as $index => $value)
    @if ($index === 0)
    <div>
        <input type="radio" id="color-{{$value['id']}}" name="{{ $attribute_name}}" value="{{$value['id']}}" checked>
        <label for="color-{{$value['id']}}">
            <span style="background-color: {{$value['value']}}"></span>
        </label>
    </div>
    @else
    <div>
        <input type="radio" id="color-{{$value['id']}}" name="{{ $attribute_name}}" value="{{$value['id']}}">
        <label for="color-{{$value['id']}}">
            <span style="background-color: {{$value['value']}}"></span>
        </label>
    </div>
    @endif
    @endforeach
</div>
@elseif($attributeType($attribute_value) === "checkbox")
<div class="custom-checkbox">
    @foreach($attribute_value as $index => $value)
    @if ($index === 0)
    <div>
        <input type="radio" id="color-{{$value['id']}}" name="{{ $attribute_name}}" value="{{$value['id']}}" checked>
        <label for="color-{{$value['id']}}">
            <span style="background-color: {{$value['value']}}">{{$value['label']}}</span>
        </label>
    </div>
    @else
    <div>
        <input type="radio" id="color-{{$value['id']}}" name="{{ $attribute_name}}" value="{{$value['id']}}">
        <label for="color-{{$value['id']}}">
            <span style="background-color: {{$value['value']}}">{{$value['label']}}</span>
        </label>
    </div>
    @endif
    @endforeach
</div>
@elseif($attributeType($attribute_value) === "radio")
<div class="custom-radio d-flex">
    @foreach($attribute_value as $index => $value)
    @if ($index === 0)
    <label class="container"><p>{{$value['label']}}</p>
    <input type="radio" checked="checked" name="{{ $attribute_name}}" value="{{$value['id']}}">
    <span class="checkmark"></span>
</label>
@else
<label class="container"><p>{{$value['label']}}</p>
<input type="radio" name="{{ $attribute_name}}" value="{{$value['id']}}">
<span class="checkmark"></span>
</label>
@endif
@endforeach
</div>
@else
<div class="btn-group custom-button" data-toggle="buttons">
@foreach($attribute_value as $index => $value)
@if($index === 0)
<label class="btn btn-light active text-capitalize">
<input type="radio" name="{{ $attribute_name}}" id="{{$value['id']}}" autocomplete="off" value="{{$value['id']}}" checked > {{$value['label']}}
</label>
@else
<label class="btn btn-light text-capitalize">
<input type="radio" name="{{ $attribute_name}}" id="{{$value['id']}}" value="{{$value['id']}}" autocomplete="off" > {{$value['label']}}
</label>
@endif
@endforeach
</div>
@endif
</section>
