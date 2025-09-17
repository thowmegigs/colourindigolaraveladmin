@props(['column','label'])
<th class="sorting" data-sorting_type="asc" data-column_name="{{$column}}" style="cursor: pointer">
{!! $label !!}
<span id="{{$column}}_icon">
    <i class="bi bi-arrow-up"></i>
</span>
</th>
 