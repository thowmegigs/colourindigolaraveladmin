 @props(['row', 'pluralLowercase', 'module', 'crudTitle', 'showCrudInModal', 'hasPopup'])
 @php
     $plural_lowercase = $pluralLowercase;
     $r = $row;
     $crud_title = $crudTitle;
     $show_crud_in_modal = $showCrudInModal;
     $hasPopup = $hasPopup;
     $deleteurl = domain_route($plural_lowercase . '.destroy', [\Str::singular($plural_lowercase) => $r->id]);
     $editurl = domain_route($plural_lowercase . '.edit', [\Str::singular($plural_lowercase) => $r->id]);
     $viewurl = domain_route($plural_lowercase . '.show', [\Str::singular($plural_lowercase) => $r->id]);
     
 @endphp
 @if ($hasPopup)
     @if ($show_crud_in_modal)
        
             <a class="btn btn-outline-primary btn-sm" title="View"
                 href="javascript:load_form_modal('{!! strtolower($module) !!}','{!! $viewurl !!}','{!! $crud_title !!}','View')">
                <i class="bi bi-eye"></i>
             </a>
      
             <a class="btn btn-outline-warning btn-sm" title="View"
                 href="javascript:load_form_modal('{!! strtolower($module) !!}','{!! $editurl !!}','{!! $crud_title !!}','Edit')">
                 <i class="bi bi-pencil"></i>
             </a>
       
     @else
       
             <a class="btn btn-outline-primary btn-sm" title="View"
                 href="javascript:load_form_offcanvas('{!! strtolower($module) !!}','{!! $viewurl !!}','{!! $crud_title !!}','View')">
                 <i class="bi bi bi-eye"></i>
             </a>
       
             <a class="btn btn-outline-warning btn-sm" title="View"
                 href="javascript:load_form_offcanvas('{!! strtolower($module) !!}','{!! $editurl !!}','{!! $crud_title !!}','Edit')">
                 <i class="bi bi-pencil"></i>
             </a>
     @endif
 @else
     
         <a class="btn btn-outline-primary btn-sm" title="View" href='{!! $viewurl !!}'>
             <i class="bi bi bi-eye"></i>
         </a>
   
         <a class="btn  btn-outline-warning btn-sm" title="Edit" href="{{ $editurl }}">
             <i class="bi bi bi-pencil"></i> </a>
    

 @endif

     <a class="btn  btn-outline-danger btn-sm" title="Delete"
         href="javascript:deleteRecord('{!! $r->id !!}','{!! $deleteurl !!}');">
         <i class="bi bi-trash"></i></a>

