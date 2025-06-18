<?php

namespace App\Http\Controllers;

use App\Http\Requests\VendorRequest;
use App\Models\Vendor;
use File;
use \Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use \App\Services\ShiprocketService;
use Milon\Barcode\DNS1D;
use Barryvdh\DomPDF\Facade\Pdf;
class VendorController extends Controller
{
    public function __construct()
    {
         $this->dashboard_url=\URL::to('/admin');
        $this->index_url=domain_route('vendors.index');
        $this->module='Vendor';
        $this->view_folder='vendors';
        $this->storage_folder='vendor_documents';
        $this->has_upload=0;
        $this->is_multiple_upload=0;
        $this->has_export=0;
        $this->pagination_count=100;
        $this->crud_title='Seller';
         $this->show_crud_in_modal=1;
         $this->has_popup=1;
        $this->has_detail_view =0;
        $this->has_side_column_input_group =0;
        $this->form_image_field_name =[
              [
                'field_name' => 'gst_image',
                'single' => true, 'has_thumbnail' => false,
            ],
              [
                'field_name' => 'pan_image',
                'single' => true, 'has_thumbnail' => false,
            ],
              [
                'field_name' => 'business_license_image',
                'single' => true, 'has_thumbnail' => false,
            ],
              [
                'field_name' => 'trademark_image',
                'single' => true, 'has_thumbnail' => false,
            ],
        ];

        $this->model_relations =[
   
    [
        'name' => 'city',
        'type' => 'BelongsTo',
        'save_by_key' => '',
        'column_to_show_in_view' => 'name'
    ],
    [
        'name' => 'state',
        'type' => 'BelongsTo',
        'save_by_key' => '',
        'column_to_show_in_view' => 'name'
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
                'placeholder' => 'Enter name',
                'name' => 'name',
                'label' => 'Business Name',
                'tag' => 'input',
                'type' => 'text',
                'default' => isset($model) ? $model->name : "",
                'attr' => []
            ],
            [
                'placeholder' => 'Enter email',
                'name' => 'email',
                'label' => 'Email',
                'tag' => 'input',
                'type' => 'email',
                'default' => isset($model) ? $model->email : "",
                'attr' => []
            ],
            [
                'placeholder' => 'Enter phone',
                'name' => 'phone',
                'label' => 'Phone',
                'tag' => 'input',
                'type' => 'number',
                'default' => isset($model) ? $model->phone : "",
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
                'placeholder' => 'Enter name',
                'name' => 'name',
                'label' => 'Business Name',
                'tag' => 'input',
                'type' => 'text',
                'default' => isset($model) ? $model->name : "",
                'attr' => []
            ],
            [
                'placeholder' => 'Enter email',
                'name' => 'email',
                'label' => 'Email',
                'tag' => 'input',
                'type' => 'email',
                'default' => isset($model) ? $model->email : "",
                'attr' => []
            ],
            [
                'placeholder' => 'Enter phone',
                'name' => 'phone',
                'label' => 'Phone',
                'tag' => 'input',
                'type' => 'number',
                'default' => isset($model) ? $model->phone : "",
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
                    'default' => $g['single'] ? $this->storage_folder . '/' . $model->{$g['field_name']} : json_encode($this->getImageList($model->id, $g['table_name'], $g['parent_table_field'],$this->storage_folder)),
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
        'column' => 'name',
        'label' => 'Busines Name',
        'sortable' => 'Yes',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'email',
        'label' => 'Email',
        'sortable' => 'Yes',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'phone',
        'label' => 'Phone',
        'sortable' => 'Yes',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'pincode',
        'label' => 'Pincode',
        'sortable' => 'Yes',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'status',
        'label' => 'Status',
        'sortable' => 'Yes',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ]
];
        $view_columns =[
      [
        'column' => 'name',
        'label' => 'Business Name',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
      ],
     
       [
        'column' => 'gst',
        'label' => 'GST No',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
     [
        'column' => 'gst_image',
        'label' => 'GST Image',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
     [
        'column' => 'pan',
        'label' => 'PAN No',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
     [
        'column' => 'pan_image',
        'label' => 'PAN Image',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
     [
        'column' => 'business_license_image',
        'label' => 'Business License  Certificate',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
     [
        'column' => 'trademark_image',
        'label' => 'Trademark',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
   
  
    [
        'column' => 'address',
        'label' => 'Address',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
       [
        'column' => 'address2',
        'label' => 'Address2',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'city_id',
        'label' => 'City Id',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'state_id',
        'label' => 'State Id',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
   
   
 
     [
        'column' => 'created_at',
        'label' => 'Joined Date',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    
  
   
];

        $searchable_fields=[
    [
        'name' => 'email',
        'label' => 'Email'
    ],
    [
        'name' => 'phone',
        'label' => 'Phone'
    ],
    [
        'name' => 'name',
        'label' => 'Name'
    ],
    [
        'name' => 'pincode',
        'label' => 'Pincode'
    ],
    [
        'name' => 'gst',
        'label' => 'Gst'
    ],
    [
        'name' => 'pan',
        'label' => 'Pan'
    ]
];
        $filterable_fields=[
    [
        'name' => 'created_at',
        'label' => 'Created At',
        'type' => 'date'
    ],
    [
        'name' => 'city_id',
        'label' => 'City ',
        'type' => 'select',
        'options' => getList('City ')
    ],
    [
        'name' => 'state_id',
        'label' => 'State ',
        'type' => 'select',
        'options' => getList('State ')
    ]
];

        $data['data'] = [

            'dashboard_url' => $this->dashboard_url,
            'index_url' => $this->index_url,
            'title' => 'All '.$this->crud_title.'s',
            'module' => $this->module,
            'model_relations' => $this->model_relations,
            'searchable_fields' => $searchable_fields,
            'filterable_fields' => $filterable_fields,
            'storage_folder' => $this->storage_folder,
            'plural_lowercase' =>'vendors',
            'has_image' => $this->has_upload,
            'table_columns'=>$table_columns,
            'view_columns'=>$view_columns,
           
            'image_field_names' => $this->form_image_field_name,
            'storage_folder' => $this->storage_folder,
             'module_table_name'=>'vendors',
            'has_export' => $this->has_export,
            'crud_title' => $this->crud_title,
            'show_crud_in_modal'=>$this->show_crud_in_modal,
          'has_popup' => $this->has_popup,
            'has_side_column_input_group'=>$this->has_side_column_input_group,
           'has_detail_view'=>$this->has_detail_view,
            'repeating_group_inputs' => $repeating_group_inputs,
            'toggable_group' => $toggable_group,
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
            $data['row'] = Vendor::with(array_column($this->model_relations, 'name'))->findOrFail($id);
        } else {
            $data['row'] = Vendor::findOrFail($id);
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
$vendor_id=auth()->guard('vendor')->id();
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

            $db_query =  Vendor::when(!empty($search_val), function ($query) use ($search_val, $search_by) {
                return $query->where($search_by, 'like', '%' . $search_val . '%');
            })
                ->when(!empty($sort_by), function ($query) use ($sort_by, $sort_type) {
                    return $query->orderBy($sort_by, $sort_type);
                })
                ->when(!empty($vendor_id), function ($query) use ($vendor_id,$sort_by, $sort_type) {
                    return $query->where('vendor_id',$vendor_id);
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
                'bulk_update' =>  json_encode([
                   'status'=>['label'=>'Status','data'=>getListFromIndexArray(['Active','In-Active'])],
               ])
               
                /*
            Multi rows select karke koi column mein values update kara ho jaise status update,user assign
            'bulk_update' => json_encode([
            'status'=>['label'=>'Status','data'=>getListFromIndexArray(['Active','In-Active'])],
            'user_id'=>['label'=>'Assign User','data'=>getList('User')]

            ])
             */

            ]);
            return view('admin.' . $this->view_folder . '.page', with($data));
        } else {
            if (!can('list_vendors')) {
                return redirect()->back()->withError('Dont have permission to list');
            }
            $query = null;
            if (count($this->model_relations) > 0) {
                $query = Vendor::with(array_column($this->model_relations, 'name'));
            } else {
                $query =  Vendor::query();
            }
            $query = $this->buildFilter($request, $query);
            $list = $query->latest()->paginate($this->pagination_count);
            $view_data = array_merge($common_data, [

                'list' => $list,
                'bulk_update' =>  json_encode([
                   'status'=>['label'=>'Status','data'=>getListFromIndexArray(['Active','In-Active'])],
                ])
          
                /*
            Multi rows select karke koi column mein values update kara ho jaise status update,user assign
            'bulk_update' => json_encode([
            'status'=>['label'=>'Status','data'=>getListFromIndexArray(['Active','In-Active'])],
            'user_id'=>['label'=>'Assign User','data'=>getList('User')]

            ])
             */

            ]);
            $index_view = count($tabs) > 0 ? 'index_tabs' : 'index';
            return view('admin.' . $this->view_folder . '.' . $index_view, $view_data);
        }
    
    }

    public function create(Request $r)
    {
        $data = $this->createInputsData();  
          $view_data = array_merge($this->commonVars()['data'], [
                    'data' => $data,

                ]);
            
         if($r->ajax()){
           
             if (!can('create_vendors')) {
                return createResponse(false,'Dont have permission to create');
                }
              
                $html=view('admin.' . $this->view_folder . '.modal.add', with($view_data))->render();
                return  createResponse(true, $html);
         }
       else{
         
         if (!can('create_vendors')) {
                return redirect()->back()->withError('Dont have permission to create');
            }
         return view('admin.' . $this->view_folder . '.add', with($view_data));
       }
    
    }
    public function store(VendorRequest $request)
    {
         if (!can('create_vendors')) {
        return createResponse(false,'Dont have permission to create');
        }
        \DB::beginTransaction();

        try {
            $post = $request->all();

             $post = formatPostForJsonColumn($post);
              /* Saving name alongwith id in json column takki join se na retrive karna pade
               copy this code from contrller file and paste and edit here 
              $post=$this->processJsonColumnToAddNameOrAddtionalData($post);
            */
            
             $vendor = Vendor::create($post);
               $this->afterCreateProcess($request,$post,$vendor);
             \DB::commit();
            return createResponse(true, $this->crud_title . ' created successfully', $this->index_url);
        } catch (\Exception $ex) { \Sentry\captureException($ex);
            \DB::rollback();

            return createResponse(false, $ex->getMessage());
        }
    }
    public function edit(Request $request,$id)
    {
        

        $model = Vendor::findOrFail($id);

        $data = $this->editInputsData($model);

        $view_data = array_merge($this->commonVars($model)['data'], [
            'data' => $data, 'model' => $model,

        ]);
       
       

         if ($request->ajax()) {
            if (!can('edit_vendors')) {
                return createResponse(false, 'Dont have permission to edit');
            }

            $html = view('admin.' . $this->view_folder . '.modal.edit', with($view_data))->render();
            return createResponse(true, $html);

        } else {
            if (!can('edit_vendors')) {
                return redirect()->back()->withError('Dont have permission to edit');
            }

            return view('admin.' . $this->view_folder . '.edit', with($view_data));

        }

    }

    public function show(Request $request,$id)
    {
         $view = $this->has_detail_view ? 'view_modal_detail' : 'view_modal';
         $data = $this->common_view_data($id);
       
        if ($request->ajax()) {
             if (!can('view_vendors')) {
                return createResponse(false,'Dont have permission to view');
         }
        
            $html = view('admin.' . $this->view_folder . '.view.' . $view, with($data))->render();
            return createResponse(true, $html);

        } else {
            if (!can('view_vendors')) {
                return redirect()->back()->withError('Dont have permission to view');
         }
           

            return view('admin.' . $this->view_folder . '.view.' . $view, with($data));

        }

      

    }
    
    public function update(VendorRequest $request, $id)
    {
         if (!can('edit_vendors')) {
        return createResponse(false,'Dont have permission to update');
        }
        \DB::beginTransaction();

        try
        {
            $post = $request->all();

            $vendor = Vendor::findOrFail($id);

            $post = formatPostForJsonColumn($post);
             /* Saving name alongwith id in json column takki join se na retrive karna pade
               copy this code from contrller file and paste and edit here 
              $post=$this->processJsonColumnToAddNameOrAddtionalData($post);
            */
            $vendor->update($post);
              $this->afterCreateProcess($request,$post,$vendor);
                  \DB::commit();
            return createResponse(true, $this->crud_title . ' updated successfully', $this->index_url);
        } catch (\Exception $ex) { \Sentry\captureException($ex);
             \DB::rollback();
            return createResponse(false, $ex->getMessage());
        }
    }

    public function destroy($id)
    {
                if (!can('delete_vendors')) {
        return createResponse(false,'Dont have permission to delete');
        }
         \DB::beginTransaction();
        try
        {
            if(Vendor::where('id',$id)->exists())
               Vendor::destroy($id);
     
            if($this->has_upload){
                $this->deleteFile($id);
            }
             \DB::commit();
           return createResponse(true,$this->module.' Deleted successfully'); 
        }
        catch(\Exception $ex){
             \DB::rollback();
            return createResponse(false,'Failed to  Delete Properly');
        }

    }
    public function deleteFile($id)
    {

        return $this->deleteFileBase($id,$this->storage_folder);

    }
   

    
     public function exportVendor(Request $request,$type){
        if (!can('export_vendors')) {
            return redirect()->back()->withError('Not allowed to export');
        }
      $meta_info=$this->commonVars()['data'];
      return $this->exportModel('Vendor','vendors',$type,$meta_info);
     
      
   
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
    public function orders(Request $request)
{
    $vendor_id=auth()->guard('vendor')->id();
    $view_columns=[
 [
            'column' => 'shiprocket_shipment_id',
            'label' => 'Shiprocket Shipment Id',
            'sortable' => 'Yes',
            ],
            [
            'column' => 'shiprocket_order_id',
            'label' => 'Shiprocket Order Id',
            'sortable' => 'Yes',
            ],
    ];
            $table_columns = [
            [
            'column' => 'vendor_id',
            'label' => 'Seller Name',
            'sortable' => 'Yes',
            ],
            [
            'column' => 'uuid',
            'label' => 'Order Id',
            'sortable' => 'Yes',
            ],
           
            [
            'column' => 'vendor_total',
            'label' => 'Amount(Rs.)',
            'sortable' => 'Yes',
            ],
            [
            'column' => 'net_profit',
            'label' => 'Expected Profit(Rs.)',
            'sortable' => 'Yes',
            ],
           
           
            [
            'column' => 'awb',
            'label' => 'AWB',
            'sortable' => 'Yes',
            ],
           
            // [
            // 'column' => 'delivered_at',
            // 'label' => 'Delivered Date',
            // 'sortable' => 'Yes',
            // ],
           
           
            [
            'column' => 'paid_status',
            'label' => 'Paid Status',
            'sortable' => 'Yes',
            ],
            [
            'column' => 'delivery_status',
            'label' => 'Order Status',
            'sortable' => 'Yes',
            ],  
            [
            'column' => 'created_at',
            'label' => 'Create Date',
            'sortable' => 'Yes',
            ],

            ];
            $filterable_fields = [
            [
            'name' => 'created_at',
            'label' => 'Created At',
            'type' => 'date',
            ],
            // [
            //     'name' => 'paid_status',
            //     'label' => 'Payment Status ',
            //     'type' => 'select',
            //     'options' => getListFromIndexArray(['Paid','Unpaid']),
            // ],
            
           
            ];
            $searchable_fields = [
            [
            'name' => 'uuid',
            'label' => 'Order Id',
            'type' => 'text',
            ],
            ];
          $model_relations =[
   
    [
        'name' => 'vendor',
        'type' => 'BelongsTo',
        'save_by_key' => '',
        'column_to_show_in_view' => 'name'
    ],
    // [
    //     'name' => 'order',
    //     'type' => 'BelongsTo',
    //     'save_by_key' => '',
    //     'column_to_show_in_view' => 'name'
    // ],
  
];
            $this->pagination_count = 100;
            if ($request->ajax()) {
            $sort_by = $request->get('sortby');
            $sort_type = $request->get('sorttype');
            $search_by = $request->get('search_by');
           
            $query = $request->get('query');

            $search_val = str_replace(" ", "%", $query);
            if (empty($search_by)) {
            $search_by = 'name';
            }
           if($search_by=='order_id'){
            $order=\DB::table('orders')->where('uuid',$search_val)->first();
            $search_val=$order?$order->id:$search_val;
           
          }
            $list = \App\Models\VendorOrder::when(!empty($search_val), function ($query) use ($search_val, $search_by) {
            return $query->where($search_by, 'like', '%' . $search_val . '%');
            })->when(!empty($vendor_id), function ($query) use ($vendor_id) {
                    return $query->where('vendor_id',$vendor_id);
                })
            ->when(!empty($sort_by), function ($query) use ($sort_by, $sort_type) {
            return $query->orderBy($sort_by, $sort_type);
            })->where('status','Success')->latest()->paginate($this->pagination_count);
            $data = [
            'table_columns'=> $table_columns,
            'list'=>$list,
              'searchable_fields' => $searchable_fields,
            'filterable_fields' => $filterable_fields,
            'plural_lowercase'=>'vendor_order',
            'sort_by'=> $sort_by,
            'sort_type'=> $sort_type,
            'storage_folder'=>'',
            'plural_lowercase'=>'vendor_order',
            'module'=>'VendorOrder',
            'has_image'=>0,
            'model_relations'=>[],
            'image_field_names'=>[],
            'crud_title'=>'Vendor Order',
            'has_popup'=>false,
            'show_crud_in_modal'=>false,
             'bulk_update' => json_encode([
                    'order_ship_status' => ['label' => 'Transfer To Shiprocket', 'data' => getListFromIndexArray(['Yes','No'])],
                ]),


            ];
            return view('admin.vendors.order_page', with($data));
            } else {

            $query = null;

            $query = \App\Models\VendorOrder::when(!empty($vendor_id), function ($query) 
            use ($vendor_id) {
                    return $query->where('vendor_id',$vendor_id);
                })->where('status','Success');

            $query = $this->buildFilter($request, $query);
            $list = $query->latest()->paginate($this->pagination_count);

            $view_data = [
            'list' => $list,

            'title' => 'Vendor Order',
            'searchable_fields' => $searchable_fields,
            'filterable_fields' => $filterable_fields,
            'plural_lowercase'=>'vendor_order',
            'table_columns' => $table_columns,
            'module_table_name'=>'vendors',
            
            'model_relations'=>$model_relations,
            'module'=>'VendorOrder',
            'crud_title'=>'Vendor Order',
            'has_popup'=>false,
            'show_crud_in_modal'=>false,
              'bulk_update' => json_encode([
                    'is_transferred' => ['label' => 'Transfer To Shiprocket', 'data' => getListFromIndexArray(['Yes','No'])],
                ]),


            ];
            return view('admin.vendors.order', $view_data);
            }

}
   public function return_shipments(Request $request)
{
    $vendor_id=auth()->guard('vendor')->id();
    $view_columns=[
 
          
    ];
            $table_columns = [
            [
            'column' => 'uuid',
            'label' => 'Return Order Id',
            'sortable' => 'Yes',
            ],
            [
            'column' => 'pol',
            'label' => 'Related To Order Id ',
            'sortable' => 'Yes',
            ],
           
            [
            'column' => 'is_transferred',
            'label' => 'Is Shipped?',
            'sortable' => 'Yes',
            ],
           
           
            [
            'column' => 'created_at',
            'label' => 'Create Date',
            'sortable' => 'Yes',
            ],

            ];
            $filterable_fields = [
            [
            'name' => 'created_at',
            'label' => 'Created At',
            'type' => 'date',
            ],
            // [
            //     'name' => 'paid_status',
            //     'label' => 'Payment Status ',
            //     'type' => 'select',
            //     'options' => getListFromIndexArray(['Paid','Unpaid']),
            // ],
            
           
            ];
            $searchable_fields = [
            [
            'name' => 'uuid',
            'label' => 'Return Order Id',
            'type' => 'text',
            ],
            ];
          $model_relations =[
   
    [
        'name' => 'vendor_order',
        'type' => 'BelongsTo',
        'save_by_key' => '',
        'column_to_show_in_view' => 'name'
    ],
    
  
];
            $this->pagination_count = 100;
            if ($request->ajax()) {
            $sort_by = $request->get('sortby');
            $sort_type = $request->get('sorttype');
            $search_by = $request->get('search_by');
           
            $query = $request->get('query');

            $search_val = str_replace(" ", "%", $query);
            if (empty($search_by)) {
            $search_by = 'name';
            }
           if($search_by=='order_id'){
            $order=\DB::table('orders')->where('uuid',$search_val)->first();
            $search_val=$order?$order->id:$search_val;
           
          }
            $list = \App\Models\ReturnShipment::with('vendor_order:id,uuid')->when(!empty($search_val), function ($query) use ($search_val, $search_by) {
            return $query->where($search_by, 'like', '%' . $search_val . '%');
            })->when(!empty($vendor_id), function ($query) use ($vendor_id) {
                    return $query->where('vendor_id',$vendor_id);
                })
            ->when(!empty($sort_by), function ($query) use ($sort_by, $sort_type) {
            return $query->orderBy($sort_by, $sort_type);
            })->latest()->paginate($this->pagination_count);
            $data = [
            'table_columns'=> $table_columns,
            'list'=>$list,
              'searchable_fields' => $searchable_fields,
            'filterable_fields' => $filterable_fields,
            'plural_lowercase'=>'vendor_order',
            'sort_by'=> $sort_by,
            'sort_type'=> $sort_type,
            'storage_folder'=>'',
            'plural_lowercase'=>'vendor_order',
            'module'=>'VendorOrder',
            'has_image'=>0,
            'model_relations'=>[],
            'image_field_names'=>[],
            'crud_title'=>'Vendor Order',
            'has_popup'=>false,
            'show_crud_in_modal'=>false,
              'bulk_update' =>""


            ];
            return view('admin.vendors.return_shipment_page', with($data));
            } else {

            $query = null;

            $query = \App\Models\ReturnShipment::with('vendor_order:id,uuid')->when(!empty($vendor_id), function ($query) 
            use ($vendor_id) {
                    return $query->where('vendor_id',$vendor_id);
                });

            $query = $this->buildFilter($request, $query);
            $list = $query->latest()->paginate($this->pagination_count);

            $view_data = [
            'list' => $list,

            'title' => 'Return Shipemnt Order',
            'searchable_fields' => $searchable_fields,
            'filterable_fields' => $filterable_fields,
            'plural_lowercase'=>'return_shipments',
            'table_columns' => $table_columns,
            'module_table_name'=>'return_shipments',
            
            'model_relations'=>$model_relations,
            'module'=>'ReturnShipment',
            'crud_title'=>'Return Shipmnet ',
            'has_popup'=>false,
            'show_crud_in_modal'=>false,
             'bulk_update' => ""

            ];
            return view('admin.vendors.return_shipments', $view_data);
            }

}
public function getVendorOrderDetail(Request $request){
    $vendor_id=$request->vendor_id;
    $order_id=$request->order_id;
   $q = \App\Models\VendorOrder::with([
         'order.items' => function ($query) use ($vendor_id) {
        $query->where('vendor_id', $vendor_id);
        }
    ])->whereVendorId($vendor_id)->whereOrderId($order_id)->first();
   $orderItemIds = optional($q->order)->items->pluck('id')->all();
   // dd($orderItemIds);
    $order_items=\DB::table('order_items as oi')
     ->select(
        'p.name',
        'p.image',
        'oi.product_id',
        'oi.variant_id',
        'oi.sale_price',
        'oi.discount_share',
        'oi.qty',
        'pv.atributes_json'
    )
    ->leftJoin('product_variants as pv','pv.id','oi.variant_id')
    ->leftJoin('products as p','p.id','oi.product_id')
    ->whereIn('oi.id',$orderItemIds)->get();

 return view('admin.vendors.partial_order_detail', compact('order_items'))->render();

}
   public function sales(Request $request)
{
    $vendor_id=auth()->guard('vendor')->id();
     $data['orders'] = \App\Models\VendorOrder::with(['order.items.product','order.items.variant'])
     ->whereDoesntHave('shipping_status_updates', function ($query) {
            $query->where('status', 'like', '%Return%')
                ->orWhere('status', 'like', '%Exchange%');
        })->whereVendorId($vendor_id)->get();
  return view('admin.vendors.sales',with($data));
}
public function generateDocumentPost(Request $request)
{
   $shipservice = app(ShiprocketService::class);
    $type = $request->type; // 'label', 'manifest', 'invoice'

    $id= $request->id;
    $resp=$shipservice->generateDocument($type,$id);
 
     if($type=='label'){
        if($resp['label_created']){
            return response()->json(['url'=>$resp['label_url']]);
        }
        else{
            return response()->json(['error' => 'Failed to generate label'], 400); 
        }
    }
     else if($type=='manifest'){
        if(isset($resp['manifest_url'])){
           
            return response()->json(['url'=>$resp['manifest_url']]);
        }
        else{
            return response()->json(['error' => $resp['message']], 400); 
        }
    }
    
     else if($type=='invoice'){
      // dd($resp);
         if(isset($resp['invoice_url'])){
            return response()->json(['url'=>$resp['invoice_url']]);
        }
        else{
            return response()->json(['error' => $resp['message']], 400); 
        }
    }
     return response()->json(['error' => 'Failed to generate document'], 400);
}
public function vendor_bank(Request $request, $id = null)
{
    $user_id =auth()->guard('vendor')->id();
    $bankDetail = \App\Models\VendorBank::firstOrNew(['vendor_id' => $user_id]);

    if ($request->isMethod('post')) {
        $validated = $request->validate([
            'account_holder' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'ifsc_code' => 'required|string|max:20',
            'bank_name' => 'required|string|max:255',
            'branch_name' => 'nullable|string|max:255',
        ]);

        $bankDetail->fill($validated);
        $bankDetail->vendor_id = $user_id;
        $bankDetail->save();

        return back()->with('success', 'Bank details saved successfully.');
    }

    return view('admin.vendors.bank', compact('bankDetail'));
}
public function barcode($d,$invoice){
//$awb = '19041760409866'; // Example AWB number from Shiprocket
$barcode = new DNS1D();
$barcode->setStorPath(__DIR__."/cache/");

// Get barcode HTML
$data['html'] = $barcode->getBarcodeHTML($d, 'C128');
$data['invoice'] = $barcode->getBarcodeHTML($invoice, 'C128');
return view('admin.vendors.invoice',with($data));
}

public function transferReturnShipments(Request $r,$id){

$return=\App\Models\ReturnShipment::with(['vendor',
   
    'return_items' => function ($query) {
        $query->select('id', 'order_item_id','reason','exchange_variant_id','return_shipment_id');
    },
    'return_items.order_item' => function ($query) {
        $query->select('id', 'product_id', 'variant_id','discount_share'); // include foreign keys
    },
    'return_items.exchange_variant' => function ($query) {
        $query->select('id', 'name', 'atributes_json','sale_price','sku','product_id'); // include foreign keys
    },
    'return_items.exchange_variant.product' => function ($query) {
        $query->select('id', 'name','sale_price','sku','package_dimension'); // include foreign keys
    },
    'return_items.order_item.product' => function ($query) {
        $query->select('id', 'name', 'sale_price','sku','image'); // customize columns you want
    },
    'return_items.order_item.variant' => function ($query) {
        $query->select('id', 'name', 'sku','sale_price','image'); // customize columns you want
    },
    'vendor_order' => function ($query) {
        $query->select('id', 'order_id', 'vendor_id','uuid'); // include foreign key for 'order'
    },
    'vendor_order.order' => function ($query) {
        $query->select('id','user_id','uuid' ,'shipping_address_id','payment_method'); // include key for 'shipping_address'
    },
    'vendor_order.order.user' => function ($query) {
        $query->select('id', 'name','phone','email'); // include key for 'shipping_address'
    },
    'vendor_order.order.shipping_address',
    'vendor_order.order.shipping_address.city' => function ($query) {
        $query->select('id', 'name'); // city columns
    },
    'vendor_order.order.shipping_address.state' => function ($query) {
        $query->select('id', 'name'); // state columns
    }
])->where('id',$id)->first();
//dd($return->toArray());
$order_items=[];
 $shipservice = app(ShiprocketService::class);
if($return->is_transferred=='No'){
    $returnOrder=[];
$order=$return->vendor_order->order;
$patment_method=$order->payment_method=='COD'?'Cash on delivery':'prepaid';
$vendor=$return->vendor;
$vendor_order=$return->vendor_order;
$customer=$order->user;

$customer_ship_address=$order->shipping_address;
$only_order_items=$return->return_items->map(function ($item) {
       return $item->order_item;
       
    });
$final_package_dimesnion=getFinalShipmentDimensionsAndWeight($only_order_items);

if($return->type!='Exchange'){
   
$returnOrder = [
    'order_items' => $return->return_items->map(function ($item) {
        $product = optional($item->order_item->product);
        $variant = optional($item->order_item->variant);
        $exchange_variant = optional($item->exchange_variant);
        $variant_attributes= $variant? json_decode($variant->atributes_json,true):null;
        $color=$variant_attributes && isset($variant_attributes['Color'])?$variant_attributes['Color']:'';
        $size=$variant_attributes && isset($variant_attributes['Size'])?$variant_attributes['Size']:'';

        return [
            'name' => $product->name ?? 'Unknown Product',
            'selling_price' =>$variant?$variant->sale_price: $product->sale_price,
            'units' => $item->quantity ?? 1,
            'hsn' => '',
            'sku' => $variant?$variant->sku: $product->sku,
            'tax' => '',
            'discount' => $item->discount_share??0.0,
            'brand' => $vendor->name ?? '',
            'color' => $color,
            'exchange_item_id' => (string) $exchange_variant->id,
            'exchange_item_name' => $exchange_variant->product->name ?? '',
            'exchange_item_sku' => $exchange_variant->sku ?? '',
            'qc_enable' => false,
            'qc_product_name' => $product->name ?? '',
            'qc_product_image' => $variant
                          ?asset('storage/products/'.$product->id.'/variants/'.$variant->image)
                        :asset('storage/products/'.$product->id.'/'.$product->image),
            'qc_brand' => $vendor->name ?? '',
            'qc_color' => $color ?? '',
            'qc_size' => $size ?? '',
            'accessories' => '',
            'qc_used_check' => '1',
            'qc_sealtag_check' => '1',
            'qc_brand_box' => '1',
            'qc_check_damaged_product' => 'yes',
        ];
    })->toArray(),

    // Buyer Pickup Info
    'buyer_pickup_first_name' =>$customer_ship_address->name?$customer->name:'', // replace with actual
    'buyer_pickup_last_name' => '',
    'buyer_pickup_email' =>$customer->email,
    'buyer_pickup_address' =>$customer_ship_address->address1,
    'buyer_pickup_address_2' =>$customer_ship_address->address2,
    'buyer_pickup_city' =>$customer_ship_address->city->name,
    'buyer_pickup_state' =>$customer_ship_address->state->name,
    'buyer_pickup_country' => 'India',
    'buyer_pickup_phone' =>$customer->phone,
    'buyer_pickup_pincode' =>$customer_ship_address->pincode,

    // Buyer Shipping Info (same as pickup)
     'buyer_shipping_first_name' =>$customer_ship_address->name?$customer->name:'', // replace with actual
    'buyer_shipping_last_name' => '',
    'buyer_shipping_email' =>$customer->email,
    'buyer_shipping_address' =>$customer_ship_address->address1,
    'buyer_shipping_address_2' =>$customer_ship_address->address2,
    'buyer_shipping_city' =>$customer_ship_address->city->name,
    'buyer_shipping_state' =>$customer_ship_address->state->name,
    'buyer_shipping_country' => 'India',
    'buyer_shipping_phone' =>$customer->phone,
    'buyer_shipping_pincode' =>$customer_ship_address->pincode,

    // Seller warehouse location
    'seller_pickup_location_id' => $vendor->pickup_location_id,
    'seller_shipping_location_id' => $vendor->pickup_location_id,

    // Order meta
    'exchange_order_id' =>$return->uuid,
    'return_order_id' => $return->uuid,
    'payment_method' =>$patment_method,
    'order_date' => now()->toDateString(),
   
    'existing_order_id' => $return->vendor_order->uuid,
 
    // Charges
    'sub_total' => $return->return_items->sum(fn($item) =>
      ($item->order_item->variant?$item->order_item->variant->sale_price:$item->order_item->product->sale_price)- $item->order_item->discount_share
    ),
    'shipping_charges' => '',
    'giftwrap_charges' => '',
     'total_discount' => $return->return_items->sum(fn($item) =>
       $item->order_item->discount_share
    ),
 
    'transaction_charges' => '',

   'return_reason'=>'Size or color issue',
    "return_length"=>$final_package_dimesnion['length']>=1?$final_package_dimesnion['length']:1,
     "return_breadth"=>$final_package_dimesnion['breadth']>=1?$final_package_dimesnion['breadth']:1,
    "return_height"=>$final_package_dimesnion['height']>=1?$final_package_dimesnion['height']:1,
    "return_weight"=>$final_package_dimesnion['weight']>=1?$final_package_dimesnion['weight']:1,
   
   
    "exchange_length"=>$final_package_dimesnion['length']>=1?$final_package_dimesnion['length']:1,
     "exchange_breadth"=>$final_package_dimesnion['breadth']>=1?$final_package_dimesnion['breadth']:1,
    "exchange_height"=>$final_package_dimesnion['height']>=1?$final_package_dimesnion['height']:1,
    "exchange_weight"=>$final_package_dimesnion['weight']>=1?$final_package_dimesnion['weight']:1,
   

   
];
// dd($returnOrder);
$resp=$shipservice->createExchOrder($returnOrder);
if($resp['success']){
 $str=view('mails.return_order',['type'=>$return->type,'vendor_name'=>$vendor->name,'return_id'=>$return->uuid,'order_id'=>$order->uuid])->render();
      $resp=$this->mail($vendor->email,"New Exchange Order received",$str);
}
return $resp['success']? back()->withSuccess($resp['message']):back()->withError($resp['message']);
}else{
   // dd($customer->toArray());
    $returnOrder = [
    'order_items' => $return->return_items->map(function ($item) {
        $product = optional($item->order_item->product);
        $variant = optional($item->order_item->variant);
        // $exchange_variant = optional($item->exchange_variant);
        $variant_attributes= $variant? json_decode($variant->atributes_json,true):null;
        $color=$variant_attributes && isset($variant_attributes['Color'])?$variant_attributes['Color']:'';
        $size=$variant_attributes && isset($variant_attributes['Size'])?$variant_attributes['Size']:'';
         $itemImageForQCCheuque =str_replace('.webp', '.jpg', $variant?$variant->image:$product->image);
        return [
            'name' => $product->name ?? 'Unknown Product',
            'selling_price' =>$variant?$variant->sale_price: $product->sale_price,
            'units' => $item->quantity ?? 1,
            'hsn' => '',
            'sku' => $variant?$variant->sku: $product->sku,
            'tax' => '',
            'discount' => $item->discount_share??0.0,
            'brand' => $vendor->name ?? '',
            'color' => $color,
            // 'exchange_item_id' => (string) $exchange_variant->id,
            // 'exchange_item_name' => $exchange_variant->product->name ?? '',
            // 'exchange_item_sku' => $exchange_variant->sku ?? '',
            'qc_enable' => false,
            'qc_product_name' => $product->name ?? '',
            'qc_product_image' => $variant
                          ?asset('storage/products/'.$product->id.'/variants/'.$variant->image)
                        :asset('storage/products/'.$product->id.'/'.$product->image),
            'qc_brand' => $vendor->name ?? '',
            'qc_color' => $color ?? '',
            'qc_size' => $size ?? '',
            'accessories' => '',
            'qc_used_check' => '1',
            'qc_sealtag_check' => '1',
            
            'qc_check_damaged_product' => 'yes',
        ];
    })->toArray(),

    // Buyer Pickup Info
    'order_id'=>$return->uuid,
    'pickup_customer_name' =>$customer->name??"v", // replace with actual
    'pickup_last_name' => '',
    'company_name'=>$vendor->name,
    'pickup_email' =>$customer->email,
    'pickup_address' =>$customer_ship_address->address1,
    'pickup_address_2' =>$customer_ship_address->address2,
    'pickup_city' =>$customer_ship_address->city->name,
    'pickup_state' =>$customer_ship_address->state->name,
    'pickup_country' => 'India',
   
    'order_date'=>date('Y-m-d'),
    'pickup_phone' =>$customer->phone,
    'pickup_pincode' =>$customer_ship_address->pincode,

    // Buyer Shipping Info (same as pickup)
    'shipping_customer_name' => $vendor->name,
    'shipping_last_name' => '',
    'shipping_email' => $vendor->email ?? '',
    'shipping_address' => $vendor->address1 ?? 'Jankipuram',
    'shipping_address_2' =>  $vendor->address2,
    'shipping_city' => optional($vendor->city)->name ?? '',
    'shipping_state' => optional($vendor->state)->name ?? '',
    'shipping_country' => 'India',
    'shipping_phone' => $vendor->phone ?? '',
    'shipping_pincode' => $vendor->pincode ?? '',
    'shipping_isd_code'=>"91",
   'payment_method' => $patment_method,
   
     'sub_total' => $return->return_items->sum(fn($item) =>
       ($item->order_item->variant?$item->order_item->variant->sale_price:$item->order_item->product->sale_price)- $item->order_item->discount_share
    ),
     'total_discount' => $return->return_items->sum(fn($item) =>
       $item->order_item->discount_share
    ),
 
    "length"=> $final_package_dimesnion['length']>=0.5?$final_package_dimesnion['length']:0.5,
    "breadth"=>$final_package_dimesnion['breadth']>=0.5?$final_package_dimesnion['breadth']:0.5,
    "height"=>$final_package_dimesnion['height']>=0.5?$final_package_dimesnion['height']:0.5,
    "weight"=>$final_package_dimesnion['weight']>=0.5?$final_package_dimesnion['weight']:0.5,
];
//dd($returnOrder);
$resp=$shipservice->createReturnOrder($returnOrder);
if($resp['success']){
 $str=view('mails.return_order',['type'=>$return->type,'vendor_name'=>$vendor->name,'return_id'=>$return->uuid,'order_id'=>$order->uuid])->render();
      $resp=$this->mail($vendor->email,"New Return Order Received",$str);
}
return $resp['success']? back()->withSuccess($resp['message']):back()->withError($resp['message']);
}


  
}

}
public function showLabel($vendorOrderId)
{
    $vendorId = \Auth::guard('vendor')->id();
 $vendorId = $vendorId ? $vendorId :4;
   $shipservice = app(ShiprocketService::class);
   

   $vendorOrder =\App\Models\VendorOrder::with([
    'order','order.user:id,name,phone,email',
    'order.items.product',
    'order.items.variant',
    'order.billing_address',
    'order.shipping_address',
    'vendor',
])->findOrFail($vendorOrderId);

    // Filter items for this vendor only
   $items = $vendorOrder->order->items->where('vendor_id',$vendorId);

   $url=$shipservice->generateDocument('label',$vendorOrder->shiprocket_shipment_id);
   if(!isset($url['label_url']) || empty($url['label_url']))
     return redirect()->back()->withError('Faliled to generate label');
  $routing_code='NA';
  $url=$url['label_url'];
    $routing_code=extractTextValueFromPdfUrl($url,'Routing Code:');
    $dimension=extractTextValueFromPdfUrl($url,'Dimensions:');
    $weight=extractTextValueFromPdfUrl($url,'Weight:');
   
    if (empty($routing_code) || empty($dimension) || empty($weight)) {
       echo "some value from pdf caould not be extracted ";die;
    }
    
    // return view('label', [
    //     'routing_code'=>$routing_code,
    //     'vendorOrder' => $vendorOrder,
    //     'order' => $vendorOrder->order,
    //     'items' => $items,
    //     'dimension'=>$dimension,'weight'=>$weight
       
    // ]);
   $pdf = Pdf::loadView('label', [
        'routing_code'=>$routing_code,
        'vendorOrder' => $vendorOrder,
        'order' => $vendorOrder->order,
        'items' => $items,'dimension'=>$dimension,'weight'=>$weight
    ]);
    return $pdf->download('shipping-label_'.$vendorOrder->id.'.pdf');
   
}
}
