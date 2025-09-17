<?php

namespace App\Http\Controllers;

use \Illuminate\Http\Request;
use DB;
class ReturnItemsController extends Controller
{
    public function __construct()
    {
         $this->dashboard_url=\URL::to('/');
        $this->index_url=domain_route('return_items.index');
        $this->module = 'Return Items';
        $this->view_folder = 'return_items';
        $this->storage_folder ='returns';
        $this->has_upload = 0;
        $this->is_multiple_upload = 0;
        $this->has_export = 0;
        $this->pagination_count = 100;
        $this->crud_title = 'Return Items';
        $this->show_crud_in_modal = 0;
        $this->has_popup = 0;
        $this->has_detail_view = 0;
        $this->has_side_column_input_group = 0;
        $this->form_image_field_name = [
           
            [
                'field_name' => 'first_image',
                'single' => true,
                'has_thumbnail' => false,
            ],
            [
                'field_name' => 'second_image',
                'single' => true,
                'has_thumbnail' => false,
            ],
            [
                'field_name' => 'third_image',
                'single' => true,
                'has_thumbnail' => false,
            ],
            [
                'field_name' => 'fourth_image',
                'single' => true,
                'has_thumbnail' => false,
            ],
            [
                'field_name' => 'qrcode_image',
                'single' => true,
                'has_thumbnail' => false,
            ],
        ];

        $this->model_relations = [
            [
                'name' => 'order',
                'type' => 'BelongsTo',
                'save_by_key' => '',
                'column_to_show_in_view' => 'name',
            ],
            [
                'name' => 'order_item',
                'type' => 'BelongsTo',
                'save_by_key' => '',
                'column_to_show_in_view' => 'name',
            ],
            [
                'name' => 'exchange_variant',
                'type' => 'BelongsTo',
                'save_by_key' => '',
                'column_to_show_in_view' => 'name',
            ],
            [
                'name' => 'user',
                'type' => 'BelongsTo',
                'save_by_key' => '',
                'column_to_show_in_view' => 'name',
            ],
            [
                'name' => 'order_item',
                'type' => 'BelongsTo',
                'save_by_key' => '',
                'column_to_show_in_view' => 'order_id',
            ],
        ];

    }

