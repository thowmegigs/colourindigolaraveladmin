<?php

namespace App\Http\Controllers;

use App\Http\Requests\VideoRequest;
use App\Models\Video;
use App\Models\VideoFile;
use File;
use DB;
use Str;
use Storage;
use \Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class VideoController extends Controller
{
    public function __construct()
    {
         $this->dashboard_url=\URL::to('/');
        $this->index_url=domain_route('videos.index');
        $this->module='Video';
        $this->view_folder='videos';
        $this->storage_folder=$this->view_folder;
        $this->has_upload=1;
        $this->is_multiple_upload=0;
        $this->has_export=0;
        $this->pagination_count=100;
        $this->crud_title='Video';
         $this->show_crud_in_modal=1;
         $this->has_popup=1;
        $this->has_detail_view =0;
        $this->has_side_column_input_group =0;
        $this->form_image_field_name =[
    
];

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
                'placeholder' => 'Enter name',
                'name' => 'name',
                'label' => 'Name',
                'tag' => 'input',
                'type' => 'text',
                'default' => isset($model) ? $model->name : "",
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
                'label' => 'Name',
                'tag' => 'input',
                'type' => 'text',
                'default' => isset($model) ? $model->name : "",
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
                    'default' =>ode($this->getImageList($model->id, $g['table_name'], $g['parent_table_field'],$this->storage_folder)),
                    'attr' => $g['single'] ? [] : ['multiple' => 'multiple'],
                ];
                array_push($data[0]['inputs'], $y);
            }
        }
        return $data;
    }
    public function commonVars($model = null)
    {
        $collections=getList('Collection');

        $repeating_group_inputs=[
         
        ];
        $toggable_group=[];
      
        $table_columns=[
    [
        'column' => 'name',
        'label' => 'Name',
        'sortable' => 'Yes',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ],
   
    [
        'column' => 'created_at',
        'label' => 'CreatedAt',
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
        'column' => 'name',
        'label' => 'Name',
        'show_json_button_click' => false,
        'by_json_key' => 'id',
        'inline_images' => true
    ]
];

        $searchable_fields=[
    [
        'name' => 'name',
        'label' => 'Name'
    ]
];
        $filterable_fields=[
    [
        'name' => 'created_at',
        'label' => 'Created At',
        'type' => 'date'
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
            'plural_lowercase' =>'videos',
            'has_image' => $this->has_upload,
            'table_columns'=>$table_columns,
            'view_columns'=>$view_columns,
           
            'image_field_names' => $this->form_image_field_name,
            'storage_folder' => $this->storage_folder,
             'module_table_name'=>'videos',
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
            $data['row'] = Video::with(array_column($this->model_relations, 'name'))->findOrFail($id);
        } else {
            $data['row'] = Video::findOrFail($id);
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

            $db_query =  Video::when(!empty($search_val), function ($query) use ($search_val, $search_by) {
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
            if (!can('list_videos')) {
                return redirect()->back()->withError('Dont have permission to list');
            }
            $query = null;
            if (count($this->model_relations) > 0) {
                $query = Video::with(array_column($this->model_relations, 'name'));
            } else {
                $query =  Video::query();
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
       
        $products=getList('Product',['status'=>'Active']);
      
          $view_data = array_merge($this->commonVars()['data'], [
                    'data' => $data,
                    'products'=>$products

                ]);
           
         if($r->ajax()){
           
             if (!can('create_videos')) {
                return createResponse(false,'Dont have permission to create');
                }
              
                $html=view('admin.' . $this->view_folder . '.modal.add', with($view_data))->render();
                return  createResponse(true, $html);
         }
       else{
         
         if (!can('create_videos')) {
                return redirect()->back()->withError('Dont have permission to create');
            }
         return view('admin.' . $this->view_folder . '.add', with($view_data));
       }
    
    }
    public function store(VideoRequest $request)
    {
         if (!can('create_videos')) {
        return createResponse(false,'Dont have permission to create');
        }
        \DB::beginTransaction();

        try {
            $post = $request->all();
            $images = $request->file('images');
            $uploaded = [];
            $uploaded1 = [];

            $prodIds = $request->input('product_id', []);

            foreach ($images as $index => $image) {
                $extension = $image->getClientOriginalExtension();
                $filename = 'video_' . ($prodIds[$index] ?? 'unknown') . '_' . time() . '_' . \Str::random(6) . '.' . $extension;
            
                // Store with custom name
                $path = $image->storeAs('videos', $filename, 'public');
                $product=\App\Models\Product::whereId($prodIds[$index])->first();
                $uploaded[] = [
                    'video' => $filename,
                    'product_id' => $prodIds[$index] ?? null,
                    'name' => $product?->name ?? null,
                    'slug' =>$product?->slug ?? null,
                ];
                $uploaded1[] = [
                    'video' => $filename,
                    'product_id' => $prodIds[$index] ?? null,
                ];
            }
                 
           $post['json_column']=json_encode($uploaded);
           $post['product_ids']=json_encode($prodIds);
            
             $websitebanner = Video::create($post);
             $uploaded1=array_map(function($b) use($websitebanner){
                 $b['video_id']= $websitebanner->id;
                 return $b;
             },$uploaded1);
             \DB::table('video_files')->insert($uploaded1);
               $this->afterCreateProcess($request,$post,$websitebanner);
             \DB::commit();
            return createResponse(true, $this->crud_title . ' created successfully', $this->index_url);
        } catch (\Exception $ex) { \Sentry\captureException($ex);
            \DB::rollback();

            return createResponse(false, $ex->getMessage());
        }
    }
    public function edit(Request $request,$id)
    {
        

        $model = Video::with('videos')->findOrFail($id);

        $data = $this->editInputsData($model);
      
         $products=getList('Product',['status'=>'Active']);
      
       
        $view_data = array_merge($this->commonVars($model)['data'], [
            'data' => $data, 'model' => $model,
          
            'products'=>$products

        ]);
       
       

         if ($request->ajax()) {
            if (!can('edit_videos')) {
                return createResponse(false, 'Dont have permission to edit');
            }

            $html = view('admin.' . $this->view_folder . '.modal.edit', with($view_data))->render();
            return createResponse(true, $html);

        } else {
            if (!can('edit_videos')) {
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
             if (!can('view_videos')) {
                return createResponse(false,'Dont have permission to view');
         }
           
            $html = view('admin.' . $this->view_folder . '.view.' . $view, with($data))->render();
            return createResponse(true, $html);

        } else {
            if (!can('view_videos')) {
                return redirect()->back()->withError('Dont have permission to view');
         }
           

            return view('admin.' . $this->view_folder . '.view.' . $view, with($data));

        }

      

    }
    
    public function update(VideoRequest $request, $id)
    {
         if (!can('edit_videos')) {
        return createResponse(false,'Dont have permission to update');
        }
        \DB::beginTransaction();

   try {
    $banner = Video::findOrFail($id);

    $existingIds    =  array_filter($request->input('existing_ids', []));
    $collectionIds  = $request->input('product_id', []);
    $uploadedImages =$request->file('images', []);

    $savedIds = [];
    $jsonData = [];

    // Determine max count based on whichever array is longer
    $maxCount = count($collectionIds);
       

    for ($index = 0; $index < $maxCount; $index++) {
        $existingId   = $existingIds[$index] ?? null;
        $collectionId = $collectionIds[$index] ?? null;
        $imageFile    = $uploadedImages[$index] ?? null;

        // CASE 1: Update existing row
        if ($existingId) {
            $row = VideoFile::find($existingId);
            if (!$row) continue;
             $filename=$row->video;
            // Replace image if a new one is uploaded
            if ($imageFile) {
                if ($row->video && Storage::disk('public')->exists('videos/' . $row->video)) {
                    Storage::disk('public')->delete('videos/' . $row->video);
                }

                $filename = 'video_' . time() . '_' . Str::random(6) . '.' . $imageFile->getClientOriginalExtension();
                $imageFile->storeAs('videos', $filename, 'public');
                $row->video = $filename;
            }

            // Always update collection
            $row->product_id = $collectionId ?: null;
            $row->save();

            $savedIds[] = $row->id;

            $collection = $collectionId ? DB::table('products')->find($collectionId) : null;
            $jsonData[] = [
                'video' => $filename,
                'product_id' => $collectionId,
                'name' => $collection?->name ?? '',
                'slug' => $collection?->slug ?? '',
            ];
          
        }

        // CASE 2: New row (only if image uploaded)
        elseif ($imageFile) {
           
            $filename = 'video_' . time() . '_' . Str::random(6) . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->storeAs('videos', $filename, 'public');

            $new = $banner->videos()->create([
                'video' => $filename,
                'product_id' => $collectionId ?: null,
            ]);

            $savedIds[] = $new->id;

            $collection = $collectionId ? DB::table('products')->find($collectionId) : null;
            $jsonData[] = [
                'video' => $filename,
                'product_id' => $collectionId,
                'name' => $collection?->name ?? '',
                'slug' => $collection?->slug ?? '',
            ];
        }

        // CASE 3: No image and no existing ID – skip
    }

    // Delete removed image rows
    $banner->videos()
        ->whereNotIn('id', $savedIds)
        ->get()
        ->each(function ($image) {
            if ($image->video && Storage::disk('public')->exists('videos/' . $image->video)) {
                Storage::disk('public')->delete('videos/' . $image->video);
            }
            $image->delete();
        });

      //  dd($jsonData);
    // Optional: Save the image data as JSON
    $banner->update([
        'name'         => $request->name,
        'json_column'  => json_encode($jsonData),
        'product_ids'  => json_encode($collectionIds),
    ]);

    DB::commit();
    return createResponse(true, 'Website banner updated successfully.');
} catch (\Exception $e) {
    DB::rollBack();
    return createResponse(false, 'Update failed: ' . $e->getMessage());
}

}
  
    private function processForm(Request $request, $existing = []) {
        $images = $request->file('images', []);
        $collectionIds = $request->input('collection_id', []);
    
        $data = [];
        foreach ($collectionIds as $i => $collectionId) {
            $image = $images[$i] ?? null;
    
            if ($image) {
                $filename = 'banner_' . time() . '_' . \Str::random(6) . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('videos', $filename, 'public');
                $imagePath = 'storage/' . $path;
            } else {
                $imagePath = $existing[$i]['image'] ?? null;
            }
    
            $data[] = [
                'image' => $imagePath,
                'collection_id' => $collectionId,
            ];
        }
    
        return $data;
    }

    public function destroy($id)
    {
                if (!can('delete_videos')) {
        return createResponse(false,'Dont have permission to delete');
        }
        
        try
        {
           
            $banner=Video::with('videos')->where('id',$id)->first();
          
            if($banner){
               $banner->delete();
               foreach ($banner->videos as $image) {
                // Delete image file from public storage
                $path=public_path('storage/videos/'.$image->video);
                if ($image->video && file_exists($path)) {
                    @unlink($path);
                }

            }
    
            // Delete related records from database
           $banner->videos()->delete();
        }
             
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
   

    
     public function exportVideo(Request $request,$type){
        if (!can('export_videos')) {
            return redirect()->back()->withError('Not allowed to export');
        }
      $meta_info=$this->commonVars()['data'];
      return $this->exportModel('Video','videos',$type,$meta_info);
     
      
   
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
