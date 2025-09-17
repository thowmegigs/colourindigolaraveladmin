<?php

namespace App\Http\Controllers;

use App\Http\Requests\WebsiteSliderRequest;
use App\Models\WebsiteSlider;
use App\Models\WebsiteCarouselImage;
use File;
use \Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver; // or GD
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class WebsiteSliderController extends Controller
{
    public function __construct()
    {
         $this->dashboard_url=\URL::to('/');
        $this->index_url=domain_route('website_sliders.index');
        $this->module='WebsiteSlider';
        $this->view_folder='website_sliders';
        $this->storage_folder=$this->view_folder;
        $this->has_upload=1;
        $this->is_multiple_upload=0;
        $this->has_export=0;
        $this->pagination_count=100;
        $this->crud_title='Website Slider';
         $this->show_crud_in_modal=1;
         $this->has_popup=1;
        $this->has_detail_view =0;
        $this->has_side_column_input_group =0;
         $this->dimensions=  [
                    'tiny'  => 360,
                    'small' => 480,
                    'medium' => 768,
                    'large' => 1224,
        ];
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
        $collections=getList('Collection');

        $repeating_group_inputs=[
            [
                'colname' => 'repeatable',
                'label' => 'Add Images',
                'inputs' => [
                    [
                        'placeholder' => 'Enter image',
                        'name' => 'repeatable__json__image[]',
                        'label' => 'Image',
                        'tag' => 'input',
                        'type' => 'file',
                        'default' => '',
                        'attr' => []
                    ],
                    [
                        'name' => 'repeatable__json__collection[]',
                        'label' => 'Select Collection',
                        'tag' => 'select',
                        'type' => 'select',
                        'default' => (!empty($collections)?$collections[0]->id:''),
                        'attr' => [],
                        'custom_key_for_option' => 'name',
                        'options' => $collections,
                        'custom_id_for_option' => 'id',
                        'multiple' => false
                    ]
                ],
                'index_with_modal' => 0,
                'modalInputBoxIdWhoseValueToSetInSelect' => '',
                'hide' => false,
                'disable_buttons' => false
            ]
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
            'plural_lowercase' =>'website_sliders',
            'has_image' => $this->has_upload,
            'table_columns'=>$table_columns,
            'view_columns'=>$view_columns,
           
            'image_field_names' => $this->form_image_field_name,
            'storage_folder' => $this->storage_folder,
             'module_table_name'=>'website_sliders',
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
            $data['row'] = WebsiteSlider::with(array_column($this->model_relations, 'name'))->findOrFail($id);
        } else {
            $data['row'] = WebsiteSlider::findOrFail($id);
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

            $db_query =  WebsiteSlider::when(!empty($search_val), function ($query) use ($search_val, $search_by) {
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
            if (!can('list_website_sliders')) {
                return redirect()->back()->withError('Dont have permission to list');
            }
            $query = null;
            if (count($this->model_relations) > 0) {
                $query = WebsiteSlider::with(array_column($this->model_relations, 'name'));
            } else {
                $query =  WebsiteSlider::query();
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
    $collections=getList('Collection');
      
          $view_data = array_merge($this->commonVars()['data'], [
                    'data' => $data,
                    'collections'=>$collections

                ]);
            
         if($r->ajax()){
           
             if (!can('create_website_sliders')) {
                return createResponse(false,'Dont have permission to create');
                }
              
                $html=view('admin.' . $this->view_folder . '.modal.add', with($view_data))->render();
                return  createResponse(true, $html);
         }
       else{
         
         if (!can('create_website_sliders')) {
                return redirect()->back()->withError('Dont have permission to create');
            }
         return view('admin.' . $this->view_folder . '.add', with($view_data));
       }
    
    }
   public function store(WebsiteSliderRequest $request)
{
    if (!can('create_website_sliders')) {
        return createResponse(false, 'Dont have permission to create');
    }
  \DB::beginTransaction();
$uploadedPaths = [];
    try {
        $post = $request->all();
        $images = $request->file('images');
        $uploaded = [];
        $uploaded1 = [];
        $collectionIds = $request->input('collection_id', []);

        $manager = new ImageManager(new Driver());

        // ✅ Define thumbnail sizes
         $sizes=$this->dimensions;
        $folder=$this->storage_folder;

        foreach ($images as $index => $image) {
            $originalExtension = strtolower($image->getClientOriginalExtension());

            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $slug = \Str::slug($originalName); // SEO-friendly
            $filenameBase = "{$slug}_" . time() . "_" . \Str::random(6);
            $webpFilename = $filenameBase . '.webp';

            // ✅ Create Intervention Image
            $img = $manager->read($image->getPathname());

            // ✅ Convert to WebP if not already
            if ($originalExtension !== 'webp') {
                $webpContent = $img->toWebp(80);
            } else {
                // Just read the raw file if it's already WebP
                $webpContent = file_get_contents($image->getPathname());
            }

            $originalPath = "{$folder}/{$webpFilename}";
            \Storage::disk('public')->put($originalPath, (string) $webpContent);

            // ✅ Generate thumbnails
            foreach ($sizes as $label => $width) {
                $thumb = clone $img;
                $thumb->scaleDown(width: $width)->sharpen(5);

                $thumbName = "{$label}_{$filenameBase}.webp";
                $thumbPath = "{$folder}/thumbnail/{$thumbName}";
                 $quality=85;
                if (\Str::contains($label, 'tiny') || \Str::contains($label, 'small') || \Str::contains($label, 'medium') ) {
                    $quality=95;
                    }

                \Storage::disk('public')->put($thumbPath, (string) $thumb->toWebp($quality));
                 $uploadedPaths[] = $thumbPath;
            }
           $collection=$collectionIds[$index]?\DB::table('collections')->where('id',$collectionIds[$index])->first():null;
         
            $uploaded[] = [
                'image' => $webpFilename,
                'collection_id' => $collectionIds[$index] ?? null,
                'collection_name'=>$collection?->name??'',
                'slug'=>$collection?->slug??'',
            ];
            $uploaded1[] = [
                'name' => $webpFilename,
                'collection_id' => $collectionIds[$index] ?? null,
               
            ];
        }

        $post['json_column'] = json_encode($uploaded);
        $post['collection_ids'] = json_encode($collectionIds);
        $websiteslider = WebsiteSlider::create($post);

        $uploaded1 = array_map(function ($b) use ($websiteslider) {
            $b['website_slider_id'] = $websiteslider->id;
            return $b;
        }, $uploaded1);

        \DB::table('website_carousel_images')->insert($uploaded1);
        $this->afterCreateProcess($request, $post, $websiteslider);

        \DB::commit();
        return createResponse(true, $this->crud_title . ' created successfully', $this->index_url);

    } catch (\Exception $ex) {
        \Sentry\captureException($ex);
        \DB::rollback();
        foreach ($uploadedPaths as $path) {
        \Storage::disk('public')->delete($path);
    }
        return createResponse(false, $ex->getMessage());
    }
}
    public function edit(Request $request,$id)
    {
        

        $model = WebsiteSlider::with('images')->findOrFail($id);

        $data = $this->editInputsData($model);
        $collections=getList('Collection');
       
        $view_data = array_merge($this->commonVars($model)['data'], [
            'data' => $data, 'model' => $model,'collections'=>$collections

        ]);
       
       

         if ($request->ajax()) {
            if (!can('edit_website_sliders')) {
                return createResponse(false, 'Dont have permission to edit');
            }

            $html = view('admin.' . $this->view_folder . '.modal.edit', with($view_data))->render();
            return createResponse(true, $html);

        } else {
            if (!can('edit_website_sliders')) {
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
             if (!can('view_website_sliders')) {
                return createResponse(false,'Dont have permission to view');
         }
           
            $html = view('admin.' . $this->view_folder . '.view.' . $view, with($data))->render();
            return createResponse(true, $html);

        } else {
            if (!can('view_website_sliders')) {
                return redirect()->back()->withError('Dont have permission to view');
         }
           

            return view('admin.' . $this->view_folder . '.view.' . $view, with($data));

        }

      

    }
    
  public function update(WebsiteSliderRequest $request, $id)
{
    if (!can('edit_website_sliders')) {
        return createResponse(false, 'You do not have permission to update.');
    }

    DB::beginTransaction();

    try {
    $banner = WebsiteSlider::findOrFail($id);
    $manager = new ImageManager(new Driver());
    $existingIds    =  array_filter($request->input('existing_ids', []));
    $collectionIds  = $request->input('collection_id', []);
    $uploadedImages =$request->file('images', []);

    $savedIds = [];
    $jsonData = [];

    // Determine max count based on whichever array is longer
    $maxCount = count($collectionIds);
         $folder=$this->storage_folder;

    for ($index = 0; $index < $maxCount; $index++) {
        $existingId   = $existingIds[$index] ?? null;
        $collectionId = $collectionIds[$index] ?? null;
        $imageFile    = $uploadedImages[$index] ?? null;

        // CASE 1: Update existing row
        if ($existingId) {
            $row = WebsiteCarouselImage::find($existingId);
            if (!$row) continue;
             $webpFilename=$row->name;
            // Replace image if a new one is uploaded
            if ($imageFile) {
                  $originalExtension = strtolower($imageFile->getClientOriginalExtension());
                  $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $slug = \Str::slug($originalName); // SEO-friendly
                    $filenameBase = "{$slug}_" . time() . "_" . \Str::random(6);
                    $webpFilename = $filenameBase . '.webp';

                    // ✅ Create Intervention Image
                    $img = $manager->read($imageFile->getPathname());

                    // ✅ Convert to WebP if not already
                    if ($originalExtension !== 'webp') {
                        $webpContent = $img->toWebp(80);
                    } else {
                        // Just read the raw file if it's already WebP
                        $webpContent = file_get_contents($imageFile->getPathname());
                    }

                        $originalPath = "{$folder}/{$webpFilename}";
                        \Storage::disk('public')->put($originalPath, (string) $webpContent);
                        $sizes=$this->dimensions;

                        // ✅ Generate thumbnails
                        foreach ($sizes as $label => $width) {
                            $thumb = clone $img;
                            $thumb->scaleDown(width: $width)->sharpen(5);

                            $thumbName = "{$label}_{$filenameBase}.webp";
                            $thumbPath = "{$folder}/thumbnail/{$thumbName}";
                            $quality=85;
                            if (\Str::contains($label, 'tiny') || \Str::contains($label, 'small') || \Str::contains($label, 'medium') ) {
                                $quality=95;
                                }

                            \Storage::disk('public')->put($thumbPath, (string) $thumb->toWebp($quality));
                            $uploadedPaths[] = $thumbPath;
                        }
                $row->name = $webpFilename ;
            }

            // Always update collection
            $row->collection_id = $collectionId ?: null;
            $row->save();

            $savedIds[] = $row->id;

            $collection = $collectionId ? DB::table('collections')->find($collectionId) : null;
            $jsonData[] = [
                'image' => $webpFilename ,
                'collection_id' => $collectionId,
                'collection_name' => $collection?->name ?? '',
                'slug' => $collection?->slug ?? '',
            ];
          
        }

        // CASE 2: New row (only if image uploaded)
        elseif ($imageFile) {
            $webpFilename =null;
             $originalExtension = strtolower($imageFile->getClientOriginalExtension());
            $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $slug = \Str::slug($originalName); // SEO-friendly
                    $filenameBase = "{$slug}_" . time() . "_" . \Str::random(6);
                    $webpFilename = $filenameBase . '.webp';

                    // ✅ Create Intervention Image
                    $img = $manager->read($imageFile->getPathname());

                    // ✅ Convert to WebP if not already
                    if ($originalExtension !== 'webp') {
                        $webpContent = $img->toWebp(80);
                    } else {
                        // Just read the raw file if it's already WebP
                        $webpContent = file_get_contents($imageFile->getPathname());
                    }

                        $originalPath = "{$folder}/{$webpFilename}";
                        \Storage::disk('public')->put($originalPath, (string) $webpContent);
                        $sizes=$this->dimensions;

                        // ✅ Generate thumbnails
                        foreach ($sizes as $label => $width) {
                            $thumb = clone $img;
                            $thumb->scaleDown(width: $width)->sharpen(5);

                            $thumbName = "{$label}_{$filenameBase}.webp";
                            $thumbPath = "{$folder}/thumbnail/{$thumbName}";
                            $quality=85;
                            if (\Str::contains($label, 'tiny') || \Str::contains($label, 'small') || \Str::contains($label, 'medium') ) {
                                $quality=95;
                                }

                            \Storage::disk('public')->put($thumbPath, (string) $thumb->toWebp($quality));
                            $uploadedPaths[] = $thumbPath;
                        }

            $new = $banner->images()->create([
                'name' => $webpFilename,
                'collection_id' => $collectionId ?: null,
            ]);

            $savedIds[] = $new->id;

            $collection = $collectionId ? DB::table('collections')->find($collectionId) : null;
            $jsonData[] = [
                'image' => $webpFilename,
                'collection_id' => $collectionId,
                'collection_name' => $collection?->name ?? '',
                'slug' => $collection?->slug ?? '',
            ];
        }

        // CASE 3: No image and no existing ID – skip
    }

    // Delete removed image rows
    $banner->images()
        ->whereNotIn('id', $savedIds)
        ->get()
        ->each(function ($image) {
            if ($image->name && Storage::disk('public')->exists('website_sliders/' . $image->name)) {
                Storage::disk('public')->delete('website_sliders/' . $image->name);
            }
            $image->delete();
        });

       // dd($jsonData);
    // Optional: Save the image data as JSON
    $banner->update([
        'name'         => $request->name,
        'json_column'  => json_encode($jsonData),
        'collection_ids'  => json_encode($collectionIds),
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
                $filename = 'slider_' . time() . '_' . \Str::random(6) . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('website_sliders', $filename, 'public');
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
                if (!can('delete_website_sliders')) {
        return createResponse(false,'Dont have permission to delete');
        }
        
        try
        {
            $slider=WebsiteSlider::with('images')->where('id',$id)->first();
            if($slider){
               $slider->delete();
               foreach ($slider->images as $image) {
                // Delete image file from public storage
                $path=public_path('storage/website_sliders/'.$image->name);
                if ($image->name && file_exists($path)) {
                    @unlink($path);
                }
            }
   
            // Delete related records from database
            $slider->images()->delete();
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
   

    
     public function exportWebsiteSlider(Request $request,$type){
        if (!can('export_website_sliders')) {
            return redirect()->back()->withError('Not allowed to export');
        }
      $meta_info=$this->commonVars()['data'];
      return $this->exportModel('WebsiteSlider','website_sliders',$type,$meta_info);
     
      
   
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
