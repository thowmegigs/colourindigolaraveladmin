   <div style="max-height:400px;overflow-y:auto">
   <x-displayViewData :module="$module" :row1="$row" :modelRelations="$model_relations" :viewColumns="$view_columns"
   :repeatingGroupInputs="$repeating_group_inputs" 
    :imageFieldNames="$image_field_names" 
    :storageFolder="$storage_folder.'/'.$row->id" 
   />
</div>