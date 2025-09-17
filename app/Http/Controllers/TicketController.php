<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketRequest;
use App\Models\Ticket;
use File;
use \Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TicketController extends Controller
{
    public function __construct()
    {
         $this->dashboard_url=\URL::to('/admin');
        $this->index_url=domain_route('tickets.index');
        $this->module='Ticket';
        $this->view_folder='tickets';
        $this->storage_folder=$this->view_folder;
        $this->has_upload=0;
        $this->is_multiple_upload=0;
        $this->has_export=0;
        $this->pagination_count=100;
        $this->crud_title='Ticket';
         $this->show_crud_in_modal=1;
         $this->has_popup=1;
        $this->has_detail_view =1;
        $this->has_side_column_input_group =0;
         $this->dimensions=  [
                    'tiny'  => 360,
                    'small' => 480,
                    'medium' => 768,
                    'large' => 1224,
        ];
        $this->form_image_field_name =[];

        $this->model_relations =[
              [
                'name' => 'user',
                'type' => 'BelongsTo',
                'save_by_key' => '',
                'column_to_show_in_view' => 'name',
            ],
              [
                'name' => 'replies',
                'type' => 'HasMany',
                'save_by_key' => '',
                'column_to_show_in_view' => 'name',
            ],
        ];

    }
  public function sideColumnInputs($model=null)
    {
        $data = [
            'side_title'=>'Any Title',
            'side_inputs'=>[]
            
          
        ];
      
        return $data;
    }
    public function createInputsData()
    {
        $data =[
    [
        'label' => null,
        'inputs' => [
            [
                'placeholder' => 'Enter subject',
                'name' => 'subject',
                'label' => 'Subject',
                'tag' => 'input',
                'type' => 'text',
                'default' => isset($model) ? $model->subject : "",
                'attr' => []
            ],
            [
                'placeholder' => 'Enter description',
                'name' => 'description',
                'label' => 'Description',
                'tag' => 'textarea',
                'type' => 'textarea',
                'default' => isset($model) ? $model->description : "",
                'attr' => []
            ]
        ]
    ]
];
        if (count($this->form_image_field_name) > 0) {
            foreach ($this->form_image_field_name as $g) {
                $y = [
                    'placeholder' => '',
                    'name' => $g['single'] ? $g['field_name'] : $g['field_name'] . '[]',
                    'label' => $g['single'] ? properSingularName($g['field_name']) : properPluralName($g['field_name']),
                    'tag' => 'input',
                    'type' => 'file',
                    'default' => '',
                    'attr' => $g['single'] ? [] : ['multiple' => 'multiple'],
                ];
                array_push($data[0]['inputs'], $y);
            }
        }
        return $data;
    }
    public function editInputsData($model)
    {
        $data =[
    [
        'label' => null,
        'inputs' => [
            [
                'placeholder' => 'Enter subject',
                'name' => 'subject',
                'label' => 'Subject',
                'tag' => 'input',
                'type' => 'text',
                'default' => isset($model) ? $model->subject : "",
                'attr' => []
            ],
            [
                'placeholder' => 'Enter description',
                'name' => 'description',
                'label' => 'Description',
                'tag' => 'textarea',
                'type' => 'textarea',
                'default' => isset($model) ? $model->description : "",
                'attr' => []
            ]
        ]
    ]
];
        if (count($this->form_image_field_name) > 0) {
            foreach ($this->form_image_field_name as $g) {
                $y = [
                    'placeholder' => '',
                    'name' => $g['single'] ? $g['field_name'] : $g['field_name'] . '[]',
                    'label' => $g['single'] ? properSingularName($g['field_name']) : properPluralName($g['field_name']),
                    'tag' => 'input',
                    'type' => 'file',
                    'default' => $model->{$g['field_name']}?($g['single'] ? $this->storage_folder . '/' . $model->{$g['field_name']} : json_encode($this->getImageList($model->id, $g['table_name'], $g['parent_table_field'], $this->storage_folder))):null,
                    'attr' => $g['single'] ? [] : ['multiple' => 'multiple'],
                ];
                array_push($data[0]['inputs'], $y);
            }
        }
        return $data;
    }
    public function commonVars($model = null)
    {

       $repeating_group_inputs=[];
        $toggable_group=[];
      
        $table_columns=[
    [
        'column' => 'user_id',
        'label' => 'Seller',
        'sortable' => 'Yes',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'subject',
        'label' => 'Subject',
        'sortable' => 'No',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'description',
        'label' => 'Description',
        'sortable' => 'No',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'status',
        'label' => 'Status',
        'sortable' => 'No',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'created_at',
        'label' => 'Created At',
        'sortable' => 'Yes',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'updated_at',
        'label' => 'Last Update',
        'sortable' => 'Yes',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ]
];
$table_columns = array_filter($table_columns, function($item) {
    return current_role()=='Vendor'?$item['column'] !='user_id':true;
});
        $view_columns =[
    
    [
        'column' => 'user_id',
        'label' => 'User Id',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'subject',
        'label' => 'Subject',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'description',
        'label' => 'Description',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'status',
        'label' => 'Status',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'created_at',
        'label' => 'Created At',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ]
];
$view_columns = array_filter($view_columns, function($item) {
    return current_role()=='Vendor'?$item['column'] !='user_id':true;
});

        $searchable_fields=[
    [
        'name' => 'subject',
        'label' => 'Subject'
    ]
];
        $filterable_fields=[
    [
        'name' => 'user_id',
        'label' => 'User ',
        'type' => 'select',
        'options' => getList('Vendor')
    ],
    [
        'name' => 'status',
        'label' => 'Status',
        'type' => 'select',
        'options'=>getListFromIndexArray(['Open','Closed'])
    ],
    [
        'name' => 'created_at',
        'label' => 'Created At',
        'type' => 'date'
    ]
];
$filterable_fields = array_filter($filterable_fields, function($item) {
    return current_role()=='Vendor'?$item['name'] !='user_id':true;
});

        $data['data'] = [

            'dashboard_url' => $this->dashboard_url,
            'index_url' => $this->index_url,
            'title' => 'All '.$this->crud_title.'s',
            'module' => $this->module,
            'model_relations' => $this->model_relations,
            'searchable_fields' => $searchable_fields,
            'filterable_fields' => $filterable_fields,
            'storage_folder' => $this->storage_folder,
            'plural_lowercase' =>'tickets',
            'has_image' => $this->has_upload,
            'table_columns'=>$table_columns,
            'view_columns'=>$view_columns,
           
            'image_field_names' => $this->form_image_field_name,
            'storage_folder' => $this->storage_folder,
             'module_table_name'=>'tickets',
            'has_export' => $this->has_export,
            'crud_title' => $this->crud_title,
            'show_crud_in_modal'=>$this->show_crud_in_modal,
          'has_popup' => $this->has_popup,
            'has_side_column_input_group'=>$this->has_side_column_input_group,
           'has_detail_view'=>$this->has_detail_view,
            'repeating_group_inputs' => $repeating_group_inputs,
            'toggable_group' => $toggable_group,
            'thumbnailDimensions'=>$this->dimensions
        ];
       

        return $data;

    }
   public function afterCreateProcess($request, $post, $model)
    {
        $meta_info=$this->commonVars()['data'];

        return $this->afterCreateProcessBase($request, $post, $model,$meta_info);
    }
     public function common_view_data($id)
    {
        $data['row'] = null;
        if (count($this->model_relations) > 0) {
            $data['row'] = Ticket::with(array_column($this->model_relations, 'name'))->findOrFail($id);
        } else {
            $data['row'] = Ticket::findOrFail($id);
        }
        $data['view_inputs']=[];
        /***If you want to show any form iput in view ***
        $data['view_inputs'] = [
            [
                'label' => '',
                'inputs' => [
                    [
                        'placeholder' => 'Enter title',
                        'name' => 'title',
                        'label' => 'Title',
                        'tag' => 'input',
                        'type' => 'text',
                        'default' => '',
                        'attr' => [],
                    ],
                    [
                        'placeholder' => 'Enter remark',
                        'name' => 'remark',
                        'label' => 'Remark',
                        'tag' => 'input',
                        'type' => 'file',
                        'default' => '',
                        'attr' => ['class'=>'summernote'],
                    ],
                ],
            ],
        ];
        ***/
        $data = array_merge($this->commonVars()['data'], $data);
        // dd($data);
        return $data;
    }
    public function index(Request $request)
    {
      $folder=current_role()=='Vendor'?'vendor':'admin' ;
      $is_vendor=current_role()=='Vendor'?true:false ;

          $tabs = [
    /*[
        'label' => 'Active',
        'value' => 'Active',
        'count' => 1,
        'column' => 'status',
    ],
    [
        'label' => 'In-Active',
        'value' => 'In-Active',
        'count' => 3,
        'column' => 'status',
    ],*/
];
        $common_data = $this->commonVars()['data'];
        if ($request->ajax()) {
            $sort_by = $request->get('sortby');
            $sort_type = $request->get('sorttype');
            $search_by = $request->get('search_by');
            $query = $request->get('query');

            $search_val = str_replace(" ", "%", $query);
            if (empty($search_by)) {
                $search_by = 'name';
            }

           
            $tabs_column = count($tabs) > 0 ? array_column($tabs, 'column') : [];

            $db_query =  Ticket::when(!empty($search_val), function ($query) use ($search_val, $search_by) {
                return $query->where($search_by, 'like', '%' . $search_val . '%');
            })
                ->when(!empty($sort_by), function ($query) use ($sort_by, $sort_type) {
                    return $query->orderBy($sort_by, $sort_type);
                })
                ->when($is_vendor, function ($query) {
                    return $query->where('user_id',auth()->guard('vendor')->id());
                });

            if (count($tabs_column) > 0) {
                foreach ($tabs_column as $col) {
                    if ($request->has($col) && !empty($request->{$col})) {
                        $db_query = $db_query->where($col, $request->{$col});
                    }

                }

            }

            $list = $db_query->latest()->paginate($this->pagination_count);
            $data = array_merge($common_data, [

                'list' => $list,
                'sort_by' => $sort_by,
                'sort_type' => $sort_type,
                'bulk_update' => json_encode([
                      'status'=>['label'=>'Status','data'=>getListFromIndexArray(['Open','Closed'])],
             ])
               
                /*
            Multi rows select karke koi column mein values update kara ho jaise status update,user assign
            'bulk_update' => json_encode([
            'status'=>['label'=>'Status','data'=>getListFromIndexArray(['Active','In-Active'])],
            'user_id'=>['label'=>'Assign User','data'=>getList('User')]

            ])
             */

            ]);
            return view($folder.'.' .$this->view_folder . '.page', with($data));
        } else {
            if (!can('list_tickets')) {
                return redirect()->back()->withError('Dont have permission to list');
            }
            $query = null;
            if (count($this->model_relations) > 0) {
                $query = Ticket::with(array_column($this->model_relations, 'name'));
            } else {
                $query =  Ticket::query();
            }
            $query = $this->buildFilter($request, $query);
            $list = $query->when($is_vendor, function ($query) {
                    return $query->where('user_id',auth()->guard('vendor')->id());
                })->latest()->paginate($this->pagination_count);
            $view_data = array_merge($common_data, [

                'list' => $list,
                'bulk_update' => json_encode([
                      'status'=>['label'=>'Status','data'=>getListFromIndexArray(['Open','Closed'])],
                ]),'tabs'=>$tabs
                /*
            Multi rows select karke koi column mein values update kara ho jaise status update,user assign
            'bulk_update' => json_encode([
            'status'=>['label'=>'Status','data'=>getListFromIndexArray(['Active','In-Active'])],
            'user_id'=>['label'=>'Assign User','data'=>getList('User')]

            ])
             */

            ]);
            $index_view = count($tabs) > 0 ? 'index_tabs' : 'index';
            return view($folder.'.' . $this->view_folder . '.' . $index_view, $view_data);
        }
    
    }

    public function create(Request $r)
    {
        $data = $this->createInputsData();  
          $view_data = array_merge($this->commonVars()['data'], [
                    'data' => $data,

                ]);
            
         if($r->ajax()){
           
             if (!can('create_tickets')) {
                return createResponse(false,'Dont have permission to create');
                }
              
                $html=view('admin.' . $this->view_folder . '.modal.add', with($view_data))->render();
                return  createResponse(true, $html);
         }
       else{
         
         if (!can('create_tickets')) {
                return redirect()->back()->withError('Dont have permission to create');
            }
         return view('admin.' . $this->view_folder . '.add', with($view_data));
       }
    
    }
    public function store(TicketRequest $request)
{
    if (!can('create_tickets')) {
        return createResponse(false, 'Dont have permission to create');
    }

    \DB::beginTransaction();

    try {
        // Validate file (max 2MB, allow images and common docs)
       
       
        $post = $request->all();
        $post = formatPostForJsonColumn($post);

        // Add user info
        $post['user_id'] = auth()->guard('vendor')->id();
        $post['status'] = 'Open';

        // Create ticket
       
        $ticket = Ticket::create($post);

        // Handle file upload if exists
       

        // Create first reply with optional attachment
        \App\Models\TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->guard('vendor')->id(),
            'message' => $post['description'],
            'name' => auth()->guard('vendor')->user()->name,
            'attachment' => $attachmentPath, // Make sure you have 'attachment' column in TicketReply
        ]);

        $this->afterCreateProcess($request, $post, $ticket);

        \DB::commit();

        return createResponse(true, $this->crud_title . ' created successfully', $this->index_url);
    } catch (\Exception $ex) {
        \Sentry\captureException($ex);
        \DB::rollback();
        return createResponse(false, $ex->getMessage());
    }
}

    public function edit(Request $request,$id)
    {
        

        $model = Ticket::findOrFail($id);

        $data = $this->editInputsData($model);

        $view_data = array_merge($this->commonVars($model)['data'], [
            'data' => $data, 'model' => $model,

        ]);
       
       

         if ($request->ajax()) {
            if (!can('edit_tickets')) {
                return createResponse(false, 'Dont have permission to edit');
            }

            $html = view('admin.' . $this->view_folder . '.modal.edit', with($view_data))->render();
            return createResponse(true, $html);

        } else {
            if (!can('edit_tickets')) {
                return redirect()->back()->withError('Dont have permission to edit');
            }

            return view('admin.' . $this->view_folder . '.edit', with($view_data));

        }

    }

    public function show(Request $request,$id)
    {
       
          $folder=current_role()=='Vendor'?'vendor':'admin' ;
         $view = $this->has_detail_view ? 'view_modal_detail' : 'view_modal';
         $data = $this->common_view_data($id);
    
        if ($request->ajax()) {
             if (!can('view_tickets')) {
                return createResponse(false,'Dont have permission to view');
         }
           
            $html = view($folder.'.' . $this->view_folder . '.view.' . $view, with($data))->render();
            return createResponse(true, $html);

        } else {
            if (!can('view_tickets')) {
                return redirect()->back()->withError('Dont have permission to view');
         }
           
// dd($data['row']->toArray);
            return view($folder.'.' . $this->view_folder . '.view.' . $view, with($data));

        }

      

    }
    
    public function update(TicketRequest $request, $id)
    {
         if (!can('edit_tickets')) {
        return createResponse(false,'Dont have permission to update');
        }
        \DB::beginTransaction();

        try
        {
            $post = $request->all();

            $ticket = Ticket::findOrFail($id);

            $post = formatPostForJsonColumn($post);
             /* Saving name alongwith id in json column takki join se na retrive karna pade
               copy this code from contrller file and paste and edit here 
              $post=$this->processJsonColumnToAddNameOrAddtionalData($post);
            */
            $ticket->update($post);
              $this->afterCreateProcess($request,$post,$ticket);
                  \DB::commit();
            return createResponse(true, $this->crud_title . ' updated successfully', $this->index_url);
        } catch (\Exception $ex) { \Sentry\captureException($ex);
             \DB::rollback();
            return createResponse(false, $ex->getMessage());
        }
    }

    public function destroy($id)
    {
                if (!can('delete_tickets')) {
        return createResponse(false,'Dont have permission to delete');
        }
      
        try
        {
            if(Ticket::where('id',$id)->exists())
               Ticket::destroy($id);
     
            if($this->has_upload){
                $this->deleteFile($id);
            }
           \DB::table('ticket_replies')->where('ticket_id',$id)->delete();
           return createResponse(true,$this->module.' Deleted successfully'); 
        }
        catch(\Exception $ex){
           
            return createResponse(false,'Failed to  Delete Properly');
        }

    }
    public function deleteFile($id)
    {

        return $this->deleteFileBase($id,$this->storage_folder);

    }
   

    
     public function exportTicket(Request $request,$type){
        if (!can('export_tickets')) {
            return redirect()->back()->withError('Not allowed to export');
        }
      $meta_info=$this->commonVars()['data'];
      return $this->exportModel('Ticket','tickets',$type,$meta_info);
     
      
   
    }
	public function load_toggle(Request $r)
    {
        $value = trim($r->val);
        $rowid=$r->has('row_id')?$r->row_id:null;
        $row=null;
        if($rowid)
        {
            $model = app("App\\Models\\".$this->module);
            $row=$model::where('id', $rowid)->first();
        }
        $index_of_val = 0;
        $is_value_present = false;
       $i=0;
        foreach ($this->toggable_group as $val) {
           
            if($val['onval'] == $value) {
               
                $is_value_present = true;
                $index_of_val = $i;
                break;
            }
            $i++;
        }
        if ($is_value_present) {
            if($row){
                $this->toggable_group =[];
    
               }
            $data['inputs'] = $this->toggable_group[$index_of_val]['inputs'];
           
            $v = view('admin.attribute_families.toggable_snippet', with($data))->render();
            return createResponse(true, $v);
        } else {
            return createResponse(true, "");
        }
 
    }
    public function getImageList($id, $table, $parent_field_name)
    {

       return $this->getImageListBase($id, $table, $parent_field_name,$this->storage_folder);
    }
    public function reply_ticket(Request $r,$id,)
    {
        $message=$r->message;
        $attachmentPath = null;
        //  $validated = $r->validate([
        //     'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,webp|max:2048',
        // ]);
      if ($r->hasFile('attachment')) {
                $file = $r->file('attachment');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Absolute path to storage/app/public/tickets
                $destinationPath = storage_path('app/public/tickets');

                // Ensure directory exists
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0775, true);
                }

                // Move the file
                $file->move($destinationPath, $filename);

                // Save only filename in DB
                $attachmentPath = $filename;
            }
        if(empty($message)){
              return back()->withError('Add some message also ');
        }
        $user_id=auth()->id()?auth()->id():auth()->guard('vendor')->id();
        $name=auth()->id()?auth()->user()->name:auth()->guard('vendor')->user()->name;
        \App\Models\TicketReply::create(['ticket_id'=>$id,'user_id'=>$user_id,'message'=>$message,'name'=>$name,'attachment'=>$attachmentPath]);
        return back()->withSuccess('Added');
    }
}
