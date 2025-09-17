<?php
use Illuminate\Support\Facades\Http as Http;
use Illuminate\Support\Facades\Log as Log;
use Intervention\Image\ImageManager;
use \Carbon\Carbon as Carbon;
use Intervention\Image\Drivers\Imagick\Driver;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Smalot\PdfParser\Parser;
function extractTextValueFromPdfInStorage($pdfPath,$which_text_value_to_extract)
{
     if (!file_exists($pdfPath)) {
        throw new \Exception("PDF file not found at: $pdfPath");
    }
    $parser = new Parser(); 
    $pdf = $parser->parseFile($pdfPath);
    $text = $pdf->getText();

   $label = preg_quote($which_text_value_to_extract, '/');

    if (preg_match('/' . $label . ':\s*([^\n]+)/i', $text, $matches)) {
        return trim($matches[1]);
    }

    return null; // Not found
}
function extractTextValueFromPdfUrl($url,$which_text_value_to_extract)
{
   
    $pdfContent = file_get_contents($url);
   // dd($pdfContent);
    if (!$pdfContent) {
        throw new \Exception('Failed to load PDF from URL');
    }

    // Parse from string content
    $parser = new Parser();
    $pdf = $parser->parseContent($pdfContent);
    $text = $pdf->getText();

    $text = preg_replace('/[\x{00A0}\x{2000}-\x{200B}\x{202F}\x{205F}\x{3000}]/u', ' ', $text);

    // Split into lines and search
    $lines = preg_split('/\r\n|\r|\n/', $text);
//   dd($text);
    foreach ($lines as $line) {
      
        if (stripos($line,$which_text_value_to_extract) !== false) {
             // dd($line);
            // Found a line like: " Routing Code: NA"
            $parts = explode(':', $line, 2);
            if (count($parts) === 2) {
                return trim($parts[1]);
            }
        }
    }
    return null; // Routing code not found
}
function getFriendlyShipmentStatus(string $status): string {
     $orderConfirmed = ['AWB Assigned', 'Label Generated', 'Manifest Generated', 'Shipment Booked','Ordered'];
        $pickupScheduled = ['Pickup Scheduled', 'Pickup Queued', 'Pickup Rescheduled', 'Picked Up', 'Out For Pickup'];
        $inTransit = ['Shipped', 'In Transit', 'In Flight', 'In Transit Overseas', 'Connection Aligned', 'Reached at Destination', 'Reached Warehouse', 'Reached Overseas Warehouse', 'Handover to Courier'];
        $outForDelivery = ['Out For Delivery', 'OFD', 'Out For Delivery'];
        $delivered = ['Delivered', 'Fulfilled', 'Partial_Delivered'];
        $returnRto = ['RETURN PICKUP GENERATED', 'RETURN OUT FOR PICKUP','RETURN DELIVERED' ,'RETURN IN TRANSIT', 'Return Completed'];
        // $exchangeRto = ['EXCHANGE PICKUP GENERATED', 'RETURN OUT FOR PICKUP','RETURN DELIVERED' 'RRETURN IN TRANSIT', 'Return Completed'];
        $cancelledFailed = ['Cancelled', 'Cancellation Requested', 'CANCELLED_BEFORE_DISPATCHED', 'Lost', 'Pickup Error', 'Pickup Exception', 'Undelivered', 'Delayed', 'Damaged', 'Destroyed', 'QC Failed', 'DISPOSED_OFF'];
        $customsOthers = ['Custom Cleared', 'Custom Cleared Overseas', 'Box Packing', 'Self Fulfilled', 'Pending', 'Shipment Booked', 'Misrouted'];
     
    if (in_array($status, $orderConfirmed)) return 'Order Confirmed';
    if (in_array($status, $pickupScheduled)) return 'Pickup Scheduled';
    if (in_array($status, $inTransit)) return 'In Transit';
    if (in_array($status, $outForDelivery)) return 'Out For Delivery';
    if (in_array($status, $delivered)) return 'Delivered';
    if (in_array($status, $returnRto)) return 'Return / RTO';
    if (in_array($status, $cancelledFailed)) return 'Cancelled / Failed';
    if (in_array($status, $customsOthers)) return $status;

    // fallback
    return $status;
       
      
    
}
function registerCrudRoutes(string $uri, string $controller, array $options = [],$singular=null,$namePrefix = '',)
{
    $routeBaseName =$uri;
    $fullPrefix = $namePrefix ? $namePrefix . '.' : '';

    Route::resource($uri, $controller)->names([
        'index' => $fullPrefix . $routeBaseName . '.index',
        'create' => $fullPrefix . $routeBaseName . '.create',
        'store' => $fullPrefix . $routeBaseName . '.store',
        'show' => $fullPrefix . $routeBaseName . '.show',
        'edit' => $fullPrefix . $routeBaseName . '.edit',
        'update' => $fullPrefix . $routeBaseName . '.update',
        'destroy' => $fullPrefix . $routeBaseName . '.destroy',
    ]);

    if (!empty($options['custom'])) {
        foreach ($options['custom'] as $custom) {
            $method = $custom['method'];
            $uriCustom = $custom['uri'];
            $action = $custom['action'];
            $name = isset($custom['name']) ? $fullPrefix . $custom['name'] : null;

            Route::$method($uriCustom, [$controller, $action])->name($name);
        }
    }

    if (isset($options['export'])) {
        if($singular)
        Route::get("$uri/export", [$controller, $options['export']])->name($fullPrefix . $singular . '.export');
       else
        Route::get("$uri/export", [$controller, $options['export']])->name($fullPrefix . $routeBaseName . '.export');
    }
}
function domain_route($name, $parameters = [], $absolute = true) {
    $host = request()->getHost();

    if (str_contains($host, 'admin')) {
        $name = 'admin.' . $name;
    } elseif (str_contains($host, 'vendor')) {
        $name = 'vendor.' . $name;
    }

    return route($name, $parameters, $absolute);
}
function route_prefix() {
    $host = request()->getHost();
$name="";
    if (str_contains($host, 'admin')) {
        $name = 'admin.';
    } elseif (str_contains($host, 'vendor')) {
        $name = 'vendor.';
    }

    return $name;
}
function getFinalShipmentDimensionsAndWeight($items) {
    $actualWeight = 0;
    $totalVolume = 0;

    $maxLength = 0;
    $maxBreadth = 0;
    $stackedHeight = 0;

    foreach ($items as $item) {
        $dim=$item->product->package_dimension;
        $dimesion=$dim?json_decode($dim,true):null;
        if($dimesion){
        $itemVolume = $dimesion['length'] * $dimesion['width'] * $dimesion['height'];
        $totalVolume += $itemVolume * $item['qty'];

        $actualWeight += $dimesion['weight'] * $item['qty'];

        // For simplified dimensional assumption: stacking
        $maxLength = max($maxLength, $dimesion['length']);
        $maxBreadth = max($maxBreadth, $dimesion['width']);
        $stackedHeight += $dimesion['height'] * $item['qty'];
        }
    }

    // Estimate overall package dimensions
    $estimatedLength = $maxLength;
    $estimatedBreadth = $maxBreadth;
    $estimatedHeight = $stackedHeight;

    // Calculate volumetric weight
    $volumetricWeight = ($estimatedLength * $estimatedBreadth * $estimatedHeight) / 5000;

    // Determine final chargeable weight
    $finalWeight = roundUpToHalf(max($actualWeight, $volumetricWeight));
    return    [
            'width'  =>18,
            'breadth'  =>18,
            'height' => 3,
            'length' => 20,
            'weight' =>0.4,
        ];
    // return [
    //     'weight'   => $finalWeight,
    //     'length'   => ceil($estimatedLength),
    //     'breadth'  => ceil($estimatedBreadth),
    //     'height'   => ceil($estimatedHeight),
    //     'actual_weight'     => $actualWeight,
    //     'volumetric_weight' => $volumetricWeight
    // ];
}
function roundUpToHalf($number)
{
    return ceil($number * 2) / 2;
}
function SendSms($toNumber, $otp)
{
    $senderId = null;
    $userId = null;
    $templateId = null;
    $entityId = null;
    
    $senderId = 'OTPSSS';
    $userId = 'colourind';
    $templateId = '1207161729866691748';
    $entityId = '1201159543060917386';
    $msg ='Dear Customer,Your OTP is '.$otp.' for Colour Indigo, Please do not share this OTP. Regards';
    $key = '34b4d5f77fXX';
   

    $response = Http::get('http://sms.bulkssms.com/submitsms.jsp?user=' . $userId . '&key=' . $key . '&mobile=' . $toNumber . '&message=' . $msg . '&senderid=' . $senderId . '&accusage=1&entityid=' . $entityId . '&tempid=' . $templateId);
    if (!str_contains($response->body(), 'success')) {
        \DB::table('sms_error')->insert(['error_msg' => $response->body()]);
    }

    return $response;

}
if (!function_exists('baseUrl')) {
    function baseUrl()
    {
        return config('app.base_url');
    }
}
function colors(){
    $colors = [
        "White", "Black", "Red", "Green", "Blue", "Yellow", "Cyan", "Magenta",
        
        // Shades of Red
        "LightCoral", "Salmon", "DarkSalmon", "LightSalmon", "Crimson", "FireBrick", "DarkRed",
        
        // Shades of Green
        "Lime", "LimeGreen", "ForestGreen", "DarkGreen", "Olive", "OliveDrab", "SpringGreen",
        "MediumSpringGreen", "SeaGreen", "MediumSeaGreen", "PaleGreen", "LightGreen",
        
        // Shades of Blue
        "DodgerBlue", "MediumBlue", "RoyalBlue", "SteelBlue", "LightSteelBlue", "CornflowerBlue",
        "SlateBlue", "MediumSlateBlue", "DarkSlateBlue", "LightBlue", "SkyBlue", "DeepSkyBlue",
        "LightSkyBlue", "PowderBlue",
        
        // Shades of Yellow
        "Gold", "YellowGreen", "Chartreuse", "OliveYellow", "LightYellow",
        
        // Shades of Orange
        "DarkOrange", "OrangeRed", "Coral", "Tomato", "Orchid",
        
        // Shades of Purple
        "Purple", "BlueViolet", "Indigo", "Violet", "MediumOrchid", "MediumPurple", "DarkOrchid", "SlateBlue",
        
        // Shades of Brown
        "SaddleBrown", "Sienna", "Chocolate", "Peru", "BurlyWood", "RosyBrown", "Tan",
        
        // Shades of Gray
        "Gray", "LightGray", "DarkGray", "DimGray", "Gainsboro", "Silver", "SlateGray", "LightSlateGray",
        
        // Others
        "Beige", "AntiqueWhite", "Linen", "Lavender", "Thistle", "Plum", "MistyRose", "Azure",
        "AliceBlue", "PapayaWhip", "BlanchedAlmond"
    ];
    return $colors;
}
function dlog($append, $data)
{
    Log::info($append . '{id}', ['id' => $data]);
}
function getLastDayOfMonth($date = null)
{
    return $date ? date("d", strtotime(date("Y-m-t", strtotime($date)))) : date("d", strtotime(date("Y-m-t")));
}
function monthIndexFromName($name)
{
    $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    return array_search($months, $name);
}
function monthsArray()
{
    return ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

}
function current_role(): string
{
   $roles=auth()->user()?auth()->user()->getRoleNames():auth()->guard('vendor')->user()->getRoleNames();
   return $roles->isNotEmpty() ? $roles[0] : null;
}
function current_user()
{
  return auth()->user()?auth()->user():auth()->guard('vendor')->user();
  
}
function schoolSession()
{
    $session = '';
    $current_month = intval(date("m"));

    $data['session_start_month'] = \DB::table('setting')->first()->session_start_month;

    if ($current_month > $data['session_start_month']) {
        $session = date('Y') . '-' . (intval(date('y')) + 1);
    } else {
        $session = (intval(date('y')) - 1) . '-' . date('Y');
    }
    return $session;
}
if (!function_exists('adminUrl')) {
    function adminUrl()
    {
        return config('app.admin_subdomain') . '.' . baseUrl();
    }
}

if (!function_exists('formateDate')) {
    function formateDate($v, $show_time = false)
    {
        return strlen($v) > 2 ? ($show_time ? date('j M,Y h:i a', strtotime($v)) : date('j M,Y', strtotime($v))) : '';
    }
}
if (!function_exists('formateNumber')) {
    function formateNumber($v, $decimal = 2)
    {
        return strlen($v) > 0 ? number_format($v, $decimal) : '';
    }
}
if (!function_exists('showInInr')) {
    function showInInr($v)
    {
        return '₹' . formateNumber($v);
    }
}
function getFieldById($model, $id, $field)
{
    $mod = app("App\\Models\\$model");
    $model = $mod->find($id);
    return $model->{$field};

}
function getCount($table, $where = [])
{
    return count($where) > 0?\DB::table($table)->where($where)->count() : \DB::table($table)->count();

}
function getArrayFromModel($model, $fields_label = [])
{
    $mod = app("App\\Models\\$model");
    $fields = $mod->getFillable();
    return array_combine($fields_label, $fields);

}
function getNameToIdPairFromModel($model, $array_names = [])
{
    $mod = app("App\\Models\\$model");
    return $mod->whereIn('name', $array_names)->get(['id', 'name'])->toArray();

}
function extractNameOnlyAsArray($val = [])
{

    return array_map(function ($v) {
        return explode("-", $v)[1];

    }, $val);

}
function convertToKeyValPair($val = [])
{

    return array_map(function ($v) {
        $r = explode("-", $v);
        return ["id" => $r[0], "key" => $r[1]];

    }, $val);

}
function renderSelectOptionsFromJsonCOlumnOfIdNamePair($json_val = [])
{

    return array_map(function ($v) {
        $r = explode("-", $v);
        return [$r[0] => $r[1]];

    }, $val);

}
function properPluralName($str)
{

    return ucwords(str_replace('_', ' ', $str));

}
function properSingularName($str)
{
    $g = ucwords(str_replace('_', ' ', \Str::singular($str)));
    $g = str_replace(' Id', ' ', $g);
    return $g;

}
function modelName($str)
{
    $str = \Str::singular($str);
    $spl = explode('_', $str);
    $spl = array_map(function ($v) {
        return ucfirst($v);
    }, $spl);
    $new_str = implode('', $spl);
    return $new_str;

}
function isFieldBelongsToManyToManyRelation($rel_ar, $field)
{
    $found = -1;
    $i = 0;
    foreach ($rel_ar as $item) {
        if ($field == $item['name'] && $item['type'] == 'BelongsToMany') {
            $found = $i;
            break;
        }
        $i++;
    }
    return $found;
}
function isFieldBelongsToHasManyRelation($rel_ar, $field)
{
    $found = -1;
    $i = 0;
    foreach ($rel_ar as $item) {
        if ($field == $item['name'] && $item['type'] == 'HasMany') {
            $found = $i;
            break;
        }
        $i++;
    }
    return $found;
}
function isFieldPresentInRelation($rel_ar, $field)
{
    $found = -1;
    $i = 0;
    foreach ($rel_ar as $item) {
        if (($item['name'] == $field) || ($item['name'] . '_id' == $field)) {
            $found = $i;

            break;
        }
        $i++;
    }
    return $found;
}
function findCategoryWithAttributes($categoryId)
{
    while ($categoryId) {
        // Check if this category has attributes
        $hasAttributes = \DB::table('facet_attributes')
            ->where('category_id', $categoryId)
            ->exists();

        if ($hasAttributes) {
            return $categoryId; // Found a category with attributes
        }

        // Get parent_id of current category
        $category = \DB::table('categories')->where('id', $categoryId)->first();

        if (!$category || !$category->category_id) {
            break; // No more parents to check
        }

        $categoryId = $category->category_id; // Go to parent and repeat
    }

    return null; // No category in the chain has attributes
}
function isFieldPresentInRelationTableAsColumn($rel_ar, $field)
{
    $found = -1;
    $i = 0;
    foreach ($rel_ar as $item) {
        $class = $item['class'];
        $table = app($class)->getTable();
        $columns = \DB::getSchemaBuilder()->getColumnListing($table);

        if (in_array($field, $columns)) {
            $found = $i;

            break;

            $i++;
        }
    }
    return $found;
}
function isFieldPresentInTableAsColumn($model_class, $field)
{
    $table = app($model_class)->getTable();
    $columns = \DB::getSchemaBuilder()->getColumnListing($table);

    $found = -1;

    if (in_array($field, $columns)) {
        $found = 1;

    }

    return $found;
}

function getForeignKeyFieldValue($rel_ar, $row, $field)
{

    $resp = '';

    // $item['name'] = 'category';
    // $field = 'category_id';
    foreach ($rel_ar as $item) {
        $field = $field == $item['name'] . '_id' ? $item['name'] : $field; //here field is column which is accosciated to other table $row->state->name //here state is file name is key to get
        //get_by_field===mens relatioship table ka kunsa field access karna hai ye cheej $key_toget_as_per_relation array mein relation name ke saath dikhatee hai ;
        $get_by_field = isset($item['column_to_show_in_view']) ? $item['column_to_show_in_view'] : 'name';
        //dd($field.'==='.$item['name']);
        if ($field == $item['name']) {

            if ($item['type'] == 'BelongsTo' || $item['type'] == 'HasOne') {
                $resp = $row->{$field} ? $row->{$field}->{$get_by_field} : '';
            } elseif ($item['type'] == 'HasMany' || $item['type'] == 'ManyToMany' || $item['type'] == 'BelongsToMany') {

                if ($row->{$field}) {
                    if (count($row->{$field}->toArray()) > 0) {

                        $val_ar = array_column($row->{$field}->toArray(), $get_by_field);
                        $resp = showArrayInColumn($val_ar);

                    }
                }

            }
        }
    }
    return $resp;
}

