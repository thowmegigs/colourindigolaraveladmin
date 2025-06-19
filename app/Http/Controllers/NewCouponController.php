<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewCouponRequest;
use App\Models\NewCoupon;
use File;
use \Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class NewCouponController extends Controller
{
    public function __construct()
    {
         $this->dashboard_url=\URL::to('/');
        $this->index_url=domain_route('new_coupons.index');
        $this->module='NewCoupon';
        $this->view_folder='new_coupons';
        $this->storage_folder=$this->view_folder;
        $this->has_upload=0;
        $this->is_multiple_upload=0;
        $this->has_export=0;
        $this->pagination_count=100;
        $this->crud_title='Coupon';
         $this->show_crud_in_modal=1;
         $this->has_popup=1;
        $this->has_detail_view =0;
        $this->has_side_column_input_group =0;
        $this->form_image_field_name =[];

        $this->model_relations =[];

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
                'placeholder' => 'Enter code',
                'name' => 'code',
                'label' => 'Coupon Code',
                'tag' => 'input',
                'type' => 'text',
                'default' => isset($model) ? $model->code : "",
                'attr' => [],'col'=>6
            ],
            [
                'placeholder' => 'Enter short_description',
                'name' => 'short_description',
                'label' => 'Short Description',
                'tag' => 'input',
                'type' => 'text',
                'default' => isset($model) ? $model->short_description : "",
                'attr' => [],'col'=>6
            ],
            [
                'placeholder' => 'Enter description',
                'name' => 'description',
                'label' => 'Description',
                'tag' => 'textarea',
                'type' => 'textarea',
                'default' => isset($model) ? $model->description : "",
                'attr' => []
            ],
            [
                'placeholder' => 'Enter cart_amount',
                'name' => 'cart_amount',
                'label' => 'Cart Amount',
                'tag' => 'input',
                'type' => 'number',
                'default' => isset($model) ? $model->cart_amount : "",
                'attr' => [],'col'=>6
            ],
            [
                'placeholder' => 'Enter usage_limit_per_user',
                'name' => 'usage_limit_per_user',
                'label' => 'Usage Limit',
                'tag' => 'input',
                'type' => 'number',
                'default' => isset($model) ? $model->usage_limit_per_user : "",
                'attr' => [],'col'=>6
            ],
            [
                'placeholder' => 'Enter discount',
                'name' => 'discount',
                'label' => 'Discount(*)',
                'tag' => 'input',
                'type' => 'number',
                'default' => isset($model) ? $model->discount : "",
                'attr' => [],'col'=>6
            ],
            [
                'placeholder' => 'Enter start_date',
                'name' => 'start_date',
                'label' => 'Start Date',
                'tag' => 'input',
                'type' => 'date',
                'default' => isset($model) ? $model->start_date : "",
                'attr' => [],'col'=>6
            ],
            [
                'placeholder' => 'Enter end_date',
                'name' => 'end_date',
                'label' => 'End Date',
                'tag' => 'input',
                'type' => 'date',
                'default' => isset($model) ? $model->end_date : "",
                'attr' => [],'col'=>6
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
                'placeholder' => 'Enter code',
                'name' => 'code',
                'label' => 'Coupon Code',
                'tag' => 'input',
                'type' => 'text',
                'default' => isset($model) ? $model->code : "",
                'attr' => [],'col'=>6
            ],
            [
                'placeholder' => 'Enter short_description',
                'name' => 'short_description',
                'label' => 'Short Description',
                'tag' => 'input',
                'type' => 'text',
                'default' => isset($model) ? $model->short_description : "",
                'attr' => [],'col'=>6
            ],
            [
                'placeholder' => 'Enter description',
                'name' => 'description',
                'label' => 'Description',
                'tag' => 'textarea',
                'type' => 'textarea',
                'default' => isset($model) ? $model->description : "",
                'attr' => [],'col'=>6
            ],
            [
                'placeholder' => 'Enter cart_amount',
                'name' => 'cart_amount',
                'label' => 'Cart Amount',
                'tag' => 'input',
                'type' => 'number',
                'default' => isset($model) ? $model->cart_amount : "",
                'attr' => [],'col'=>6
            ],
            [
                'placeholder' => 'Enter usage_limit_per_user',
                'name' => 'usage_limit_per_user',
                'label' => 'Usage Limit',
                'tag' => 'input',
                'type' => 'number',
                'default' => isset($model) ? $model->usage_limit_per_user : "",
                'attr' => [],'col'=>6
            ],
            [
                'placeholder' => 'Enter discount',
                'name' => 'discount',
                'label' => 'Discount(*)',
                'tag' => 'input',
                'type' => 'number',
                'default' => isset($model) ? $model->discount : "",
                'attr' => [],'col'=>6
            ],
            [
                'placeholder' => 'Enter start_date',
                'name' => 'start_date',
                'label' => 'Start Date',
                'tag' => 'input',
                'type' => 'date',
                'default' => isset($model) ? $model->start_date : "",
                'attr' => [],'col'=>6
            ],
            [
                'placeholder' => 'Enter end_date',
                'name' => 'end_date',
                'label' => 'End Date',
                'tag' => 'input',
                'type' => 'date',
                'default' => isset($model) ? $model->end_date : "",
                'attr' => [],'col'=>6
            ],
              [
                        'name' => 'status',
                        'label' => 'Status',
                        'tag' => 'input',
                        'type' => 'radio',
                        'default' => isset($model) && isset($model->status) ? $model->status : 'Active',
                        'attr' => [],
                        'value' => [
                            (object) [
                                'label' => 'Active',
                                'value' => 'Active',
                            ],
                            (object) [
                                'label' => 'In-Active',
                                'value' => 'In-Active',
                            ],
                        ],
                        'has_toggle_div' => [],
                        'multiple' => false,
                        'inline' => true,'col'=>6
                    ],
        ]
    ]
];
// dd($data[0]['inputs']);
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
        'column' => 'code',
        'label' => 'Code',
        'sortable' => 'Yes',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'cart_amount',
        'label' => 'Cart Amount',
        'sortable' => 'Yes',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'discount',
        'label' => 'Discount',
        'sortable' => 'Yes',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'start_date',
        'label' => 'StartDate',
        'sortable' => 'Yes',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'end_date',
        'label' => 'EndDate',
        'sortable' => 'Yes',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ]
];
        $view_columns =[
    [
        'column' => null,
        'label' => '',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'code',
        'label' => 'Code',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'short_description',
        'label' => 'Short Description',
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
        'column' => 'cart_amount',
        'label' => 'Cart Amount',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'usage_limit_per_user',
        'label' => 'Usage Limit Per User',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'start_date',
        'label' => 'Start Date',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'end_date',
        'label' => 'End Date',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
    [
        'column' => 'discount',
        'label' => 'Discount',
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

        $searchable_fields=[
    [
        'name' => 'code',
        'label' => 'Code'
    ]
];
        $filterable_fields=[
    [
        'name' => 'cart_amount',
        'label' => 'Cart Amount',
        'type' => 'number'
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
            'plural_lowercase' =>'new_coupons',
            'has_image' => $this->has_upload,
            'table_columns'=>$table_columns,
            'view_columns'=>$view_columns,
           
            'image_field_names' => $this->form_image_field_name,
            'storage_folder' => $this->storage_folder,
             'module_table_name'=>'new_coupons',
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
            $data['row'] = NewCoupon::with(array_column($this->model_relations, 'name'))->findOrFail($id);
        } else {
            $data['row'] = NewCoupon::findOrFail($id);
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

            $db_query =  NewCoupon::when(!empty($search_val), function ($query) use ($search_val, $search_by) {
                return $query->where($search_by, 'like', '%' . $search_val . '%');
            })
                ->when(!empty($sort_by), function ($query) use ($sort_by, $sort_type) {
                    return $query->orderBy($sort_by, $sort_type);
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
                'bulk_update' => ''
               
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
            if (!can('list_new_coupons')) {
                return redirect()->back()->withError('Dont have permission to list');
            }
            $query = null;
            if (count($this->model_relations) > 0) {
                $query = NewCoupon::with(array_column($this->model_relations, 'name'));
            } else {
                $query =  NewCoupon::query();
            }
            $query = $this->buildFilter($request, $query);
            $list = $query->latest()->paginate($this->pagination_count);
            $view_data = array_merge($common_data, [

                'list' => $list,
                'bulk_update' => '','tabs'=>$tabs
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
           
             if (!can('create_new_coupons')) {
                return createResponse(false,'Dont have permission to create');
                }
              
                $html=view('admin.' . $this->view_folder . '.modal.add', with($view_data))->render();
                return  createResponse(true, $html);
         }
       else{
         
         if (!can('create_new_coupons')) {
                return redirect()->back()->withError('Dont have permission to create');
            }
         return view('admin.' . $this->view_folder . '.add', with($view_data));
       }
    
    }
    public function store(NewCouponRequest $request)
    {
         if (!can('create_new_coupons')) {
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
            $post['slug']=\Str::slug($post['short_description']);
             $newcoupon = NewCoupon::create($post);
               $this->afterCreateProcess($request,$post,$newcoupon);
             \DB::commit();
            return createResponse(true, $this->crud_title . ' created successfully', $this->index_url);
        } catch (\Exception $ex) { \Sentry\captureException($ex);
            \DB::rollback();

            return createResponse(false, $ex->getMessage());
        }
    }
    public function edit(Request $request,$id)
    {
        

        $model = NewCoupon::findOrFail($id);

        $data = $this->editInputsData($model);

        $view_data = array_merge($this->commonVars($model)['data'], [
            'data' => $data, 'model' => $model,

        ]);
       
       

         if ($request->ajax()) {
            if (!can('edit_new_coupons')) {
                return createResponse(false, 'Dont have permission to edit');
            }

            $html = view('admin.' . $this->view_folder . '.modal.edit', with($view_data))->render();
            return createResponse(true, $html);

        } else {
            if (!can('edit_new_coupons')) {
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
             if (!can('view_new_coupons')) {
                return createResponse(false,'Dont have permission to view');
         }
           
            $html = view('admin.' . $this->view_folder . '.view.' . $view, with($data))->render();
            return createResponse(true, $html);

        } else {
            if (!can('view_new_coupons')) {
                return redirect()->back()->withError('Dont have permission to view');
         }
           

            return view('admin.' . $this->view_folder . '.view.' . $view, with($data));

        }

      

    }
    
    public function update(NewCouponRequest $request, $id)
    {
         if (!can('edit_new_coupons')) {
        return createResponse(false,'Dont have permission to update');
        }
        \DB::beginTransaction();

        try
        {
            $post = $request->all();

            $newcoupon = NewCoupon::findOrFail($id);

            $post = formatPostForJsonColumn($post);
             /* Saving name alongwith id in json column takki join se na retrive karna pade
               copy this code from contrller file and paste and edit here 
              $post=$this->processJsonColumnToAddNameOrAddtionalData($post);
            */
             $post['slug']=\Str::slug($post['short_description']);
            $newcoupon->update($post);
              $this->afterCreateProcess($request,$post,$newcoupon);
                  \DB::commit();
            return createResponse(true, $this->crud_title . ' updated successfully', $this->index_url);
        } catch (\Exception $ex) { \Sentry\captureException($ex);
             \DB::rollback();
            return createResponse(false, $ex->getMessage());
        }
    }

    public function destroy($id)
    {
                if (!can('delete_new_coupons')) {
        return createResponse(false,'Dont have permission to delete');
        }
         \DB::beginTransaction();
        try
        {
            if(NewCoupon::where('id',$id)->exists())
               NewCoupon::destroy($id);
     
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
   

    
     public function exportNewCoupon(Request $request,$type){
        if (!can('export_new_coupons')) {
            return redirect()->back()->withError('Not allowed to export');
        }
      $meta_info=$this->commonVars()['data'];
      return $this->exportModel('NewCoupon','new_coupons',$type,$meta_info);
     
      
   
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
}
