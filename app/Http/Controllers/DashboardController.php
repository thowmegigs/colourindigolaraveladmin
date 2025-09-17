<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use DB;
use \Carbon\Carbon;
use \App\Models\VendorOrder;
use \App\Models\Order;
use \App\Models\OrderItem;
class DashboardController extends Controller
{
    public function index()
    {

      
    $data['widgets']=[];
        $user = auth()->id()?auth()->id():auth()->guard('vendor')->id();
        if(auth()->id()){
        $data['widgets'] = [
          
            [

                'bg_color' => 'warning',
                'title' => 'Total Earnings',
                'sub_heading' => '',
                'value' => getTableRecordSum('orders', ['paid_status' => 'Paid',], 'net_payable') - getTableRecordSum('refund', ['status'=>'Paid'], 'amount'),
                'link' => domain_route('orders.index'),
                'append' => '₹'

            ],
            [

                'bg_color' => 'success',
                'title' => 'Total Products',
                'sub_heading' => '',
                'value' => getTableRecordCount('products',[]),
                'link' => domain_route('products.index'),
                'append' => ''

            ],
            [

                'bg_color' => 'info',
                'title' => 'Total Categories',
                'sub_heading' => '',
                'value' => getTableRecordCount('categories', []),
                'link' => domain_route('categories.index'),
                'append' => ''

            ],
            [

                'bg_color' => 'primary',
                'title' => 'Total Incoming Orders',
                'sub_heading' => '',
                'value' => getTableRecordCount('orders', []),
                'link' => domain_route('orders.index'),
                'append' => ''

            ],
            [

                'bg_color' => 'success',
                'title' => 'Total Unpaid Orders',
                'sub_heading' => '',
                'value' => getTableRecordCount('orders', ['paid_status' => 'Pending']),
                'link' => domain_route('orders.index'),
                'append' => '',

            ],
            [

                'bg_color' => 'info',
                'title' => 'Total Pending Deliveries',
                'sub_heading' => '',
                'value' => getTableRecordCount('orders', ['delivery_status' => 'Pending']),
                'link' => domain_route('orders.index'),
                'append' => '',

            ]
            

        ];
    $data['latest_pending_orders'] = \DB::table('orders')->where('delivery_status','Ordered')->limit(5)->latest()->get();
    $data['latest_paid_orders'] = \DB::table('orders')->where('paid_status','Paid')->limit(5)->latest()->get();
     return view('admin.dashboard', with($data));
    }else{
    $vendorId=auth()->guard('vendor')->id();
    $earnings = \App\Models\VendorOrder::where('vendor_id', $vendorId)
                    ->whereHas('shipping_status_updates', function ($query) {
                        $query->where('shipping_status', 'Delivered');
                    })
                    ->whereDoesntHave('shipping_status_updates', function ($query) {
                          $query->where(function ($q) {
                                $q->where('shipping_status', 'LIKE', '%Return%')
                                ->orWhere('shipping_status', 'LIKE', '%Exchange%');
                            });
                    })
                    ->select(\DB::raw('SUM(vendor_total - (commission_total + refunded_amount)) as total_profit'))
                    ->value('total_profit');

          $data['widgets'] = [
          
            [

                'bg_color' => 'warning',
                'title' => 'Total Earnings',
                'sub_heading' => '',
                'value' =>$earnings??0.0,
                'link' => domain_route('vendor_orders'),
                'append' => '₹'

            ],
            [

                'bg_color' => 'success',
                'title' => 'Total Products',
                'sub_heading' => '',
                'value' => getTableRecordCount('products', ['vendor_id'=>$vendorId]),
                'link' => domain_route('products.index'),
                'append' => ''

            ],
           
            [

                'bg_color' => 'primary',
                'title' => 'Total New Orders',
                'sub_heading' => '',
                'value' => getTableRecordCount('vendor_orders', ['vendor_id'=>$vendorId,'delivery_status'=>'Ordered']),
               'link' => domain_route('vendor_orders'),
                'append' => ''

            ],
            [

                'bg_color' => 'success',
                'title' => 'Total Unpaid Orders',
                'sub_heading' => '',
                'value' => getTableRecordCount('vendor_orders', ['vendor_id'=>$vendorId,'paid_status'=>'Unpaid','status'=>'Success']),
               'link' => domain_route('vendor_orders'),
                'append' => '',

            ],
            [

                'bg_color' => 'success',
                'title' => 'Total Paid Orders',
                'sub_heading' => '',
                'value' => getTableRecordCount('vendor_orders', ['vendor_id'=>$vendorId,'paid_status'=>'Paid','status'=>'Success']),
                'link' => domain_route('orders.index'),
                'append' => '',

            ],
          
            

        ];
         $data['latest_pending_orders'] = \DB::table('vendor_orders')->where('vendor_id',$vendorId)->where('delivery_status','Ordered')->limit(5)->latest()->get();
        $data['latest_paid_orders'] = \DB::table('vendor_orders')->where('vendor_id',$vendorId)->where('paid_status','Paid')->limit(5)->latest()->get();
        $data['salesData'] = [500, 700, 800, 650, 900, 1100, 1000];
        $data['months'] = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];
        
        $data['topProducts'] = DB::table('order_items')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->join('vendor_orders', 'vendor_orders.order_id', '=', 'orders.id')
        ->where('vendor_orders.vendor_id', $vendorId)
        ->select('order_items.name', DB::raw('SUM(order_items.qty) as total_qty'))
        ->groupBy('order_items.name')
        ->orderByDesc('total_qty')
        ->limit(5)
        ->get();
       

    // 3️⃣ Sales overview (last 7 days revenue trend)
    $data['salesOverview'] = VendorOrder::where('vendor_id', $vendorId)
        ->where('paid_status', 'Paid')
        ->whereBetween('created_at', [Carbon::now()->subDays(100), Carbon::now()])
        ->select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(vendor_earnings) as total_sales')
        ) 
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    // 4️⃣ Monthly orders count (last 12 months)
    $data['monthlyOrders'] = VendorOrder::where('vendor_id', $vendorId)
        ->select(
            DB::raw("DATE_FORMAT(created_at, '%b %Y') as month"),
            DB::raw('COUNT(id) as total_orders')
        )
        ->groupBy('month')
        ->orderBy(DB::raw("MIN(created_at)"))
        ->get();