function getModelRelations($model)
{
    $model = app("App\\Models\\$model");
    $reflector = new \ReflectionClass($model);
    $relations = [];
    foreach ($reflector->getMethods() as $reflectionMethod) {
        $returnType = $reflectionMethod->getReturnType();

        if ($returnType) {
            $type = class_basename($returnType->getName());
            if (in_array($type, ['HasOne', 'HasMany', 'BelongsTo', 'BelongsToMany', 'MorphToMany', 'MorphTo'])) {
                $t = (array) $reflectionMethod;
                $t['type'] = $type;
                $t['save_by_key'] = '';
                $t['column_to_show_in_view'] = 'name';
                if (in_array($type, ['HasMany'])) {
                    $t['save_by_key'] = 'name';
                }
/**can be change in contrller name to other column  */
                unset($t['class']);
                $relations[] = $t;
            }
        }
    }
    return $relations;
}

function getAllModels()
{
    $path = app_path() . "/Models";
    $out = [];
    $results = scandir($path);
    foreach ($results as $result) {
        if ($result === '.' or $result === '..') {
            continue;
        }

        $filename = $result;
        $out[] = substr($filename, 0, -4);

    }
    $out[] = 'Self';
    return $out;

}
function getTables()
{
    return array_map('current', \DB::select('SHOW TABLES'));
}
function getPivotTableName($model1, $model2)
{
    $t = [$model1, $model2];
    sort($t);

    return implode('_', $t);

}
function isJSON($string)
{
    return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
}
function formatPostForJsonColumn($post)
{

    $json_cols = []; /****like storing first part in json field */
    $json_keys = []; /****like storing last part in json field */
    $ar_val = [];
    $no_of_values_in_arr = 0;
    foreach ($post as $key => $val) {
        if (is_array($post[$key])) {
            if (count($post[$key]) > 0) {
                if (str_contains($key, '__json__')) {

                    $spl = explode("__json__", $key);
                    $col_name = $spl[0];
                    $key_name = $spl[1];

                    // $no_of_values_in_arr = count($post[$key]);
                    if (!isset($post[$col_name])) {

                        $json_cols[] = $col_name;

                        $json_keys[] = $key_name; /***like storing size */
                    } else { /****kyunki json column toggle mein bhi hota hai to unko unset karo psot se  */
                        $val = $post[$key];
                        unset($post[$key]);
                        $post[$key_name] = $val[0]; /*toggable div ke case mein ham kewal first val store kara rahe hai not array u can change***/
                    }

                } else { /***if key is index array  */
                    $post[$key] = json_encode($post[$key]);
                }
            } else { /***if key val is empty  */
                $post[$key] = null;
            }

        }
    }

    $json_cols = array_unique($json_cols);

    foreach ($json_cols as $colname) {
        if (count($json_keys) > 0) {

            $p = [];
            foreach ($json_keys as $key) {

                if (isset($post[$colname . '__json__' . $key])) {
                    $values = $post[$colname . '__json__' . $key];
                    $p[$key] = $values;
                }
            }
            $ar_val[$colname][] = $p;

        }

    }
   //dd($ar_val );
    if (count($ar_val) > 0) {
        foreach ($ar_val as $key => $val) {
            $keys = array_keys($val[0]);
        //    dd($val);
            $val_count = count($val[0][$keys[0]]);

            $t = [];
            for ($i = 0; $i < ($val_count); $i++) {
                $x = [];
              
                foreach ($keys as $k) {
                    //echo "<pre>";print_r();
                    $x[$k] = array_key_exists($i,$val[0][$k])?$val[0][$k][$i]:null;

                }
                $t[] = $x;
            }
            // dd($t);
            $post[$key] = json_encode($t);

        }
    }
//dd($post);
    return $post;
}

function showArrayWithNamesOnly($ar)
{
    $str = '';
    foreach ($ar as $t) {
        $str .= ',' . $t['name'];
    }
    return ltrim($str, ',');
}
function showArrayInColumn($arr = [], $row_index = 0, $by_json_key = 'id', $size = 'md', $title = '', $show_delete = false,
    $delete_data_info = ['row_id_val' => '', 'table' => '', 'json_column_name' => '', 'delete_url' => '']) {
    if (!empty($arr)) {
        if (!is_array($arr)) {
            $arr = $arr->toArray();
        }

        if (isset($arr[0]) && !is_array($arr[0])) {

            return implode(',', $arr);
        } elseif (!isArrayEmpty($arr)) {

            $keys = array_keys($arr[0]);
            $header = '<tr>';
            foreach ($keys as $k) {
                if (!str_contains($k, '_id') && $k != $by_json_key) {
                    $k = str_replace('_', ' ', $k);
                    $k = ucwords($k);
                    $header .= '<th>' . $k . '</th>';
                }

            }
            if ($show_delete) {
                $header .= '<th>Action</th>';
            }

            $header .= '</th>';
            $body = '';
            $i = 0;
            foreach ($arr as $val) {

                $i = isset($by_json_key) && !empty($by_json_key) && isset($val[0]) ? intval($val[0][$by_json_key]) : $i + 1;
                $body .= '<tr  id="row-' . $i . '">';
                foreach ($val as $k => $v) {
                    if (!str_contains($k, '_id') && $k != $by_json_key) {
                        $body .= '<td style="white-space: -o-pre-wrap;
                                        word-wrap: break-word;
                                        white-space: pre-wrap;
                                        white-space: -moz-pre-wrap;
                                        white-space: -pre-wrap; ">' . $v . '</td>';
                    }
                }
                if ($show_delete) {$body .= <<<STR
                        <td><button class="btn btn-xs btn-danger" onClick="deleteJsonColumnData({$delete_data_info["row_id_val"]},'{$by_json_key}','{$delete_data_info["table"]}','{$val[$by_json_key]}','{$delete_data_info["json_column_name"]}','{$delete_data_info["delete_url"]}')"><i class="bx bx-trash"></i></button></td>
                        STR;
                }

                $body .= '</tr>';

            }

            $str = '<button type="button" class="btn-sm btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal' . $row_index . '">
          View
        </button>
        <div class="modal detail" id="myModal' . $row_index . '">
          <div class="modal-dialog modal-' . $size . '">
            <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title">' . ucwords($title) . '</h5></div>
              <div class="modal-body">
              <div class="table-responsive">
              <table class="table table-bordered" >
              <thead>
              ' . $header . '
              </thead>
              <tbody>' . $body . '</tbody>
              </table>
              </div>
              </div>

              <!-- Modal footer -->
              <div class="modal-footer">
                <button type="button" class="btn-sm btn btn-danger" data-bs-dismiss="modal">Close</button>
              </div>

            </div>
          </div>
        </div>';
            return $str;
        }
    } else {
        return '';
    }

}
function showArrayInColumnNotButtonForm($arr = [], $row_index = 0, $by_json_key = 'id', $size = 'md', $title = '', $show_delete = false,

    $delete_data_info = ['row_id_val' => '', 'table' => '', 'json_column_name' => '', 'delete_url' => '']) {

    if (!empty($arr)) {
        if (!is_array($arr)) {
            $arr = $arr->toArray();
        }

        if (isset($arr[0]) && !is_array($arr[0])) {

            return implode(',', $arr);
        } elseif (!isArrayEmpty($arr)) {

            $keys = array_keys($arr[0]);

            $header = '<tr>';
            foreach ($keys as $k) {
                if (!str_contains($k, '_id') && $k != $by_json_key) {
                    $k = str_replace('_', ' ', $k);
                    $k = ucwords($k);
                    $header .= '<th>' . $k . '</th>';
                }

            }

            if ($show_delete) {
                $header .= '<th>Action</th>';
            }

            $header .= '</th>';
            $body = '';
            $i = 0;
            foreach ($arr as $val) {

                $i = isset($by_json_key) && !empty($by_json_key) && isset($val[0]) ? intval($val[0][$by_json_key]) : $i + 1;
                $body .= '<tr  id="row-' . $i . '">';
                foreach ($val as $k => $v) {
                    if (!str_contains($k, '_id') && $k != $by_json_key) {
                        $body .= '<td style="white-space: -o-pre-wrap;
                                        word-wrap: break-word;
                                        white-space: pre-wrap;
                                        white-space: -moz-pre-wrap;
                                        white-space: -pre-wrap; ">' . $v . '</td>';
                    }
                }
                if ($show_delete) {$body .= <<<STR
                        <td><button class="btn btn-xs btn-danger" onClick="deleteJsonColumnData({$delete_data_info["row_id_val"]},'{$by_json_key}','{$delete_data_info["table"]}','{$val[$by_json_key]}','{$delete_data_info["json_column_name"]}','{$delete_data_info["delete_url"]}')"><i class="bx bx-trash"></i></button></td>
                        STR;
                }

                $body .= '</tr>';

            }

            $str = '<div class="table-responsive">
              <table class="table table-bordered" >
              <thead>
              ' . $header . '
              </thead>
              <tbody>' . $body . '</tbody>
              </table>
              </div>
             ';
            return $str;
        }
    } else {
        return '';
    }

}

function showArrayInColumnNoPopup($arr = [], $by_json_key = 'id', $show_delete = false,
    $delete_data_info = ['row_id_val' => '', 'table' => '', 'json_column_name' => '', 'delete_url' => '']) {
    if (!empty($arr)) {
        if (!is_array($arr)) {
            $arr = $arr->toArray();
        }

        if (!is_array($arr[0])) {

            return implode(',', $arr);
        } elseif (isset($arr[0]) && !is_array($arr[0])) {

            return implode(',', $arr);
        } elseif (!isArrayEmpty($arr)) {

            $keys = array_keys($arr[0]);
            $header = '<tr>';
            foreach ($keys as $k) {
                if (!str_contains($k, '_id') && $k != $by_json_key) {
                    $header .= '<th>' . $k . '</th>';
                }

            }
            if ($show_delete) {
                $header .= '<th>Action</th>';
            }

            $header .= '</th>';
            $body = '';
            $i = 0;
            foreach ($arr as $val) {

                $i = isset($by_json_key) && !empty($by_json_key) && isset($val[0]) ? intval($val[0][$by_json_key]) : $i + 1;
                $body .= '<tr  id="row-' . $i . '">';
                foreach ($val as $k => $v) {
                    if (!str_contains($k, '_id') && $k != $by_json_key) {
                        $body .= '<td style="white-space: -o-pre-wrap;
                                        word-wrap: break-word;
                                        white-space: pre-wrap;
                                        white-space: -moz-pre-wrap;
                                        white-space: -pre-wrap; ">' . $v . '</td>';
                    }
                }
                if ($show_delete) {$body .= <<<STR
                        <td><button class="btn btn-xs btn-danger" onClick="deleteJsonColumnData({$delete_data_info["row_id_val"]},'{$by_json_key}','{$delete_data_info["table"]}',{$val[$by_json_key]},'{$delete_data_info["json_column_name"]}','{$delete_data_info["delete_url"]}')"><i class="bx bx-trash"></i></button></td>
                        STR;
                }

                $body .= '</tr>';

            }

            $str = '<div class="table-responsive">
              <table class="table table-bordered" >
              <thead>
              ' . $header . '
              </thead>
              <tbody>' . $body . '</tbody>
              </table>
              </div>';
            return $str;
        }
    } else {
        return '';
    }

}
function showArrayInColumnPlainText($ar)
{

    $str = '';
    if (is_array($ar)) {
        //   dd($ar);

        $keys = array_keys($ar[0]);
        foreach ($ar as $item) {
            foreach ($keys as $k) {

                $str .= $k . '-' . $item[$k] . ',';
            }

        }

    }
    return $str;

}
function fieldExist($model, $field_name, $value)
{
    $mod = app("App\\Models\\$model");
    return $mod->where($field_name, $value)->exists();
}
function tinifyImage($path)
{
    // \Tinify\setKey("BFy3vXxC1njTCnRqT4Bxq06Mk9g8Bgmq");
    // \Tinify\fromFile($path)->toFile($path);
}
/***Storage app folder default location for storeAS */
function convertToWebp($requestFile,$seoFileNameWithoutExtension){
     if (!@getimagesize($requestFile->getRealPath())) {
        return response()->json(['error' => 'Uploaded image is not valid.'], 422);
    }
      $manager = new ImageManager(Driver::class,autoOrientation: true);
     $filename =$seoFileNameWithoutExtension?$seoFileNameWithoutExtension: uniqid();
     $webpimage = $manager->read($requestFile->getPathname())
                    ->encode('webp', 80);
    $webpFilename = $filename . '-converted.webp';

}
// function storeSingleFile($folder, $filerequest,$generateThumbnail = false,$seoFileNameWithoutExtension)
// {
    
//     $folder = str_replace('\\', '/', $folder);
//     $filenameWithExt = $filerequest->getClientOriginalName();
//     $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
//     $extension = $filerequest->getClientOriginalExtension();
//      if ( $extension!= 'webp') {
//        convertToWebp($filerequest,$seoFileNameWithoutExtension);
//     }
//     $filename = uniqid();
//     $fileNameToStore = $filename . '.' . $extension;
//     $path = $filerequest->storeAs('public/' . $folder, $fileNameToStore);
//     chmod(Storage::path('public/' . $folder), 0755);
//      if ($generateThumbnail) {
//         generateThumbnail($filerequest, $folder, getThumbnailDimensions(), $filename, $extension);
//     }
//     return $fileNameToStore;
// }

function storeSingleFile($folder,$filerequest, $generateThumbnail = false,$thumbnailDimensions=[], $seoFileNameWithoutExtension = null)
{
    $folder = str_replace('\\', '/', $folder);
    $originalExtension = strtolower($filerequest->getClientOriginalExtension());
    $mimeType = $filerequest->getMimeType();

   
    // Handle video files (e.g. mp4, webm)
    if (str_starts_with($mimeType, 'video/')) {
        $name=$filerequest->getClientOriginalName();
        $fileNameToStore = $name;

        // Store the video directly without conversion
        $filerequest->storeAs('public/' . $folder, $fileNameToStore);

        // No thumbnail generation for video in this function
        return $fileNameToStore;
    }
 $filename = $seoFileNameWithoutExtension
        ? $seoFileNameWithoutExtension . '-' . time()
        : uniqid();

    // Handle image files
    if ($originalExtension !== 'webp') {
        $fileNameToStore = $filename . '.webp';
    
        $manager = new ImageManager(Driver::class, autoOrientation: true);
        $image = $manager->read($filerequest->getPathname());

        $webpImage = $image->toWebp(95);
        \Storage::disk('public')->put($folder . '/' . $fileNameToStore, (string) $webpImage);

        if ($generateThumbnail) {
            generateThumbnail($filerequest, $folder,$thumbnailDimensions, $filename, 'webp');
        }
    } else {
        $fileNameToStore = $filename . '.webp';
        $filerequest->storeAs('public/' . $folder, $fileNameToStore);

        if ($generateThumbnail) {
            generateThumbnail($filerequest, $folder, $thumbnailDimensions, $filename, 'webp');
        }
    }

    return $fileNameToStore;
}
function storeMultipleFile($folder, $filerequest, $imagemodel, $parent_id, $parent_key_fieldname, $generate_thumbnail = false,$thumbnailDimensions=[],$seoFileNameWithoutExtension=null)
{
    $generateThumbnail = $generate_thumbnail;
   //dd(func_get_args());
     $manager = new ImageManager(Driver::class,
              autoOrientation: true);// or 'gd' // or 'gd'
    $folder = str_replace('\\', '/', $folder);
    $mod = app("App\\Models\\$imagemodel");
    $files = $filerequest;
    $i = 0;
    $ar_files = [];
    $data = [];
    chmod(storage_path('app/public/' . $folder), 0755);
    foreach ($files as $file) {
        ++$i;
       $originalExtension = strtolower($file->getClientOriginalExtension());
        $filename =(string) \Str::uuid();

    if ($originalExtension !== 'webp') {
        // Convert to webp

        $fileNameToStore = $filename . '.webp';

      
        $image = $manager->read($file->getPathname());

        // Convert & encode image to webp with quality 80
        $webpImage = $image->toWebp(100);
        
        // Save webp image to storage
        \Storage::disk('public')->put($folder . '/' . $fileNameToStore, (string) $webpImage);

        if ($generateThumbnail) {
            generateThumbnail($file, $folder,$thumbnailDimensions, $filename, 'webp');
        }

    } else {
        // Already webp, store directly with the new filename

        $fileNameToStore = $filename . '.webp';

        // Use storeAs on uploaded file to rename and save
        $file->storeAs('public/' . $folder, $fileNameToStore);

        if ($generateThumbnail) {
            generateThumbnail($file, $folder, $thumbnailDimensions, $filename, 'webp');
        }
    }

    
        array_push($ar_files, $fileNameToStore);
        //  dd($ar_files);
        $data[] = [
            'name' => $fileNameToStore,
            '' . $parent_key_fieldname . '' => $parent_id, 'created_at' => date("Y-m-d H:i:s")];

    }
    $mod->insert($data);
    return $ar_files;
}


