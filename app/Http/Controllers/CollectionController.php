<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectionRequest;
use App\Models\Collection;
use Image;
use DB;
use \Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function __construct()
    {
         $this->dashboard_url=\URL::to('/');
        $this->index_url=domain_route('collections.index');
        $this->module = 'Collection';
        $this->view_folder = 'collections';
        $this->storage_folder = $this->view_folder;
        $this->has_upload = 1;
        $this->is_multiple_upload = 0;
        $this->has_export = 0;
        $this->pagination_count = 100;
        $this->crud_title = 'Collection';
        $this->show_crud_in_modal = 1;
        $this->has_popup = 1;
        $this->has_detail_view = 0;
        $this->has_side_column_input_group = 0;
        $this->dimensions=[
            
                'tiny'  => 200,
                'small' => 350,
                'medium' => 550,
                'large'=>750
               
            
        ];
        $this->form_image_field_name = [
            [
                'field_name' => 'image',
                'single' => true,
                'has_thumbnail' => true,
            ],
        ];

        $this->model_relations = [];

    }
    public function sideColumnInputs($model = null)
    {
        $data = [
            'side_title' => 'Any Title',
            'side_inputs' => [],

        ];

        return $data;
    }
    public function createInputsData()
    {
        $data = [
            [
                'label' => null,
                'inputs' => [
                    [
                        'name' => 'product_id',
                        'label' => 'Select Products',
                        'tag' => 'select',
                        'type' => 'select',
                        'default' => '',
                        'attr' => [
                            'id' => 'sdsd',
                            'data-ajax-search' => 'true',
                            'data-search-table' => 'products',
                            'data-search-id-column' => 'id',
                            'data-search-name-column' => 'name',
                            'data-search-by-column' => 'name',
                            'data-search-wherein' => 'category_id',
                        ],
                        'custom_key_for_option' => 'name',
                        'options' => [],
                        'custom_id_for_option' => 'id',
                        'multiple' => true, 'col' => '12','order_no'=>2
                    ],
                    [
                        'placeholder' => 'Enter name',
                        'name' => 'name',
                        'label' => 'Name',
                        'tag' => 'input',
                        'type' => 'text',
                        'default' => isset($model) ? $model->name : "",
                        'attr' => [],'col'=>6,'order_no'=>1
                    ],
                     
                    [
                        'placeholder' => 'Enter subtitle',
                        'name' => 'subtitle',
                        'label' => 'Subtitle',
                        'tag' => 'input',
                        'type' => 'text',
                        'default' => isset($model) ? $model->subtitle : "",
                        'attr' => [],'col'=>6,'order_no'=>2
                    ],
                    // [
                    //     'name' => 'product_show_only_in_collection',
                    //     'label' => 'Product Show Only In Collection',
                    //     'tag' => 'input',
                    //     'type' => 'radio',
                    //     'default' => isset($model) && isset($model->product_show_only_in_collection) ? $model->product_show_only_in_collection : 'No',
                    //     'attr' => [
                           
                    //     ],
                    //     'value' => [
                    //         (object) [
                    //             'label' => 'Yes',
                    //             'value' => 'Yes',
                    //         ],
                    //         (object) [
                    //             'label' => 'No',
                    //             'value' => 'No',
                    //         ],
                    //     ],
                    //     'has_toggle_div' => [],
                    //     'multiple' => false,
                    //     'inline' => true, 'col' => 6,'order_no'=>3
                    // ],
                    // [
                    //     'placeholder' => 'Enter end',
                    //     'name' => 'edn_date',
                    //     'label' => 'Timer End Date',
                    //     'tag' => 'input',
                    //     'type' => 'date',
                    //     'default' => isset($model) ? $model->end_date : "",
                    //     'attr' => [],'col'=>6
                    // ],
                    [
                        'name' => 'collection_type',
                        'label' => 'Collection Type',
                        'tag' => 'input',
                        'type' => 'radio',
                        'default' => isset($model) && isset($model->collection_type) ? $model->collection_type : 'Manual',
                        'attr' => [
                            'onchange'=>'toggleCollectionTypeDisplay(this.value)'
                        ],
                        'value' => [
                            (object) [
                                'label' => 'Manual',
                                'value' => 'Manual',
                            ],
                            (object) [
                                'label' => 'Automatic',
                                'value' => 'Automatic',
                            ],
                        ],
                        'has_toggle_div' => [],
                        'multiple' => false,
                        'inline' => true, 'col' => 6,'order_no'=>3
                    ],
                ],
            ],

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
                    'attr' => $g['single'] ? [] : ['multiple' => 'multiple'],'col'=>12
                ];
                array_push($data[0]['inputs'], $y);
            }
        }
        return $data;
    }
    public function editInputsData($model)
    {
        $products = !empty($model->product_id) ? json_decode($model->product_id, true) : [];
        $data = [
            [
                'label' => null,
                'inputs' => [
                    [
                        'name' => 'product_id',
                        'label' => 'Products',
                        'tag' => 'select',
                        'type' => 'select',
                        'default' => !empty($products) ? array_column($products,'id') : '',
                        'attr' => [],
                        'custom_key_for_option' => 'name',
                        'options' => getList('Product'),
                        'custom_id_for_option' => 'id',
                        'multiple' => true,
                    ],
                    [
                        'placeholder' => 'Enter name',
                        'name' => 'name',
                        'label' => 'Name',
                        'tag' => 'input',
                        'type' => 'text',
                        'default' => isset($model) ? $model->name : "",
                        'attr' => [],'order_no'=>1
                    ],
                   
                    [
                        'placeholder' => 'Enter subtitle',
                        'name' => 'subtitle',
                        'label' => 'Subtitle',
                        'tag' => 'input',
                        'type' => 'text',
                        'default' => isset($model) ? $model->subtitle : "",
                        'attr' => [],'order_no'=>2
                    ],
                    // [
                    //     'name' => 'product_show_only_in_collection',
                    //     'label' => 'Product Show Only In Collection',
                    //     'tag' => 'input',
                    //     'type' => 'radio',
                    //     'default' => isset($model) && isset($model->product_show_only_in_collection) ? $model->product_show_only_in_collection : 'No',
                    //     'attr' => [
                           
                    //     ],
                    //     'value' => [
                    //         (object) [
                    //             'label' => 'Yes',
                    //             'value' => 'Yes',
                    //         ],
                    //         (object) [
                    //             'label' => 'No',
                    //             'value' => 'No',
                    //         ],
                    //     ],
                    //     'has_toggle_div' => [],
                    //     'multiple' => false,
                    //     'inline' => true, 'col' => 6,'order_no'=>3
                    // ],
                    // [
                    //     'placeholder' => 'Enter end',
                    //     'name' => 'edn_date',
                    //     'label' => 'Timer End Date',
                    //     'tag' => 'input',
                    //     'type' => 'date',
                    //     'default' => isset($model) ? $model->end_date : "",
                    //     'attr' => [],'col'=>6,'order_no'=>4
                    // ],
                    [
                        'order_no'=>3,
                        'name' => 'collection_type',
                        'label' => 'Collection Type',
                        'tag' => 'input',
                        'type' => 'radio',
                        'default' => isset($model) && isset($model->collection_type) ? $model->collection_type : 'Manual',
                        'attr' => [
                            'onchange'=>'toggleCollectionTypeDisplay(this.value)'
                        ],
                        'value' => [
                            (object) [
                                'label' => 'Manual',
                                'value' => 'Manual',
                            ],
                            (object) [
                                'label' => 'Automatic',
                                'value' => 'Automatic',
                            ],
                        ],
                        'has_toggle_div' => [],
                        'multiple' => false,
                        'inline' => true, 'col' => 6,
                    ],
                ],
            ],
        ];
        if (count($this->form_image_field_name) > 0) {
            foreach ($this->form_image_field_name as $g) {
                $y = [
                    'placeholder' => '',
                    'name' => $g['single'] ? $g['field_name'] : $g['field_name'] . '[]',
                    'label' => $g['single'] ? properSingularName($g['field_name']) : properPluralName($g['field_name']),
                    'tag' => 'input',
                    'type' => 'file',
                    'default' => $g['single'] ? $this->storage_folder . '/' . $model->{$g['field_name']} : json_encode($this->getImageList($model->id, $g['table_name'], $g['parent_table_field'], $this->storage_folder)),
                    'attr' => $g['single'] ? [] : ['multiple' => 'multiple'],
                ];
                array_push($data[0]['inputs'], $y);
            }
        }
        return $data;
    }
    public function commonVars($model = null)
    {

        $repeating_group_inputs=[
            [
                'colname' => 'conditions',
                'label' => 'Conditions',
                'inputs' => [
                    [
                        'name' => 'conditions__json__name[]',
                        'label' => 'Select Name',
                        'tag' => 'select',
                        'type' => 'select',
                        'default' => 'Tag',
                        'attr' => [],
                        'custom_key_for_option' => 'name',
                        'options' => [
                            (object) [
                                'id' => 'Tag',
                                'name' => 'Tag'
                            ],
                            (object) [
                                'id' => 'Brand',
                                'name' => 'Brand'
                            ],
                            (object) [
                                'id' => 'Discount',
                                'name' => 'Discount'
                            ],
                            (object) [
                                'id' => 'Price',
                                'name' => 'Price'
                            ]
                        ],
                        'custom_id_for_option' => 'id',
                        'multiple' => false
                    ],
                    [
                        'name' => 'conditions__json__comparison[]',
                        'label' => 'Select Comparison',
                        'tag' => 'select',
                        'type' => 'select',
                        'default' => 'Eq',
                        'attr' => [],
                        'custom_key_for_option' => 'name',
                        'options' => [
                            (object) [
                                'id' => 'Eq',
                                'name' => 'Equalt To '
                            ],
                            (object) [
                                'id' => 'Gt',
                                'name' => 'Greater than'
                            ],
                            (object) [
                                'id' => 'Lt',
                                'name' => 'Less than'
                            ],
                            
                        ],
                        'custom_id_for_option' => 'id',
                        'multiple' => false
                    ],
                    [
                        'placeholder' => 'Enter value',
                        'name' => 'conditions__json__value[]',
                        'label' => 'Value',
                        'tag' => 'input',
                        'type' => 'text',
                        'default' => '',
                        'attr' => []
                    ]
                ],
                'index_with_modal' => 0,
                'modalInputBoxIdWhoseValueToSetInSelect' => '',
                'hide' => false,
                'disable_buttons' => false
            ]];
             $toggable_group = [];

        $table_columns = [
            [
                'column' => 'name',
                'label' => 'Name',
                'sortable' => 'Yes',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
           
            [
                'column' => 'subtitle',
                'label' => 'Subtitle',
                'sortable' => 'Yes',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'image',
                'label' => 'Image',
                'sortable' => 'Yes',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'product_id',
                'label' => 'Products',
                'sortable' => 'Yes',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
        ];
        $view_columns = [
            [
                'column' => 'category_id',
                'label' => 'Categories',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'product_id',
                'label' => 'Products ',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
          
            [
                'column' => 'name',
                'label' => 'Name',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'subtitle',
                'label' => 'Subtitle',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],

            [
                'column' => 'image',
                'label' => 'Image',

            ],

        ];

        $searchable_fields = [
            [
                'name' => 'name',
                'label' => 'Name',
            ],
        ];
        $filterable_fields = [
            [
                'name' => 'created_at',
                'label' => 'Created At',
                'type' => 'date',
            ],
        ];

        $data['data'] = [

            'dashboard_url' => $this->dashboard_url,
            'index_url' => $this->index_url,
            'title' => 'All ' . $this->crud_title . 's',
            'module' => $this->module,
            'model_relations' => $this->model_relations,
            'searchable_fields' => $searchable_fields,
            'filterable_fields' => $filterable_fields,
            'storage_folder' => $this->storage_folder,
            'plural_lowercase' => 'collections',
            'has_image' => $this->has_upload,
            'table_columns' => $table_columns,
            'view_columns' => $view_columns,

            'image_field_names' => $this->form_image_field_name,
            'storage_folder' => $this->storage_folder,
            'module_table_name' => 'collections',
            'has_export' => $this->has_export,
            'crud_title' => $this->crud_title,
            'show_crud_in_modal' => $this->show_crud_in_modal,
            'has_popup' => $this->has_popup,
            'has_side_column_input_group' => $this->has_side_column_input_group,
            'has_detail_view' => $this->has_detail_view,
            'repeating_group_inputs' => $repeating_group_inputs,
            'toggable_group' => $toggable_group,
              'thumbnailDimensions'=>$this->dimensions
        ];

        return $data;

    }
  
    public function afterCreateProcess($request, $post, $model)
    {
        $meta_info = $this->commonVars()['data'];

        return $this->afterCreateProcessBase($request, $post, $model, $meta_info);
    }
    public function common_view_data($id)
    {
        $data['row'] = null;
        if (count($this->model_relations) > 0) {
            $data['row'] = Collection::with(array_column($this->model_relations, 'name'))->findOrFail($id);
        } else {
            $data['row'] = Collection::findOrFail($id);
        }
        $data['view_inputs'] = [];
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

            $db_query = Collection::when(!empty($search_val), function ($query) use ($search_val, $search_by) {
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
                'bulk_update' => '',

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
            if (!can('list_collections')) {
                return redirect()->back()->withError('Dont have permission to list');
            }
            $query = null;
            if (count($this->model_relations) > 0) {
                $query = Collection::with(array_column($this->model_relations, 'name'));
            } else {
                $query = Collection::query();
            }
            $query = $this->buildFilter($request, $query);
            $list = $query->latest()->paginate($this->pagination_count);
            $view_data = array_merge($common_data, [

                'list' => $list,
                'bulk_update' => '', 'tabs' => $tabs,
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
        $cats = \App\Models\Category::whereNull('category_id')->get()->toArray();
        $s = '';
        $i = 0;
        $category_options = gt($cats, $i, $s);
        $data = $this->createInputsData();
        $view_data = array_merge($this->commonVars()['data'], [
            'data' => $data, 'category_options' => $category_options,

        ]);
        if ($r->ajax()) {

            if (!can('create_collections')) {
                return createResponse(false, 'Dont have permission to create');
            }

            $html = view('admin.' . $this->view_folder . '.modal.add', with($view_data))->render();
            return createResponse(true, $html);
        } else {

            if (!can('create_collections')) {
                return redirect()->back()->withError('Dont have permission to create');
            }
            return view('admin.' . $this->view_folder . '.add', with($view_data));
        }

    }
    public function store(CollectionRequest $request)
    {
        if (!can('create_collections')) {
            return createResponse(false, 'Dont have permission to create');
        }
        \DB::beginTransaction();

        try {
            $post = $request->all();
           // dd($post);
             if (empty($post['category_id'][0])) {
                 $post['category_id'] = null;
             }
           
            if (empty($post['product_id'][0])) {
                $post['product_id'] = null;
            }

            //  !empty($post['customer_group_id'][0])
            $post = formatPostForJsonColumn($post);
            /* Saving name alongwith id in json column takki join se na retrive karna pade
            copy this code from contrller file and paste and edit here
            $post=$this->processJsonColumnToAddNameOrAddtionalData($post);
             */
            // if (!empty($post['category_id'])) {
            //      $post['category_id'] = json_encode($post['category_id']);
            // }
          //  dd($post);
            if (!empty($post['product_id'])) {
                $ids = json_decode($post['product_id']);
                // if($post['product_show_only_in_collection']=='Yes'){
                //     \DB::table('products')->whereIn('id', $ids)->update(['visibilty'=>'Collection Only']);   
                // }
                 $array=\App\Models\Product::with('vendor:id,name')->whereIn('id',$ids)->get();

              
                $ar = [];
                foreach ($array  as $item) {
                 
                    $ar[] = ['id'=> $item->id, 
                    'name' => $item->name,
                    'image'=>$item->image,
                    'slug'=>$item->slug,
                    'price'=>$item->price,'sale_price'=>$item->sale_price,
                    'discount'=>$item->discount,'rating'=>$item->rating,'brand'=>$item->vendor->name
                   ];
                
                 }

                unset($post['product_id']);
                $post['product_id'] = json_encode($ar);
            }
         
            $post['slug']=\Str::slug($post['name']);
            // dd($post);
            $collection = Collection::create($post);
            $this->afterCreateProcess($request, $post, $collection);
            \DB::commit();
            return createResponse(true, $this->crud_title . ' created successfully', $this->index_url);
        } catch (\Exception $ex) { \Sentry\captureException($ex);
            \DB::rollback();

            return createResponse(false, $ex->getMessage());
        }
    }
    public function edit(Request $request, $id)
    {

        $model = Collection::findOrFail($id);
        $data = $this->editInputsData($model);
        $cats = \App\Models\Category::whereNull('category_id')->get()->toArray();
        $s = '';
        $i = 0;
       $categories = !empty($model->category_id) ? json_decode($model->category_id, true) : null;
        $category_options = $categories ? gt_multiple($cats, $i, $s, $categories) : gt($cats, $i, $s);
     
        $view_data = array_merge($this->commonVars($model)['data'], [
            'data' => $data, 'model' => $model, 'category_options' => $category_options,

        ]);

        if ($request->ajax()) {
            if (!can('edit_collections')) {
                return createResponse(false, 'Dont have permission to edit');
            }

            $html = view('admin.' . $this->view_folder . '.modal.edit', with($view_data))->render();
            return createResponse(true, $html);

        } else {
            if (!can('edit_collections')) {
                return redirect()->back()->withError('Dont have permission to edit');
            }

            return view('admin.' . $this->view_folder . '.edit', with($view_data));

        }

    }

    public function show(Request $request, $id)
    {
        $view = $this->has_detail_view ? 'view_modal_detail' : 'view_modal';
        $data = $this->common_view_data($id);

        if ($request->ajax()) {
            if (!can('view_collections')) {
                return createResponse(false, 'Dont have permission to view');
            }

            $html = view('admin.' . $this->view_folder . '.view.' . $view, with($data))->render();
            return createResponse(true, $html);

        } else {
            if (!can('view_collections')) {
                return redirect()->back()->withError('Dont have permission to view');
            }

            return view('admin.' . $this->view_folder . '.view.' . $view, with($data));

        }

    }

    public function update(CollectionRequest $request, $id)
    {
        if (!can('edit_collections')) {
            return createResponse(false, 'Dont have permission to update');
        }
        \DB::beginTransaction();

        try
        {
            $post = $request->all();
            if (empty($post['category_id'][0])) {
                $post['category_id'] = null;
            }
            if (empty($post['product_id'][0])) {
                $post['product_id'] = null;
            }

            $collection = Collection::findOrFail($id);

            $post = formatPostForJsonColumn($post);

            // if (!empty($post['category_id'])) {
            //     $ids = json_decode($post['category_id']);
            //     $names_array = \DB::table('categories')->whereIn('id', $ids)->pluck('name', 'id')->toArray();
            //     $ar = [];
            //     foreach ($ids as $id) {
            //         $name = isset($names_array[$id]) ? $names_array[$id] : '';
            //         $ar[] = ['id' => $id, 'name' => $name];
            //     }

            //     unset($post['category_id']);
            //     $post['category_id'] = json_encode($ar);
            // }

            if (!empty($post['product_id'])) {
                $ids = json_decode($post['product_id']);
              
                $array =\App\Models\Product::with('vendor:id,name')->whereIn('id',$ids)->get();
              
                $ar = [];
                foreach ($array  as $item) {
                 
                    $ar[] = ['id'=> $item->id, 'name' => $item->name,'image'=>$item->image,
                    'price'=>$item->price,'sale_price'=>$item->sale_price,
                     'slug'=>$item->slug,
                    'discount'=>$item->discount,'rating'=>$item->rating,'brand'=>$item->vendor->name
                   ];
                
                 }

                unset($post['product_id']);
                $post['product_id'] = json_encode($ar);
            }
            $old_image = $collection->image;
            if ($request->hasFile('image')) {
                if ($old_image) {
                    $file_path = 'storage/' . $this->storage_folder . '/' . $old_image;
                    if (\File::exists(public_path($file_path))) {
                        \File::delete(public_path($file_path));
                    }

                }
            }
            $post['slug']=\Str::slug($post['name']);
            $collection->update($post);
           
            $this->afterCreateProcess($request, $post, $collection);
           
            \DB::commit();
             $collection=\DB::table('collections')->where('id',$collection->id)->first();
             $this->updateInFrontendSectionsWhenCollectionChange($collection);
            return createResponse(true, $this->crud_title . ' updated successfully', $this->index_url);
        } catch (\Exception $ex) { \Sentry\captureException($ex);
            \DB::rollback();
            return createResponse(false, $ex->getMessage().'===='.$ex->getLine());
        }
    }

    public function destroy($id)
    {
        if (!can('delete_collections')) {
            return createResponse(false, 'Dont have permission to delete');
        }
       /// \DB::beginTransaction();
        try
        {
            if (Collection::where('id', $id)->exists()) {
                Collection::destroy($id);
                \DB::table('contentsection_collection')->where('collection_id',$id)->delete();
            }

            if ($this->has_upload) {
                $this->deleteFile($id);
            }
        //    \DB::commit();
            return createResponse(true, $this->module . ' Deleted successfully');
        } catch (\Exception $ex) { \Sentry\captureException($ex);
           // \DB::rollback();
            return createResponse(false, 'Failed to  Delete Properly');
        }

    }
    public function deleteFile($id)
    {

        return $this->deleteFileBase($id, $this->storage_folder);

    }

    public function exportCollection(Request $request, $type)
    {
        if (!can('export_collections')) {
            return redirect()->back()->withError('Not allowed to export');
        }
        $meta_info = $this->commonVars()['data'];
        return $this->exportModel('Collection', 'collections', $type, $meta_info);

    }
   
    public function getImageList($id, $table, $parent_field_name)
    {

        return $this->getImageListBase($id, $table, $parent_field_name, $this->storage_folder);
    }
   
    protected function updateInFrontendSectionsWhenCollectionChange($collection){
   
    $web_content_section_rows = \DB::table('content_sections')
            ->whereNotNull('collection_ids') // Ensure it's not NULL
            ->where('collection_ids', '!=', '[]') 
             ->where('collection_ids', '!=', '[null]')// Not an empty array
            // ->where('product_ids', '!=', '[null]') // Not exactly [null]
            ->whereRaw('JSON_CONTAINS(collection_ids, ?)', [json_encode((string) $collection->id)])
            ->get();
    $app_content_section_rows = \DB::table('website_content_sections')
            ->whereNotNull('collection_ids') // Ensure it's not NULL
            ->where('collection_ids', '!=', '[]') // Not an empty array
            ->where('collection_ids', '!=', '[null]') // Not exactly [null]
          ->whereRaw('JSON_CONTAINS(collection_ids, ?)', [json_encode((string) $collection->id)])
            ->get();
     $slider_rows = \DB::table('website_sliders')
            ->whereNotNull('collection_ids') // Ensure it's not NULL
            ->where('collection_ids', '!=', '[]') // Not an empty array
            ->where('collection_ids', '!=', '[null]') // Not exactly [null]
          ->whereRaw('JSON_CONTAINS(collection_ids, ?)', [json_encode((string) $collection->id)])
            ->get();
     $banner_rows = \DB::table('website_banners')
            ->whereNotNull('collection_ids') // Ensure it's not NULL
            ->where('collection_ids', '!=', '[]') // Not an empty array
            ->where('collection_ids', '!=', '[null]') // Not exactly [null]
          ->whereRaw('JSON_CONTAINS(collection_ids, ?)', [json_encode((string) $collection->id)])
            ->get();
  
    $collectionId=$collection->id;
   
   // dd($web_content_section_rows->toArray());
    if($web_content_section_rows->count()>0){
        foreach($web_content_section_rows as $row){
            if($row->content_type=='Collections'){
                $collectionInfo=json_decode($row->collections1,true);
                  foreach ($collectionInfo as &$product) {
                    if ($product['id'] == $collectionId) {
                        $product = array_merge($product,[
                             "id"=> $collectionId,
                             "name"=>  $collection->name,
                            "slug"=>  $collection->slug,
                            "image"=>  $collection->image,
                          
                        ]);
                        break;
                    }
                }
             // dd($collectionInfo);
                  \DB::table('content_sections')
                    ->where('id', $row->id)
                    ->update(['collections1' => json_encode($collectionInfo)]);
            }
             }
    
    }
    if($app_content_section_rows->count()>0){
        foreach($app_content_section_rows as $row){
            if($row->content_type=='Collections'){
                $collectionInfo=json_decode($row->collections1,true);
                  foreach ($collectionInfo as &$product) {
                    if ($product['id'] == $collectionId) {
                        $product = array_merge($product,[
                             "id"=> $collectionId,
                             "name"=>  $collection->name,
                            "slug"=>  $collection->slug,
                            "image"=>  $collection->image,
                          
                        ]);
                        break;
                    }
                }
                  DB::table('website_content_sections')
                    ->where('id', $row->id)
                    ->update(['collections1' => json_encode($collectionInfo)]);
            }
             }
    
    }
    if($slider_rows->count()>0){
        foreach($slider_rows as $row){
           
                $collectionInfo=json_decode($row->json_column,true);
                foreach ($collectionInfo as &$collection) {
                    if ($collection['collection_id'] == $collectionId) {
                        $product = array_merge($product,[
                             "collection_id"=> $collectionId,
                             "collection_name"=>  $collection->name,
                             "slug"=>  $collection->slug,
                          
                          
                        ]);
                        break;
                    }
                }
                  DB::table('website_sliders')
                    ->where('id', $row->id)
                    ->update(['json_column' => json_encode($collectionInfo)]);
         
             }
    
    }
    if($banner_rows->count()>0){
        foreach($banner_rows as $row){
           
                $collectionInfo=json_decode($row->json_column,true);
                foreach ($collectionInfo as &$collection) {
                   
                    if ($collection['collection_id'] == $collectionId) {
                        $collection = array_merge($collection,[
                             "collection_id"=> $collectionId,
                             "collection_name"=>  $collection['collection_name'],
                             "slug"=>  $collection['slug'],
                          
                          
                        ]);
                        break;
                    }
                }
                  DB::table('website_banners')
                    ->where('id', $row->id)
                    ->update(['json_column' => json_encode($collectionInfo)]);
         
             }
    
    }
    
    
}
}
