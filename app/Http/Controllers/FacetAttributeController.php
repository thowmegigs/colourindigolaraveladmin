<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FacetAttribute;
use Batch;


class FacetAttributeController extends Controller
{
   public function __construct()
    {
         $this->dashboard_url=\URL::to('/');
        $this->index_url=domain_route('facet_attributes.index');
        $this->module = 'FacetAttribute';
        $this->view_folder = 'facet_attributes';
        $this->storage_folder = 'facet_attributes';
        $this->has_upload = 0;
        $this->is_multiple_upload = 0;
        $this->has_export = 0;
        $this->pagination_count = 100;
        $this->crud_title = 'Facet Attribute';
        $this->show_crud_in_modal = 1;
        $this->has_popup = 1;
        $this->has_detail_view = 0;
        $this->has_side_column_input_group = 0;
        $this->form_image_field_name = [
           
        ];

        $this->model_relations = [

            [
                'name' => 'category',
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

        $lists = \App\Models\FacetAttribute::get(['id', 'name']);

        $facet_attributes = [];
        foreach ($lists as $list) {
            $ar = (object) ['id' => $list['id'], 'name' => $list['name']];
            array_push($facet_attributes, $ar);
        }

        $data = [
            [
                'label' => null,
                'inputs' => [
                    [
                        'placeholder' => 'Enter name',
                        'name' => 'name',
                        'label' => 'Name*',
                        'tag' => 'input',
                        'type' => 'text',
                        'default' => isset($model) ? $model->name : "",
                        'attr' => [],
                    ],
                    [
                        'placeholder' => 'Enter category_id',
                        'name' => 'category_id',
                        'label' => 'Parent FacetAttribute',
                        'tag' => 'select',
                        'type' => 'select',
                        'custom_key_for_option' => 'name',
                        'custom_id_for_option' => 'id',
                        'default' => "",
                        'options' => $facet_attributes,
                        'attr' => ['class' => 'select2'],
                        'multiple' => false,
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

        $lists = \App\Models\FacetAttribute::get(['id', 'name']);

        $facet_attributes = [];
        foreach ($lists as $list) {
            $ar = (object) ['id' => $list['id'], 'name' => $list['name']];
            array_push($facet_attributes, $ar);
        }

        $data = [
            [
                'label' => null,
                'inputs' => [
                    [
                        'placeholder' => 'Enter name',
                        'name' => 'name',
                        'label' => 'Name*',
                        'tag' => 'input',
                        'type' => 'text',
                        'default' => isset($model) ? $model->name : "",
                        'attr' => [],
                    ],
                    [
                        'placeholder' => 'Enter category_id',
                        'name' => 'category_id',
                        'label' => 'Category ',
                        'tag' => 'select',
                        'type' => 'select',
                        'custom_key_for_option' => 'name',
                        'custom_id_for_option' => 'id',
                        'default' => $model->category_id,
                        'options' => getList('Category'),
                        'attr' => [],
                        'multiple' => false,
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
                    'default' => $g['single'] ? $this->storage_folder . '/' . $model->{$g['field_name']} : json_encode($this->getImageList($id, $g['table_name'], $g['parent_table_field'], $this->storage_folder)),
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
        // $repeating_group_inputs = [

        //     [
        //         'colname' => 'product_common_features',
        //         'label' => 'Feature',
        //         'inputs' => [

        //             [
        //                 'placeholder' => 'Enter name',
        //                 'name' => 'product_common_features__json__name[]',
        //                 'label' => 'Feature Name',
        //                 'tag' => 'input',
        //                 'type' => 'text',
        //                 'default' => '',
        //                 'attr' => [],
        //             ],

        //         ],
        //         'index_with_modal' => 0,
        //         'modalInputBoxIdWhoseValueToSetInSelect' => '',
        //         'hide' => false,
        //         'disable_buttons' => false,
        //     ],
        // ];
        $toggable_group = [];

        $table_columns = [
           
            [
                'column' => 'category_id',
                'label' => 'Category',
                'sortable' => 'Yes',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'name',
                'label' => 'Name',
                'sortable' => 'Yes',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
           
         
        ];
        $view_columns = [
              [
                'column' => 'category_id',
                'label' => 'Categry',
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

          
            
        ];

        $searchable_fields = [
           
        ];
        $filterable_fields = [
            [
                'name' => 'category_id',
                'label' => 'FacetAttribute ',
                'type' => 'select',
                'options' => getList('FacetAttribute '),
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
            'plural_lowercase' => 'facet_attributes',
            'has_image' => $this->has_upload,
            'table_columns' => $table_columns,
            'view_columns' => $view_columns,

            'image_field_names' => $this->form_image_field_name,
            'storage_folder' => $this->storage_folder,
            'module_table_name' => 'facet_attributes',
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
            $data['row'] = FacetAttribute::with(array_column($this->model_relations, 'name'))->findOrFail($id);
        } else {
            $data['row'] = FacetAttribute::findOrFail($id);
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

            $db_query = FacetAttribute::when(!empty($search_val), function ($query) use ($search_val, $search_by) {
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
                'bulk_update' =>'',

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
            if (!can('list_facet_attributes')) {
                return redirect()->back()->withError('Dont have permission to list');
            }
            $query = null;
            if (count($this->model_relations) > 0) {
                $query = FacetAttribute::with(array_column($this->model_relations, 'name'));
            } else {
                $query = FacetAttribute::query();
            }
            $query = $this->buildFilter($request, $query);
            $list = $query->latest()->paginate($this->pagination_count);
            $view_data = array_merge($common_data, [

                'list' => $list,
                'bulk_update' => '',
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
            'categories'=>getList('Category')

        ]);

        if ($r->ajax()) {

            if (!can('create_facet_attributes')) {
                return createResponse(false, 'Dont have permission to create');
            }
          
            $html = view('admin.' . $this->view_folder . '.modal.add', with($view_data))->render();
            return createResponse(true, $html);
        } else {

            if (!can('create_facet_attributes')) {
                return redirect()->back()->withError('Dont have permission to create');
            }
            return view('admin.' . $this->view_folder . '.add', with($view_data));
        }

    }
   
    public function store(Request $request)
    {
        if (!can('create_facet_attributes')) {
            return createResponse(false, 'Dont have permission to create');
        }
       
      $request->validate([
        'category_id' => 'required|exists:categories,id',
        'attributes' => 'required|array',
        'attributes.*' => 'required|string|max:255',
    ]);
        try {
            $post=$request->all();
            $t=[];
        foreach ($post['attributes'] as $attrName) {
            if($attrName=='Size' || $attrName=='size' || $attrName=='Color'|| $attrName=='color'){
          return createResponse(false,"Color and Size attribute name not allowed ");
            }
               $t[]=[
                    'category_id' => $post['category_id'],
                    'name' => $attrName,
                ];
            }
           
           \DB::table('facet_attributes')->insert($t);
            return createResponse(true, $this->crud_title . ' created successfully', $this->index_url);
        } catch (\Exception $ex) { \Sentry\captureException($ex);
         

            return createResponse(false, $ex->getMessage());
        }
    }

    public function edit(Request $request, $id)
    {

        $model = FacetAttribute::findOrFail($id);

        $data = $this->editInputsData($model);

        $view_data = array_merge($this->commonVars($model)['data'], [
            'data' => $data, 'model' => $model,
            'categories'=>getList('Category')

        ]);

        if ($request->ajax()) {
            if (!can('edit_facet_attributes')) {
                return createResponse(false, 'Dont have permission to edit');
            }

            $html = view('admin.' . $this->view_folder . '.modal.edit', with($view_data))->render();
            return createResponse(true, $html);

        } else {
            if (!can('edit_facet_attributes')) {
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
            if (!can('view_facet_attributes')) {
                return createResponse(false, 'Dont have permission to view');
            }

            $html = view('admin.' . $this->view_folder . '.view.' . $view, with($data))->render();
            return createResponse(true, $html);

        } else {
            if (!can('view_facet_attributes')) {
                return redirect()->back()->withError('Dont have permission to view');
            }

            return view('admin.' . $this->view_folder . '.view.' . $view, with($data));

        }

    }

    public function update(Request $request, $id)
    {
        if (!can('edit_facet_attributes')) {
            return createResponse(false, 'Dont have permission to update');
        }
        \DB::beginTransaction();

        try {
          $post=$request->all();
          \DB::table('facet_attributes')->where('id',$id)
          ->update(['category_id'=>$post['category_id'],'name'=>$post['name']]);
           
            \DB::commit();
            return createResponse(true, $this->crud_title . ' updated successfully', $this->index_url);
        } catch (\Exception $ex) { \Sentry\captureException($ex);
            \DB::rollback();
            return createResponse(false, $ex->getMessage());
        }
    }

    public function destroy($id)
    {
        if (!can('delete_facet_attributes')) {
            return createResponse(false, 'Dont have permission to delete');
        }

        try
        {
           
            \DB::table('facet_attributes')->where('id',$id)->delete();
            \DB::table('facet_attribute_values')->where('facet_Attribute_id',$id)->delete();
             
            return createResponse(true, $this->module . ' Deleted successfully');
        } catch (\Exception $ex) { \Sentry\captureException($ex);

            return createResponse(false, 'Failed to  Delete Properly');
        }

    }
    public function deleteFile($id)
    {

        return $this->deleteFileBase($id, $this->storage_folder);

    }

   

   

   
  
}