function getValidation()
{
    return [
        (object) ['label' => 'Required', 'value' => 'required'],
        (object) ['label' => 'Image', 'value' => 'image'],
        (object) ['label' => 'Numeric', 'value' => 'numeric'],
        (object) ['label' => 'Nullable', 'value' => 'nullable'],
        (object) ['label' => 'String', 'value' => 'string'],
        (object) ['label' => 'Email', 'value' => 'email'],
        (object) ['label' => 'Sometimes', 'value' => 'sometimes'],
    ];
}
function getInputs()
{
    return [
        (object) ['label' => 'Text', 'value' => 'text'],
        (object) ['label' => 'Date', 'value' => 'date'],
        (object) ['label' => 'DateTimeLocal', 'value' => 'datetime-local'],
        (object) ['label' => 'Email', 'value' => 'email'],
        (object) ['label' => 'Textarea', 'value' => 'textarea'],
        (object) ['label' => 'Number', 'value' => 'number'],
        (object) ['label' => 'File', 'value' => 'file'],
        (object) ['label' => 'Select', 'value' => 'select'],
        (object) ['label' => 'Radio', 'value' => 'radio'],
        (object) ['label' => 'Checkbox', 'value' => 'checkbox'],

    ];
}


function generateThumbnail(
    $image_request, $folder,
    $dimensions,
    $original_file_name_without_ext,
    $original_file_ext
) {

    //$filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
    $filename = $original_file_name_without_ext;

    //get file extension
    $extension = $original_file_ext;
    //small thumbnail name
    $tinythumbnail = null;
    $smallthumbnail = null;

    //medium thumbnail name
    $mediumthumbnail = null;

    //large thumbnail name
    $largethumbnail = null;
    //small thumbnail name
 
    if (in_array('tiny', array_keys($dimensions))) {
        $tinythumbnail = 'tiny_'.$filename . '.' . $extension;
    }

    if (in_array('small', array_keys($dimensions))) {
        $smallthumbnail = 'small_'.$filename . '.' . $extension;
    }

    if (in_array('medium', array_keys($dimensions))) {
        $mediumthumbnail = 'medium_'.$filename . '.' . $extension;
    }

    //large thumbnail name
    if (in_array('large', array_keys($dimensions))) {
        $largethumbnail = 'large_'.$filename . '.' . $extension;
    }
    if (in_array('xlarge', array_keys($dimensions))) {
        $xlargethumbnail = 'xlarge_'.$filename . '.' . $extension;
    }

    //Upload File
    $base_path = $folder . '/thumbnail';
    // dd(storage_path('app\\' . $folder . '\\thumbnail'));

    //chmod(Storage::path('public/' . $base_path), 0755);
    // if (!empty($tinythumbnail)) {
    //     $image_request->storeAs('public/' . $base_path, $tinythumbnail);
    // }

    // if (!empty($smallthumbnail)) {
    //     $image_request->storeAs('public/' . $base_path, $smallthumbnail);
    // }

    // if (!empty($mediumthumbnail)) {
    //     $image_request->storeAs('public/' . $base_path, $mediumthumbnail);
    // }

    // if (!empty($largethumbnail)) {
    //     $image_request->storeAs('public/' . $base_path, $largethumbnail);
    // }

   // chmod(Storage::path('public/' . $base_path), 0755);
    if (!empty($tinythumbnail)) {
        $tinythumbnailpath = $base_path . '/' . $tinythumbnail;
        createThumbnail($image_request,$tinythumbnailpath, $dimensions['tiny']);
    }

    if (!empty($smallthumbnail)) {
        $smallthumbnailpath =$base_path.'/'.$smallthumbnail;
        createThumbnail($image_request,$smallthumbnailpath, $dimensions['small']);
    }
    //create medium thumbnail
    if (!empty($mediumthumbnail)) {
        $mediumthumbnailpath =$base_path . '/' . $mediumthumbnail;
        createThumbnail($image_request,$mediumthumbnailpath, $dimensions['medium']);

     
    }
    //create large thumbnail
    if (!empty($largethumbnail)) {
        $largethumbnailpath = $base_path . '/' . $largethumbnail;
        createThumbnail($image_request,$largethumbnailpath, $dimensions['large']);
       
    }
    if (!empty($xlargethumbnail)) {
        $largethumbnailpath = $base_path . '/' . $xlargethumbnail;
        createThumbnail($image_request,$largethumbnailpath, $dimensions['xlarge']['width']);
       
    }
    // return $filenametostore;
}
function createThumbnail($filerequest,$path, $width)
{
    $manager = new ImageManager(Driver::class,
    autoOrientation: true);
  
    $img =  $manager->read($filerequest);
    $img->scaleDown(width: $width)->sharpen(5);
    $quality=85;
    if (\Str::contains($path, 'tiny') || \Str::contains($path, 'small') || \Str::contains($path, 'medium') ) {
      $quality=95;
    }
    $thumbWebp = $img->toWebp(quality: $quality);
     \Storage::disk('public')->put($path,(string) $thumbWebp);
  
  
}
function createResponse($success, $message, $redirect_url = null)
{
    return response()->json(['success' => $success, 'message' => $message, 'redirect_url' => $redirect_url]);

}
function isPlainArray($array)
{
    $keys = array_keys($array);

    if (isset($keys[0]) && !is_array($array[$keys[0]])) {
        // associative array
        return true;
    } else {
        // sequential array
        return false;
    }
}
function isArrayEmpty($ar)
{

    $keys = array_keys($ar[0]);

    $is_empty = false;

    foreach ($keys as $key) {
        if (empty($ar[0][$key])) {
            $is_empty = true;
        } else {
            $is_empty = false;
            break;
        }

    }

    return $is_empty;
}
function getTableNameFromImageFieldList($list, $fieldname)
{

    $table_name = null;
    if (count($list) > 0) {
        foreach ($list as $item) {
            if ($item['field_name'] == $fieldname) {
                $table_name = !empty($item['table_name']) ? $item['table_name'] : '';

                break;
            }
        }
    }
    return $table_name;
}
function deleteSingleFileFromRelatedTable($folder, $fileid, $filemodel)
{
    $mod = app("App\\Models\\$filemodel");
    $filerow = $mod->findOrFail($fileid);
    $path = storage_path('app/public/' . $folder . '/' . $filerow->name);
    if (\File::exists($path)) {
        unlink($path);
        $thumbs = getThumbnailsFromImage($filerow->name);
        foreach ($thumbs as $p) {
            $path = storage_path('app/public/' . $folder . '/thumbnail/' . $p);
            if (\File::exists($path)) {
                unlink($path);
            }
        }

    }

}
function deleteAllFilesFromRelatedTable($folder, $parent_field_name, $parent_id, $filemodel)
{
    $mod = app("App\\Models\\$filemodel");
    $rows = $mod->where($parent_field_name, $parent_id);
    if ($rows->count() > 0) {
        foreach ($rows as $t) {
            $path = storage_path('app/public/' . $folder . '/' . $t->name);
            if (\File::exists($path)) {
                unlink($path);
                $thumbs = getThumbnailsFromImage($t->name);
                foreach ($thumbs as $p) {
                    $path = storage_path('app/public/' . $folder . '/thumbnail/' . $p);
                    if (\File::exists($path)) {
                        unlink($path);
                    }
                }
            }

        }

    }
}
function deleteSingleFileOwnTable($folder, $model, $model_field, $rowid)
{
    $mod = app("App\\Models\\$model");
    $row = $mod->findOrFail($rowid);
    $path = storage_path('app/public/' . $folder . '/' . $row->{$model_field});
    $mod->findOrFail($rowid)->update(['' . $model_field . '' => null]);
    if (\File::exists($path)) {
        unlink($path);
        $thumbs = getThumbnailsFromImage($row->{$model_field});
        foreach ($thumbs as $p) {
            $path = storage_path('app/public/' . $folder . '/thumbnail/' . $p);
            if (\File::exists($path)) {
                unlink($path);
            }
        }
    }

}
function getImageList($id, $image_model, $parent_field_name)
{
    $model = "App\\Models\\$image_model";
    return $model::where($parent_field_name, $id)->get(['id', 'name']);
}
function getTableNameFromModel($model)
{
    $model_class = app("\App\Models" . '\\' . $model);
    return $model_class->getTable();
}
function getFieldValuesFromModelAsArray($model, $field, $where = [])
{
    $model_class = "\App\Models" . '\\' . $model;
    $lists = $model_class::query();
    if (count($where) > 0) {
        $lists = $lists->where('status', 'Active')->where($where);
    }
    $lists = $lists->get([$field]);

    $list4 = [];
    foreach ($lists as $list) {
        $list4[] = $list[$field];

    }
    return $list4;
}
function getRadioOptions($model, $where = [], $by_field = 'name')
{
    $model_class = "\App\Models" . '\\' . $model;
    $lists = $model_class::query();
    if (count($where) > 0) {
        $lists = $lists->where('status', 'Active')->where($where);
    }
    $field_to_get = !empty($by_field) ? $by_field : 'name';
    $lists = $lists->get(['id', $field_to_get]);
    $alist = [];
    foreach ($lists as $list) {
        $ar = (object) ['label' => $list[$field_to_get], 'value' => $list['id']];
        array_push($alist, $ar);
    }
    return $alist;
}
function getListFromIndexArray($arr = []) /* for optinos in select not from model but from an array liek ['apple','mango']*/
{

    $list3 = [];
    foreach ($arr as $item) {
        $ar = (object) ['id' => $item, 'name' => $item];
        array_push($list3, $ar);
    }
    return $list3;
}
function getListFromIndexArrayForRadio($arr = []) /* for optinos in select not from model but from an array liek ['apple','mango']*/
{

    $list3 = [];
    foreach ($arr as $item) {
        $ar = (object) ['label' => $item, 'value' => $item];
        array_push($list3, $ar);
    }
    return $list3;
}
function getList($model, $where = [], $by_field = 'name',$whereNot=[],$append_column = '')
{
    $model_class = "\App\Models" . '\\' . trim($model);
    $lists = $model_class::query();
    if (count($where) > 0) {
        $lists = $lists->where($where);
    }
    if (count($whereNot) > 0) {
        foreach ($whereNot as $column => $value) {
                    $$lists->where($column, '!=', $value);
                }
      
    }
    $lists = $lists->get(['id', $by_field]);

    $list2 = [];
    foreach ($lists as $list) {
        $ar = (object) ['id' => $list['id'], 'name' => $list[$by_field] . (!empty($append_column) ? '(' . $list[$append_column] . ')' : '')];
        array_push($list2, $ar);
    }
    return $list2;
}
function getListAssignedProduct()
{
    $store = \DB::table('stores')->whereOwnerId(auth()->id())->first();
    $lists = \App\Models\StoreAssignedProductStock::with('product:id,name')->whereStoreId($store->id)->get();

    $list2 = [];
    foreach ($lists as $list) {
        $ar = (object) ['id' => $list->product_id, 'name' => $list->product->name . ' (' . $list->current_quantity . ')'];
        array_push($list2, $ar);
    }
    return $list2;
}
function getListProductWithQty()
{

    $lists = \App\Models\AdminProductStock::with('product:id,name')->get();

    $list2 = [];
    foreach ($lists as $list) {
        $ar = (object) ['id' => $list->product_id, 'name' => $list->product->name . ' (' . $list->current_quantity . ')'];
        array_push($list2, $ar);
    }
    return $list2;
}

function getListMaterialWithQty()
{

    $lists = \App\Models\MaterialStock::with('material:id,name')->get();

    $list2 = [];
    foreach ($lists as $list) {
        $ar = (object) ['id' => $list->material_id, 'name' => $list->material->name . ' (' . $list->current_stock . ')'];
        array_push($list2, $ar);
    }
    return $list2;
}

function getListWithRoles($role = 'name')
{

    $lists = \App\Models\User::role($role)->get(['name', 'id'])->toArray();
    //dd($lists);
    $list2 = [];
    foreach ($lists as $list) {
        $ar = (object) ['id' => $list['id'], 'name' => $list['name']];
        array_push($list2, $ar);
    }
    return $list2;
}

function getListOnlyNonIdValue($model, $where = [], $by_field = 'name')
{
    $model_class = "\App\Models" . '\\' . $model;

    $lists = $model_class::query();
    if (count($where) > 0) {
        $lists = $lists->where($where);
    }
    $lists = $lists->pluck($by_field)->toArray();

    return $lists;
}
function getListWithSameIdAndName($model, $where = [], $by_field = 'name')
{
    $model_class =$model=='Role'?'Role': "\App\Models" . '\\' . $model;
    $lists = $model=='Role'?Role::query():$model_class::query();
    if (count($where) > 0) {
        $lists = $lists->where('status', 'Active')->where($where);
    }
    $lists = $lists->get(['id', $by_field]);

    $list2 = [];
    foreach ($lists as $list) {
        $ar = (object) ['id' => $list[$by_field], 'name' => $list[$by_field]];
        array_push($list2, $ar);
    }
    return $list2;

}
/******remove below thing any time  */

function getValOfArraykey($ar, $key, $is_array = true)
{
    return isset($ar[$key]) ? $ar[$key] : ($is_array ? [] : null);
}

function isThisATableColumn($table, $field)
{
    $fields_ar = \Illuminate\Support\Facades\Schema::getColumnListing($table);
    return in_array($field, $fields_ar);

}

function isIndexedArray($ar)
{

    return !is_array($ar[0]);
}
function getColumnType($table, $column)
{
    \Schema::getColumnType($table, $column);
}
function isFieldInRelationsArray($rel_ar, $field)
{
    $value_fetchable_field = str_contains($field, '_id') ? explode('_', $field)[0] : $field;
    return in_array($value_fetchable_field, array_column($rel_ar, 'name'));
}

function formatDefaultValueForSelect($model_relations, $model, $field, $is_multiple, $get_json_by_key_for_show_in_default = 'name')
{

    $table = $model->getTable();
    $value_fetchable_field = str_contains($field, '_id') ? explode('_', $field)[0] : $field;
    // dd($field);
    $field_type = getColumnType($table, $field);
    if ($is_multiple) {
        $field_value = ($field_type == 'json' || $field_type == 'Json') ? json_decode($model->{$value_fetchable_field}, true) : $model->{$value_fetchable_field};
        $isTableColumn = isThisATableColumn($table, $field);
        if ($isTableColumn) {
            return isIndexedArray($field_value) ? $field_value : (isFieldInRelationsArray($model_relations, $field) ? array_column($field_value, 'id') : array_column($field_value, $get_json_by_key_for_show_in_default));
        } else {
            return $model->{$value_fetchable_field} ? array_column($model->{$value_fetchable_field}->toArray(), 'id') : [];
        }
    } else {
        return $model->{$value_fetchable_field};
    }
    function formatDefaultValueForCheckbox($model, $field)
    {

        return $model->{$field} ? json_decode($model->{$field}, true) : [];

    }

}
// function showRelationalColumn($model_relations, $model, $field)
// {

//     $table = $model->getTable();
//     $value_fetchable_field = str_contains($field, '_id') ? explode('_', $field)[0] : $field;
//    $resp=null;
//    $isTableColumn = isThisATableColumn($table, $field);
//    if( $isTableColumn){
//      $field_type = getColumnType($table, $field);
//       $field_value= json_decode($model->{$value_fetchable_field}, true);
//    return isIndexedArray($field_value) ? $field_value : (isFieldInRelationsArray($model_relations, $value_fetchable_field) ? array_column($field_value, $get_json_by_key_for_show_in_default) : array_column($field_value, $get_json_by_key_for_show_in_default));

//    }

//     if ($field_type == 'json' || $field_type == 'Json') {
//         $field_value= json_decode($model->{$value_fetchable_field}, true);
//         $isTableColumn = isThisATableColumn($table, $field);
//         if ($isTableColumn) {
//             return isIndexedArray($field_value) ? $field_value : (isFieldInRelationsArray($model_relations, $value_fetchable_field) ? array_column($field_value, $get_json_by_key_for_show_in_default) : array_column($field_value, $get_json_by_key_for_show_in_default));
//         } else {
//             return $model->{$field} ? array_column($model->{$field}->toArray(), 'id') : [];
//         }
//     } else {
//         return $model->{$value_fetchable_field};
//     }
//     function formatDefaultValueForCheckbox($model, $field)
//     {

//         return $model->{$field} ? json_decode($model->{$field}, true) : [];

//     }

// }

function formatDefaultValueForEdit($model, $field, $is_multiple = false)
{

    if ($is_multiple) {
        return json_decode($model->{$field}, true);

    } else {
        return $model->{$field};
    }
}
function getTableValueForManyTypeRelation($model, $field)
{
    return $model->{$field} ? $model->{$field}->pluck('id') : [];
}

