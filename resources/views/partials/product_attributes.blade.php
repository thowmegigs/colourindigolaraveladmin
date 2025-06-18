@foreach($attributes as $at)
    @php
        $name=$at->name;
        $slug=\Str::slug($at->name);
        $values=json_decode($at->attribute_values?->attribute_value_template->values_json,true);

    @endphp
    @if($values)
    <div class="mb-3">
        <label class="form-label" for="meta-title-input">{{ $name }}</label>
        <select class="form-select" name="facet_attribute__{{$slug.'==='.$at->id}}">
            <option value="">Select {{ $name }} type</option>
            @foreach($values as $f)
              @php 
              $product_feature_row=null;
              if($product_existing_features){
                       $i=0;
                      foreach ($product_existing_features as $x) {
                            if (isset($x['attribute_id']) && $x['attribute_id'] === $at->id) {
                                $product_feature_row = $product_existing_features[$i];
                                break;
                            }
                            $i++;
                        }
                } 
              $selected= $product_feature_row?($product_feature_row['value']==$f['name']?'selected':''):'';
            
              @endphp
                <option value="{{ $f['name'] }}" {{$selected}}>
                    {{ ucwords($f['name']) }}</option>
            @endforeach

        </select>
    </div>
    @endif
@endforeach
