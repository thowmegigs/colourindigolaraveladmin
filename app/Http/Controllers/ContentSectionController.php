<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContentSectionRequest;
use App\Models\ContentSection;
use \Illuminate\Http\Request;

class ContentSectionController extends Controller
{
    public function __construct()
    {
         $this->dashboard_url=\URL::to('/');
        $this->index_url=domain_route('content_sections.index');
        $this->module = 'ContentSection';
        $this->view_folder = 'content_sections';
        $this->storage_folder = $this->view_folder;
        $this->has_upload = 1;
        $this->is_multiple_upload = 0;
        $this->has_export = 0;
        $this->pagination_count = 100;
        $this->crud_title = 'Website Content Section';
        $this->show_crud_in_modal = 1;
        $this->has_popup = 1;
        $this->has_detail_view = 0;
        $this->has_side_column_input_group = 0;
        $this->form_image_field_name = [
            [
                'field_name' => 'header_image',
                'single' => true,
            ],
        ];

        $this->model_relations = [
            [
                'name' => 'website_banner',
                'type' => 'BelongsTo',
                'save_by_key' => '',
                'column_to_show_in_view' => 'name',
            ],
            [
                'name' => 'categories',
                'type' => 'BelongsToMany',
                'column_to_show_in_view' => 'name',
            ],
            [
                'name' => 'products',
                'type' => 'BelongsToMany',
                'column_to_show_in_view' => 'name',
            ],
            [
                'name' => 'collections',
                'type' => 'BelongsToMany',
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
        $today=now();
        $coupon_rows=\DB::table('new_coupons')->where('start_date', '<=', $today)
        ->where('end_date', '>=', $today)->get(['id', 'code']);

        $coupons = [];
        foreach ($coupon_rows as $list) {
            $ar = (object) ['id' => $list->id, 'name' => $list->code];
            array_push($coupons, $ar);
        }
        $data = [
            [
                'label' => null,
                'inputs' => [
                   
                    [
                        'name' => 'product_ids',
                        'label' => 'Products',
                        'tag' => 'select',
                        'type' => 'select',
                        'default' => '',
                        'attr' => [],
                        'custom_key_for_option' => 'name',
                        'options' => [],
                        'custom_id_for_option' => 'id',
                        'multiple' => true,
                    ],
                    [
                        'name' => 'collection_ids',
                        'label' => 'Collection',
                        'tag' => 'select',
                        'type' => 'select',
                        'default' => '',
                        'attr' => [],
                        'custom_key_for_option' => 'name',
                        'options' => getList('Collection'),
                        'custom_id_for_option' => 'id',
                        'multiple' => true, 'col' => '6',
                    ],
                    [
                        'name' => 'coupon_ids',
                        'label' => 'Coupons',
                        'tag' => 'select',
                        'type' => 'select',
                        'default' => '',
                        'attr' => [],
                        'custom_key_for_option' => 'name',
                        'options' =>$coupons,
                        'custom_id_for_option' => 'id',
                        'multiple' => true, 'col' => '6',
                    ],
                    [
                        'placeholder' => 'Enter no of items ',
                        'name' => 'no_of_items',
                        'label' => 'No Of Items',
                        'tag' => 'input',
                        'type' => 'text',
                        'default' => isset($model) ? $model->no_of_items : "",
                        'attr' => [], 'col' => '6',
                    ],
                   
                    [
                        'name' => 'display',
                        'label' => 'Grid Display Orientation',
                        'tag' => 'input',
                        'type' => 'radio',
                        'default' => isset($model) && isset($model->display) ? $model->display : 'Horizontal',
                        'attr' => [],
                        'value' => [
                            (object) [
                                'label' => 'Horizontal',
                                'value' => 'Horizontal',
                            ],
                            (object) [
                                'label' => 'Vertical',
                                'value' => 'Vertical',
                            ],
                        ],
                        'has_toggle_div' => [],
                        'multiple' => false,
                        'inline' => true, 'col' => 6,
                    ],
                  
                    [
                        'name' => 'website_banner_id',
                        'label' => 'Select banner',
                        'tag' => 'select',
                        'type' => 'select',
                        'default' => '',
                        'attr' => ['class' => 'no-select2'],
                        'custom_key_for_option' => 'name',
                        'options' => getList('WebsiteBanner'),
                        'custom_id_for_option' => 'id',
                        'multiple' => false, 'col' => 6,
                    ],
                    [
                        'name' => 'vidoe_id',
                        'label' => 'Select Video Banner',
                        'tag' => 'select',
                        'type' => 'select',
                        'default' => '',
                        'attr' => ['class' => 'no-select2'],
                        'custom_key_for_option' => 'name',
                        'options' => getList('Video'),
                        'custom_id_for_option' => 'id',
                        'multiple' => false, 'col' => 6,
                    ],
                    [
                        'name' => 'website_slider_id',
                        'label' => 'Select Slider',
                        'tag' => 'select',
                        'type' => 'select',
                        'default' => '',
                        'attr' => ['class' => 'no-select2'],
                        'custom_key_for_option' => 'name',
                        'options' => getList('WebsiteSlider'),
                        'custom_id_for_option' => 'id',
                        'multiple' => false, 'col' => 6,
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
                    'default' =>'',
                    'attr' => $g['single'] ? [] : ['multiple' => 'multiple'],
                ];
                array_push($data[0]['inputs'], $y);
            }
        }
        return $data;
    }
    public function editInputsData($model)
    {
        $today=now();
        $coupon_rows=\DB::table('new_coupons')->where('start_date', '<=', $today)
        ->where('end_date', '>=', $today)->get(['id', 'code']);

        $coupons = [];
        foreach ($coupon_rows as $list) {
            $ar = (object) ['id' => $list->id, 'name' => $list->code];
            array_push($coupons, $ar);
        }
        $product_ids= $model->product_ids;
        $collection_ids = $model->collection_ids?json_decode(json_decode($collection_ids,true),true):[];
        $coupons_ids = $model->coupon_ids;
// dd(json_decode(json_decode($collection_ids,true),true));
        $data = [
            [
                'label' => null,
                'inputs' => [
                  
                    [
                        'name' => 'product_ids',
                        'label' => 'Products',
                        'tag' => 'select',
                        'type' => 'select',
                        'default' => !empty($product_ids) ? json_decode($product_ids,true) : [],

                         'attr' => [],
                        'custom_key_for_option' => 'name',
                        'options' => getList('Product'),
                        'custom_id_for_option' => 'id',
                        'multiple' => true,
                    ],
                    [
                        'name' => 'collection_ids',
                        'label' => 'Collection',
                        'tag' => 'select',
                        'type' => 'select',
                        'default' => !empty($collection_ids) ? json_decode($collection_ids,true) : [],
                      
                        'attr' => [],
                        'custom_key_for_option' => 'name',
                        'options' => getList('Collection'),
                        'custom_id_for_option' => 'id',
                        'multiple' => true, 'col' => '6',
                    ],
                   
                    [
                        'name' => 'coupon_ids',
                        'label' => 'Coupons',
                        'tag' => 'select',
                        'type' => 'select',
                        'default' => !empty($coupons_ids) ? jdon_decode($coupons_ids,true) : [],
                        'attr' => [],
                        'custom_key_for_option' => 'name',
                        'options' => getList('Coupon'),
                        'custom_id_for_option' => 'id',
                        'multiple' => true, 'col' => '6',
                    ],
                   
                    [
                        'placeholder' => 'Enter no of items ',
                        'name' => 'no_of_items',
                        'label' => 'No Of Items',
                        'tag' => 'input',
                        'type' => 'text',
                        'default' => isset($model) ? $model->no_of_items : "",
                        'attr' => [], 'col' => '6',
                    ],
                  
                    [
                        'name' => 'display',
                        'label' => 'Grid Display Orientation',
                        'tag' => 'input',
                        'type' => 'radio',
                        'default' => isset($model) && isset($model->display) ? $model->display : 'Horizontal',
                        'attr' => [],
                        'value' => [
                            (object) [
                                'label' => 'Horizontal',
                                'value' => 'Horizontal',
                            ],
                            (object) [
                                'label' => 'Vertical',
                                'value' => 'Vertical',
                            ],
                        ],
                        'has_toggle_div' => [],
                        'multiple' => false,
                        'inline' => true, 'col' => 6,
                    ],
                 
                    [
                        'name' => 'website_banner_id',
                        'label' => 'Select banner',
                        'tag' => 'select',
                        'type' => 'select',
                        'default' =>$model->website_banner_id,
                        'attr' => ['class' => 'no-select2'],
                        'custom_key_for_option' => 'name',
                        'options' => getList('WebsiteBanner'),
                        'custom_id_for_option' => 'id',
                        'multiple' => false, 'col' => 6,
                    ],
                    [
                        'name' => 'website_slider_id',
                        'label' => 'Select Slider',
                        'tag' => 'select',
                        'type' => 'select',
                        'default' =>$model->website_slider_id,
                        'attr' => ['class' => 'no-select2'],
                        'custom_key_for_option' => 'name',
                        'options' => getList('WebsiteSlider'),
                        'custom_id_for_option' => 'id',
                        'multiple' => false, 'col' => 6,
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

        $repeating_group_inputs = [];
        $toggable_group = [];

        $table_columns = [
            [
                'column' => 'section_title',
                'label' => 'Section Title',
                'sortable' => 'Yes',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'sequence',
                'label' => 'Sequence',
                'sortable' => 'Yes',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
           
        ];
        $view_columns = [
            [
                'column' => 'categories',
                'label' => 'Categories',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'products',
                'label' => 'Products',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'collections',
                'label' => 'Collections',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'section_title',
                'label' => 'Section Title',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
          
            [
                'column' => 'header_image',
                'label' => 'Header Image',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'display',
                'label' => 'Display Orientation',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'banner',
                'label' => 'Banner/Slider Attached',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
          
        ];

        $searchable_fields = [
            [
                'name' => 'section_title',
                'label' => 'Section Title',
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
            'plural_lowercase' => 'content_sections',
            'has_image' => $this->has_upload,
            'table_columns' => $table_columns,
            'view_columns' => $view_columns,

            'image_field_names' => $this->form_image_field_name,
            'storage_folder' => $this->storage_folder,
            'module_table_name' => 'content_sections',
            'has_export' => $this->has_export,
            'crud_title' => $this->crud_title,
            'show_crud_in_modal' => $this->show_crud_in_modal,
            'has_popup' => $this->has_popup,
            'has_side_column_input_group' => $this->has_side_column_input_group,
            'has_detail_view' => $this->has_detail_view,
            'repeating_group_inputs' => $repeating_group_inputs,
            'toggable_group' => $toggable_group,
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
            $data['row'] = ContentSection::with(array_column($this->model_relations, 'name'))->findOrFail($id);
        } else {
            $data['row'] = ContentSection::findOrFail($id);
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

            $db_query = ContentSection::when(!empty($search_val), function ($query) use ($search_val, $search_by) {
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

            $list = $db_query->orderBy('sequence','ASC')->paginate($this->pagination_count);
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
            if (!can('list_content_sections')) {
                return redirect()->back()->withError('Dont have permission to list');
            }
            $query = null;
            if (count($this->model_relations) > 0) {
                $query = ContentSection::with(array_column($this->model_relations, 'name'));
            } else {
                $query = ContentSection::query();
            }
            $query = $this->buildFilter($request, $query);
            $list = $query->orderBy('sequence','ASC')->paginate($this->pagination_count);
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
        
        $data = $this->createInputsData();
        $cats = \App\Models\Category::whereNull('category_id')->get()->toArray();
        $s = '';
        $i = 0;
        $category_options = gt($cats, $i, $s);
        $data = $this->createInputsData();
        $view_data = array_merge($this->commonVars()['data'], [
            'data' => $data, 'category_options' => $category_options,

        ]);
        if ($r->ajax()) {

            if (!can('create_content_sections')) {
                return createResponse(false, 'Dont have permission to create');
            }

            $html = view('admin.' . $this->view_folder . '.modal.add', with($view_data))->render();
            return createResponse(true, $html);
        } else {

            if (!can('create_content_sections')) {
                return redirect()->back()->withError('Dont have permission to create');
            }
            return view('admin.' . $this->view_folder . '.add', with($view_data));
        }

    }
    public function store(ContentSectionRequest $request)
    {
        if (!can('create_content_sections')) {
            return createResponse(false, 'Dont have permission to create');
        }
        \DB::beginTransaction();

        try {
            $post = $request->all();
            if (empty($post['category_ids'][0])) {
                unset($post['category_ids']);
            }
            if (empty($post['product_ids'][0])) {
                unset($post['product_ids']);
            }
            if (empty($post['collection_ids'][0])) {
                unset($post['collection_ids']);
            }
            if (empty($post['coupon_ids'][0])) {
                unset($post['coupon_ids']);
            }
            
           
            $post = formatPostForJsonColumn($post);
            /* Saving name alongwith id in json column takki join se na retrive karna pade
            copy this code from contrller file and paste and edit here
            $post=$this->processJsonColumnToAddNameOrAddtionalData($post);
             */
           $last_sequence=\DB::table('content_sections')->max('sequence');
           $post['sequence']=$last_sequence+1;
            $content_section = ContentSection::create($post);
            $this->afterCreateProcess($request, $post, $content_section);
            if (isset($post['category_ids'])) {
                $catids=json_decode($post['category_ids'],true);
                $cats=\DB::table('categories')->whereIn('id',$catids)->get();
                $cat_infos=[];
                foreach($cats as $c){
                 $cat_infos[]=[
                     "id"=>$c->id,'name'=>$c->name,'slug'=>$c->slug,'image'=>$c->image
                 ];
 
                }
                if(count($cat_infos)>0){
                 $content_section->categories1=json_encode($cat_infos);
                 $content_section->category_ids=$post['category_ids'];

                 $content_section->save();
                }
             }
             if (isset($post['product_ids'])) {
                    $prodids=json_decode($post['product_ids'],true);
                    $prods=\DB::table('products')->whereIn('id',$prodids)->get();
                    $prod_infos=[];
                    foreach($prods as $c){
                    $prod_infos[]=[
                        "id"=>$c->id,'name'=>$c->name,'image'=>$c->image,
                        'price'=>$c->price,'sale_price'=>$c->sale_price,
                        'quantity'=>$c->quantity,
                        'discount'=>$c->discount,'slug'=>$c->slug,
                        'discount_type'=>$c->discount_type
                        
                    ];
    
                    }
                    if(count($prod_infos)>0){
                    $content_section->products1=json_encode($prod_infos);
                    $content_section->product_ids=$post['product_ids'];
                    $content_section->save();
                    }
                }
             if (isset($post['collection_ids'])) {
                 $prodids=json_decode($post['collection_ids'],true);
                 $prods=\DB::table('collections')->whereIn('id',$prodids)->get();
                 $prod_infos=[];
                 $prod_ids=[];
                 foreach($prods as $c){
                 $prod_ids[]=$c->id;
                  $prod_infos[]=[
                      "id"=>$c->id,'name'=>$c->name,'image'=>$c->image,'slug'=>$c->slug
                    ];
                }
                 if(count($prod_infos)>0){
                 $content_section->collections1=json_encode($prod_infos);
                 $content_section->product_ids=json_encode($prod_ids);
                 $content_section->collection_ids=$post['collection_ids'];
                 if(count($prodids)==1){
                
                    $content_section->collection_products_when_single_collection_set=$prods[0]->product_id;
                    $content_section->product_ids=$prods[0]->product_id;
                  

                 }
                 $content_section->save();
                 }
             }
             if (isset($post['coupon_ids'])) {
                $prodids=json_decode($post['coupon_ids'],true);
                $prods=\DB::table('new_coupons')->whereIn('id',$prodids)->get();
                $prod_infos=[];
                foreach($prods as $c){
                 $prod_infos[]=(array)$c;
 
                }
                if(count($prod_infos)>0){
                $content_section->coupons1=json_encode($prod_infos);
                $content_section->coupon_ids=$post['coupon_ids'];
                $content_section->save();
                }
            }
            \DB::commit();
            return createResponse(true, $this->crud_title . ' created successfully', $this->index_url);
        } catch (\Exception $ex) { \Sentry\captureException($ex);
            \DB::rollback();
             \DB::table('system_errors')->insert([
                  'error'=>$ex->getMessage(),
                  'which_function'=>'ContentSectionController store function at line '.$ex->getLine()
               ]);
            return createResponse(false, $ex->getMessage());
        }
    }
    public function edit(Request $request, $id)
    {

        $model = ContentSection::findOrFail($id);

      
        $data = $this->editInputsData($model);

        $cats = \App\Models\Category::whereNull('category_id')->get()->toArray();
        $s = '';
        $i = 0;
        $categories = !empty($model->category_ids) ? array_column(json_decode($model->categories, true), 'id') : null;
        $category_options =$categories?gt_multiple($cats, $i, $s, $categories):gt($cats, $i, $s);
        $view_data = array_merge($this->commonVars($model)['data'], [
            'data' => $data, 'model' => $model, 'category_options' => $category_options,

        ]);


        if ($request->ajax()) {
            if (!can('edit_content_sections')) {
                return createResponse(false, 'Dont have permission to edit');
            }

            $html = view('admin.' . $this->view_folder . '.modal.edit', with($view_data))->render();
            return createResponse(true, $html);

        } else {
            if (!can('edit_content_sections')) {
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
            if (!can('view_content_sections')) {
                return createResponse(false, 'Dont have permission to view');
            }

            $html = view('admin.' . $this->view_folder . '.view.' . $view, with($data))->render();
            return createResponse(true, $html);

        } else {
            if (!can('view_content_sections')) {
                return redirect()->back()->withError('Dont have permission to view');
            }

            return view('admin.' . $this->view_folder . '.view.' . $view, with($data));

        }

    }

    public function update(ContentSectionRequest $request, $id)
    {
        if (!can('edit_content_sections')) {
            return createResponse(false, 'Dont have permission to update');
        }
        \DB::beginTransaction();

        try
        {
            $post = $request->all();

            $content_section = ContentSection::findOrFail($id);

            $post = formatPostForJsonColumn($post);
            /* Saving name alongwith id in json column takki join se na retrive karna pade
            copy this code from contrller file and paste and edit here
            $post=$this->processJsonColumnToAddNameOrAddtionalData($post);
             */
            // $old_image = $content_section->header_image;
            // if ($request->hasFile('header_image')) {
            //     if ($old_image) {
            //         $file_path = 'storage/' . $this->storage_folder . '/' . $old_image;
            //         if (\File::exists(public_path($file_path))) {
            //             \File::delete(public_path($file_path));
            //         }

            //     }
            // }
           
            $content_section->update($post);
            $this->afterCreateProcess($request, $post, $content_section);
            if (isset($post['categories'])) {
                $catids=json_decode($post['categories'],true);
              
                $cats=\DB::table('categories')->whereIn('id',$catids)->get();
                $cat_infos=[];
                foreach($cats as $c){
                 $cat_infos[]=[
                     "id"=>$c->id,'name'=>$c->name,'image'=>$c->image
                 ];
 
                }
                if(count($cat_infos)>0){
                 $content_section->categories1=json_encode($cat_infos);
                  $content_section->category_ids=$post['categories'];
                 $content_section->save();
                }
             }
            if (isset($post['products'])) {
                 $prodids=json_decode($post['products'],true);
                 $prods=\DB::table('products')->whereIn('id',$prodids)->get();
                 $prod_infos=[];
                 foreach($prods as $c){
                  $prod_infos[]=[
                      "id"=>$c->id,'name'=>$c->name,'image'=>$c->image,
                      'price'=>$c->price,'sale_price'=>$c->sale_price,
                      'quantity'=>$c->quantity
                     
                  ];
  
                 }
                 if(count($prod_infos)>0){
                 $content_section->products1=json_encode($prod_infos);
                 $content_section->product_ids=$post['product_ids'];
                 $content_section->save();
                 }
             }
             if (isset($post['collection_ids'])) {
                 $prodids=json_decode($post['collection_ids'],true);
                 $prods=\DB::table('collections')->whereIn('id',$prodids)->get();
                 $prod_infos=[];
                 $prod_ids=[];
                 foreach($prods as $c){
                 $prod_ids[]=$c->id;
                  $prod_infos[]=[
                      "id"=>$c->id,'name'=>$c->name,'image'=>$c->image,'slug'=>$c->slug
                    ];
                }
                 if(count($prod_infos)>0){
                 $content_section->collections1=json_encode($prod_infos);
                 $content_section->product_ids=json_encode($prod_ids);
                 $content_section->collection_ids=$post['collection_ids'];
                 if(count($prodids)==1){
                
                    $content_section->collection_products_when_single_collection_set=$prods[0]->product_id;
                    $content_section->product_ids=$prods[0]->product_id;
                  

                 }
                 $content_section->save();
                 }
             }
             if (isset($post['coupon_ids'])) {
                $prodids=json_decode($post['coupon_ids'],true);
                $prods=\DB::table('new_coupons')->whereIn('id',$prodids)->get();
                $prod_infos=[];
                foreach($prods as $c){
                 $prod_infos[]=(array)$c;
 
                }
                if(count($prod_infos)>0){
                $content_section->coupons1=json_encode($prod_infos);
                $content_section->coupon_ids=$post['coupon_ids'];
                $content_section->save();
                }
            }
            \DB::commit();
            return createResponse(true, $this->crud_title . ' updated successfully', $this->index_url);
        } catch (\Exception $ex) { \Sentry\captureException($ex);
            \DB::rollback();
               \DB::table('system_errors')->insert([
                                    'error'=>$ex->getMessage(),
                                    'which_function'=>'ContentSectionController update function at line '.$ex->getLine()
               ]);
            return createResponse(false, $ex->getMessage());
        }
    }

    public function destroy($id)
    {
        if (!can('delete_content_sections')) {
            return createResponse(false, 'Dont have permission to delete');
        }
       
        try
        {
            if (ContentSection::where('id', $id)->exists()) {
                ContentSection::destroy($id);
            }

            if ($this->has_upload) {
                $this->deleteFile($id);
            }
           
            return createResponse(true, $this->module . ' Deleted successfully');
        } catch (\Exception $ex) { \Sentry\captureException($ex);
         
            return createResponse(false, 'Failed to  Delete Properly');
        }

    }
    public function deleteFile($id)
    {

        return $this->deleteFileBase($id, $this->storage_folder);

    }

    public function exportContentSection(Request $request, $type)
    {
        if (!can('export_content_sections')) {
            return redirect()->back()->withError('Not allowed to export');
        }
        $meta_info = $this->commonVars()['data'];
        return $this->exportModel('ContentSection', 'content_sections', $type, $meta_info);

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

        return $this->getImageListBase($id, $table, $parent_field_name, $this->storage_folder);
    }
    public function updateSequence(Request $request)
{
    // Validate the incoming order array
    $request->validate([
        'order' => 'required|array',
        'table' => 'required',
        'order.*.id' => 'required|integer',
        'order.*.sequence' => 'required|integer'

    ]);

    // Update each product's sequence number
    foreach ($request->order as $item) {
        \DB::table($request->table)->where('id', $item['id'])->update([
            'sequence' => $item['sequence'],  // Update sequence or any other field
        ]);
    }

    // Return a success response
    return response()->json(['message' => 'Sequence updated successfully']);
}
}
