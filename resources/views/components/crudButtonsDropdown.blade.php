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
 <div class="btn-group position-static" id="hover-dropdown-demo{{$row->id}}">
     <button type="button" class="btn btn-default btn-icon rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown" data-trigger="hover"
         aria-expanded="false"><i class="bx bx-dots-vertical-rounded"></i></button>
     <ul class="dropdown-menu position-absolute dropdown-menu-end" style="">
         @if ($hasPopup)
             @if ($show_crud_in_modal)
                 @if (current_user()->can('view_' . $plural_lowercase))
                     <li><a class="dropdown-item" title="View"
                             href="javascript:load_form_modal('{!! strtolower($module) !!}','{!! $viewurl !!}','{!! $crud_title !!}','View')">
                            Show
                         </a></li>
                 @endif
                 @if (current_user()->can('edit_' . $plural_lowercase))
                     <li><a class="dropdown-item btn btn-warning btn-icon" title="View"
                             href="javascript:load_form_modal('{!! strtolower($module) !!}','{!! $editurl !!}','{!! $crud_title !!}','Edit')">
                             Edit
                         </a></li>
                 @endif
             @else
                 @if (current_user()->can('view_' . $plural_lowercase))
                     <li><a class="dropdown-item" title="View"
                             href="javascript:load_form_offcanvas('{!! strtolower($module) !!}','{!! $viewurl !!}','{!! $crud_title !!}','View')">
                            Show
                         </a></li>
                 @endif
                 @if (current_user()->can('edit_' . $plural_lowercase))
                     <li><a class="dropdown-item" title="View"
                             href="javascript:load_form_offcanvas('{!! strtolower($module) !!}','{!! $editurl !!}','{!! $crud_title !!}','Edit')">
                            Edit
                         </a></li>
                 @endif
             @endif
         @else
             @if (current_user()->can('view_' . $plural_lowercase))
                 <li><a class="dropdown-item" title="View" href='{!! $viewurl !!}'>
                        Show
                     </a></li>
             @endif
             @if (current_user()->can('edit_' . $plural_lowercase))
                 <li><a class="dropdown-item" title="Edit" href="{{ $editurl }}">
                         Edit</a></li>
             @endif

         @endif

         @if (current_user()->can('delete_' . $plural_lowercase))
             <li><a class="dropdown-item" title="Delete"
                     href="javascript:deleteRecord('{!! $r->id !!}','{!! $deleteurl !!}');">
                     Delete </a></li>
         @endif
      
     </ul>
 </div>