    public function buildFilter(Request $r, $query,$except=[])
    {
        $get = $r->all();
        if (count($get) > 0 && $r->isMethod('get')) {
            foreach ($get as $key => $value) {
                if ((!is_array($value) && strlen($value) > 0) || (is_array($value) && count($value) > 0)) {
                    if (strpos($key, 'start') !== false) {
                        $field_name = explode('_', $key);

                        $x = array_shift($field_name);
                        $field_name = implode('_', $field_name);

                        $query = $query->whereDate($field_name, '>=', \Carbon\Carbon::parse($value));
                    } elseif (strpos($key, 'end') !== false) {
                        $field_name = explode('_', $key);
                        $x = array_shift($field_name);
                        $field_name = implode('_', $field_name);
                        $query = $query->whereDate($field_name, '<=', \Carbon\Carbon::parse($value));
                    } else {
                        if (!is_array($value)) {
                            $query = $query->where($key, $value);
                        } else {
//dd($value);
                            $query = $query->whereIn($key, $value);
                        }
                    }
                }
            }
        }
        return $query;
    }
    public function show(Request $request, $id)
    {
        $data= [];
       
        $data=[
            'module'=>'return_items',
            'model_relations'=>$this->model_relations,
            'image_field_names' => $this->form_image_field_name,
            'storage_folder' => $this->storage_folder,
            'module_table_name' => 'return_items',
            'has_export' => $this->has_export,
            'crud_title' => $this->crud_title,
            'show_crud_in_modal' => $this->show_crud_in_modal,
            'has_popup' => $this->has_popup,
            'has_side_column_input_group' => $this->has_side_column_input_group,
            'has_detail_view' => $this->has_detail_view,
           
        ];
        if (count($this->model_relations) > 0) {
            $data['row'] = \App\Models\ReturnItem::with(['order:id,uuid','order_item:id,product_id,variant_id,order_id','order_item.product','order_item.variant','exchange_variant:id,name'])->findOrFail($id);
        
        } else {
            $data['row'] = \App\Models\ReturnItem::findOrFail($id);
        }
      //  dd($data['row']->toArray());
        $data['view_columns'] = [
            [
                'label' => 'Return Id #',
                'column' => 'uuid',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'label' => 'Order #',
                'column' => 'order_uid',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
                'link'=>domain_route('orders.view_item_id',['id'=>$data['row']->order_item_id ])
            ],
          
            [
                'column' => 'product',
                'label' => 'Product',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'variant',
                'label' => 'Variant Name',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'user_id',
                'label' => 'Customer',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            
            [
                'column' => 'type',
                'label' => 'Return Type',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'column' => 'exchange_variant',
                'label' => 'Exchange With Variant',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            
            [
                'label' => 'Reason',
                'column' => 'reason',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'label' => 'Detail',
                'column' => 'details',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'label' => 'Refund Method',
                'column' => 'refund_method',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
           
         
           
            [
                'label' => 'UPI Id',
                'column' => 'upi',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            
            [
                'label' => 'QR Code Image',
                'column' => 'qrcode_image',
               
            ],
           
            [
                'label' => 'First Image',
                'column' => 'first_image',
               
            ],
            [
                'label' => 'Second Image',
                'column' => 'second_image',
              
            ],
            [
                'label' => 'Third image',
                'column' => 'third_image',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'label' => 'Fourth image',
                'column' => 'fourth_image',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'label' => 'Return Status',
                'column' => 'return_status',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'label' => 'Refund Status',
                'column' => 'refund_status',
                'show_json_button_click' => false,
                'by_json_key' => 'id',
                'inline_images' => true,
            ],
            [
                'label' => 'Return Created Date',
                'column' => 'created_at',
               
            ],
            [
                'label' => 'Return Completed Date',
                'column' => 'returned_date',
              
            ],
          

        ];
        $view = $this->has_detail_view ? 'view_modal_detail' : 'view';
      
        $data['view_inputs'] = [];
        
        $data = array_merge(['plural_lowercase' => 'return_items'], $data);

        if ($request->ajax()) {
            if (!can('view_attributes')) {
                return createResponse(false, 'Dont have permission to view');
            }

            $html = view('admin.' . $this->view_folder . '.view.' . $view, with($data))->render();
            return createResponse(true, $html);

        } else {
            if (!can('view_attributes')) {
                return redirect()->back()->withError('Dont have permission to view');
            }

            return view('admin.' . $this->view_folder . '.view.' . $view, with($data));

        }

    }
    public function index(Request $request)
    {
        
         $is_vendor=current_role()=='Vendor'?true:false;
        $view_columns = [
           [
                'column' => 'order_uid',
                'label' => 'Order #',
                'sortable' => 'Yes',
            ],
            [
                'column' => 'product',
                'label' => 'Product',
                'sortable' => 'Yes',
            ],
            [
                'column' => 'variant',
                'label' => 'Variant',
                'sortable' => 'Yes',
            ],
            [
                'column' => 'user_id',
                'label' => 'Customer',
                'sortable' => 'Yes',
            ],
            [
                'column' => 'type',
                'label' => 'Return Type',
                'sortable' => 'Yes',
            ],
            [
                'column' => 'return_status',
                'label' => 'Status',
                'sortable' => 'Yes',
            ],
            [
                'column' => 'refund_status',
                'label' => 'Refund Status',
                'sortable' => 'Yes',
            ],
            [
                'column' => 'refund_method',
                'label' => 'Refund Method',
                'sortable' => 'Yes',
            ],
            [
                'column' => 'exchange_variant',
                'label' => 'Exchange With ',
                'sortable' => 'Yes',
            ],
            [
                'column' => 'created_at',
                'label' => ' Date',
                'sortable' => 'Yes',
            ],
          

        ];
        $table_columns = [
            [
                'column' => 'order_id',
                'label' => 'Order #',
                'sortable' => 'Yes',
            ],
            [
                'column' => 'product',
                'label' => 'Product',
                'sortable' => 'Yes',
            ],
            [
                'column' => 'variant',
                'label' => 'Variant',
                'sortable' => 'Yes',
            ],
           
            [
                'column' => 'type',
                'label' => 'Return Type',
                'sortable' => 'Yes',
            ],
            [
                'column' => 'return_status',
                'label' => 'Status',
                'sortable' => 'Yes',
            ],
             [
                'column' => 'exchange_variant',
                'label' => 'Exchange With ',
                'sortable' => 'Yes',
            ],
          
            [
                'column' => 'created_at',
                'label' => 'Return Created Date',
                'sortable' => 'Yes',
            ],
            [
                'column' => 'status',
                'label' => 'Approval Status',
                'sortable' => 'Yes',
            ],
          

        ];
        $filterable_fields = [
            [
                'name' => 'created_at',
                'label' => 'Created At',
                'type' => 'date',
            ],
           
        ];
        $searchable_fields = [
          
        ];
        $this->pagination_count = 100;
        $vendor_id=auth()->id()?null:auth()->guard('vendor')->id();
        if ($request->ajax()) {
            $sort_by = $request->get('sortby');
            $sort_type = $request->get('sorttype');
            $search_by = $request->get('search_by');

            $query = $request->get('query');

            $search_val = str_replace(" ", "%", $query);
            if (empty($search_by)) {
                $search_by = 'name';
            }

            $list = \App\Models\ReturnItem::with(['return_shipment:id,is_transferred','order:id,uuid,created_at','order_item:id,product_id,variant_id,order_id,vendor_id','order_item.product:id,name,category_id','order_item.product,category:id,name','order_item.variant:id,name','exchange_variant:id,name'])->when(!empty($search_val), function ($query) use ($search_val, $search_by) {
                return $query->where($search_by, 'like', '%' . $search_val . '%');
            })
                ->when(!empty($sort_by), function ($query) use ($sort_by, $sort_type) {
                    return $query->orderBy($sort_by, $sort_type);
                })
                ->when(!empty($vendor_id), function ($query) use ($vendor_id) {
                    return $query->whereHas('order_item', function ($q) use ($vendor_id) {
                                $q->where('vendor_id', $vendor_id);
                            });
                })
                ->latest()->paginate($this->pagination_count);
            $data = [
                'table_columns' => $table_columns,
                'list' => $list,
                'sort_by' => $sort_by,
                'sort_type' => $sort_type,
                'storage_folder' => '',
                'plural_lowercase' => 'return_items',
                'module' => 'ComapnyLedger',
                'has_image' => 0,
                'model_relations' => [],
                'image_field_names' => [],

            ];
            return $is_vendor?view("vendor.return_orders.index", $view_data):view('admin.return_items.page', with($data));
        } else {

            $query = null;

            $query =  \App\Models\ReturnItem::with(['return_shipment:id,is_transferred','order:id,uuid,created_at','order_item:id,product_id,variant_id,order_id,vendor_id','order_item.product:id,name,category_id','order_item.product.category:id,name','order_item.variant:id,name','exchange_variant:id,name'])
             ->when(!empty($vendor_id), function ($query) use ($vendor_id) {
                    return $query->whereHas('order_item', function ($q) use ($vendor_id) {
                                $q->where('vendor_id', $vendor_id);
                            });
                });

            $query = $this->buildFilter($request, $query);
            $list = $query->latest()->paginate($this->pagination_count);

            $view_data = [
                'list' => $list,

                'title' => 'Return Items',
                'searchable_fields' => $searchable_fields,
                'filterable_fields' => $filterable_fields,
                'plural_lowercase' => 'return_items',
                'table_columns' => $table_columns,
                'module' => 'ReturnItem',
                'has_export' => 0,
                'model_relations' => $this->model_relations,
                'module_table_name' => 'return_items',
                'bulk_update' => json_encode([
                    'return_status' => ['label' => 'Return Status', 'data' => getListFromIndexArray(['Pending', 'Returned','Cancelled'])],
                    'refund_status' => ['label' => 'Refund Status', 'data' => getListFromIndexArray(['Pending', 'Paid','Cancelled'])],

                ]),
                'show_view_in_popup' => false,
                'crud_title' => 'Return Item',

            ];
            return $is_vendor?view("vendor.return_orders.index", $view_data):view('admin.return_items.index', $view_data);
        }
    }
    public function update_return_status(Request $r){
    $status=$r->status;
    $reason=$r->reason;
    $id=$r->id;/**return item id  */
   
     $rturn_item=\App\Models\ReturnItem::with(['order:id,uuid','order_item:id,vendor_id'])->where('id',$id)->first();
     $vendor_order_uuid=$rturn_item->order->uuid.'/'.$rturn_item->order_item->vendor_id;
    $related_vendor_order=\DB::table('vendor_orders')->where('uuid',$vendor_order_uuid)->first();
    $related_return_shipment=\DB::table('return_shipments')->where('vendor_order_id',$related_vendor_order->id)
        ->where('type',$rturn_item->type)->where('vendor_id',$rturn_item->order_item->vendor_id)->where('is_transferred','No')->first();
   
     if($status=='Approved' &&  $rturn_item->status!='Approved'){
           if(is_null($related_return_shipment)){
            $returnOrderId =now()->format('YmdHis') . rand(10, 99);
            $shipment_id=\DB::table('return_shipments')->insertGetId(['vendor_id'=>$rturn_item->order_item->vendor_id,'uuid'=>$returnOrderId,'vendor_order_id'=>$related_vendor_order->id,'type'=>$rturn_item->type]);
              $newStatus = [
                    'status' => 'APPROVED','icon'=>'Approved',
                    'date' =>now(),
                    'message' => '',
                ];
                $existing = \DB::table('return_items')->where('id', $id)->value('return_status_updates');
                $updates = $existing ? json_decode($existing, true) : [];
                $updates[] = $newStatus;

                // Update the record
                DB::table('return_items')
                    ->where('id', $id)
                    ->update([
                        'return_status' => $newStatus['status'],
                        'return_status_updates' => json_encode($updates),
                        'status'=>$status,'reject_reason'=>$reason,
                         'return_shipment_id'=>$shipment_id
                    ]);
           
        }else{
            \DB::table('return_items')->where('id',$id)->update(['status'=>$status,'reject_reason'=>$reason,
            'return_shipment_id'=>$related_return_shipment->id]);
        }
    }
    else{
        if($status=='Rejected' &&   $rturn_item->status!='Rejected'){
            $count=\App\Models\ReturnItem::where('return_shipment_id',$rturn_item->return_shipment_id)->count();
            
            if($related_return_shipment && $count===1){
                \DB::table('return_shipments')->where('is_transferred','No')->where('id',$rturn_item->return_shipment_id)->delete();
            }
           \App\Models\ReturnItem::where('id',$id)->update(['status'=>'Rejected','reject_reason'=>$reason]);
        }
    }
    return createResponse(true,$status.' successfully');

}
}
