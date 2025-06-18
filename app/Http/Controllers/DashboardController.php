<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use DB;
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
;
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

 
    }

         return view('admin.dashboard', with($data));
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
        $data['city_name']=\DB::table('city')->where('state_id',$data['me']->state_id)->first()->name;
              $data['cities']=\DB::table('city')->where('state_id',$data['me']->state_id)->get();

        return view('admin.profile_setting',with($data));
    }
    /*
public function buildFilter(Request $r, $query)
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

public function company_ledger(Request $request)
{
$table_columns = [
[
'column' => 'name',
'label' => 'Title',
'sortable' => 'Yes',
],
[
'column' => 'amount',
'label' => 'Amount',
'sortable' => 'Yes',
],
[
'column' => 'mode',
'label' => 'Income/Expense',
'sortable' => 'Yes',
],
[
'column' => 'created_at',
'label' => 'Date',
'sortable' => 'Yes',
],

];
$filterable_fields = [
[
'name' => 'created_at',
'label' => 'Created At',
'type' => 'date',
],
[
'name' => 'mode',
'label' => 'Expense/Income',
'type' => 'select',
'options'=>getListFromIndexArray(['Income','Spent'])
],
];
$searchable_fields = [
[
'name' => 'name',
'label' => 'Title',
'type' => 'text',
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

$list = \App\Models\CompanyLedger::when(!empty($search_val), function ($query) use ($search_val, $search_by) {
return $query->where($search_by, 'like', '%' . $search_val . '%');
})
->when(!empty($sort_by), function ($query) use ($sort_by, $sort_type) {
return $query->orderBy($sort_by, $sort_type);
})->latest()->paginate($this->pagination_count);
$data = [
'table_columns'=> $table_columns,
'list'=>$list,
'sort_by'=> $sort_by,
'sort_type'=> $sort_type,
'storage_folder'=>'',
'plural_lowercase'=>'company_ledger',
'module'=>'ComapnyLedger',
'has_image'=>0,
'model_relations'=>[],
'image_field_names'=>[],

];
return view('admin.company_ledger_page', with($data));
} else {

$query = null;

$query = \App\Models\CompanyLedger::query();

$query = $this->buildFilter($request, $query);
$list = $query->latest()->paginate($this->pagination_count);

$view_data = [
'list' => $list,

'title' => 'Company Ledger',
'searchable_fields' => $searchable_fields,
'filterable_fields' => $filterable_fields,

'table_columns' => $table_columns,

];
return view('admin.company_ledger', $view_data);
}

}
public function exportLedger(Request $request, $type)
{
$filter = [];
$filter_date = [];
$date_field = null;
foreach ($_GET as $key => $val) {
if(!empty($val)){
if (str_contains($key, 'start_')) {
$date_field = str_replace('start_', '', $key);
$filter_date['min'] = $val;
} else if (str_contains($key, 'end_')) {
$date_field = str_replace('end_', '', $key);
$filter_date['max'] = $val;
} else {
$filter[$key] = $val;
}
}

}
if ($type == 'excel') {
return Excel::download(new \App\Exports\LedgerExport([], $filter, $filter_date, $date_field), 'ledger_report' . date("Y-m-d H:i:s") . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
}

if ($type == 'csv') {
return Excel::download(new \App\Exports\LedgerExport([], $filter, $filter_date, $date_field), 'ledger_report' . date("Y-m-d H:i:s") . '.csv', \Maatwebsite\Excel\Excel::CSV);
}

}
 */

}