function formatDefaultValueForCheckbox($model, $field)
{

    return $model->{$field} ? json_decode($model->{$field}, true) : [];

}
function is_admin()
{

    return auth()->user()->hasRole(['Admin']);
}
function can($permission)
{
    // dd(auth()->user());
    // return (auth()->user()->hasRole(['Admin'])) || (auth()->user()->can($permission));
    return true;
}
/******Monthly weekly daily recors for chart */
function getDailyRecord($table, $date_column = 'created_at', $to_do = 'sum', $cond = "", $column_for_sum = "amount", $for_days = 7)
{
    $perday_records = null;
    if ($to_do == 'sum') {
        $query = "SELECT SUM(`" . $column_for_sum . "`) AS total, Date(`" . $date_column . "`) AS d FROM  " . $table . " WHERE Date(`" . $date_column . "`) BETWEEN ADDDATE(NOW(),-" . $for_days . ")
        AND NOW()  " . (strlen($cond) > 1 ? "AND " . $cond : null) . " GROUP BY Date(`" . $date_column . "`) ORDER BY DATE(`" . $date_column . "`) DESC";
    } else {
        $query = "SELECT COUNT(*) AS c, Date(`" . $date_column . "`) AS d FROM  " . $table . " WHERE Date(`" . $date_column . "`)
         BETWEEN ADDDATE(NOW(),-" . $for_days . ") AND NOW()  " . (strlen($cond) > 1 ? "AND " . $cond : null) . " GROUP BY Date(`" . $date_column . "`) ORDER BY DATE(`" . $date_column . "`) DESC";
    }

    $perday_records = \DB::select($query);
    $perday_records = array_map(function ($v) {
        return (array) $v;
    }, $perday_records);
    return ['val' => array_column($perday_records, $to_do == 'sum' ? 'total' : 'c'), 'datetime' => array_column($perday_records, 'd')];

}
function getMonthlyRecord($table, $date_column = 'created_at', $to_do = 'sum', $cond = "", $column_for_sum = "amount")
{
    $month_ar = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

    $monthly_records = null;
    if ($to_do == 'sum') {
        $query = "SELECT SUM(`" . $column_for_sum . "`) AS total, MONTH(`" . $date_column . "`) AS m FROM  " . $table . "
         WHERE YEAR(`" . $date_column . "`) =YEAR(NOW())  " . (strlen($cond) > 1 ? "AND " . $cond : null) . " GROUP BY MONTH(`" . $date_column . "`)";
    } else {
        $query = "SELECT COUNT(*) AS c, MONTH(`" . $date_column . "`) AS m FROM  " . $table . "
         WHERE YEAR(`" . $date_column . "`) =YEAR(NOW())  " . (strlen($cond) > 1 ? "AND " . $cond : null) . " GROUP BY MONTH(`" . $date_column . "`)";

    }

    $monthly_records = \DB::select($query);

    $monthly_records = array_map(function ($v) {
        $v->m = date("M", mktime(0, 0, 0, $v->m, 10));
        return (array) $v;
    }, $monthly_records);

    $monthly_records = collect($monthly_records)->pluck($to_do == 'sum' ? 'total' : 'c', 'm')->toArray();
    $monthly_records_val = [];
    foreach ($month_ar as $m) {
        if (isset($monthly_records[$m])) {
            $monthly_records_val[ucfirst($m)] = $monthly_records[$m];
        } elseif (isset($monthly_records[ucfirst($m)])) {
            $monthly_records_val[ucfirst($m)] = $monthly_records[ucfirst($m)];

        } else {
            $monthly_records_val[ucfirst($m)] = 0.0;
        }
    }

    return array_values($monthly_records_val);

}
function getWeeklyRecord($table, $date_column = 'created_at', $to_do = 'sum', $cond = "", $column_for_sum = "amount", $no_weeks = 4)
{

    $monthly_records = null;
    if ($to_do == 'sum') {
        $query = "SELECT SUM(`" . $column_for_sum . "`) AS total, WEEK(`" . $date_column . "`) AS w FROM  " . $table . "
         WHERE YEAR(`" . $date_column . "`) =YEAR(NOW())   AND  Date(`" . $date_column . "`) BETWEEN (NOW() - INTERVAL " . $no_weeks . " WEEK)
        AND NOW() " . (strlen($cond) > 1 ? "AND " . $cond : null) . " GROUP BY WEEK(`" . $date_column . "`) ORDER BY WEEK(`" . $date_column . "`) DESC";
    } else {
        $query = "SELECT COUNT(*) AS c, WEEK(`" . $date_column . "`) AS w FROM  " . $table . "
         WHERE YEAR(`" . $date_column . "`) =YEAR(NOW()) AND  Date(`" . $date_column . "`) BETWEEN (NOW() - INTERVAL " . $no_weeks . " WEEK)
        AND NOW()  " . (strlen($cond) > 1 ? "AND " . $cond : null) . " GROUP BY WEEK(`" . $date_column . "`) ORDER BY WEEK(`" . $date_column . "`) DESC";

    }

    //dd($query);
    $weekly_records = \DB::select($query);

    $weekly_records = collect($weekly_records)->pluck($to_do == 'sum' ? 'total' : 'c', 'w')->toArray();
    $weekly_records_val = [];
    $i = 1;
    foreach (array_keys($weekly_records) as $w) {
        if (isset($weekly_records[$w])) {
            $weekly_records_val[$i] = $weekly_records[$w];
        } else {
            $weekly_records_val[$i] = 0.0;
        }
        $i++;
    }

    return array_values($weekly_records_val);

}

function getIndianCurrency(float $number)
{
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'one', 2 => 'two',
        3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
        7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve',
        13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
        16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
        19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
        40 => 'forty', 50 => 'fifty', 60 => 'sixty',
        70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
    $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
    while ($i < $digits_length) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str[] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
        } else {
            $str[] = null;
        }

    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
    return ucwords(($Rupees ? $Rupees . 'Rupees ' : '') . $paise) . 'Only';
}
function getCurrency()
{
    return '₹';

}
function setting()
{
    return \DB::table('settings')->first();

}
function getTableRecordSum($table, $where, $by_column)
{
   $q = \Schema::hasColumn($table, 'deleted_at')?\DB::table($table)->whereNull('deleted_at'):\DB::table($table);
    if (count($where) > 0) {
        $q = $q->where($where);
    }
    return $q->sum($by_column);
}
function getTableRecordCount($table, $where)
{
    $q = \Schema::hasColumn($table, 'deleted_at')?\DB::table($table)->whereNull('deleted_at'):\DB::table($table);
    if (count($where) > 0) {
        $q = $q->where($where);
    }
    return $q->count();
}
function getNonSubjectType()
{
    return ['PT', 'Sports', 'Prayer', 'Lunch', 'Music', 'PTM', 'Other'];
}
function getCLassSubjects($class_id)
{
    $subjects_row = \DB::table('school_classes')->whereId($class_id)->first();
    if (is_null($subjects_row->stream_id)) {
        return null;
    } else {
        $stream_row = \DB::table('streams')->whereId($subjects_row->stream_id)->first();
        return $stream_row->subjects ? json_decode($stream_row->subjects) : null;

    }

}
function studentInfo($student)
{
    $str = '<div class="table-responsive">
                    <table class="table table-bordered">
                       <tbody>
                         <tr>
                           <th>Full Name</th><td>' . $student->full_name . '</td>
                         </tr>
                         <tr>
                           <th>Father Name</th><td>' . $student->father_name . '</td>
                         </tr>
                         <tr>
                           <th>Admission No.</th><td>' . $student->admission_number . '</td>
                         </tr>
                         <tr>
                           <th>Address</th><td>' . $student->address . '</td>
                         </tr>
                       </tbody>
                    </table>
                    </div>';
    return $str;
}
function randomPassword()
{
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}
function differenceInHours($startdate, $enddate)
{
    $starttimestamp = strtotime($startdate);
    $endtimestamp = strtotime($enddate);
    $difference = abs($endtimestamp - $starttimestamp) / 3600;
    return $difference;
}
function differenceInDays($startdate, $enddate)
{
    $starttimestamp = date_create($startdate);
    $endtimestamp = date_create($enddate);
    $difference = abs($endtimestamp - $starttimestamp) / 3600;
    return $difference;
}
function displayDates($date1, $date2)
{
    $dates = array();
    $current = strtotime($date1);
    $date2 = strtotime($date2);
    $stepVal = '+1 day';
    while ($current <= $date2) {
        $dates[] = date($format, $current);
        $current = strtotime($stepVal, $current);
    }
    return $dates;
}
function formattedPaginatedApiResponse($paginated_data)
{
    $list = $paginated_data->toArray();
    $ar_price = !empty($list['data']) ? array_column($list['data'], 'price') : [];

    $minPrice = 0;
    $maxPrice = 0;
    if (!empty($ar_price)) {
        $minPrice = min($ar_price);
        $maxPrice = max($ar_price);
    }
    $resp = [];
    $resp['data'] = $list['data'];
    $resp['meta'] = [
        'prev_page' => $list['current_page'] <= 1 ? null : $list['current_page'] - 1,
        'next_page' => $list['current_page'] >= $list['last_page'] ? null : $list['current_page'] + 1,
        'last_page' => $list['last_page'],
        'per_page' => $list['per_page'],
        'total_pages' => $list['last_page'],
        'total_items' => $list['total'],
        'current_page' => $list['current_page'],
        'minPrice' => $minPrice,
        'maxPrice' => $maxPrice,
    ];
    return $resp;
}
function getThumbnailsFromImage($image_name)
{
    $filename = pathinfo($image_name, PATHINFO_FILENAME);
    $ext = pathinfo($image_name, PATHINFO_EXTENSION);
    return [
        'tiny' => 'tiny'.$filename .'.' . $ext,
        'small' => 'small_'.$filename . '.' . $ext,
        'medium' => 'medium_'.$filename . '.' . $ext,
        'large' => 'large_'.$filename .'.' . $ext,
    ];
}
function gt($ar, $i, $s, $selected_id = null, )
{
    $i++;
    foreach ($ar as $k) {
        $selected = $selected_id == $k['id'] ? 'selected' : '';
        $s .= '<option ' . $selected . ' value="' . $k['id'] . '"> ' . str_repeat('-', $i) . $k['name'] . '</option>';

        $childs = \App\Models\Category::whereCategoryId($k['id'])->get()->toArray();
        if (count($childs) > 0) {
            $s = gt($childs, $i, $s, $selected_id);
        }

    }

    return $s;
}
function gt_multiple($ar, $i, $s, $selected_id)
{
    $i++;
    foreach ($ar as $k) {
        if ($k['id']) {
            $selected = ($k['id'] && in_array($k['id'], $selected_id)) ? 'selected' : '';

            $s .= '<option ' . $selected . ' value="' . $k['id'] . '"> ' . str_repeat('-', $i) . $k['name'] . '</option>';

            $childs = \App\Models\Category::whereCategoryId($k['id'])->get()->toArray();
            if (count($childs) > 0) {

                $s = gt_multiple($childs, $i, $s, $selected_id);
            }
        }

    }

    return $s;
}
function gt1($ar, $final_ar)
{

    foreach ($ar as $k) {
        $final_ar[] = ['id' => $k['id'], 'name' => $k['name']];
        $childs = \App\Models\Category::whereCategoryId($k['id'])->get()->toArray();
        if (count($childs) > 0) {
            $final_ar = gt1($childs, $final_ar);
        }

    }

    return $final_ar;
}
function getRowFromCartItems($cart_items, $product_id)
{
    $item_row = null;
    foreach ($cart_items as $row) {
        if ($row->product_id == $product_id) {
            $item_row = $row;
            break;
        }
    }
    return $item_row;
}

function getNetAmountAfterIndividualDiscountForSingleCartItem($el, $discount_rules)
{
    /* includes bulk discount and combo and product discount all ***/
    $cart_amount_without_offer = 0;
    $offerDiscount = 0.0; /**incldes above discount value***/
    $cart_net_amount = 0;

    $price = $el->price;
    $sale_price = $el->sale_price;
    $cart_amount_without_offer += $price * $el->qty;

    $total_saving_per_item = 0;
    $qty = $el->qty;
    $row_final_val = 0;
    //**this discount is on overall cost of item so no need to multiply by qty on discount just totalcost-discount */
    if (!empty($discount_rules)) {
        $rule = $discount_rules;
        // foreach ($rules as $rule) {
        if ($rule['discount_type'] != null && $rule['discount'] != null) {
            if ($rule['has_range'] == 'No') {
                /****1- Cart item qty can be greater than rule exact qty so jitna extra cart qty hai uspe normal
                 * discount column rule lagega taking into discount_applies_qty column into account */
                $disc_for_exact_qty = $rule['discount_type'] == 'Flat'
                ? $rule['discount']
                : ($price * $rule['discount'] / 100);
                $offerDiscount += $disc_for_exact_qty * $rule['exact_qty'];
                $sale_price_for_the_exact_qty = $price - $disc_for_exact_qty;
                $sale_price = $sale_price_for_the_exact_qty;
                $total_sale_price_for_exact_qty = $sale_price_for_the_exact_qty * $rule['exact_qty'];
                $sale_price_for_next_qty = 0;
                $total_sale_price_for_next_qty = 0; /***isme ek to wo ayega jo ki less than or equa lt discount_applies_qty column ke doosra wo jo isse xtra bache */
                if ($qty > $rule['exact_qty']) { /***qty ya to badi hogi ya equal kam nai hogi ***/
                    $remaining_qty = $qty - $rule['exact_qty'];
                    $disc_for_the_next_qty = $el->discount_type != null && $el->discount != null
                    ? ($el->discount_type == 'Flat'
                        ? $el->discount
                        : ($price * $el->discount / 100))
                    : 0;
                    if ($disc_for_the_next_qty > 0) {
                        $offerDiscount += $disc_for_the_next_qty * ($remaining_qty);
                        $sale_price_for_next_qty = $price - $disc_for_the_next_qty;
                        $sale_price = $sale_price_for_next_qty;
                        $total_sale_price_for_next_qty = $sale_price_for_next_qty * ($remaining_qty);

                    } else {
                        /***esle sale price will be regular item sale _price */

                        $total_sale_price_for_next_qty = $el->sale_price * ($remaining_qty);
                    }

                }
                $row_final_val = $total_sale_price_for_exact_qty + $total_sale_price_for_next_qty;
                $cart_net_amount = $row_final_val;
            } else {
                $disc = $rule['discount_type'] == 'Flat'
                ? $rule->discount
                : ($price * $rule['discount'] / 100);
                $offerDiscount += $disc * $rule['min_qty'];
                $sale_price = $price - $disc;
                $total_sale_price = $sale_price * $rule['item_qty'];
                $cart_net_amount = $total_sale_price;
            }

        }

        // }

    } else {

        $offerDiscount = ($price - $sale_price) * ($qty);
        $cart_net_amount = $sale_price * $el->qty;

    }
    $total_tax = ($cart_net_amount * ($el->sgst + $el->cgst + $el->igst)) / 100;

    return ['net_cart_amount' => $cart_net_amount + $total_tax, 'total_discount' => $offerDiscount, 'total_tax' => $total_tax];

}
function getProductQtyCouponRuleIdsForSingleCartItem($cart_item, $cart_items, $user_id, $discount_method)
{

    $product_id = $cart_item->product_id;
    $item_category_id = \DB::table('products')->whereId($product_id)->first()->category_id;
    $product_coupons = \DB::table('coupons')->whereStatus('Active')->whereDate('start_date', '<=', Carbon::now())
        ->whereDate('end_date', '>=', Carbon::now())
        ->whereType('Individual Quantity')->whereDiscountMethod($discount_method)
        ->get();
    $applicable_coupon_rows = [];
    if (count($product_coupons->toArray()) > 0) {
        foreach ($product_coupons as $coupon_row) {
            $is_eligible_coupon = checkCouponEligibilityByCustomerGrpAndUseLimit($user_id, $rule);
            if ($is_eligible_coupon['is_eligible']) {
                $coupon_categories = $coupon_row->category_id != null ? array_column(json_decode($coupon_row->category_id, true), 'id') : [];
                $coupon_product_ids = $coupon_row->product_id != null ? array_column(json_decode($coupon_row->product_id, true), 'id') : [];
                $is_item_eligible = shouldCartItemIncludeForCoupon($$coupon_row->include_or_exclude, $cart_item, $coupon_category_ids, $coupon_product_ids);
                if ($is_item_eligible) {
                    $minimum_cart_amount_required = $coupon_row->minimum_order_amount;
                    if ($minimum_cart_amount_required != null && $minimum_cart_amount_required > 0) {
                        $sum_of_cart_amount = getCartAmountTotalConditionally($coupon_row->include_or_exclude, $cart_items, $coupon_categories, $coupon_product_ids);
                        if ($sum_of_cart_amount > 0) {
                            if ($minimum_cart_amount_required != null && $sum_of_cart_amount < $minimum_cart_amount_required) {
                                continue;
                            } else {

                            }
                        }
                    } else {
                        $applicable_coupon_rows[] = $coupon_row;
                    }
                }

            }
        }
    }
    return $applicable_coupon_rows;
}
function getProductQtyRules($cart_item, $cart_items, $user_id, $discount_method)
{
    $item_category_id = \DB::table('products')->whereId($product_id)->first()->category_id;
    $product_rules = getProductQtyCouponRuleIdsForSingleCartItem($cart_item, $cart_items, $user_id, $discount_method);
    if (count($product_rules->toArray()) > 0) {
        foreach ($product_rules as $rule) {
            if (!empty($rule->quantity_rule)) {

                $qty_rules_ar = json_decode($rule->quantity_rule, true);
                foreach ($qty_rules_ar as $qr) {
                    if ($qr['is_range'] == 'Yes') {
                        $go = true;
                        if (isset($qr['max_quantity']) && !empty($qr['max_quantity'])) {
                            if ($qr['max_quantity'] < $item_qty) {
                                continue;
                            }

                        }
                        if (isset($qr['min_quantity']) && !empty($qr['min_quantity'])) {
                            if ($qr['min_quantity'] > $item_qty) {
                                continue;
                            }

                        }
                        // $product_offer_detail_text=$item_qty.' Items have discount of '.($qr['discount_type']=='Flat'
                        // ?'Rs.'.$qr['discount'].' each'
                        // :$qr['discount'].'% each');
                        $discount_rules[] = [
                            'rule_name' => $rule->name, 'offer_text' => '',
                            'discount_type' => $qr['discount_type'],
                            'discount' => $qr['discount'], 'has_range' => 'Yes',
                            'min_qty' => $qr['min_quantity'], 'max_qty' => $qr['max_quantity'],
                        ];
                    } else {
                        $required_qty = isset($qr['min_quantity']) ? $qr['min_quantity'] : $qr['max_quantity'];
                        $product_offer_detail_text = '';
                        if ($required_qty <= $item_qty) {
                            if ($required_qty < $item_qty) {
                                $text_when_qty_more = $required_qty . ' Items have discount of ' . ($qr['discount_type'] == 'Flat'
                                    ? 'Rs.' . $qr['discount'] . ' each'
                                    : $qr['discount'] . '% each');
                                if ($cart_item->discount_type != null) {
                                    $text_when_qty_more .= '<br>remaining ' . $item_qty - $required_qty . ' items have discount of ';
                                    $text_when_qty_more .= $cart_item->discount_type == 'Flat'
                                    ? 'Rs.' . $cart_item->discount . ' each'
                                    : $cart_item->discount . '% each';
                                }
                                $product_offer_detail_text = $text_when_qty_more;
                            } else {
                                $product_offer_detail_text = $required_qty . ' Items have discount of ' . ($qr['discount_type'] == 'Flat'
                                    ? 'Rs.' . $qr['discount'] . ' each'
                                    : $qr['discount'] . '% each');
                            }

                            $discount_rules[] = [
                                'rule_name' => $rule->name,
                                'offer_text' => $product_offer_detail_text,
                                'discount_type' => $qr['discount_type'],
                                'discount' => $qr['discount'],
                                'has_range' => 'No',
                                'coupon_id' => $rule->id,
                                'exact_qty' => $required_qty,
                            ];
                        }

                    }

                }

            }
        }

    }

    return $discount_rules;
}

