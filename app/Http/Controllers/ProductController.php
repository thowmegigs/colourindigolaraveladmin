<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Batch;
use File;
use DB;
use \Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->dashboard_url=\URL::to('/');
        $this->index_url=domain_route('products.index');
        $this->module = 'Product';
        $this->view_folder = 'products';
        $this->storage_folder = $this->view_folder;
        $this->has_upload = 1;
        $this->is_multiple_upload = 0;
        $this->has_export = 1;
        $this->pagination_count = 100;
        $this->crud_title = 'Product';
        $this->show_crud_in_modal = 1;
        $this->has_popup = 0;
        $this->has_detail_view = 0;
        $this->has_side_column_input_group = 0;
       /***also set in commonVars the thumbnailsDimesions  */
        $this->dimensions = [
                'tiny'  => 100,
                'small' => 300,
                 'medium' => 600,
                // 'large' => 1080,
                
                ];
        $this->form_image_field_name = [
            [
                'field_name' => 'image',
                'single' => true,
                'has_thumbnail' => true,
            ],
            [
                'field_name' => 'size_chart_image',
                'single' => true,
                'has_thumbnail' => false,
            ],
            [
                'field_name' => 'product_images',
                'single' => false,
                'parent_table_field' => 'product_id',
                'table_name' => 'product_images',
                'image_model_name' => 'ProductImage',
                'has_thumbnail' => true,
            ],
        ];

        $this->model_relations = [
            [
                'name' => 'category',
                'type' => 'BelongsTo',
                'save_by_key' => '',
                'column_to_show_in_view' => 'name',
            ],
            [
                'name' => 'product_images',
                'type' => 'BelongsTo',
                'save_by_key' => '',
                'column_to_show_in_view' => 'name',

            ],
            [
                'name' => 'variants',
                'type' => 'BelongsTo',
                'save_by_key' => '',
                'column_to_show_in_view' => 'name',

            ],
         
            [
                'name' => 'vendor',
                'type' => 'BelongsTo',
                'save_by_key' => '',
                'column_to_show_in_view' => 'name',

            ],
         
          
        ];

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
                        'placeholder' => 'Enter name',
                        'name' => 'name',
                        'label' => 'Name',
                        'tag' => 'input',
                        'type' => 'text',
                        'default' => isset($model) ? $model->name : "",
                        'attr' => [],
                    ],
                    [
                        'placeholder' => 'Enter name',
                        'name' => 'image',
                        'label' => 'Image',
                        'tag' => 'input',
                        'type' => 'file',
                        'default' => '',
                        'attr' => [],
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
                    'attr' => $g['single'] ? [] : ['multiple' => 'multiple'],
                ];
                array_push($data[0]['inputs'], $y);
            }
        }
        return $data;
    }
    public function editInputsData($model)
    {

        $data = [
            [
                'label' => null,
                'inputs' => [
                    [
                        'placeholder' => 'Enter name',
                        'name' => 'name',
                        'label' => 'Name',
                        'tag' => 'input',
                        'type' => 'text',
                        'default' => isset($model) ? $model->name : "",
                        'attr' => [],
                    ],
                    [
                        'placeholder' => 'Enter name',
                        'name' => 'image',
                        'label' => 'Image',
                        'tag' => 'input',
                        'type' => 'file',
                        'default' => '',
                        'attr' => [],
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
       $is_vendor=auth()->guard('vendor')->check();
       $cats=Category::whereDoesntHave('children')->get();
      $leafCategories = [];
        foreach ($cats as $list) {
            $ar = (object) ['id' => $list->id, 'name' => $list->name];
            array_push($leafCategories, $ar);
        }
        $repeating_group_inputs = [

          
        ];
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
                'column' => 'uuid',
                'label' => 'ID ',
                'sortable' => 'Yes',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'vendor_id',
                'label' => 'Brand',
                'sortable' => 'Yes',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'sku',
                'label' => 'SKU',
                'sortable' => 'Yes',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'category_id',
                'label' => 'Category',
                'sortable' => 'Yes',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'price',
                'label' => 'Price',
                'sortable' => 'Yes',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'sale_price',
                'label' => 'Sale Price',
                'sortable' => 'Yes',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
           
            [
                'column' => 'image',
                'label' => 'Image',
                'sortable' => 'No',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'status',
                'label' => 'Status',
                'sortable' => 'No',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'visibility',
                'label' => 'Visibility',
                'sortable' => 'No',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
           
        ];
        $view_columns = [

            [
                'column' => 'name',
                'label' => 'Name',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],

            [
                'column' => 'sku',
                'label' => 'SKU',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'description',
                'label' => 'Description',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'price',
                'label' => 'Price',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'sale_price',
                'label' => 'Sale Price',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
          
            [
                'column' => 'status',
                'label' => 'Status',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'vendor_id',
                'label' => 'Brand Name',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'category_id',
                'label' => 'Category Id',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'image',
                'label' => 'Image',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'product_images',
                'label' => 'Gallery Images',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'size_chart_image',
                'label' => 'Size Chart Image',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'tags',
                'label' => 'Tags',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'short_description',
                'label' => 'Short Description',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
         
           
           
            [
                'column' => 'max_quantity_allowed',
                'label' => 'Max Quantity Allowed',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'quantity',
                'label' => 'Stock Quantity',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
          

            [
                'column' => 'meta_title',
                'label' => 'Meta Title',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'meta_description',
                'label' => 'Meta Description',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'meta_keywords',
                'label' => 'Meta Keywords',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            
            [
                'column' => 'created_at',
                'label' => 'Created At',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
        ];

        $searchable_fields = [
            [
                'name' => 'name',
                'label' => 'Name',
            ],
            [
                'name' => 'sku',
                'label' => 'SKU',
            ],
            [
                'name' => 'uuid',
                'label' => 'Id',
            ],
        ];
        $filterable_fields = [
            [
                'name' => 'category_id',
                'label' => 'Category ',
                'type' => 'select',
                'options' => $leafCategories,
            ],
           
            [
                'name' => 'sale_price',
                'label' => 'Sale Price',
                'type' => 'number',
            ],
            
          
            [
                'name' => 'status',
                'label' => 'Status',
                'type' => 'select',
                'options' => getListFromIndexArray(['Active','In-Active','Under Review']),
            ],
          
          
        ];
        if(current_role()!='Vendor'){
            array_push($filterable_fields,
            [
                'name' => 'vendor_id',
                'label' => 'Vendor ',
                'type' => 'select',
                'options' => getList('Vendor',['status'=>'Active']),
            ]);
        }

        $data['data'] = [ 

            'dashboard_url' => $this->dashboard_url,
            'index_url' => $this->index_url,
            'title' => 'All ' . $this->crud_title . 's',
            'module' => $this->module,
            'model_relations' => $this->model_relations,
            'searchable_fields' => $searchable_fields,
            'filterable_fields' => $filterable_fields,
            'storage_folder' => $this->storage_folder,
            'plural_lowercase' => 'products',
            'has_image' => $this->has_upload,
            'table_columns' => $table_columns,
            'view_columns' => $view_columns,

            'image_field_names' => $this->form_image_field_name,
            'storage_folder' => $this->storage_folder,
            'module_table_name' => 'products',
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
            $data['row'] = Product::with(['category:id,name','vendor:id,name','product_images:id,product_id,name','variants.images'])->findOrFail($id);
        } else {
            $data['row'] = Product::findOrFail($id);
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
     $view_prefix=current_role()=='Vendor'?'vendor':'admin';
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
        $vendor_id=auth()->id()?null:auth()->guard('vendor')->id();
      
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

            $db_query = Product::when(!empty($search_val), function ($query) use ($search_val, $search_by) {
                return $query->where($search_by, 'like', '%' . $search_val . '%');
            })->when(!empty($vendor_id), function ($query) use ($vendor_id) {
                    return $query->where('vendor_id', $vendor_id);
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

            $list = $db_query->whereNUll('deleted_at')->latest()->paginate($this->pagination_count);
            $data = array_merge($common_data, [

                'list' => $list,
                'sort_by' => $sort_by,
                'sort_type' => $sort_type,
                'bulk_update' => json_encode([
                    'status' => ['label' => 'Status', 'data' => getListFromIndexArray(['Active', 'In-Active','Under Review'])],
                    // 'visibility' => ['label' => 'Visibility', 'data' => getListFromIndexArray(['Public', 'Hidden'])],
                ]),

                /*
            Multi rows select karke koi column mein values update kara ho jaise status update,user assign
            'bulk_update' => json_encode([
            'status'=>['label'=>'Status','data'=>getListFromIndexArray(['Active','In-Active'])],
            'user_id'=>['label'=>'Assign User','data'=>getList('User')]

            ])
             */

            ]);
            return view($view_prefix.'.' . $this->view_folder . '.page', with($data));
        } else {
            if (!can('list_products')) {
                return redirect()->back()->withError('Dont have permission to list');
            }
            $query = null;
            if (count($this->model_relations) > 0) {
                $query = Product::with(array_column($this->model_relations, 'name'))->when(!empty($vendor_id), function ($query) use ($vendor_id) {
                    return $query->where('vendor_id', $vendor_id);
                });
            } else {
                $query = Product::when(!empty($vendor_id), function ($query) use ($vendor_id) {
                    return $query->where('vendor_id', $vendor_id);
                });
            }
            $query = $this->buildFilter($request, $query);
            $list = $query->orderByRaw('IF(updated_at IS NULL, created_at, updated_at) DESC')->whereNUll('deleted_at')->paginate($this->pagination_count);
            $view_data = array_merge($common_data, [

                'list' => $list,
                'bulk_update' => json_encode([
                    'status' => ['label' => 'Status', 'data' => getListFromIndexArray(['Active', 'In-Active','Under Review'])],
                    // 'visibility' => ['label' => 'Visibility', 'data' => getListFromIndexArray(['Public', 'Hidden'])],

                ]), 'tabs' => $tabs,

            ]);
            $index_view = count($tabs) > 0 ? 'index_tabs' : 'index';

            return view($view_prefix.'.' .$this->view_folder . '.' . $index_view, $view_data);
        }

    }

public function gt($ar, $i, $s, $selected_id = null)
{
    $i++;
    foreach ($ar as $k) {
        // Get children for this category
        $childs = \App\Models\Category::whereCategoryId($k['id'])->get()->toArray();

        // Disable if it has children
        $disabled = count($childs) > 0 ? 'disabled' : '';

        // Selected option
        $selected = $selected_id == $k['id'] ? 'selected' : '';

        // Check if this is a root category
        $isRoot = $k['category_id'] === null;

        // Indentation for children only
        $indent = $isRoot ? '' : str_repeat('&nbsp;&nbsp;', $i - 1);

        // Arrow only for children
        $arrow = $isRoot ? '' : '↳ ';

        // Style: Bold for root categories only
        $style = $isRoot ? 'style="font-weight:bold;"' : '';

        // Build the option
        $s .= '<option ' . $selected . ' ' . $disabled . ' value="' . $k['id'] . '" ' . $style . '>'
            . $indent . $arrow . $k['name']
            . '</option>';

        // Recurse into children
        if (count($childs) > 0) {
            $s = $this->gt($childs, $i, $s, $selected_id);
        }
    }

    return $s;
}


    public function create(Request $r)
    {
         $view_prefix=current_role()=='Vendor'?'vendor':'admin';
       
        $cats = \App\Models\Category::whereNull('category_id')->get()->toArray();
        $s = '';
        $i = 0;
        $category_options = $this->gt($cats, $i, $s);
       $view_data = array_merge($this->commonVars()['data'], [

          
            'category_options' => $category_options,
            'collections' => getList('Collection'),
            'attributes' => getList('Attribute'),
            
           
          

        ]);

        if ($r->ajax()) {

            if (!can('create_products')) {
                return createResponse(false, 'Dont have permission to create');
            }

            $html = view($view_prefix.'.' . $this->view_folder . '.modal.add', with($view_data))->render();
            return createResponse(true, $html);
        } else {

            if (!can('create_products')) {
                return redirect()->back()->withError('Dont have permission to create');
            }
            return view($view_prefix.'.'.$this->view_folder . '.add', with($view_data));
        }

    }

    public function store(Request $request)
    {
        if (!can('create_products')) {
            return createResponse(false, 'Dont have permission to create');
        }
        \DB::beginTransaction();

        try {
            $post = $request->all();
            $post['has_variant'] = isset($post['has_variant']) ? 'Yes' : 'No';
            $post['is_return_eligible'] = isset($post['is_returnable']) ? 'Yes' : 'No';
            $post['continue_selling'] = isset($post['continue_selling']) ? 'Yes' : 'No';
            $post['notify_out_of_stock'] = isset($post['notify_out_of_stock']) ? 'Yes' : 'No';
            $post['featured'] = isset($post['featured']) ? 'Yes' : 'No';
            $post['is_combo'] = isset($post['is_combo']) ? 'Yes' : 'No';
            $post['sgst'] = isset($post['sgst']) ? $post['sgst'] : 0.0;
            $post['cgst'] = isset($post['cgst']) ? $post['cgst'] : 0.0;
            $post['igst'] = isset($post['igst']) ? $post['igst'] : 0.0;
            $post['use_as_addon']=isset($post['use_as_addon'])?'Yes':'No';
            $post['show_qty_option_for_addon']=isset($post['show_qty_option_for_addon'])?'Yes':'No';
            $collections = isset($post['collections']) && !empty($post['collections']) ? $post['collections'] : [];

            $variants = [];
            $attributes = [];
            $variant_error = [];
            $category_based_features = [];
            $facet_attributes_values = [];
          //  dd($post);
            foreach ($post as $k => $v) {
                if (str_contains($k, 'variant_')) {
                    $k = str_replace('variant_', '', $k);
                  
                    $t = explode('__', $k);
                    //t[1]==REd-S
                    //$t[0]=price/quantiy
                      if(!empty($v))
                        $variants[$t[1]][$t[0]] =$v;
                    
                } 
                 elseif (str_contains($k, 'product_features__')) {

                    $p = explode('__', $k);
                    $category_based_features[] = ['name' => $p[1], 'value' => $post[$k]];

                }
                 elseif (str_contains($k, 'facet_attribute__')) {

                    $p = explode('__', $k);
                    
                    $c=explode('===',$p[1]);
                    $facet_attributes_values[] =['id'=>$c[1],'name'=>$c[0],'value'=>$post[$k]];

                }
            }
           
            if (!empty($variant_error)) {
                return createResponse(false, 'Price is no set for variants ' . implode(',', $variant_error));
            }
            if (!empty($category_based_features)) {
                $post['category_based_features'] = json_encode($category_based_features);
            }
            // dd($attributes);
            $attribte_json = [];
            $searchable_attribte_json = [];

            
            if ($post['has_variant']=='Yes' && count($post['attributes']) > 0 && count($variants) > 0) {
                $attributes=$post['attributes'];
                $attr = \App\Models\Attribute::whereIn('id', $attributes)->get();

                foreach ($attr as $t) {
                    $value=$post['value-' . $t->id];
                    array_push($attribte_json, ['id' => $t->id, 'name' => $t->name,
                        'value' => isset($post['value-' . $t->id]) ? $post['value-' . $t->id] : '']);
                        $searchable_attribte_json[$t->name]=is_array($value) ? $value : array_map('trim', explode(',', $value));
              
              
                    }
            }
            else{
                 $post['has_variant']=='No';
            }
             
            $post['attributes'] = !empty($attribte_json) ? json_encode($attribte_json) : null;
            $post['searchable_attributes'] = !empty($searchable_attribte_json) ? json_encode($searchable_attribte_json) : null;
         // dd($searchable_attribte_json);
            $post = formatPostForJsonColumn($post);
            //$post['package_dimension']=json_encode(['weight'=>$post['package_weight'],'length'=>$post['package_length'],'width'=>$post['package_width'],'height'=>$post['package_height']]);
            $post['package_dimension']=json_encode(['weight'=>0.4,'length'=>20,'width'=>18,'height'=>3]);

            if (!empty($post['price']) && !empty($post['sale_price']) && $post['price'] > 0 && $post['sale_price'] < $post['price']) {
                $post['discount'] = round((($post['price'] - $post['sale_price']) / $post['price']) * 100, 2);
                $post['discount_type'] = 'Percent';
            } else {
                $post['discount'] = 0;
                $post['discount_type'] = null;
            }
            $post['vendor_id']=auth()->id()?null:auth()->guard('vendor')->id();
           
            $post['status']='Under Review'; //by default product will be inactive when created by vendor or admin
          //  dd($post);
            $product = Product::create($post);
           
             if(!empty($facet_attributes_values)){
                $ar=[]; 
                foreach($facet_attributes_values as $k){
                    if($k['value'] && $k['name']!='Size' && $k['name']!='Color'){
                    $ar[]=['product_id'=>$product->id,
                        'attribute_id'=>$k['id'],'attribute_name'=>$k['name'],'value'=>$k['value']];
                    }
                }
               if(count($ar)>0){
                \DB::table('product_facet_attribute_values')
                ->insert($ar);}
                }
             if(!empty($searchable_attribte_json)){
                $ar=[]; 
                foreach($searchable_attribte_json as $k=>$v){
                    foreach($v as $it){
                          $ar[]=['product_id'=>$product->id,'attribute_name'=>$k,'value'=>$it];
                    }
                }
                \DB::table('product_facet_attribute_values')
                ->insert($ar);
             }
          
            $this->storage_folder = 'products\\' . $product->id;
            if (!empty($collections)) {
                foreach ($collections as $c) {
                    $row = \DB::table('collections')->where('id',$c)->first()->product_id;
                    $products = !empty($row) ? json_decode($row, true) : [];
                    array_push($products, ['id' => $product->id, 'name' => $post['name']]);
                    \DB::table('collections')->where('id',$c)->update(['product_id' => json_encode($products)]);
                }
                \DB::table('products')->where('id',$product->id)->update(['collections' => json_encode($collections)]);
            }
          
            $this->afterCreateProcess($request, $post, $product);
          //  dd($searchable_attribte_json);
            $this->saveVariantWithFiles($request, $variants, $product->id,$searchable_attribte_json);
           
            \DB::commit();

            return createResponse(true, 'Product submitted successfully! It is now under review and we’ll update you once approved.', $this->index_url);
        } catch (\Exception $ex) { \Sentry\captureException($ex);
            \DB::rollback();
                \DB::table('system_errors')->insert([
                                    'error'=>$ex->getMessage(),
                                    'which_function'=>'ProductController store function at line '.$ex->getLine()
               ]);
            return createResponse(false, $ex->getMessage() . '===' .$ex->getFile().'=== at line '. $ex->getLine());
        }
    }





    
    public function saveVariantWithFiles(Request $r, $variants, $product_id,$searchable_attributes)
    {
        $variant_main_image = [];
        $variant_gallery = [];
        $stored_main_files = [];
        $post = $r->all();
//    dd($searchable_attributes);

        foreach ($post as $k => $v) {
            if (str_contains($k, 'variant_image')) {
                $x = str_replace('variant_image__', '', $k);

                $variant_main_image[$x] = $v;
            } elseif (str_contains($k, 'variant_product_images')) {
                $x = str_replace('variant_product_images__', '', $k);

                $variant_gallery[$x] = $v;
            }

        }

        if (count($variant_main_image) > 0) {
            foreach ($variant_main_image as $k => $v) {
                $filerequest = $r->file('variant_image__' . $k);
                $folder = $this->storage_folder . '/variants';
                $filename = storeSingleFile($folder, $filerequest, true,$this->dimensions);
                // generateThumbnail($filerequest, $folder,$this->dimensions);
                $stored_main_file[$k] = $filename;
            }
        }

        $generated_variant_id = [];
    //  dd($searchable_attributes);
        foreach ($variants as $k => $v) {
            $file = isset($stored_main_file[$k]) ? $stored_main_file[$k] : null;
             $parts = explode('-', $k);
              $result = [];
             foreach ($parts as $part) {
                $part=str_replace('_',' ',$part);
                    foreach ($searchable_attributes as $key => $options) {
                        if (in_array($part, $options)) {
                            $result[$key] = $part;
                            break;
                        }
                    }
                }
           
       // dd($result);
                $insert_ar = [
                'product_id' => $product_id,
                'price' => $v['price'], 'name' => $k,
                'sale_price' => $v['sale_price'],
                'atributes_json'=>json_encode($result),
                'max_quantity_allowed' => isset($v['max_quantity_allowed'])?$v['max_quantity_allowed']:$post['max_quantity_allowed'],
                'quantity' => isset($v['quantity'])?$v['quantity']:$post['quantity'],
                'image' => $file,
            ];
          //  dd($insert_ar);
            $product_variant = \App\Models\ProductVariant::create($insert_ar);
            $generated_variant_id[$k] = $product_variant->id;
        }

        if (count($variant_gallery) > 0) {
            foreach ($variant_gallery as $k => $v) {
                $filerequest = $r->file('variant_product_images__' . $k);
                $folder = $this->storage_folder . '\\variants';
                storeMultipleFile($folder, $filerequest, 'ProductVariantImage', $generated_variant_id[$k], 'product_variant_id', true,$this->dimensions);
            }
        }

    }
    public function edit(Request $request, $id)
    {
         $view_prefix=current_role()=='Vendor'?'vendor':'admin';
        $model = Product::with(['variants', 'images', 'variants.images'])->findOrFail($id);
        $relatedCategoryWithAttributes=findCategoryWithAttributes($model->category_id);
          $facet_atributes = \App\Models\FacetAttribute::with('attribute_values.attribute_value_template')
            ->where('category_id',$relatedCategoryWithAttributes)->get();
        
        $product_existing_features=\DB::table('product_facet_attribute_values')
        ->where('product_id',$id)->get();
      
        $data = $this->editInputsData($model);
        $cats = \App\Models\Category::whereNull('category_id')->get()->toArray();
        $s = '';
        $i = 0;
     
        $category_options = $this->gt($cats, $i, $s, $model->category_id);
        $view_data = array_merge($this->commonVars($model)['data'], [
            'data' => $data, 'model' => $model,
            'category_options' => $category_options,
             'collections' => getList('Collection'),
            'attributes' => getList('Attribute'),
            'facet_atributes'=>$facet_atributes,
            'product_existing_features'=>$product_existing_features
          
           

        ]);

        if ($request->ajax()) {
            if (!can('edit_products')) {
                return createResponse(false, 'Dont have permission to edit');
            }

            $html = view($view_prefix.'.' . $this->view_folder . '.modal.edit', with($view_data))->render();
            return createResponse(true, $html);

        } else {
            if (!can('edit_products')) {
                return redirect()->back()->withError('Dont have permission to edit');
            }

            return view($view_prefix.'.' . $this->view_folder . '.edit_with_variant_image', with($view_data));

        }

    }

    public function show(Request $request, $id)
    {
        $view = 'view';
          $view_prefix=current_role()=='Vendor'?'vendor':'admin';
        $data = $this->common_view_data($id);
// dd($data['row']->toArray());
        if ($request->ajax()) {
            if (!can('view_products')) {
                return createResponse(false, 'Dont have permission to view');
            }
 
            $html = view('admin.' . $this->view_folder . '.view.' . $view, with($data))->render();
            return createResponse(true, $html);

        } else {
            if (!can('view_products')) {
                return redirect()->back()->withError('Dont have permission to view');
            }
 //dd($data['row']->toArray());
            return view($view_prefix.'.' . $this->view_folder . '.view.' . $view, with($data));

        }

    }

    public function update(Request $request, $id)
    {
        if (!can('edit_products')) {
            return createResponse(false, 'Dont have permission to update');
        }
        \DB::beginTransaction();

        try
        {
            $post = $request->all();
           
            $post['sgst'] = isset($post['sgst']) ? $post['sgst'] : 0.0;
            $post['cgst'] = isset($post['cgst']) ? $post['cgst'] : 0.0;
            $post['igst'] = isset($post['igst']) ? $post['igst'] : 0.0;
             $post['is_return_eligible'] = isset($post['is_returnable']) ? 'Yes' : 'No';
            $post['has_variant'] = isset($post['has_variant']) ? 'Yes' : 'No';
            $post['continue_selling'] = isset($post['continue_selling']) ? 'Yes' : 'No';
            $post['notify_out_of_stock'] = isset($post['notify_out_of_stock']) ? 'Yes' : 'No';
            $post['featured'] = isset($post['featured']) ? 'Yes' : 'No';
            $post['is_combo'] = isset($post['is_combo']) ? 'Yes' : 'No';
            $post['use_as_addon']=isset($post['use_as_addon'])?'Yes':'No';
            $post['show_qty_option_for_addon']=isset($post['show_qty_option_for_addon'])?'Yes':'No';
            $product = Product::with(['variants', 'variants.images'])->findOrFail($id);
            $collections = isset($post['collections']) && !empty($post['collections']) ? $post['collections'] : [];

            $variants = [];

            $attributes = [];
            $variant_error = [];
            $category_based_features=[];
            $facet_attributes_values=[];
            foreach ($post as $k => $v) {
                if (str_contains($k, 'variant_')) {
                    $k = str_replace('variant_', '', $k);
                    $t = explode('__', $k);
                    if(!empty($v))
                    $variants[$t[1]][$t[0]] =$v;
                } 
                elseif (str_contains($k, 'product_features__')) {

                    $p = explode('__', $k);
                    $category_based_features[] = ['name' => $p[1], 'value' => $post[$k]];

                }
                  elseif (str_contains($k, 'facet_attribute__')) {

                     $p = explode('__', $k);
                    $c=explode('===',$p[1]);
                     $facet_attributes_values[$c[1]] =['id'=>$c[1],'name'=>$c[0],'value'=>$post[$k]];


                }
            }
            if (!empty($variant_error)) {
                return createResponse(false, 'Price is no set for variants ' . implode(',', $variant_error));
            }
            $attribte_json = [];
           $searchable_attributes=[];
         
            if ($post['has_variant']=='Yes' && count($post['attributes']) > 0 && count($variants)) {
                $attributes=$post['attributes'];
                $attr = \App\Models\Attribute::whereIn('id', $attributes)->get();

                foreach ($attr as $t) {
                    array_push($attribte_json, ['id' => $t->id, 'name' => $t->name,
                        'value' => isset($post['value-' . $t->id]) ? $post['value-' . $t->id] : '']);
                }
            }
            else{
                 $post['has_variant']='No';
            }
           
            if (count($attribte_json)>0)
            // dd($attribte_json);
            {
                $post['attributes'] = !empty($attribte_json) && $attribte_json[0]? json_encode($attribte_json) : null;
                if($attribte_json[0]['name']=='Color'){
                     $searchable_attributes['Color']=$attribte_json[0]['value'];
                }
                else if($attribte_json[0]['name']=='Size'){
                     $searchable_attributes['Size']=explode(',',$attribte_json[0]['value']);
                }
                if(isset($attribte_json[1])){
                    if($attribte_json[1]['name']=='Color'){
                      $searchable_attributes['Color']=$attribte_json[1]['value'];
                    }
                    else if($attribte_json[1]['name']=='Size'){
                            $searchable_attributes['Size']=explode(',',$attribte_json[1]['value']);
                    }

                }
                $post['searchable_attributes']=json_encode($searchable_attributes);
            }
            else{
                $post['attributes']=null;
                 $post['has_variant']='No';
            }

           
            if (!empty($category_based_features)) {
                $post['category_based_features'] = json_encode($category_based_features);
            }
            $post = formatPostForJsonColumn($post);
            if (!empty($collections)) {
                foreach ($collections as $c) {
                    $row = \DB::table('collections')->where('id',$c)->first()->product_id;
                    $products = !empty($row) ? json_decode($row, true) : [];
                    if (!empty($products)) {
                        $product_ids = array_column($products, 'id');
                        if (!in_array($id, $product_ids)) {
                            array_push($products, ['id' => $product->id, 'name' => $post['name']]);
                        }

                    }
                    \DB::table('collections')->where('id',$c)->update(['product_id' => json_encode($products)]);
                }
                $post['collections'] = json_encode($collections);
            }
          
            // $post['package_dimension']=json_encode(['weight'=>$post['package_weight'],'length'=>$post['package_length'],'width'=>$post['package_width'],'height'=>$post['package_height']]);
             $post['package_dimension']=json_encode(['weight'=>0.4,'length'=>20,'width'=>18,'height'=>3]);

            if (!empty($post['price']) && !empty($post['sale_price']) && $post['price'] > 0 && $post['sale_price'] < $post['price']) {
                $post['discount'] = round((($post['price'] - $post['sale_price']) / $post['price']) * 100, 2);
                $post['discount_type'] = 'Percent';
            } else {
                $post['discount'] = 0;
                $post['discount_type'] = null;
            }
            $post['discount'] = (($post['price'] - $post['sale_price']) / $post['price']) * 100;
          // dd('ok');
             $post['status']='Under Review';
            $product->update($post);
            $this->updateInFrontendSectionsWhenProdChange($product);
        //    dd($facet_attributes_values);
       
             if(!empty($facet_attributes_values)){
                $ar=[];
                
             \DB::table('product_facet_attribute_values')->where('product_id',$product->id)->delete();
                foreach($facet_attributes_values as $k){
                          if($k['value'] && $k['name']!='Size' && $k['name']!='Color'){
                    $ar[]=['product_id'=>$product->id,
                        'attribute_id'=>$k['id'],'attribute_name'=>$k['name'],'value'=>$k['value']];
                    }
                       
                    
                }
                    if(count($ar)>0)
                { \DB::table('product_facet_attribute_values')
                    ->insert($ar);
                }
            }
            if(!empty($searchable_attribte_json)){
                 \DB::table('product_facet_attribute_values')
                 ->where('product_id',$product->id)->delete();
                $ar=[]; 
                foreach($searchable_attribte_json as $k=>$v){
                    foreach($v as $it){
                          $ar[]=['product_id'=>$product->id,'attribute_name'=>$k,'value'=>$it];
                    }
                }
                \DB::table('product_facet_attribute_values')
                ->insert($ar);
             }

            $this->storage_folder = 'products\\' . $product->id;

            $this->saveVariantWithFilesUpdate($request, $variants, $product,$searchable_attributes);

            $this->afterCreateProcess($request, $post, $product);
            // dd('ok');
            \DB::commit();
            return createResponse(true, $this->crud_title . ' updated successfully', $this->index_url);
        } catch (\Exception $ex) { \Sentry\captureException($ex);
            \DB::rollback();
                \DB::table('system_errors')->insert([
                                    'error'=>$ex->getMessage(),
                                    'which_function'=>'ProductController u[date function at line '.$ex->getLine()
               ]);
            return createResponse(false, $ex->getMessage() . '==' . $ex->getLine().'==='.$ex->getFile());
        }
    }
    public function saveVariantWithFilesUpdate(Request $r, $variants, $product,$searchable_attributes)
    {
       
        $variant_main_image = [];
        $variant_gallery = [];
        $stored_main_files = [];
        $post = $r->all();
        $variant_ids = [];
        $product_id = $product->id;
        $old_variants = $product->variants->toArray();
        $old_variants_name = !empty($old_variants) ? array_column($old_variants, 'name') : [];
        $cur_variants_name = array_keys($variants);
        $unmatched_ids = [];
        $is_changed_attribute = false;
       
        if (count($old_variants_name) > 0) {
            if (count($old_variants_name) == count($cur_variants_name)) {
                /**If count is same before and after edit then it means either all variants keys are completely same
                 *  or completely changed,so if completely changed delete all variant at one using product id  */
                $is_changed_attribute = false;
                foreach ($cur_variants_name as $k) {
                    if (!in_array($k, $old_variants_name)) {
                        $is_changed_attribute = true;
                    }

                }
                if ($is_changed_attribute) {
                    $var_main_images = []; /*collect all variant main images to delete ***/
                    $var_gallery_images = []; /*collect all variant gallery images to delete ***/
                    $var_images_id = []; /*collect all variant ids  to delete ***/
                    $variants_records = \App\Models\ProductVariant::with('images')->whereProductId($product_id)->get();
                    if ($variants_records) {
                        foreach ($variants_records as $rec) {
                            $var_main_images[] = $rec->image;
                            if ($rec->images) {

                                foreach ($rec->images as $img) {
                                    $var_images_id[] = $img->id;
                                    $var_gallery_images[] = $rec->name;
                                }
                            }
                        }
                    }
                    try { /***Delete main images  */
                        if (!empty($var_main_images)) {
                            foreach ($var_main_images as $img) {

                                $file_path = 'storage/products/' . $product_id . '/variants/' . $img;
                                if (\File::exists(public_path($file_path))) {
                                    \File::delete(public_path($file_path));
                                }
                                $extension = pathinfo($file_path, PATHINFO_EXTENSION);
                                foreach (array_keys($this->dimensions) as $size) {
                                    {
                                        $img = basename($img) . '_' . $size . '.' . $extension;
                                        $file_path = 'storage/products/' . $product_id . '/variants/thumbnail/' . $img;

                                        if (\File::exists(public_path($file_path))) {
                                            \File::delete(public_path($file_path));
                                        }
                                    }
                                }
                            }
                        }
                    } catch (\Exception $ex) { \Sentry\captureException($ex);
                        dd($ex->getMessage() . '===' . $ex->getLine());
                    }
                    try { /***Delete gallery images  */

                        if (!empty($var_gallery_images)) {
                            foreach ($var_gallery_images as $img) {

                                $file_path = 'storage/products/' . $product_id . '/variants/' . $img;
                                if (\File::exists(public_path($file_path))) {
                                    \File::delete(public_path($file_path));
                                }
                                $extension = pathinfo($file_path, PATHINFO_EXTENSION);
                                foreach (array_keys($this->dimensions) as $size) {
                                    $img = basename($img) . '_' . $size . '.' . $extension;
                                    $file_path = 'storage/products/' . $product_id . '/variants/thumbnail/' . $img;
                                    if (\File::exists(public_path($file_path))) {
                                        \File::delete(public_path($file_path));
                                    }
                                }

                            }
                        }
                    } catch (\Exception $ex) { \Sentry\captureException($ex);
                        dd($ex->getMessage() . '===' . $ex->getLine());
                    }
                    if (!empty($var_images_id)) {
                        try { /***Delete variant image record usign its id  */

                            \App\Models\ProductVariantImage::whereIn('product_variant_id', $var_images_id)->delete();
                        } catch (\Exception $ex) { \Sentry\captureException($ex);
                            dd($ex->getMessage() . '===' . $ex->getLine());
                        }
                    }
                    try { /***Delete all variant using product id   */
                        \App\Models\ProductVariant::whereProductId($product_id)->delete();
                    } catch (\Exception $ex) { \Sentry\captureException($ex);
                        dd($ex->getMessage() . '===' . $ex->getLine());
                    }
                }
            } else {

                $is_changed_attribute = true;
                foreach ($old_variants as $o) {
                    if (!in_array($o['name'], $cur_variants_name)) {
                        $unmatched_ids[] = $o;
                    }

                }
                $main_images = [];
                $var_images = [];
                foreach ($unmatched_ids as $var_row) {
                    if ($var_row['image']) {
                        $main_images[] = $var_row['image'];
                    }

                    if ($var_row['images']) {
                        foreach ($var_row['images'] as $img) {$var_images[] = $img;}
                    }
                }
                try {
                    if (!empty($main_images)) {
                        foreach ($main_images as $img) {

                            $file_path = 'storage/products/' . $product_id . '/variants/' . $img;
                            if (\File::exists(public_path($file_path))) {
                                \File::delete(public_path($file_path));
                            }
                        }
                    }
                } catch (\Exception $ex) { \Sentry\captureException($ex);
                    dd($ex->getMessage() . '===' . $ex->getLine());
                }
                try {
                    if (!empty($var_images)) {
                       // dd($var_images);
                        foreach ($var_images as $img) {

                            $file_path = 'storage/products/' . $product_id . '/variants/' . $img['name'];
                            if (\File::exists(public_path($file_path))) {
                                \File::delete(public_path($file_path));
                            }
                        }  
                    }
                } catch (\Exception $ex) { \Sentry\captureException($ex);
                    dd($ex->getMessage() . '===' . $ex->getLine());
                }
                try {
                    \App\Models\ProductVariant::whereIn('id', array_column($unmatched_ids, 'id'))->delete();
                    \App\Models\ProductVariantImage::whereIn('product_variant_id', array_column($unmatched_ids, 'id'))->delete();
                } catch (\Exception $ex) { \Sentry\captureException($ex);
                    dd($ex->getMessage() . '===' . $ex->getLine());
                }
            }
        } else {
            $is_changed_attribute = true;
        }

        //dd($old_variants_name);
        foreach ($post as $k => $v) {
            if (str_contains($k, 'variant_image')) {
                $x = str_replace('variant_image__', '', $k);

                $variant_main_image[$x] = $v;
            } elseif (str_contains($k, 'variant_id')) {
                $k = str_replace('variant_id__', '', $k);

                $variant_ids[$k] = $v;
            } elseif (str_contains($k, 'variant_product_images')) {
                $x = str_replace('variant_product_images__', '', $k);

                $variant_gallery[$x] = $v;
            }

        }

        if (count($variant_main_image) > 0) {
            foreach ($variant_main_image as $k => $v) {
                $filerequest = $r->file('variant_image__' . $k);
                $folder = $this->storage_folder . '/variants';
                $filename = storeSingleFile($folder, $filerequest, true,$this->dimensions);
                // generateThumbnail($filerequest, $folder,$this->dimensions);
                $stored_main_file[$k] = $filename;
            }
        }
        // echo "<pre>";print_r($variants);

        $generated_variant_id = [];
        $attributes_ids=$post['attributes'];
       // dd($attributes_ids);
        $attributes_name_chosen=[
            $attributes_ids[0]=="1"?"Color":"Size",
            isset($attributes_ids[1]) && $attributes_ids[1]=="1"?"Color":"Size"
        ];
        foreach ($variants as $k => $v) {
            //  $split_name=explode("-",$k);
            //     $attributes_json=[];
            //     $attributes_json[ $attributes_name_chosen[0]]=$split_name[0];
            //     if(isset($split_name[1]) && count( $attributes_name_chosen)>1){
            //          $attributes_json[ $attributes_name_chosen[1]]=$split_name[1];
            //     }
            $parts = explode('-', $k);
              $result = [];
             foreach ($parts as $part) {
                $part=str_replace('_',' ',$part);
                    foreach ($searchable_attributes as $key => $options) {
                        if (in_array($part, $options)) {
                            $result[$key] = $part;
                            break;
                        }
                    }
                }
               // dd($attributes_json);
            if (!in_array($k, $old_variants_name) || (!in_array($k, $old_variants_name) && $is_changed_attribute)) { /***agar old exist nai karta ya attribute change hue hai it means insert */
                $file = isset($stored_main_file[$k]) ? $stored_main_file[$k] : null;
               
                $insert_ar = [
                    'product_id' => $product_id,
                    'price' => $v['price'], 'name' => $k,
                    'sale_price' => $v['sale_price'],
                    'image' => $file,
                    'atributes_json'=>json_encode($result),
                    'max_quantity_allowed' => isset($v['max_quantity_allowed'])?$v['max_quantity_allowed']:$r->max_quantity_allowed,
                    'quantity' => isset($v['quantity'])?$v['quantity']:$r->quantity,
                ];
                $product_variant = \App\Models\ProductVariant::create($insert_ar);
                $generated_variant_id[$k] = $product_variant->id;
            } else {
                if (!empty($variant_ids)) {
                    $file = isset($stored_main_file[$k]) ? $stored_main_file[$k] : null;
                    $update_ar = [
                        'product_id' => $product_id,
                        'price' => $v['price'], 'name' => $k,
                        'sale_price' => $v['sale_price'],
                          'atributes_json'=>json_encode($result),
                        'max_quantity_allowed' => isset($v['max_quantity_allowed'])?$v['max_quantity_allowed']:$r->max_quantity_allowed,
                    'quantity' => isset($v['quantity'])?$v['quantity']:$r->quantity,

                    ];
                    if ($file) {
                        $update_ar['image'] = $file;
                    }

                    $variant_id = $variant_ids[$k];
                    if (\App\Models\ProductVariant::whereId($variant_id)->exists()) {
                        \App\Models\ProductVariant::whereId($variant_id)->update($update_ar);
                    }

                }
            }
        }

        if (count($variant_gallery) > 0) {
            foreach ($variant_gallery as $k => $v) {
                if (isset($generated_variant_id[$k])) {
                    $filerequest = $r->file('variant_product_images__' . $k);
                    $folder = $this->storage_folder . '\\variants';
                    storeMultipleFile($folder, $filerequest, 'ProductVariantImage', $generated_variant_id[$k], 'product_variant_id', true,$this->dimensions);
                } elseif (!empty($old_variants_name) && in_array($k, $old_variants_name)) {
                    $filerequest = $r->file('variant_product_images__' . $k);
                    $folder = $this->storage_folder . '\\variants';
                    storeMultipleFile($folder, $filerequest, 'ProductVariantImage', $variant_ids[$k], 'product_variant_id', true,$this->dimensions);
                }
            }
        }

    }
    public function destroy($id)
    {
        if (!can('delete_products')) {
            return createResponse(false, 'Dont have permission to delete');
        }

        try
        {

            $row = Product::with(['images','variants', 'variants.images'])->where('id', $id)->first();
            if ($row) {
             //   dd($row->toArray());
                $product_id = $row->id;
                $variants = $row->variants;
                $var_ids = $variants ? array_column($variants->toArray(), 'id') : [];

                $file_path = 'storage/products/' . $product_id;

               
                    if (\File::exists(public_path($file_path))) {

                       \File::deleteDirectory(public_path($file_path));
                    }
                    foreach ($variants as $t) {

                        \App\Models\ProductVariantImage::where('product_variant_id',$t->id)->delete();
                        

                    }
                
                \App\Models\ProductVariant::whereProductId($id)->delete();
                \App\Models\ProductImage::whereProductId($id)->delete();
               
                \DB::table('order_items')->where('product_id',$id)->delete();
                \DB::table('product_facet_attribute_values')->where('product_id',$id)->delete();
                \DB::table('carts')->where('product_id',$id)->delete();
       
                /** from collection table  */
                    $result = \DB::select(
                            "SELECT * FROM collections WHERE JSON_SEARCH(JSON_EXTRACT(product_id, '$[*].id'), 'one', ?) IS NOT NULL",
                            [$id]
                        );
                           
                     if (!empty($result)) {
                            $colInstance = new \App\Models\Collection;
                            $update_ar = [];
                            foreach ($result as $t) {
                            // echo "<pre>";print_r($t);die;
                                $products = $t->product_id != null ? json_decode($t->product_id, true) : [];
                                if (!empty($products)) {
                                    $index = $this->getIndex($products, $id);
                                    // dd($index);
                                    if ($index > -1) {
                                        unset($products[$index]);

                                        $new_ar = array_merge([], $products);
                                    //   dd($new_ar);
                                        if(!empty($new_ar))
                                        array_push($update_ar, ['id' => $t->id, 'product_id' => json_encode($new_ar)]);
                                    }
                                }
                            }
                            if(!empty($update_ar))
                            Batch::update($colInstance, $update_ar, 'id');
                          }
                     /** from video table  */
                    $results = \DB::table('videos')
                        ->whereRaw('JSON_CONTAINS(product_ids, JSON_ARRAY(?))', [$id])
                        ->get();
                
                      if (count($results)>0) {
                    
                          $colInstance = new \App\Models\Video;
                          \DB::table('video_files')->where('product_id',$id)->delete();
                            $update_ar = [];
                            $ids_to_delete_from_video_files=[];
                            foreach ($results as $t) {
                            // echo "<pre>";print_r($t);die;
                                $products = $t->json_column != null ? json_decode($t->json_column, true) : [];
                                if (!empty($products)) {
                                    $index = $this->getIndex2($products, $id);
                                    // dd($index);
                                    if ($index > -1) {
                                        unset($products[$index]);

                                        $new_ar = array_merge([], $products);
                                    //   dd($new_ar);
                                        if(!empty($new_ar))
                                        array_push($update_ar, ['id' => $t->id, 'json_column' => json_encode($new_ar)]);
                                    }
                                }
                            }
                            if(!empty($update_ar))
                            Batch::update($colInstance, $update_ar, 'id');
                        }
                
            //  echo "<pre>";print_r($result);die;
               
            }
             
          Product::destroy($id);

            if ($this->has_upload) {
                $this->deleteFile($id);
            }

            return createResponse(true, $this->module . ' Deleted successfully');
        } catch (\Exception $ex) { 
            \Sentry\captureException($ex);

            return createResponse(false, 'Failed to  Delete Properly' . $ex->getMessage().'==',$ex->getLine());
        }

    }
    public function getIndex($details, $id)
    {
        $index = -1;

        foreach ($details as $k => $v) {

            if ($v['id'] == $id) {
                $index = $k;
                break;

            }

        }

        return $index;
    }
    public function getIndex2($details, $id)
    {
        $index = -1;

        foreach ($details as $k => $v) {

            if ($v['product_id'] == $id) {
                $index = $k;
                break;

            }

        }

        return $index;
    }
    public function deleteFile($id)
    {

        return $this->deleteFileBase($id, $this->storage_folder);

    }

    public function exportProduct(Request $request, $type)
    {
        if (!can('export_products')) {
            return redirect()->back()->withError('Not allowed to export');
        }
        $meta_info = $this->commonVars()['data'];
        return $this->exportModel('Product', 'products', $type, $meta_info);

    }
   
    public function load_toggle(Request $r)
    {
        $value = trim($r->val);
        $rowid = $r->has('row_id') ? $r->row_id : null;
        $row = null;
        if ($rowid) {
            $model = app("App\\Models\\" . $this->module);
            $row = $model::where('id', $rowid)->first();
        }
        $index_of_val = 0;
        $is_value_present = false;
        $i = 0;
        foreach ($this->toggable_group as $val) {

            if ($val['onval'] == $value) {

                $is_value_present = true;
                $index_of_val = $i;
                break;
            }
            $i++;
        }
        if ($is_value_present) {
            if ($row) {
                $this->toggable_group = [];

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
        // dd($this->storage_folder);

        return $this->getImageListBase($id, $table, $parent_field_name, $this->storage_folder);
    }
    public function exportProductExcelTempalte()
    {
        return Excel::download(new \App\Exports\ProductTemplateExport, 'product_import_template.xlsx');

       
    }
    public function exportCategory()
    {
        return Excel::download(new \App\Exports\CategoryExport, 'category.xlsx');

       
    }
    public function exportProductVariantExcelTempalte()
    {
        return Excel::download(new \App\Exports\ProductVariantTemplateExport, 'product_variant_import_template.xlsx');

       
    }
    public function exportProductBasic()
    {
        return Excel::download(new \App\Exports\ProductBasicExport, 'products_basic.xlsx');

       
    }
    public function generateAccordian(Request $r)
    {
        $product_id = $r->product_id;
        $combinations = $r->combinations;
        //dd($combinations);
        $rec = \App\Models\ProductVariant::with('images')->whereProductId($product_id)->get();
        $variant_names = $rec ? array_column($rec->toArray(), 'name') : [];
        $ar = [];
        foreach ($combinations as $name) {
            $found = false;
            foreach ($rec as $r) {
                if ($r->name == $name) {
                    $found = $r;
                }

            }
            if ($found) {
                $ar[] = ['name' => $name, 'product_id' => $product_id, 'row' => $found];
            } else {
                $ar[] = ['name' => $name, 'product_id' => $product_id, 'row' => null];
            }

            //    /  dd($ar);

        }
        $str = view('admin.products.variant_accordian_for_edit', ['ar' => $ar])->render();
        return createResponse(true, $str);
    }
    public function importProduct(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);
        try{
        Excel::import(new \App\Imports\ProductImport, request()->file('file'));
        return createResponse(true, 'Imported successfully');
        }
        catch(\Exception $ex){
            return createResponse(false,$ex->getMessage());
        }
    }
    public function importProductVariant(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);
        try{
        Excel::import(new \App\Imports\ProductVariantImport, request()->file('file'));
        return createResponse(true, 'Imported successfully');
        }
        catch(\Exception $ex){
            return createResponse(false,$ex->getMessage());
        }
    }
    public function importDiscounts(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls',
    ]);

    try {
        Excel::import(new \App\Imports\ProductDiscountImport, $request->file('file'));
        return createResponse(true,'Discounts updated successfully.');
    } catch (\Exception $e) { \Sentry\captureException($e);
        return   createResponse(false,$e->getMessage());
    }
}
protected function updateInFrontendSectionsWhenProdChange($product){
    
    $app_content_section_rows = \DB::table('content_sections')
            ->whereNotNull('product_ids')
                    ->where('product_ids', '!=', '[]')
                    ->whereRaw('JSON_CONTAINS(product_ids, ?)', [json_encode((string) $product->id)])
                    ->get();
       
                $web_content_section_rows = \DB::table('website_content_sections')
                    ->whereNotNull('product_ids')
                    ->where('product_ids', '!=', '[]')
                    ->whereRaw('JSON_CONTAINS(product_ids, ?)', [json_encode((string) $product->id)])
                    ->get();
      
    $collection_rows = \DB::table('collections')
            ->whereNotNull('product_id') // Ensure it's not NULL
            ->where('product_id', '!=', '[]') // Not an empty array
            ->where('product_id', '!=', '[null]') // Not exactly [null]
            ->whereRaw("JSON_SEARCH(product_id, 'one', ?, NULL, '$[*].id') IS NOT NULL", [$product->id])
            ->get();
    $videos_rows = \DB::table('videos')
            ->whereNotNull('product_ids')
                    ->where('product_ids', '!=', '[]')
                    ->whereRaw('JSON_CONTAINS(product_ids, ?)', [json_encode((string) $product->id)])
                    ->get();
      
            $productId=$product->id;
    if($app_content_section_rows->count()>0){
        foreach($app_content_section_rows as $row){
            if($row->content_type!='Collections'){
              $productInfo = json_decode($row->products1, true);
              foreach ($productInfo as &$product) {
                    if ($product['id'] == $productId) {
                       
                        $product = array_merge($product,[
                             "id"=> $productId,
                             "name"=>  $product->name,
                            "slug"=>  $product->slug,
                            "image"=>  $product->image,
                            "price"=>  $product->price,
                            "discount"=>  $product->discount,
                            "quantity"=>  $product->quantity,
                            "sale_price"=>  $product->sale_price,
                            "discount_type"=>  $product->discount_type
                        ]);
                        break;
                    }
                }
                  DB::table('content_sections')
                    ->where('id', $row->id)
                    ->update(['products1' => json_encode($productInfo)]);
         
            }
            else{
                  $productInfo = json_decode($row->collection_products_when_single_collection_set, true);
              foreach ($productInfo as &$product1) {
                    if ($product1['id'] == $productId) {
                      
                        $product1 = array_merge($product1,[
                             "id"=> $productId,
                             "name"=>  $product->name,
                            "slug"=>  $product->slug,
                            "image"=>  $product->image,
                            "price"=>  $product->price,
                            "discount"=>  $product->discount,
                            "quantity"=>  $product->quantity,
                            "sale_price"=>  $product->sale_price,
                            "discount_type"=>  $product->discount_type
                        ]);
                       // dd($product1);
                        break;
                    }
                }
                  DB::table('content_sections')
                    ->where('id', $row->id)
                    ->update(['collection_products_when_single_collection_set' => json_encode($productInfo)]);
            }

        }
    
    }
    if($web_content_section_rows->count()>0){
       
        foreach($web_content_section_rows as $row){
          
              $productInfo = json_decode($row->products1, true);
             
              foreach ($productInfo as &$product1) {
                    if ($product1['id'] == $productId) {
                      
                        $product1 = array_merge($product1,[
                             "id"=> $productId,
                             "name"=>  $product->name,
                            "slug"=>  $product->slug,
                            "image"=>  $product->image,
                            "price"=>  $product->price,
                            "discount"=>  $product->discount,
                            "quantity"=>  $product->quantity,
                            "sale_price"=>  $product->sale_price,
                            "discount_type"=>  $product->discount_type
                        ]);
                       // dd($product1);
                        break;
                    }
                }
              
                  DB::table('website_content_sections')
                    ->where('id', $row->id)
                    ->update(['products1' => json_encode($productInfo)]);
         
            
          

        }
    
    }
    if($collection_rows->count()>0){
        foreach($collection_rows as $row){
         
              $productInfo = json_decode($row->product_id, true);
              foreach ($productInfo as &$product1) {
                    if ($product1['id'] == $productId) {
                      
                        $product1 = array_merge($product1,[
                             "id"=> $productId,
                             "name"=>  $product->name,
                            "slug"=>  $product->slug,
                            "image"=>  $product->image,
                            "price"=>  $product->price,
                            "discount"=>  $product->discount,
                            "quantity"=>  $product->quantity,
                            "sale_price"=>  $product->sale_price,
                            "discount_type"=>  $product->discount_type
                        ]);
                       // dd($product1);
                        break;
                    }
                }
                  DB::table('collections')
                    ->where('id', $row->id)
                    ->update(['product_id' => json_encode($productInfo)]);
         
            
          

        }
    
    }
  
    if($videos_rows->count()>0){
        foreach($videos_rows as $row){
         
              $productInfo = json_decode($row->json_column, true);
              foreach ($productInfo as &$product1) {
                    if ($product1['product_id'] == $productId) {
                        $product1 = array_merge($product1,[
                             "product_id"=> $productId,
                             "name"=>  $product->name,
                            "slug"=>  $product->slug,
                           
                        ]);
                        break;
                    }
                }
                  DB::table('videos')
                    ->where('id', $row->id)
                    ->update(['json_column' => json_encode($productInfo)]);
         
            
          

        }
    
    }
    
}
}
