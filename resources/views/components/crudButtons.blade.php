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
        
             <a class="btn btn-outline-primary btn-icon" title="View"
                 href="javascript:load_form_modal('{!! strtolower($module) !!}','{!! $viewurl !!}','{!! $crud_title !!}','View')">
                <i class="bx bx-slideshow"></i>
             </a>
      
             <a class="btn btn-outline-warning btn-icon" title="View"
                 href="javascript:load_form_modal('{!! strtolower($module) !!}','{!! $editurl !!}','{!! $crud_title !!}','Edit')">
                 <i class="bx bx-edit"></i>
             </a>
       
     @else
       
             <a class="btn btn-outline-primary btn-icon" title="View"
                 href="javascript:load_form_offcanvas('{!! strtolower($module) !!}','{!! $viewurl !!}','{!! $crud_title !!}','View')">
                 <i class="bx bx-slideshow"></i>
             </a>
       
             <a class="btn btn-outline-warning btn-icon" title="View"
                 href="javascript:load_form_offcanvas('{!! strtolower($module) !!}','{!! $editurl !!}','{!! $crud_title !!}','Edit')">
                 <i class="bx bx-edit"></i>
             </a>
     @endif
 @else
     
         <a class="btn btn-outline-primary btn-icon" title="View" href='{!! $viewurl !!}'>
             <i class="bx bx-slideshow"></i>
         </a>
   
         <a class="btn  btn-outline-warning btn-icon" title="Edit" href="{{ $editurl }}">
             <i class="bx bx-edit"></i> </a>
    

 @endif

     <a class="btn  btn-outline-danger btn-icon" title="Delete"
         href="javascript:deleteRecord('{!! $r->id !!}','{!! $deleteurl !!}');">
         <i class="bx bx-trash"></i></a>