        return view('vendor.dashboard', with($data));
 
    }

       
    }
    public function dashboard_data(Request $r)
    {
        if ($r->ajax()) {
            /*     $expense_perday_record = getDailyRecord('company_ledger', $date_column = 'created_at', $to_do = 'sum', $cond = "`mode`='Spent'", $column_for_sum = "amount", $for_days = 7);
        $sell_perday_record = getDailyRecord('company_ledger', $date_column = 'created_at', $to_do = 'sum', $cond = "`mode`='Income'", $column_for_sum = "amount", $for_days = 7);

        $expsnse_val = $expense_perday_record['val'];

        $sell_val = $sell_perday_record['val'];
        $dates_expsense = $expense_perday_record['datetime'];
        $dates_sell = $sell_perday_record['datetime'];

        $dates = array_unique(array_merge($dates_expsense, $dates_sell));

        $sell_monthwise_val = getMonthlyRecord('company_ledger', $date_column = 'created_at', $to_do = 'sum', $cond = "`mode`='Income'", $column_for_sum = "amount");

        $expense_monthwise_val = getMonthlyRecord('company_ledger', $date_column = 'created_at', $to_do = 'sum', $cond = "`mode`='Spent'", $column_for_sum = "amount");

        $order_monthwise_val = getMonthlyRecord('create_order', $date_column = 'created_at', $to_do = 'count', $cond = '');

        $daily_order_record = getDailyRecord('create_order', $date_column = 'created_at', $to_do = 'count', $cond = '');
        $weekly_order_val = getWeeklyRecord('create_order', $date_column = 'created_at', $to_do = 'count', $cond = '');
        $daily_order_val = $daily_order_record['val'];
        $dates_order = $daily_order_record['datetime'];

        $paid_order_monthwise_val = getMonthlyRecord('create_order', $date_column = 'created_at', $to_do = 'count', $cond = "`paid_status`='Paid'");

        $paid_daily_order_record = getDailyRecord('create_order', $date_column = 'created_at', $to_do = 'count', $cond = "`paid_status`='Paid'");
        $paid_weekly_order_val = getWeeklyRecord('create_order', $date_column = 'created_at', $to_do = 'count', $cond = "`paid_status`='Paid'");
        $paid_daily_order_val = $paid_daily_order_record['val'];
        $paid_dates_order = $paid_daily_order_record['datetime'];

        $leads_monthwise_val = getMonthlyRecord('leads', $date_column = 'created_at', $to_do = 'count', $cond = '');

        $daily_leads_record = getDailyRecord('leads', $date_column = 'created_at', $to_do = 'count', $cond = '');
        $weekly_leads_val = getWeeklyRecord('leads', $date_column = 'created_at', $to_do = 'count', $cond = '');
        $daily_leads_val = $daily_leads_record['val'];
        $dates_leads = $daily_leads_record['datetime'];

         */

        }

    }
      public function profile(Request $r)
    {
         $data['states']=getList('State');
        $data['me']=auth()->guard('vendor')->user();
        $data['city_name']=$data['me']->state_id?\DB::table('city')->where('state_id',$data['me']->state_id)->first()->name:null;
              $data['cities']=$data['me']->state_id?\DB::table('city')->where('state_id',$data['me']->state_id)->get():null;

        return view('vendor.profile_setting',with($data));
    }
      public function support(Request $r)
    {
         
        return view('vendor.support');
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
 
 public function customer_banks(Request $request)
    {
       
         
        $view_columns =[];
         $table_columns = [
            [
                "column" => "user_id",
                "label" => "Customer Name",
                "sortable" => "Yes",
            ],
            [
                "column" => "bank_name",
                "label" => "Bank Name",
                "sortable" => "Yes",
            ],

            [
                "column" => "account_number",
                "label" => "Account Number",
                "sortable" => "Yes",
            ],
            [
                "column" => "account_holder",
                "label" => "Account Holder",
                "sortable" => "Yes",
            ],
            [
                "column" => "ifsc",
                "label" => "IFSC Code",
                "sortable" => "Yes",
            ],
            [
                "column" => "upi_id",
                "label" => "UPI Id",
                "sortable" => "Yes",
            ],
            [
                "column" => "qr_image",
                "label" => "QR Code",
                "sortable" => "Yes",
            ],

            [
                "column" => "created_at",
                "label" => "Create Date",
                "sortable" => "Yes",
            ],
        ];
        $filterable_fields = [
            [
                "name" => "user_id",
                "label" => "Select Customer",
                "type" => "select",
                'options'=>getListWithRoles('Customer')

            ],
            [
                "name" => "created_at",
                "label" => "Created At",
                "type" => "date",
            ],
         
        ];
        $searchable_fields = [
             [
                "name" => "bank_name",
                "label" => "Bank Name",
                "type" => "text",
            ],

            [
                "name" => "account_number",
                "label" => "Account Number",
                "type" => "text",
            ],
            [
                "name" => "ifsc",
                "label" => "IFSC Code",
                 "type" => "text",
            ],
            [
                "name" => "branch_name",
                "label" => "Branch Name",
                 "type" => "text",
            ],
        ];
        $model_relations = [
            [
                "name" => "user",
                "type" => "BelongsTo",
                "save_by_key" => "",
                "column_to_show_in_view" => "name",
            ],
        ];
        $this->pagination_count = 100;
        if ($request->ajax()) {
            $sort_by = $request->get("sortby");
            $sort_type = $request->get("sorttype");
            $search_by = $request->get("search_by");

            $query = $request->get("query");

            $search_val = str_replace(" ", "%", $query);
            if (empty($search_by)) {
                $search_by = "name";
            }
           
            $list = \App\Models\CustomerBank::with("user:id,name")
                ->when(!empty($search_val), function ($query) use (
                    $search_val,
                    $search_by
                ) {
                    return $query->where(
                        $search_by,
                        "like",
                        "%" . $search_val . "%"
                    );
                })
               
                ->when(!empty($sort_by), function ($query) use (
                    $sort_by,
                    $sort_type
                ) {
                    return $query->orderBy($sort_by, $sort_type);
                })
                ->latest()
                ->paginate($this->pagination_count);
               
            $data = [
                "table_columns" => $table_columns,
                "list" => $list,
                "searchable_fields" => $searchable_fields,
                "filterable_fields" => $filterable_fields,
                "plural_lowercase" => "customer_banks",
                "sort_by" => $sort_by,
                "sort_type" => $sort_type,
                "storage_folder" => "",
                "plural_lowercase" => "customer_banks",
                "module" => "CustomerBank",
                "has_image" => 0,
                "model_relations" => [],
                "image_field_names" => [],
                "crud_title" => "Customer Bank",
                "has_popup" => false,
                "show_crud_in_modal" => false,
                "bulk_update" => "",
            ];
            return view("admin.customer_banks.page",with($data));
        } else {
            $query = null;

            $query = \App\Models\CustomerBank::with("user:id,name");

            $query = $this->buildFilter($request, $query);
            $list = $query->latest()->paginate($this->pagination_count);

            $view_data = [
                "list" => $list,

                "title" => "Customer Bank",
                "searchable_fields" => $searchable_fields,
                "filterable_fields" => $filterable_fields,
                "plural_lowercase" => "customer_banks",
                "table_columns" => $table_columns,
                "module_table_name" => "customer_banks",

                "model_relations" => $model_relations,
                "module" => "CustomerBank",
                "crud_title" => "Customer Bank ",
                "has_popup" => false,
                "show_crud_in_modal" => false,
                "bulk_update" => "",
            ];
            return view("admin.vendor_banks.index", $view_data);
        }
    }

 public function vendor_settlements(Request $request)
    {
       
         
        $view_columns =[];
         $table_columns = [
            [
                "column" => "vendor_id",
                "label" => "Vendor Name",
                "sortable" => "Yes",
            ],
            [
                "column" => "amount",
                "label" => "Amount",
                "sortable" => "Yes",
            ],

            [
                "column" => "paid_status",
                "label" => "Paid Status",
                "sortable" => "Yes",
            ],
           

            [
                "column" => "updated_at",
                "label" => "Last  Updated Date",
                "sortable" => "Yes",
            ],
        ];
        $filterable_fields = [
            [
                "name" => "created_at",
                "label" => "Created At",
                "type" => "date",
            ],
            [
                "name" => "paid_status",
                "label" => "Paid Status",
                "type" => "select",
                'options'=>getListFromIndexArray(['Pending','Paid'])
            ],
            [
                "name" => "vendor_id",
                "label" => "Vendor",
                "type" => "select",
                'options'=>getList('Vendor')
            ],
         
        ];
        $searchable_fields = [
            

          
        ];
        $model_relations = [
            [
                "name" => "vendor",
                "type" => "BelongsTo",
                "save_by_key" => "",
                "column_to_show_in_view" => "name",
            ],
        ];
        $this->pagination_count = 100;
        if ($request->ajax()) {
            $sort_by = $request->get("sortby");
            $sort_type = $request->get("sorttype");
            $search_by = $request->get("search_by");

            $query = $request->get("query");

            $search_val = str_replace(" ", "%", $query);
            if (empty($search_by)) {
                $search_by = "name";
            }
           
            $list = \App\Models\VendorSettlement::with("vendor:id,name")
                ->when(!empty($search_val), function ($query) use (
                    $search_val,
                    $search_by
                ) {
                    return $query->where(
                        $search_by,
                        "like",
                        "%" . $search_val . "%"
                    );
                })
               
                ->when(!empty($sort_by), function ($query) use (
                    $sort_by,
                    $sort_type
                ) {
                    return $query->orderBy($sort_by, $sort_type);
                })
                ->latest()
                ->paginate($this->pagination_count);
               
            $data = [
                "table_columns" => $table_columns,
                "list" => $list,
                "searchable_fields" => $searchable_fields,
                "filterable_fields" => $filterable_fields,
                "plural_lowercase" => "vendor_settlements",
                "sort_by" => $sort_by,
                "sort_type" => $sort_type,
                "storage_folder" => "",
                "plural_lowercase" => "vendor_settlements",
                "module" => "VendorSettlement",
                "has_image" => 0,
                "model_relations" => [],
                "image_field_names" => [],
                "crud_title" => "Vendor Settlement",
                "has_popup" => false,
                "show_crud_in_modal" => false,
                 "bulk_update" => json_encode([
                    "paid_status" => [
                        "label" => "Set Paid",
                        "data" => getListFromIndexArray(["Paid", "Pending"]),
                    ],
                ]),
            ];
            return view("admin.vendor_settlements.page",with($data));
        } else {
            $query = null;

            $query = \App\Models\VendorSettlement::with(
                "vendor:id,name"
            );

            $query = $this->buildFilter($request, $query);
            $list = $query->latest()->paginate($this->pagination_count);

            $view_data = [
                "list" => $list,

                "title" => "Vendor Settlement",
                "searchable_fields" => $searchable_fields,
                "filterable_fields" => $filterable_fields,
                "plural_lowercase" => "vendor_settlements",
                "table_columns" => $table_columns,
                "module_table_name" => "vendor_settlements",

                "model_relations" => $model_relations,
                "module" => "VendorSettlement",
                "crud_title" => "Vendor Settlement ",
                "has_popup" => false,
                "show_crud_in_modal" => false,
                "bulk_update" => json_encode([
                    "paid_status" => [
                        "label" => "Set Paid",
                        "data" => getListFromIndexArray(["Paid", "Pending"]),
                    ],
                ]),
            ];
            return view("admin.vendor_settlements.index", $view_data);
        }
    }

      public function completed_orders(Request $request)
    {
       

        $view_columns = [
           
        ];
        $table_columns = [
            [
                "column" => "vendor_id",
                "label" => "Seller Name",
                "sortable" => "Yes",
            ],
            [
                "column" => "order_id",
                "label" => "Order Id",
                "sortable" => "Yes",
            ],

            [
                "column" => "vendor_total",
                "label" => "Total Order Amount(Rs.)",
                "sortable" => "Yes",
            ],
            [
                "column" => "net_profit",
                "label" => "Earning(Rs.)",
                "sortable" => "Yes",
            ],
            [
                "column" => "paid_status",
                "label" => "Paid Status",
                "sortable" => "Yes",
            ],
            [
                "column" => "delivery_status",
                "label" => "Order Status",
                "sortable" => "Yes",
            ],
            [
                "column" => "is_settled",
                "label" => "Settle Status",
                "sortable" => "Yes",
            ],
           
        ];
        $filterable_fields = [
            [
                "name" => "created_at",
                "label" => "Created At",
                "type" => "date",
            ],
            [
                'name' => 'is_settled',
                'label' => 'Settled Status ',
                'type' => 'select',
                'options' => getListFromIndexArray(['Pending','Settled']),
            ],
        ];
        $searchable_fields = [
            [
                "name" => "order_id",
                "label" => "Order Id",
                "type" => "text",
            ],
           
        ];
        $model_relations = [
            [
                "name" => "vendor",
                "type" => "BelongsTo",
                "save_by_key" => "",
                "column_to_show_in_view" => "name",
            ],
            [
                'name' => 'order',
                'type' => 'BelongsTo',
                'save_by_key' => '',
                'column_to_show_in_view' => 'name'
            ],
        ];
        $this->pagination_count = 100;
        if ($request->ajax()) {
            $sort_by = $request->get("sortby");
            $sort_type = $request->get("sorttype");
            $search_by = $request->get("search_by");

            $query = $request->get("query");

            $search_val = str_replace(" ", "%", $query);
            if (empty($search_by)) {
                $search_by = "name";
            }
            if ($search_by == "order_id") {
                $order = \DB::table("orders")
                    ->where("uuid", $search_val)
                    ->first();
                $search_val = $order ? $order->id : $search_val;
            }
            $list = \App\Models\VendorOrder::with(array_column($model_relations, 'name'))->when(
                !empty($search_val),
                function ($query) use ($search_val, $search_by) {
                    return $query->where(
                        $search_by,
                        "like",
                        "%" . $search_val . "%"
                    );
                }
            )
              
                ->when(!empty($sort_by), function ($query) use (
                    $sort_by,
                    $sort_type
                ) {
                    return $query->orderBy($sort_by, $sort_type);
                })
                ->where("status", "Success")
                // ->where("is_completed", "Yes")
                ->latest()
                ->paginate($this->pagination_count);
            $data = [
                "table_columns" => $table_columns,
                "list" => $list,
                "searchable_fields" => $searchable_fields,
                "filterable_fields" => $filterable_fields,
                "plural_lowercase" => "vendor_order",
                "sort_by" => $sort_by,
                "sort_type" => $sort_type,
                "storage_folder" => "",
                "plural_lowercase" => "vendor_order",
                "module" => "VendorOrder",
                "has_image" => 0,
                "model_relations" => [],
                "image_field_names" => [],
                "crud_title" => "Vendor Order",
                "has_popup" => false,
                "show_crud_in_modal" => false,
                "bulk_update" => json_encode([
                    "is_settled" => [
                        "label" => "Settle Order",
                        "data" => getListFromIndexArray(["Yes", "No"]),
                    ],
                ]),
            ];
            return view("admin.orders.page", with($data));
        } else {
            $query = null;

            $query = \App\Models\VendorOrder::with(array_column($model_relations, 'name'))->where("status", "Success");
            // ->where("is_completed", "Yes");

            $query = $this->buildFilter($request, $query);
            $list = $query->latest()->paginate($this->pagination_count);

            $view_data = [
                "list" => $list,

                "title" => "Vendor Order",
                "searchable_fields" => $searchable_fields,
                "filterable_fields" => $filterable_fields,
                "plural_lowercase" => "vendor_order",
                "table_columns" => $table_columns,
                "module_table_name" => "vendor_orders",

                "model_relations" => $model_relations,
                "module" => "VendorOrder",
                "crud_title" => "Vendor Order",
                "has_popup" => false,
                "show_crud_in_modal" => false,
                "bulk_update" => json_encode([
                    "is_settled" => [
                        "label" => "Settle Order",
                        "data" => getListFromIndexArray(["Yes", "No"]),
                    ],
                ]),
            ];
           
            return view("admin.orders.completed_orders", $view_data);
        }
    }
}