function getCustomerGroups($userid)
{
    $customer_grp_ids = [];

    $c_groups = \DB::table('customer_groups')->whereStatus('Active')->get();
    foreach ($c_groups as $cg) {
        $is_rule_applied = false;
        $purchase_amount_rule = !empty($cg->purchase_amount_rule) ? json_decode($cg->purchase_amount_rule, true)[0] : [];
        $order_count_rules = !empty($cg->order_count_rules) ? json_decode($cg->order_count_rules, true)[0] : [];
        $subscription_rule = !empty($cg->subscription_rule) ? json_decode($cg->subscription_rule, true)[0] : [];
        $joining_rule = !empty($cg->joining_rule) ? json_decode($cg->joining_rule, true)[0] : [];
        $abandoned_checkout_rule = !empty($cg->abandoned_checkout_rule) ? json_decode($cg->abandoned_checkout_rule, true)[0] : [];
        $joining_rule = !empty($cg->joining_rule) ? json_decode($cg->joining_rule, true)[0] : [];
        $incomplete_order_rule = !empty($cg->incomplete_order_rule) ? json_decode($cg->incomplete_order_rule, true)[0] : [];

        $purchase_amount_rule = !empty($purchase_amount_rule)
        ? (is_null($purchase_amount_rule['maximum_amount']) && is_null($purchase_amount_rule['minimum_amount']) ? [] : $purchase_amount_rule)
        : [];
        $order_count_rules = !empty($order_count_rules)
        ? (is_null($order_count_rules['maximum']) && is_null($order_count_rules['minimum']) ? [] : $order_count_rules)
        : [];
        $subscription_rule = !empty($subscription_rule)
        ? is_null($subscription_rule['within_days']) ? [] : $subscription_rule
        : [];
        $joining_rule = !empty($joining_rule)
        ? empty($joining_rule['within_days']) ? [] : $joining_rule
        : [];
        $abandoned_checkout_rule = !empty($abandoned_checkout_rule)
        ? empty($abandoned_checkout_rule['within_days']) ? [] : $abandoned_checkout_rule
        : [];
        $abandoned_checkout_rule = !empty($abandoned_checkout_rule)
        ? empty($abandoned_checkout_rule['within_days']) ? [] : $abandoned_checkout_rule
        : [];

        if (!empty($purchase_amount_rule)) {
            $since_days = !is_null($purchase_amount_rule['within_days']) ? $purchase_amount_rule['within_days'] : null;
            $order_amount = 0;
            if ($since_days) {
                $order_amount = \DB::table('orders')->whereUserId($userid)
                    ->whereDate('created_at', '>=', Carbon::now()->subDays($since_days))
                    ->sum('net_payable');
            } else {
                $order_amount = \DB::table('orders')->whereUserId($userid)
                    ->sum('net_payable');
            }

            $minimum = ($purchase_amount_rule['minimum_amount']) ? $purchase_amount_rule['minimum_amount'] : 0;
            if ($purchase_amount_rule['maximum_amount'] != null) {
                $is_rule_applied = $order_amount >= $minimum && $order_amount <= $purchase_amount_rule['maximum_amount'];
            } else {
                $is_rule_applied = $order_amount >= $minimum;
            }

        }
        if (!empty($order_count_rules)) {

            $since_days = isset($order_count_rules['within_days']) && !is_null($order_count_rules['within_days']) ? $order_count_rules['within_days'] : null;

            $order_count = 0;
            if ($since_days) {
                $order_count = \DB::table('orders')->whereUserId($userid)
                    ->whereDate('created_at', '>=', Carbon::now()->subDays($since_days))
                    ->count();
            } else {
                $order_count = \DB::table('orders')->whereUserId($userid)
                    ->count();
            }
            $minimum = ($order_count_rules['minimum']) ? $order_count_rules['minimum'] : 0;

            if ($order_count_rules['maximum'] != null) {

                $is_rule_applied = $order_count >= $minimum && $order_count <= $order_count_rules['maximum'];
            } else {

                $is_rule_applied = $order_count >= $minimum;
            }

        }
        if (!empty($joining_rule)) {
            $since_days = $joining_rule['within_days'];

            if ($since_days) {
                if (\DB::table('users')->whereId($userid)->whereDate('created_at', '>=', Carbon::now()->subDays($since_days))
                    ->exists()) {
                    $is_rule_applied = true;
                } else {
                    $is_rule_applied = false;
                }
            }

        }
        if (!empty($abandoned_checkout_rule)) {
            $since_days = $abandoned_checkout_rule['within_days'];

            if ($since_days) {
                if (\DB::table('carts')->whereUserId($userid)->
                    whereDate('created_at', '>=', Carbon::now()->subDays($since_days))
                    ->whereDate('created_at', '<', Carbon::now())->exists()) {
                    $is_rule_applied = true;
                } else {
                    $is_rule_applied = false;
                }
            }

        }
        if (!empty($incomplete_order_rule)) {
            $since_days = $incomplete_order_rule['within_days'];

            if ($since_days) {
                if (\DB::table('orders')->whereUserId($userid)
                    ->wherePaidStatus('Pending')->whereDate('created_at', '>=', Carbon::now()->subDays($since_days))
                    ->whereDate('created_at', '<', Carbon::now())->exists()) {
                    $is_rule_applied = true;
                } else {
                    $is_rule_applied = false;
                }
            }

        }
        if ($is_rule_applied) {
            $customer_grp_ids[] = $cg->id;

        }

    }
    return $customer_grp_ids;
}
function getBogoOffers($cart_items, $user_id, $discount_method)
{
    $offer_product_to_claim = [];
    $on_buy_qty = 0;
    $multiple = 1;
    $combos = \DB::table('coupons')->whereStatus('Active')->whereDate('start_date', '<=', Carbon::now())
        ->whereDate('end_date', '>=', Carbon::now())->whereType('BOGO')->whereDiscountMethod($discount_method)->get();
    $cart_products = $cart_items->pluck('qty', 'product_id')->toArray();
    if (count($combos->toArray()) > 0) {
        foreach ($combos as $c) {
            $is_eligible_coupon = checkCouponEligibilityByCustomerGrpAndUseLimit($user_id, $c);
            $should_add_product_as_offer = false;
            if ($is_eligible_coupon['is_eligible']) {
                $buy_products = json_decode($c->buy_products, true);
                $buy_product_ids = array_column($buy_products, 'product_id');

                if (count(array_intersect($buy_product_ids, array_keys($cart_products))) > 0) {
                    foreach ($buy_products as $item) {
                        if (in_array($item['product_id'], array_keys($cart_products))) {
                            $prod_id = $item['product_id'];
                            if (isset($cart_products[$prod_id])) {
                                $requird_qty = $item['qty'];
                                $cart_qty = $cart_products[$prod_id];
                                if ($cart_qty >= $requird_qty && $cart_qty % $requird_qty == 0) {
                                    $on_buy_qty = $requird_qty;
                                    $multiple = floor($cart_qty / $requird_qty);
                                    $should_add_product_as_offer = true;
                                } else {
                                    $should_add_product_as_offer = false;
                                    break;
                                }
                            }
                        }

                    }
                }
                if ($should_add_product_as_offer) {
                    $get_products = !empty($c->get_products) ? json_decode($c->get_products, true) : null;
                    if ($get_products) {
                        foreach ($get_products as $g) {
                            if (!empty($offer_product_to_claim)) {
                                if (!in_array($g['product_id'], array_keys($offer_product_to_claim))) {
                                    $prev_qty = $offer_product_to_claim[$g['product_id']]['qty'];
                                    $offer_product_to_claim[$g['product_id']]['qty'] = $prev_qt + $g['qty'];
                                    $offer_product_to_claim[$g['product_id']]['multiple'] = $multiple;
                                    $offer_product_to_claim[$g['product_id']]['buy_qty'] = $on_buy_qty;
                                }
                            } else {
                                $offer_product_to_claim[$g['product_id']] = [
                                    'buy_qty' => $on_buy_qty, 'multiple' => $multiple,
                                    'product_id' => $g['product_id'],
                                    'coupon_id' => $c->id, 'rule_name' => $c->name,
                                    'qty' => $g['qty'],
                                    'discount_type' => $g['discount_type'],
                                    'discount' => $g['discount'],
                                ];
                            }
                        }
                    }
                }
            }
        }
    }
    return $offer_product_to_claim;
}
function getBogoOfferForSingleCoupon($coupon_row, $cart_items, $user_id)
{
    $offer_product_to_claim = [];
    $on_buy_qty = 0;
    $multiple = 1;
    $c = $coupon_row;
    $cart_products = $cart_items->pluck('qty', 'product_id')->toArray();

    $discount_method = $coupon_row->discount_method;
    $should_add_product_as_offer = false;
    $buy_products = json_decode($c->buy_products, true);
    $buy_product_ids = array_column($buy_products, 'product_id');

    if (count(array_intersect($buy_product_ids, array_keys($cart_products))) > 0) {
        foreach ($buy_products as $item) {

            if (in_array($item['product_id'], array_keys($cart_products))) {

                $prod_id = $item['product_id'];
                if (isset($cart_products[$prod_id])) {
                    $requird_qty = $item['qty'];
                    $cart_qty = $cart_products[$prod_id];
                    if ($cart_qty >= $requird_qty && $cart_qty % $requird_qty == 0) {
                        $on_buy_qty = $requird_qty;
                        $multiple = floor($cart_qty / $requird_qty);
                        $should_add_product_as_offer = true;
                    } else {
                        \Session::flash('error', 'insufficient qty');
                        $should_add_product_as_offer = false;
                        break;
                    }
                }
            }

        }
    }
    if ($should_add_product_as_offer) {
        $get_products = !empty($c->get_products) ? json_decode($c->get_products, true) : null;
        if ($get_products) {
            foreach ($get_products as $g) {
                if (!empty($offer_product_to_claim)) {
                    if (!in_array($g['product_id'], array_keys($offer_product_to_claim))) {
                        $prev_qty = $offer_product_to_claim[$g['product_id']]['qty'];
                        $offer_product_to_claim[$g['product_id']]['qty'] = $prev_qt + $g['qty'];
                        $offer_product_to_claim[$g['product_id']]['multiple'] = $multiple;
                        $offer_product_to_claim[$g['product_id']]['buy_qty'] = $on_buy_qty;
                    }
                } else {
                    $offer_product_to_claim[$g['product_id']] = [
                        'buy_qty' => $on_buy_qty, 'multiple' => $multiple,
                        'product_id' => $g['product_id'],
                        'coupon_id' => $c->id, 'rule_name' => $c->name,
                        'qty' => $g['qty'],
                        'discount_type' => $g['discount_type'],
                        'discount' => $g['discount'],
                    ];
                }
            }
        }
    }
    return $offer_product_to_claim;
}
function checkCouponEligibilityByCustomerGrpAndUseLimit($user_id, $coupon_row)
{
    $coupon_id = $coupon_row->id;
    $apply_coupon = true;
    $error = '';
    $customer_grps_applied = $coupon_row->customer_group_id
    ? array_column(json_decode($coupon_row->customer_group_id, true), 'id') : [];

    $customer_grps_applied = !empty($customer_grps_applied) && empty($customer_grps_applied[0]) ? [] : $customer_grps_applied;
    if (!empty($customer_grps_applied)) {
        $customer_group_ids = getCustomerGroups($user_id);

        if (empty($customer_group_ids)) {
            $apply_coupon = false;
        } else {
            if (count(array_intersect($customer_grps_applied, $customer_group_ids)) == count($customer_grps_applied)) {
                $apply_coupon = true;
            } else {
                $apply_coupon = false;

                $error = 'You are not eligible customer  for the coupon';
                \Session::flash('error', 'You are not eligible customer  for the coupon');

            }

        }
    }
    if ($apply_coupon) {
        /***check usage limit */
        if ($coupon_row->total_usage_limit != null && $coupon_row->total_usage_limit > 0) {
            $apply_coupon = false;
            if ($coupon_row->total_used_till_now < $coupon_row->total_usage_limit) {
                $apply_coupon = true;
                if ($coupon_row->customer_usage_limit != null && $coupon_row->customer_usage_limit > 0) {
                    $customer_usage = \DB::table('coupon_usage_by_customers')->whereUserId($user_id)->whereCouponId($coupon_id)->first();
                    if (!is_null($customer_usage)) {
                        if ($customer_usage->count < $coupon_row->customer_usage_limit) {
                            $apply_coupon = true;
                        } else {
                            $apply_coupon = false;
                            $error = "Coupon maximum usage limit reached for you";
                            \Session::flash('error', 'Coupon maximum usage limit reached for you');
                        }
                    }
                }
            } else {
                $error = "Coupon maximum usage limit reached";
                \Session::flash('error', 'Coupon maximum usage limit reached for you');

            }
        }

    }
    return ['is_eligible' => $apply_coupon, 'error_message' => $error];
}
function applyCartLevelDiscountOffer($coupon_row, $cart_sum, $cart_session_id, $user_id)
{
    $maximum_dicount_limit = $coupon_row->maximum_discount_limit;
    $discount_type = $coupon_row['discount_type'];
    $discount_value = $coupon_rowd['discount'];
    $discount_amount = $discount_type == 'Flat' ? $discount_value : ($sum_of_cart_amount * $discount_value / 100);
    if ($discount_amount > $maximum_dicount_limit) {
        $discount_amount = $maximum_dicount_limit;
    }
    \DB::insert('applied_coupons')->insert([
        'coupon_id' => $coupon_id,
        'user_id' => $user_id,
        'cart_session_id' => $cart_session_id,
        'coupon_method' => $coupon_row->discount_method,
        'coupon_type' => 'Cart',
    ]);
    return ['coupon_type' => 'Cart', 'value' => $sum_of_cart_amount - $discount_amount, 'free_shipping' => $coupon_row->free_shipping];

}
function applyShippingOffer($coupon_row, $cart_session_id, $user_id)
{
    $setting = \DB::table('settings')->first();
    $shipping_charge = $setting != null ? $setting->delivery_charge : 0;

    $discount_type = $coupon_row['discount_type'];
    $discount_value = $coupon_rowd['discount'];
    $discount_amount = $discount_type == 'Flat' ? $discount_value : ($shipping_charge * $discount_value / 100);
    \DB::insert('applied_coupons')->insert([
        'coupon_id' => $coupon_id,
        'user_id' => $user_id,
        'cart_session_id' => $cart_session_id,
        'coupon_method' => $coupon_row->discount_method,
        'coupon_type' => 'Shipping',
    ]);
    return ['coupon_type' => 'Shipping', 'value' => $shipping_charge - $discount_amount];
}
function getCartAmountTotalConditionally($coupon_row, $cart_items, $coupon_category_ids, $coupon_product_ids,
) {
    $sum = 0;
    $include_or_exclude = $coupon_row->include_or_exclude;
    $amount_calculation_method = $coupon_row->cart_amount_calculation_method;
    
    foreach ($cart_items as $item) {
        $p_id = $item->product_id;
        $additional_amount = 0;/***if item has addon items  */
        if ($item->addon_items != null) {
            $items = json_decode($item->addon_items, true);
            foreach ($items as $g) {
                $additional_amount += $g['qty'] * $g['price'];
            }
        }
        if ($item->addon_products != null) {
            $items = json_decode($item->addon_products, true);
            foreach ($items as $g) {
                $additional_amount+= $g['qty'] * $g['amount'];
            }
        }
        $should_proceed = $amount_calculation_method == 'All Items'
            || (empty($coupon_product_ids) && empty($coupon_category_ids))
            || ($amount_calculation_method == 'Exclude Selected'
            &&
            (!in_array($p_id, $coupon_product_ids) ||
                (empty($coupon_product_ids) && !in_array($cart_item->category_id, $coupon_category_ids))
            )
        );

        if ($should_proceed) {
            $sum += $item->net_cart_amount+$additional_amount;

        } else {
            $sum += 0;
        }
    }

    /*   if ($amount_calculation_method != 'All Items') {
    $loop_only_selected_items = false;

    if (empty($coupon_product_ids) && empty($coupon_category_ids)) {
    $loop_only_selected_items = false;

    } elseif (empty($coupon_product_ids) && !empty($coupon_category_ids)) {
    $loop_only_selected_items = true;

    } elseif (!empty($coupon_product_ids) && !empty($coupon_category_ids)) {
    $loop_only_selected_items = true;

    }
    if ($loop_only_selected_items) {
    foreach ($cart_items as $item) {
    $p_id = $item->product_id;

    $should_proceed = $include_or_exclude == 'Include'
    ? in_array($p_id, $coupon_product_ids) || (empty($coupon_product_ids) && in_array($cart_item->category_id, $coupon_category_ids))
    : (!in_array($p_id, $coupon_product_ids) || (empty($coupon_product_ids) && !in_array($cart_item->category_id, $coupon_category_ids)));
    if ($should_proceed) {
    if ($amount_calculation_method == 'Include Selected') {
    $sum += $item->net_cart_amount;
    } else {
    $sum += 0;
    }

    } else {
    $sum += 0;
    }

    }

    } else {
    foreach ($cart_items as $item) {

    $sum += $item->net_cart_amount;

    }
    }
    } else {
    foreach ($cart_items as $item) {

    $sum += $item->net_cart_amount;

    }
    }
     */
    return $sum;

}
function shouldCartItemIncludeForCoupon($coupon_row, $cart_item, $coupon_category_ids, $coupon_product_ids)
{
    $should_include = false;
    $include_or_exclude = $coupon_row->include_or_exclude;
    if ($coupon_row->type != 'BOGO') {
        if (empty($coupon_product_ids) && empty($coupon_category_ids)) {
            $should_include = true;

        } elseif (empty($coupon_product_ids) && !empty($coupon_category_ids)) {
            $should_include = $include_or_exclude == 'Include'
            ? in_array($cart_item->category_id, $coupon_category_ids)
            : !in_array($cart_item->category_id, $coupon_category_ids);

        } elseif (!empty($coupon_product_ids) && !empty($coupon_category_ids)) {
            $should_include = $include_or_exclude == 'Include'
            ? in_array($cart_item->product_id, $coupon_product_ids)
            : !in_array($cart_item->product_id, $coupon_product_ids);

        }} else {
        $buy_products = json_decode($coupon_row->buy_products, true);
        $product_ids = array_column($buy_products, 'product_id');
        $should_include = in_array($cart_item->product_id, $product_ids);

    }
    return $should_include;
}
function applyBulkDiscountSingeItemRule($coupon_row, $cart_item, $user_id)
{
    $t = $cart_item;
    $item_qty_present_in_cart = $t->qty;
    $price = $t->price;
    //array_push($existing_item_coupon_ids, $coupon_id);
    $disc = $coupon_row->discount_type != null && $coupon_row->discount != null
    ? ($coupon_row->discount_type == 'Flat'
        ? $coupon_row->discount
        : ($price * $coupon_row->discount / 100))
    : 0;
    $new_sale_price = $t->price - $disc;
    $net_cart_amount = $new_sale_price * $t->qty;
    $prev_cids = !empty($t->affected_by_coupon_ids)
    ? json_decode($t->affected_by_coupon_ids, true) : [];
    $affected_by_coupon_ids = array_merge($prev_cids, [$coupon_row->id]);
    return $disc > 0 ? ['id' => $t->id,
        'discount_type' => $coupon_row->discount_type,

        'discount' => $coupon_row->discount,

        'net_cart_amount' => $net_cart_amount,
        'product_discount_offer_detail' => ($coupon_row->discount_type == 'Flat'
            ? getCurrency() . ' ' . $coupon_row->discount : $coupon_row->discount . '%') . ' discount is applied on each quantity',
        'total_discount' => $disc * $t->qty,
        'affected_by_coupon_ids' => json_encode($affected_by_coupon_ids),

        //  'is_bulk_coupon_applied' => 'Yes',

    ] : [];
}
function getIndividualDiscountRuleFOrItem($coupon_row, $cart_item, $user_id)
{
    $rules = !empty($coupon_row->quantity_rule) ? json_decode($coupon_row->quantity_rule, true) : null;

    $item_qty = $cart_item->qty;
    $coupon_discount_rule = null;
    $apply_coupon = true;
    foreach ($rules as $qr) {
        if ($qr['is_range'] == 'Yes') {

            if (isset($qr['max_quantity']) && !empty($qr['max_quantity'])) {
                if ($qr['max_quantity'] < $item_qty) {
                    $apply_coupon = false;
                }

            }
            if (isset($qr['min_quantity']) && !empty($qr['min_quantity'])) {
                if ($qr['min_quantity'] > $item_qty) {
                    $apply_coupon = false;
                }

            }
            if ($apply_coupon) {
                $coupon_discount_rule = [
                    'rule_name' => $coupon_row->name, 'offer_text' => $coupon_row->name,
                    'discount_type' => $qr['discount_type'],
                    'discount' => $qr['discount'], 'has_range' => 'Yes',
                    'coupon_id' => $coupon_row->id,
                    'min_qty' => $qr['min_quantity'], 'max_qty' => $qr['max_quantity'],
                ];
            }
            if ($coupon_discount_rule != null) {
                break;
            }

        } else {
            $required_qty = isset($qr['min_quantity']) ? $qr['min_quantity'] : $qr['max_quantity'];

            if ($required_qty <= $item_qty) {
                $coupon_discount_rule = [

                    'has_range' => 'No',

                    'exact_qty' => $required_qty,
                ];
            }
            if ($coupon_discount_rule != null) {
                break;
            }

        }
    }
}
function applyProductQuantityCoupon($coupon_row, $cart_item, $user_id)
{
    $rules = !empty($coupon_row->quantity_rule) ? json_decode($coupon_row->quantity_rule, true) : null;
    if (!is_null($cart_item)) {
        $item_qty = $cart_item->qty;
        $coupon_discount_rule = null;
        $apply_coupon = true;
        foreach ($rules as $qr) {
            if ($qr['is_range'] == 'Yes') {

                if (isset($qr['max_quantity']) && !empty($qr['max_quantity'])) {
                    if ($qr['max_quantity'] < $item_qty) {
                        $apply_coupon = false;
                    }

                }
                if (isset($qr['min_quantity']) && !empty($qr['min_quantity'])) {
                    if ($qr['min_quantity'] > $item_qty) {
                        $apply_coupon = false;
                    }

                }
                if ($apply_coupon) {
                    $coupon_discount_rule = [
                        'rule_name' => $coupon_row->name, 'offer_text' => $coupon_row->name,
                        'discount_type' => $qr['discount_type'],
                        'discount' => $qr['discount'], 'has_range' => 'Yes',
                        'coupon_id' => $coupon_row->id,
                        'item_qty' => $item_qty,
                        'min_qty' => $qr['min_quantity'], 'max_qty' => $qr['max_quantity'],
                    ];
                }
                if ($coupon_discount_rule != null) {
                    break;
                }

            } else {
                $required_qty = isset($qr['min_quantity']) ? $qr['min_quantity'] : $qr['max_quantity'];
                $product_offer_detail_text = '';
                if ($required_qty <= $item_qty) {

                    if ($required_qty < $item_qty) {
                        $text_when_qty_more = $required_qty . ' Items have discount of ' . ($qr['discount_type'] == 'Flat'
                            ? 'Rs.' . $qr['discount'] . ' each'
                            : $qr['discount'] . '% each');
                        // //if ($cart_item->discount_type != null) {
                        //     $text_when_qty_more .= ',remaining ' . $item_qty - $required_qty . ' items are regular priced';
                        //     /*$text_when_qty_more .= $cart_item->discount_type == 'Flat'
                        //     ? 'Rs.' . $cart_item->discount . ' each'
                        //     : $cart_item->discount . '% each';*/
                        // //}
                        $product_offer_detail_text = $text_when_qty_more;
                    } else {
                        $product_offer_detail_text = $required_qty . ' Items have discount of ' . ($qr['discount_type'] == 'Flat'
                            ? 'Rs.' . $qr['discount'] . ' each'
                            : $qr['discount'] . '% each');
                    }

                    $coupon_discount_rule = [
                        'rule_name' => $coupon_row->name,
                        'offer_text' => $product_offer_detail_text,
                        'discount_type' => $qr['discount_type'],
                        'discount' => $qr['discount'],
                        'has_range' => 'No',
                        'coupon_id' => $coupon_row->id,
                        'exact_qty' => $required_qty,
                    ];
                }
                if ($coupon_discount_rule != null) {
                    break;
                }

            }
        }
        if (!empty($coupon_discount_rule)) {

            $p = getNetAmountAfterIndividualDiscountForSingleCartItem($cart_item, $coupon_discount_rule);

            $prev_cids = !empty($cart_item->affected_by_coupon_ids)
            ? json_decode($cart_item->affected_by_coupon_ids, true) : [];
            $affected_by_coupon_ids = array_merge($prev_cids, [$coupon_row->id]);
            dlog('net car', $p['net_cart_amount']);
            return [
                // 'discount_rules' => json_encode($coupon_discount_rule),
                'id' => $cart_item->id,
                'net_cart_amount' => $p['net_cart_amount'],
                'total_discount' => $p['total_discount'],
                'product_discount_offer_detail' => $coupon_discount_rule['offer_text'],
                'total_tax' => $p['total_tax'],

                'affected_by_coupon_ids' => json_encode($affected_by_coupon_ids),

            ];
        } else {
            return [];
        }
    } else {
        return [];
    }

}
function getProductQuantityDiscountPercentForItem($coupon_row, $cart_item, $user_id)
{
    $rules = !empty($coupon_row->quantity_rule) ? json_decode($coupon_row->quantity_rule, true) : null;

    $item_qty = $cart_item->qty;
    $coupon_discount_rule = null;
    $apply_coupon = true;
    foreach ($rules as $qr) {
        if ($qr['is_range'] == 'Yes') {

            if (isset($qr['max_quantity']) && !empty($qr['max_quantity'])) {
                if ($qr['max_quantity'] < $item_qty) {
                    $apply_coupon = false;
                }

            }
            if (isset($qr['min_quantity']) && !empty($qr['min_quantity'])) {
                if ($qr['min_quantity'] > $item_qty) {
                    $apply_coupon = false;
                }

            }
            if ($apply_coupon) {
                $coupon_discount_rule = [
                    'rule_name' => $coupon_row->name, 'offer_text' => $coupon_row->name,
                    'discount_type' => $qr['discount_type'],
                    'discount' => $qr['discount'], 'has_range' => 'Yes',
                    'coupon_id' => $coupon_row->id,
                    'min_qty' => $qr['min_quantity'], 'max_qty' => $qr['max_quantity'],
                ];
            }
            if ($coupon_discount_rule != null) {
                break;
            }

        } else {
            $required_qty = isset($qr['min_quantity']) ? $qr['min_quantity'] : $qr['max_quantity'];
            $product_offer_detail_text = '';
            if ($required_qty <= $item_qty) {

                if ($required_qty < $item_qty) {
                    $text_when_qty_more = $required_qty . ' Items have discount of ' . ($qr['discount_type'] == 'Flat'
                        ? 'Rs.' . $qr['discount'] . ' each'
                        : $qr['discount'] . '% each');

                    $product_offer_detail_text = $text_when_qty_more;
                } else {
                    $product_offer_detail_text = $required_qty . ' Items have discount of ' . ($qr['discount_type'] == 'Flat'
                        ? 'Rs.' . $qr['discount'] . ' each'
                        : $qr['discount'] . '% each');
                }

                $coupon_discount_rule = [
                    'rule_name' => $coupon_row->name,
                    'offer_text' => $product_offer_detail_text,
                    'discount_type' => $qr['discount_type'],
                    'discount' => $qr['discount'],
                    'has_range' => 'No',
                    'coupon_id' => $coupon_row->id,
                    'exact_qty' => $required_qty,
                ];
            }
            if ($coupon_discount_rule != null) {
                break;
            }

        }
    }
    return $coupon_discount_rule;

}
function getProductOffers($product_id)
{
    $valid_coupons = \App\Models\Coupon::whereStatus('Active')->whereDate('start_date', '<=', Carbon::now())
        ->whereDate('end_date', '>=', Carbon::now())
        ->whereRaw('CASE WHEN total_usage_limit IS NOT NULL AND total_usage_limit>0 THEN total_usage_limit>total_used_till_now
        END')->get();
    $offers_ar = [];
    if (count($valid_coupons->toArray()) > 0) {
        foreach ($valid_coupons as $x) {
            if ($x->type != 'BOGO') {
                if (!empty($x->product_id)) {
                    $product_ids = json_decode($x->product_id, true);
                    if (in_array($product_id, array_column($product_ids, 'id'))) {
                        $offers_ar[] = ['name' => $x->name, 'details' => $x->details,
                          'coupon_code'=>$x->coupon_code,
                        'start_date'=>$x->start_date,
                        'end_date'=>$x->end_date
                        ];
                    }

                }
            } else {
                if (!empty($x->buy_products)) {
                    $p = json_decode($x->buy_products, true);
                    if (!empty($p[0]['product_id'])) {
                        if (in_array($product_id, array_column($p, 'product_id'))) {
                            $offers_ar[] = ['name' => $x->name, 'details' => $x->details,
                              'coupon_code'=>$x->coupon_code,
                        'start_date'=>$x->start_date,
                        'end_date'=>$x->end_date
                            ];
                        }
                    }

                }
            }
        }
    }
    return $offers_ar;
}
function applyAutomaticBogoOffer($coupon_row, $cart_item, $user_id, $cart_items)
{
    $cart_session_id = $cart_item->cart_session_id;
    $combo_product_as_offer = getBogoOfferForSingleCoupon($coupon_row, $cart_items, $user_id);
    $cart_product_ids = array_column($cart_items->toArray(), 'product_id');
    $cart_offer_insert_ar = [];
    $cart_update_ar = [];
    if (!empty($combo_product_as_offer)) {

        $prod_ids = array_keys($combo_product_as_offer);
        if (!empty($prod_ids)) {
            $cart_offer_insert_ar = [];
            $prod_rec = \DB::table('products')->whereIn('id', $prod_ids)->get();
            if (!empty(count($prod_rec->toArray()))) {
                foreach ($prod_rec as $t) {

                    if (!in_array($t->id, $cart_product_ids)) {

                        $discount_type = $combo_product_as_offer[$t->id]['discount_type'];
                        $discount_value = $combo_product_as_offer[$t->id]['discount'];
                        $discount_amount = $discount_type == 'Flat' ? $discount_value : ($t->price * $discount_value / 100);
                        $qty_to_add = $combo_product_as_offer[$t->id]['qty'] * $combo_product_as_offer[$t->id]['multiple'];
                        $net_cart_amount = ($t->price - $discount_amount) * $qty_to_add;
                        if ($t->image) {
                            $t->thumbnail = getThumbnailsFromImage($t->image);
                        }
                        $cart_offer_insert_ar[] = [
                            'product_id' => $t->id,
                            'user_id' => $user_id,
                            'name' => $t->name,
                            'sgst' => $t->sgst ?? 0,
                            'cgst' => $t->cgst ?? 0,
                            'igst' => $t->igst ?? 0,
                            'price' => $t->price,
                            'qty' => $qty_to_add,
                            'sale_price' => $t->price - $discount_amount,
                            'discount_type' => $discount_type,
                            'discount' => $discount_value,
                            'category_id' => $t->category_id,
                            'is_combo' => 'Yes',
                            'cart_session_id' => $cart_session_id,
                            'unit' => $t->unit, 'total_discount' => $discount_amount * $qty_to_add,
                            'net_cart_amount' => $net_cart_amount,
                            'affected_by_coupon_ids' => json_encode([$coupon_row->id]),
                            'image' => url('/') . '/storage/products/' . $t->id . '/thumbnail/' . $t->thumbnail['tiny'],
                        ];

                    } else {

                        $prev_qty = \DB::table('carts')->whereCartSessionId($cart_session_id)->whereUserId($user_id)->whereProductId($t->id)->first()->qty;
                        $new_qty_added = $combo_product_as_offer[$t->id]['qty'] * $combo_product_as_offer[$t->id]['multiple'];
                        $total_qty = $new_qty_added + $prev_qty;
                        $discount_type = $combo_product_as_offer[$t->id]['discount_type'];
                        $discount_value = $combo_product_as_offer[$t->id]['discount'];
                        $discount_amount = $discount_type == 'Flat' ? $discount_value : ($t->price * $discount_value / 100);
                        $net_cart_amount = ($t->price - $discount_amount) * $new_qty_added;
                        if ($total_qty > $new_qty_added) {
                            $net_cart_amount += ($t->sale_price) * ($total_qty - $new_qty_added);
                        }

                        $prev_cids = !empty($cart_item->affected_by_coupon_ids)
                        ? json_decode($cart_item->affected_by_coupon_ids, true) : [];
                        $affected_by_coupon_ids = array_merge($prev_cids, [$coupon_row->id]);

                        $cart_update_ar[] = [
                            'id' => $t->id,
                            'qty' => $prev_qty > $new_qty_added ? $prev_qty : $new_qty_added,
                            'discount' => $combo_product_as_offer[$t->id]['discount'],
                            'discount_type' => $combo_product_as_offer[$t->id]['discount_type'],
                            'discount_applies_on_qty' => $new_qty_added,
                            'is_combo' => 'Yes',
                            'total_discount' => (($t->price - $t->sale_price) * ($total_qty - $new_qty_added)) + $discount_amount * $new_qty_added,
                            'net_cart_amount' => $net_cart_amount,
                            'affected_by_coupon_ids' => json_encode($affected_by_coupon_ids),
                        ];

                    }
                }

            }

        }
    } else {

        \DB::table('carts')->where(['user_id' => $user_id, 'is_combo' => 'Yes',
            'cart_session_id' => $cart_session_id])->delete();
    }

    return ['update_ar' => $cart_update_ar, 'insert_ar' => $cart_offer_insert_ar];
}
function getEligibleOffers($cart_items, $user_id)
{
    $available_coupon_offers = [];
    $valid_coupons = \App\Models\Coupon::whereStatus('Active')->whereDate('start_date', '<=', Carbon::now())
        ->whereDate('end_date', '>=', Carbon::now())
        ->whereRaw('(CASE WHEN total_usage_limit IS NOT NULL AND total_usage_limit>0 THEN total_usage_limit>total_used_till_now ELSE true END)')->orderBy('minimum_order_amount', 'DESC')->get();
    if (!empty($valid_coupons->toArray())) {

        foreach ($valid_coupons as $coupon_row) {
            //if ($coupon_row->type == 'Bulk' && (is_null($coupon_row->minimum_order_amount) || $coupon_row->minimum_order_amount < 1)) {
            if ($coupon_row->type == 'Bulk' && $coupon_row->discount_method == 'Automatic') {
                continue;
            }
            $is_eligible_coupon = checkCouponEligibilityByCustomerGrpAndUseLimit($user_id, $coupon_row);

            if ($is_eligible_coupon['is_eligible']) {
                $coupon_category_ids = $coupon_row->category_id != null ? array_column(json_decode($coupon_row->category_id, true), 'id') : [];
                $coupon_product_ids = $coupon_row->product_id != null ? array_column(json_decode($coupon_row->product_id, true), 'id') : [];
                foreach ($cart_items as $cart_item) {
                    $is_item_eligible = shouldCartItemIncludeForCoupon($coupon_row, $cart_item, $coupon_category_ids, $coupon_product_ids);
                    dlog('cyess', $is_item_eligible);
                    if ($is_item_eligible) {
                        $minimum_cart_amount_required = $coupon_row->minimum_order_amount;
                        if ($minimum_cart_amount_required != null) {
                            $sum_of_cart_amount = getCartAmountTotalConditionally($coupon_row,
                                $cart_items, $coupon_category_ids, $coupon_product_ids);
                            if ($sum_of_cart_amount < $minimum_cart_amount_required) {

                                $available_coupon_offers[] = ['coupon_name' => $coupon_row->name,
                                    'discount_type' => $coupon_row->discount_type,
                                    'discount' => $coupon_row->discount,
                                    'coupon_details' => $coupon_row->details,
                                       'coupon_code' => $coupon_row->coupon_code,
                                    'start_date' => $coupon_row->start_date,
                                    'end_date' => $coupon_row->end_date,
                                    'target_amount' => $minimum_cart_amount_required != null
                                    ? ($minimum_cart_amount_required - $sum_of_cart_amount) : null];
                            }
                        } else {
                            if (in_array($cart_item->product_id, $coupon_product_ids)) {
                                $available_coupon_offers[] = ['coupon_name' => $coupon_row->name,
                                    'discount_type' => $coupon_row->discount_type,
                                    'discount' => $coupon_row->discount,
                                       'coupon_code' => $coupon_row->coupon_code,
                                    'start_date' => $coupon_row->start_date,
                                    'end_date' => $coupon_row->end_date,
                                    'coupon_details' => $coupon_row->details,
                                    'target_amount' => null];
                            }
                        }

                    } else {

                    }
                }

            }

        }
    }
    return $available_coupon_offers;
}
function getOnylCartMinimumAmountOffers($cart_items, $user_id)
{
    $available_coupon_offers = [];
    $valid_coupons = \App\Models\Coupon::whereStatus('Active')->where('type', 'Cart')->whereDate('start_date', '<=', Carbon::now())
        ->whereDate('end_date', '>=', Carbon::now())->whereRaw('CASE WHEN total_usage_limit IS NOT NULL AND total_usage_limit>0 THEN total_usage_limit>total_used_till_now
        END')->orderBy('minimum_order_amount', 'DESC')->get();
    if (!empty($valid_coupons->toArray())) {
        foreach ($valid_coupons as $coupon_row) {

            $is_eligible_coupon = checkCouponEligibilityByCustomerGrpAndUseLimit($user_id, $coupon_row);
            if ($is_eligible_coupon['is_eligible']) {
                $coupon_category_ids = $coupon_row->category_id != null ? array_column(json_decode($coupon_row->category_id, true), 'id') : [];
                $coupon_product_ids = $coupon_row->product_id != null ? array_column(json_decode($coupon_row->product_id, true), 'id') : [];
                foreach ($cart_items as $cart_item) {
                    $is_item_eligible =shouldCartItemIncludeForCoupon($coupon_row, $cart_item, $coupon_category_ids, $coupon_product_ids);
                    if ($is_item_eligible) {
                        $minimum_cart_amount_required = $coupon_row->minimum_order_amount;
                        if ($minimum_cart_amount_required != null) {
                            $sum_of_cart_amount = getCartAmountTotalConditionally($coupon_row,
                                $cart_items, $coupon_category_ids, $coupon_product_ids);
                            if ($sum_of_cart_amount < $minimum_cart_amount_required) {

                                $available_coupon_offers[] = [
                                    'discount_type' => $coupon_row->discount_type,
                                    'discount' => $coupon_row->discount,
                                    'coupon_name' => $coupon_row->name,
                                    'coupon_type' => $coupon_row->discount_method,
                                    'coupon_code' => $coupon_row->coupon_code,
                                    'coupon_details' => $coupon_row->details,
                                    'target_amount' => $minimum_cart_amount_required - $sum_of_cart_amount];
                            }
                        }

                    } else {

                    }
                }

            }

        }
    }
    return $available_coupon_offers;
}
function applyCartOfferAndShippingOfferDetail($cart_items, $user_id, $applied_coupons = null)
{
    $cart_session_id = $cart_items[0]->id;
    if (empty($applied_coupons)) {
        $applied_coupons = \DB::table('applied_coupons')->select('applied_coupons.*', 'coupons.minimum_order_amount',
            'coupons.coupon_code', 'coupons.details', 'coupons.name', 'coupons.discount_method', 'coupons.discount_type', 'coupons.discount', 'coupons.maximum_discount_limit')
            ->join('coupons', 'coupons.id', '=', 'applied_coupons.coupon_id')->where(['cart_session_id' => $cart_session_id,
            'user_id' => $user_id])->get();
    }

    $cart_discounts = [];
    $shipping_discounts = [];
    $settings = \DB::table('settings')->first();
    if (!empty($applied_coupons)) {
        foreach ($applied_coupons as $coupon_row) {
            if ($coupon_row->coupon_type == 'Cart') {

                $cart_net_amount = array_sum(array_column($cart_items->toArray(), 'net_cart_amount'));
                $disc = $coupon_row->discount_type != null && $coupon_row->discount != null
                ? ($coupon_row->discount_type == 'Flat'
                    ? $coupon_row->discount
                    : ($cart_net_amount * $coupon_row->discount / 100))
                : 0;
                if ($disc > 0) {
                    $cart_discounts[] = ['coupon_row' => $coupon_row, 'discount_amount' => $disc];
                }

            } elseif ($coupon_row->coupon_type == 'Shipping') {
                $delivery_charge = $settings->delivery_charge;

                $disc = $coupon_row->discount_type != null && $coupon_row->discount != null
                ? ($coupon_row->discount_type == 'Flat'
                    ? $coupon_row->discount
                    : ($delivery_charge * $coupon_row->discount / 100))
                : 0;
                if ($disc > 0) {
                    $shipping_discounts[] = ['coupon_row' => $coupon_row, 'discount_amount' => $disc];
                }

            }
        }
    }

    $minimu_cart_offer = null;
    $max_cart_discount = 0;
    $max_shipping_discount = 0;
    if (!empty($cart_discounts)) {
        $nm = array_column($cart_discounts, 'discount_amount');
       // dd($nm);
        $max_cart_discount = max($nm);
//         $index = array_search($max_cart_discount, $nm);
//         dlog('mmmpppp',$cart_discounts);
//         dlog('max',$max_cart_discount);
//         if ($index > -1) {
//             $selected_cart_offer = $cart_discounts[$index]['coupon_row'];
// dlog('pppp',$selected_cart_offer);
//             $sum_of_cart_amount = array_sum(array_column($cart_items->toArray(), 'net_cart_amount'));
//             $minimu_cart_offer = [
//                 'discount_type' => $selected_cart_offer->discount_type,
//                 'discount' => $selected_cart_offer->discount,
//                 'coupon_name' => $selected_cart_offer->name,
//                 'coupon_type' => $selected_cart_offer->discount_method,
//                 'coupon_code' => $selected_cart_offer->coupon_code,
//                 'coupon_details' => $selected_cart_offer->details,
//                 'target_amount' => $selected_cart_offer->minimum_order_amount != null
//                 ? ($selected_cart_offer->minimum_order_amount - $sum_of_cart_amount) : null];

//         }

    }
    //dlog('wow',$shipping_discounts);
    if (!empty($shipping_discounts)) {
        $nm = array_column($shipping_discounts, 'discount_amount');
        $max_shipping_discount = max($nm);
    }

    return ['cart_amount_discount' => $max_cart_discount,
        'shipping_discount' => $max_shipping_discount];
}
function checkCouponApplicabilityForInsert($cart_items, $user_id, $coupon_row)
{

    $cart_update_arr_for_product_offer_detail = [];
    $applicable_coupon_rows = [];
    if ($coupon_row->type == 'Bulk' && (is_null($coupon_row->minimum_order_amount) || $coupon_row->minimum_order_amount < 1)) {
        /****Because aisa bulk offer is laready applied when product loads but if set cart amount then it can be used to insert in aplied */

    } else {
        $is_eligible_coupon = checkCouponEligibilityByCustomerGrpAndUseLimit($user_id, $coupon_row);

        if ($is_eligible_coupon['is_eligible']) {
            $coupon_category_ids = $coupon_row->category_id != null ? array_column(json_decode($coupon_row->category_id, true), 'id') : [];
            $coupon_product_ids = $coupon_row->product_id != null ? array_column(json_decode($coupon_row->product_id, true), 'id') : [];
            foreach ($cart_items as $cart_item) {
                $is_item_eligible = shouldCartItemIncludeForCoupon($coupon_row, $cart_item, $coupon_category_ids, $coupon_product_ids);

                if ($is_item_eligible) {

                    $minimum_cart_amount_required = $coupon_row->minimum_order_amount;
                    if ($minimum_cart_amount_required != null && $minimum_cart_amount_required > 0) {
                        $sum_of_cart_amount = getCartAmountTotalConditionally($coupon_row,
                            $cart_items, $coupon_category_ids, $coupon_product_ids);

                        if ($sum_of_cart_amount > 0) {
                            if ($sum_of_cart_amount < $minimum_cart_amount_required) {
                                $cart_update_arr_for_product_offer_detail[] = [
                                    'id' => $cart_item->id, 'product_discount_offer_detail' => '',
                                ];
                                if ($coupon_row->discount_method == 'Coupon Code') {
                                    \Session::flash('error', 'Insufficient cart amount as per coupon  condition.Read coupon t&c carfully whether cart amount should be inclusive of some items or not ');
                                }

                                continue;
                            } else {
                                $applicable_coupon_rows[] = [
                                    'cart_item' => $cart_item, 'coupon_row' => $coupon_row];
                            }
                        }
                    } else {
                        $applicable_coupon_rows[] = [
                            'cart_item' => $cart_item, 'coupon_row' => $coupon_row];
                    }
                } else {
                    $cart_update_arr_for_product_offer_detail[] = [
                        'id' => $cart_item->id, 'product_discount_offer_detail' => '',
                    ];
                }
            }

        }
    }

    return ['cart_update_arr_for_product_offer_detail' => $cart_update_arr_for_product_offer_detail,
        'applicable_coupon_rows' => !empty($applicable_coupon_rows) ? $applicable_coupon_rows : []];
}
function isCartCouponApplicable($cart_items, $coupon_row)
{
    $coupon_category_ids = $coupon_row->category_id != null ? array_column(json_decode($coupon_row->category_id, true), 'id') : [];
    $coupon_product_ids = $coupon_row->product_id != null ? array_column(json_decode($coupon_row->product_id, true), 'id') : [];
    $minimum_cart_amount_required = $coupon_row->minimum_order_amount;
    $sum_of_cart_amount = getCartAmountTotalConditionally($coupon_row,
        $cart_items, $coupon_category_ids, $coupon_product_ids);
    if ($sum_of_cart_amount > 0) {
        if ($sum_of_cart_amount < $minimum_cart_amount_required) {
            return false;
        } else {
            return true;
        }

    } else {
        return false;
    }

}
function insertApplicableCouponsForInsertIntoAppliedTableWithProductOfferTextUpdate(
    $cart_session_id, $user_id, $applicable_coupon_rows, $cart_update_arr_for_product_offer_detail, $cart_items) {
    $add_coupon_to_table = false;
    $insert_ar_for_coupons_aplied = [];
    $cart_update_arr = [];
    $cart_insert_arr = [];
    if (!empty($applicable_coupon_rows)) {

        $add_coupon_to_table = false;
        $insert_ar_for_coupons_aplied = [];
        foreach ($applicable_coupon_rows as $x) {

            $coupon_row = $x['coupon_row'];

            $cart_item = $x['cart_item'];
            $due_to_product_id = $cart_item->product_id;
            if ($coupon_row->type == 'Bulk') {

                $updates = applyBulkDiscountSingeItemRule($coupon_row, $cart_item, $user_id); /**updats inlucdes only single acrt item update not array */

                if (!empty($updates)) {
                    array_push($cart_update_arr, $updates);
                } else {
                    $cart_update_arr_for_product_offer_detail[] = [
                        'id' => $cart_item->id, 'product_discount_offer_detail' => '',
                    ];
                }

                $add_coupon_to_table = empty($cart_update_arr) ? false : true;
            } elseif ($coupon_row->type == 'Individual Quantity') {
                $updates = applyProductQuantityCoupon($coupon_row, $cart_item, $user_id); /***updates is to only singel cart item not array*** */

                $add_coupon_to_table = empty($updates) ? false : true;
                if (!empty($updates)) {

                    array_push($cart_update_arr, $updates);
                } else {
                    $cart_update_arr_for_product_offer_detail[] = [
                        'id' => $cart_item->id, 'product_discount_offer_detail' => '',
                    ];
                }

            } elseif ($coupon_row->type == 'BOGO') {
                $updates = applyAutomaticBogoOffer($coupon_row, $cart_item, $user_id, $cart_items); /**updates contaians more than one cart item update and insert */

                $add_coupon_to_table = empty($updates['update_ar']) && empty($updates['insert_ar']) ? false : true;
                if (!empty($updates['update_ar'])) {
                    $cart_update_arr = array_merge($cart_update_arr, $updates['update_ar']);
                }

                if (!empty($updates['insert_ar'])) {
                    $cart_insert_arr = array_merge($cart_insert_arr, $updates['insert_ar']);
                }

            } else {
                $add_coupon_to_table = true;
            }
            if ($add_coupon_to_table) {

                $exist = false;
                /**check ccopun already exist in insertable coupon array for cart ans ship type qki they are applied once */
                if ($coupon_row->type == 'Cart' || $coupon_row->type == 'Shipping') {
                    foreach ($insert_ar_for_coupons_aplied as $l) {
                        if ($l['coupon_id'] == $coupon_row->id) {
                            $exist = true;
                        }
                    }
                    // $array_filter = array_filter($insert_ar_for_coupons_aplied, function ($v) use ($coupon_row) {
                    //     return $v['coupon_id'] == $coupon_row->id;
                    // });
                    // $exist = !empty($array_filter);
                } else {
                    // $array_filter = array_filter($insert_ar_for_coupons_aplied, function ($v) use ($coupon_row, $due_to_product_id) {
                    //     return $v['coupon_id'] == $coupon_row->id && $v['due_to_product_id'] == $due_to_product_id;
                    // });
                    // $exist = !empty($array_filter);
                    foreach ($insert_ar_for_coupons_aplied as $l) {
                        if ($l['coupon_id'] == $coupon_row->id && $l['due_to_product_id'] == $due_to_product_id) {
                            $exist = true;
                        }
                    }
                }
                if (!$exist) {
                    $insert_ar_for_coupons_aplied[] = [
                        'coupon_id' => $coupon_row->id,
                        'user_id' => $user_id,
                        'cart_session_id' => $cart_session_id,
                        'coupon_method' => $coupon_row->discount_method,
                        'coupon_type' => $coupon_row->type,
                        'due_to_product_id' => ($coupon_row->type == 'Bulk' || $coupon_row->type == 'Individual Quantity' || $coupon_row->type == 'BOGO') ? $due_to_product_id : null,
                        'insert_ar' => !empty($cart_insert_arr) ? json_encode($cart_insert_arr) : null,
                        'update_ar' => !empty($cart_update_arr) ? json_encode($cart_update_arr) : null,
                    ];

                }
            }

        }

        if (!empty($insert_ar_for_coupons_aplied)) {
            $actual_insert_ar = [];
            foreach ($insert_ar_for_coupons_aplied as $k) {

                $actual_insert_ar[] = $k;

                /***order place hone pe update custoemr coupon usage****  */
            }
            if (!empty($actual_insert_ar)) {

                \DB::table('applied_coupons')->insert($actual_insert_ar);

            }

        }
    }

    if (!empty($cart_update_arr_for_product_offer_detail)) {
        foreach ($cart_update_arr_for_product_offer_detail as $y) {
            \DB::table('carts')->whereId($y['id'])->update([
                'product_discount_offer_detail' => '', 'discount_rules' => null]);
        }

    }
    return;
}

function getItem($cart_items, $item_id)
{
    $item = null;
    foreach ($cart_items as $t) {
        if ($t->id == $item_id) {
            $item = $t;
            break;
        }
    }
    return $item;
}
function applyAppliedCouponsTableToCart($cart_items, $cart_session_id, $user_id, $should_update_cart = true)
{
    $applied_coupons = [];
    $cart_update_arr = [];
    $cart_insert_arr = [];
    $applied_coupons_names = [];
    $result = null;
    $applied_coupons = \DB::table('applied_coupons')->select('applied_coupons.*',
        'coupons.minimum_order_amount', 'coupons.coupon_code', 'coupons.details', 'coupons.name', 'coupons.quantity_rule',
        'coupons.discount_method', 'coupons.discount_type', 'coupons.discount', 'coupons.start_date', 'coupons.end_date', 'coupons.maximum_discount_limit')
        ->join('coupons', 'coupons.id', '=', 'applied_coupons.coupon_id')->where(['cart_session_id' => $cart_session_id,
        'user_id' => $user_id])->whereDate('coupons.start_date', '<=', Carbon::now())
        ->whereDate('coupons.end_date', '>=', Carbon::now())->where('coupons.status', 'Active')->get();

    if (!empty($applied_coupons)) {
        foreach ($applied_coupons as $coupon_row) {

            $applied_coupons_names[] = ['name' => $coupon_row->name, 'details' => $coupon_row->details,
                'due_to_product_id' => $coupon_row->due_to_product_id];
            if (in_array($coupon_row->coupon_type, ['Bulk', 'Individual Quantity', 'BOGO'])) {

                if (!empty($coupon_row->update_ar)) { /***update_ar containe per qty update  */
                    $p = json_decode($coupon_row->update_ar, true);
                    if ($coupon_row->coupon_method == 'Coupon Code') {
                        if ($coupon_row->coupon_type == 'Bulk') {
                            $p = array_map(function ($t) use ($cart_items, $coupon_row, $user_id) {
                                $item = getItem($cart_items, $t['id']);
                                // $t['net_cart_amount'] = $item->qty * $t['net_cart_amount'];
                                $t = applyBulkDiscountSingeItemRule($coupon_row, $item, $user_id);
                                return $t;
                            }, $p);
                        } elseif ($coupon_row->coupon_type == 'Individual Quantity') {

                            $p = array_map(function ($t) use ($cart_items, $coupon_row, $user_id) {
                                $item = getItem($cart_items, $t['id']);

                                $p = applyProductQuantityCoupon($coupon_row, $item, $user_id);

                                // $t['net_cart_amount'] = $p['net_cart_amount'];
                                // $t['total_discount'] = $p['total_discount'];
                                return !empty($p) ? $p : null;
                            }, $p);
                        }
                    }

                    $cart_update_arr = $p;

                }

                if (!empty($coupon_row->insert_ar)) {
                    $p = json_decode($coupon_row->insert_ar, true);
                    $cart_insert_arr = $p;

                }

            }
        }
        if ($should_update_cart) {
            if (!empty($cart_update_arr)) {

                $cartInstance = new \App\Models\Cart;
                $cart_update_arr = array_filter($cart_update_arr, function ($v) {
                    return !is_null($v);
                });
                Batch::update($cartInstance, $cart_update_arr, 'id');
            }
            if (!empty($cart_insert_arr)) {
                foreach ($cart_insert_arr as $item) {
                    $exist = \DB::table('carts')->where([
                        'product_id' => $item['product_id'],
                        'user_id' => $user_id,
                        'is_combo' => 'Yes',
                        'cart_session_id' => $cart_session_id])->count();
                    if ($exist == 0) {
                        \DB::table('carts')->insert($item);
                    }
                }

            }
        }
        $result = applyCartOfferAndShippingOfferDetail($cart_items, $user_id, $applied_coupons);

    }
    return ['cartValueAndrShippingDiscountresult' => $result, 'applied_coupons_names' => $applied_coupons_names];
}
function modifiedProductDetail($r, $setting, $categories_with_offer_ar, $products_with_offer_ar, $get_multiple_images_also = false)
{

    if (!empty($categories_with_offer_ar)) {
        foreach ($categories_with_offer_ar as $categories_with_offer) {
            if ($categories_with_offer['inclusive']) {
                if (in_array($r->category_id, $categories_with_offer['categories'])) {

                    $r->discount_type = $categories_with_offer['dis']['discount_type'];
                    $r->discount = $categories_with_offer['dis']['discount'];

                }
            } else {
                if (!in_array($r->category_id, $categories_with_offer['categories'])) {

                    $r->discount_type = $categories_with_offer['dis']['discount_type'];
                    $r->discount = $categories_with_offer['dis']['discount'];

                }
            }
        }
    }
    if (!empty($products_with_offer_ar)) {
        foreach ($products_with_offer_ar as $products_with_offer) {
            if ($products_with_offer['inclusive']) {
                if (in_array($r->id, $products_with_offer['products'])) {

                    $r->discount_type = $products_with_offer['dis']['discount_type'];
                    $r->discount = $products_with_offer['dis']['discount'];

                }
            } else {
                if (!in_array($r->id, $products_with_offer['products'])) {

                    $r->discount_type = $products_with_offer['dis']['discount_type'];
                    $r->discount = $products_with_offer['dis']['discount'];

                }
            }
        }
    }

    unset($r->vendor_id);
    unset($r->brand_id);
    $r->brand_name = $r->brand ? $r->brand->name : '';
    // $r->brands_list = $brands_to_send;
    $r->category_name = $r->category ? $r->category->name : '';
    unset($r->brand);
    unset($r->category);
    $r->attributes = json_decode($r->attributes, true);
    $r->thumbnail = new \stdClass;
    if ($r->image) {
        $r->thumbnail = getThumbnailsFromImage($r->image);
    }
    /**update sale price from here  */
    if (count($r->variants) == 0) {
        $discount = $r->discount_type != null
        ? ($r->discount_type == 'Flat'
            ? $r->discount
            : ($r->price * $r->discount / 100))
        : 0;

        $r->sale_price = $discount > 0 ? $r->price - $discount : $r->sale_price;
    } else {
        $variants = $r->variants;

        /**update variants for discounted sale price */
        $ar1 = [];
        $ar2 = [];
        foreach ($r->variants as $el) {
            // $el->thumbnail = new \stdClass;
            // if ($el->image) {
            //     $el->thumbnail = getThumbnailsFromImage($v->image);
            // }
            $discount = $r->discount_type != null
            ? ($r->discount_type == 'Flat'
                ? $r->discount
                : ($el->price * $r->discount / 100))
            : 0;

            $own_discount = $el->discount_type != null
            ? ($el->discount_type == 'Flat'
                ? $el->discount
                : ($el->price * $el->discount / 100))
            : 0;
            $el->sale_price = $own_discount > 0
            ? $el->price - $own_discount
            : ($discount > 0 ? $el->price - $discount : $el->sale_price);
            $el->sale_price = floatVal($el->sale_price);
            if ($el->quantity > 0) { /***for when vairant is out of stock place it at bottom so ye below arangment hai array mein daaleke merge so that bottom pe jaye 0 qty wala  */
                $ar1[] = $el;
            } else {
                $ar2[] = $el;
            }

            // return $el;

        }
        unset($r->variants);
        $r->variants = array_merge($ar1, $ar2);
        // dd($r->variants[0]->toArray());
        $r->price = $r->variants[0]->price;
        $r->sale_price = floatVal($r->variants[0]->sale_price);
    }
    if ($r->sgst == null) {
        if ($r->category->sgst > 0) {
            $r->sgst = $r->category->sgst;
        } elseif (!is_null($setting) && $setting->sgst > 0) {
            $r->sgst = $setting->sgst;
        }
    }
    if ($r->cgst == null) {
        if ($r->category->cgst > 0) {
            $product->cgst = $r->category->cgst;
        } elseif (!is_null($setting) && $setting->cgst > 0) {
            $r->cgst = $setting->cgst;
        }
    }
    if ($r->igst == null) {
        if ($r->category->igst > 0) {
            $rr->igst = $r->category->igst;
        }
    }
    if ($get_multiple_images_also) {
        foreach ($r->images as $img) {
            $img->thumbnail = getThumbnailsFromImage($img->name);
        }
    }
    if ($r->addon_products->count() > 0) {
        foreach ($r->addon_products as $l) {
            $th = getThumbnailsFromImage($l->image);
            $l->image = $th['medium'];
        }
    }
    
    $r->offers = getProductOffers($r->id);
    return $r;
}
function getCurrentAutomaticBulkOfferWithoutMinimumAmountSet()
{
    $categories_with_offer = [];
    $products_with_offer = [];
    $bulk_discounts = \DB::table('coupons')->whereStatus('Active')
        ->whereDate('start_date', '<=', Carbon::now())
        ->whereDate('end_date', '>=', Carbon::now())
        ->where('type', 'Bulk')->whereDiscountMethod('Automatic')
        ->whereNull('minimum_order_amount')
        ->get();
    if (count($bulk_discounts->toArray()) > 0) {
        foreach ($bulk_discounts as $dis) {
            $categories = $dis->category_id ? json_decode($dis->category_id, true) : null;
            $products = $dis->product_id ? json_decode($dis->product_id, true) : null;
            if (!is_null($categories) && is_null($products)) {
                $categories_with_offer[] = [
                    'inclusive' => $dis->include_or_exclude == 'Include',
                    'categories' => array_column($categories, 'id'),
                    'dis' => [
                        'rule_name' => $dis->name,
                        'discount_type' => $dis->discount_type,
                        'discount' => $dis->discount,

                    ]];

            } else {
                if (!is_null($products)) {
                    $products_with_offer[] = [
                        'inclusive' => $dis->include_or_exclude == 'Include',
                        'products' => array_column($products, 'id'),
                        'dis' => [
                            'rule_name' => $dis->name,
                            'discount_type' => $dis->discount_type,
                            'discount' => $dis->discount,

                        ]];

                }
            }

        }

    }
    return ['categories_with_offer' => $categories_with_offer, 'products_with_offer' => $products_with_offer];
}
function remove_duplicate_coupon_names($arr)
{
    $names = array_column($arr, 'name');
    $x = [];

    foreach ($arr as $k => $t) {
        if (in_array($t['name'], $x)) {
            unset($arr[$k]);
        } else {
            $x[] = $t['name'];
        }

    }
    return $arr;
}

function remove_duplicate_coupon_names1($arr)
{
    $names = array_column($arr, 'coupon_name');
    $x = [];

    foreach ($arr as $k => $t) {
        if (in_array($t['coupon_name'], $x)) {
            unset($arr[$k]);
        } else {
            $x[] = $t['coupon_name'];
        }

    }
    return $arr;
}
function checkAppliedCouponsValidity($cart_items)
{
    $cart_session_id = $cart_items[0]->cart_session_id;

    $deletable_applied_coupon_ids = [];
    $applied_coupons = \DB::table('applied_coupons')
        ->join('coupons', 'applied_coupons.coupon_id', '=', 'coupons.id')
        ->whereCartSessionId($cart_session_id)->get();
    foreach ($applied_coupons as $y) {
        $currentDateTime = Carbon::now();
        $coupon_start_date = Carbon::parse($y->start_date);
        $coupon_end_date = Carbon::parse($y->end_date);
        if ($currentDateTime->gt($coupon_end_date)) {
            $deletable_applied_coupon_ids[] = $y->id;
        } elseif ($y->status != 'Active') {
            $deletable_applied_coupon_ids[] = $y->id;
        }
        if ($y->coupon_type == 'Cart' || $y->coupon_type == 'Shipping') {
            if (!isCartCouponApplicable($cart_items, $y)) {
                $deletable_applied_coupon_ids[] = $y->id;
            }

        }
    }
    if ($deletable_applied_coupon_ids) {
        \DB::table('applied_coupons')->whereIn('id', $deletable_applied_coupon_ids)->delete();
    }

}
function getChoosenVariantName($variants, $variant_id)
{
    $name = '';
    foreach ($variants as $v) {
        if ($v->id == $variant_id) {
            $name = $v->name;
            break;
        }
    }
    return str_replace('-',' ',str_replace('_',' ',$name));

}
function getChoosenVariantAttributesJson($variants, $variant_id)
{
    $name = '';
    foreach ($variants as $v) {
        if ($v->id == $variant_id) {
            $name = $v->atributes_json;
            break;
        }
    }
    return $name;

}
function cartProductCount($cart, $p_id)
{
    $qty =0;
    foreach ($cart as $item) {
        if ($item['productId'] == $p_id) {
           $qty=$qty+ $item['qty'];
           
        }
    }
    return $qty;

}
function getVariantRowFromAttributeVals($attributes,$product_id)
{
    $row=null;
    
    $attribute_val = $attributes;
    $attribute_val = array_map(function ($v) {
        $v = str_replace(' ', '_', $v);
        return $v;
    }, $attribute_val);
   
       $rows= \DB::table('product_variants')->whereProductId($product_id)->get();
       foreach($rows as $g){
           $name_ar=explode('-',$g->name);
           if(count(array_intersect($attribute_val,$name_ar))==count($attribute_val))
           {
               $row=$g;
               break;
           }
          
       }
       return $row;


}
  function makeTree($array, $parent) {
    $return = [];
    foreach ($array as $key => $value) {
        if ($value['category_id'] == $parent) {
            $return[$value['id']] = [
               
                'title' => $value['name'],'image'=>$value['image']
            ];
            $subs = false;
            foreach ($array as $search) {
                if ($search['category_id'] == $value['id']) {
                    $subs = true;
                }
            }
            if ($subs === true) {
                $return[$value['id']]['subs'] = makeTree($array, $value['id']);
            }
        }
    }
    return $return;
}
function getMenu($id=null){
    $data['categories'] = \App\Models\Category::whereStatus('Active')->get(['id','name','category_id','image'])->toArray();
    $s = [];
     $i = 0;
    return  makeTree($data['categories'], $id);
}
