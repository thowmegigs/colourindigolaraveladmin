<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Product;
use \Carbon\Carbon;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
  
    public function index(Request $r, $slug = null)
    {
        $setting = \DB::table('settings')->first();
     
        $list = [];
        $per_page = 20;
       
            $category_name = urldecode($slug);
            $category = \App\Models\Category::with('category:id,name')->where('slug', $slug)->first();
            // dd($category->toArray());
            if (!is_null($category)) {
                $category_id = $category->id;
                session(['category_id' => $category_id]);
                $all_childs_ids_ar=$category->getAllChildIds();
                $child_categories = \App\Models\Category::whereIn('id', $all_childs_ids_ar)->get(['id', 'name','image']);
                if($child_categories->count()==0)
                $child_categories = \App\Models\Category::where('category_id', $category->category_id)->get(['id', 'name','image']);
                $all_childs_ids_ar=array_merge([$category_id],$all_childs_ids_ar);
                session(['all_childs_ids_ar' => json_encode($all_childs_ids_ar)]);
                $page = $r->has('page') ? $r->page : 1;
                $brands_rows = Product::with(['brand:id,name'])->whereIn('category_id',$all_childs_ids_ar)->get();
                $brands_to_send = [];

                foreach ($brands_rows as $h) {
                    $brands = [];
                    if ($h->brand!==null && (!in_array($h->brand->id, array_column($brands_to_send,'id')))) {
                        $brands_to_send[] =['id'=>$h->brand->id,'name'=> $h->brand->name];
                    }

                }
                // $brands_to_send=array_unique($brands_to_send);

                $min_price = Product::whereIn('category_id',$all_childs_ids_ar)->min('sale_price');
                $max_price = Product::whereIn('category_id',$all_childs_ids_ar)->max('sale_price');

               
               
                $respl['category'] = $category;
                $respl['minPrice'] = $min_price;
                $respl['maxPrice'] = $max_price;
                $respl['brands_list'] = $brands_to_send;
                $respl['child_categories'] = $child_categories;
                  $respl['setting']=$setting;
                return view('frontend.product_list', with($respl));
            } else {
                return abort(404);
            }
        

    }
    public function getProductList(Request $r)
    {
        $setting = \DB::table('settings')->first();
        $list = [];
        $per_page = 10;
        $category_id = session('category_id');
       $all_childs_ids_ar=json_decode(session('all_childs_ids_ar'),true);
      
        $minPriceRange = $r->has('min_price') ? ltrim($r->min_price, '$') : null;
        $child_categories = $r->has('child_categories') ? $r->child_categories : [];
        $maxPriceRange = $r->has('max_price') ? ltrim($r->max_price, '$') : null;
        //  dd(json_decode(urldecode($r->priceRange),true));
        $brands = $r->has('brands') ? $r->brands : [];

        // Log::info('show for: {id}', ['id' => $category]);
        $sort_by = $r->has('sort_by') ? $r->sort_by : null;

        $list = Product::with(['brand:id,name', 'category:id,name', 'variants'])->
            when(!empty($child_categories), function ($query) use ($child_categories) {
            return $query->whereIn('category_id', $child_categories);
        })->when(empty($child_categories), function ($query) use ($all_childs_ids_ar) {
            return $query->whereIn('category_id',$all_childs_ids_ar);
        })->when(!empty($brands), function ($query) use ($brands) {
            return $query->whereIn('brand_id', $brands);
        
        })->when(!empty($minPriceRange), function ($query) use ($minPriceRange) {
            return $query->where('sale_price', '>=', $minPriceRange);

        })->when(!empty($maxPriceRange), function ($query) use ($maxPriceRange) {
            return $query->where('sale_price', '<=', $maxPriceRange);

        })->when(!empty($sort_by), function ($query) use ($sort_by) {
            return $query->orderBy($sort_by != 'Rating' ? 'sale_price' : 'rating', $sort_by);

        })->paginate($per_page);

        $dimensions = getThumbnailDimensions();
        $categories_with_offer = getCurrentAutomaticBulkOfferWithoutMinimumAmountSet()['categories_with_offer'];
        $products_with_offer = getCurrentAutomaticBulkOfferWithoutMinimumAmountSet()['products_with_offer'];
        $list->getCollection()->transform(function ($r) use ($setting, $categories_with_offer, $products_with_offer) {

            return modifiedProductDetail($r, $setting, $categories_with_offer, $products_with_offer);
        });
        $ar=$list->toArray();
        return response()->json(['success' => true, 'view' => view('frontend.partials.filter_product_list', ['list' => $list])->render(),'current_count'=>count($ar['data']), 'product_count' => $ar['total']], 200);

    }


    public function show($slug)
    {
        // $name=str_replace('-',' ',$name);
        $setting = \DB::table('settings')->first();
        // \DB::enableQueryLog();
        $product = Product::with(['brand:id,name', 'category:id,name,sgst,cgst,igst', 'variants', 'images', 'addon_products:id,name,price,sale_price,image', 'addon_products.variants'])
            ->where('slug', urldecode($slug))->first();

        
        $id = $product->id;
        $bulk_discounts = \DB::table('coupons')->whereStatus('Active')
            ->whereDate('start_date', '<=', Carbon::now())
            ->whereDate('end_date', '>=', Carbon::now())
            ->where('type', 'Bulk')->whereDiscountMethod('Automatic')
            ->whereNull('minimum_order_amount')
            ->get();
        $category_dis = [];
        $prod_dis = [];

        $dimensions = getThumbnailDimensions();
        $related_products = Product::with(['brand:id,name', 'variants', 'images'])
            ->whereCategoryId($product->category_id)->where('id', '!=', $id)->limit(20)->get();

        $categories_with_offer = getCurrentAutomaticBulkOfferWithoutMinimumAmountSet()['categories_with_offer'];
        $products_with_offer = getCurrentAutomaticBulkOfferWithoutMinimumAmountSet()['products_with_offer'];

        $product = modifiedProductDetail($product, $setting, $categories_with_offer, $products_with_offer, true);

        if (!empty(count($related_products) > 0)) {
            $related_products = $related_products->map(function ($r) use ($setting, $categories_with_offer, $products_with_offer) {

                return modifiedProductDetail($r, $setting, $categories_with_offer, $products_with_offer);
            });

        }
        //dd($product->addon_items);
        // dd( json_decode($product->variants[0]->atributes_json,true));
        return view('frontend.product', ['product' => $product, 'related_products' => $related_products]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function search(Request $r)
    {
        $search_string = trim($r->search_string);
        // dd($search_string);
        // $result=\App\Models\Product::whereName('')
        // $query = \App\Models\Product::with('category:id,name')->where('name', 'like', '%' . $search_string . '%')->get();
        //$products=[];
        $query = \App\Models\Product::with('category:id,name')->whereRaw(
            "MATCH(name) AGAINST(?)",
            array($search_string)
        )->get(['id', 'name', 'image', 'category_id']);
        foreach ($query as $t) {
            $t->cat_name = $t->category->name;
            unset($t->category);
            $r->thumbnail = new \stdClass;
            if ($t->image) {
                $thum = getThumbnailsFromImage($t->image);
                $t->thumbnail = !empty($thum) ? $thum['small'] : '';
            }

        }
        return response()->json(['success' => true, 'data' => $query], 200);

    }
    public function collection_products(Request $r, $id)
    {
        $collection_id = null;
       
      
        $per_page = 12;
        $min_price = 0;
        $max_price = 0;
       
        $collection =\App\Models\Collection::whereId($id)->first();
        
        $per_page = 12;
        $min_price = 0;
        $max_price = 0;
        session(['collection_id'=>$id]);
      
        $collection_type = $collection->collection_type;
        $all_categories = $collection->category_id != null ? json_decode($collection->category_id, true) : [];
        $sort_by = $r->has('sort_by') ? $r->sort_by : null;
        //$orderByDiscount = $r->has('orderbyDiscount') ? $r->orderbyDiscount : null;
        $minPriceRange =  null;
        $maxPriceRange =  null;
        $categories = $r->has('categories') ? $r->categories : [];
        $brands = $r->has('brands') ? $r->brands : [];
        
        $all_categories = [];
        $brands_to_send = [];
        $price_list = [];
        $products=null;
       $collection_type = $collection->collection_type;
         $product_ids=[];
          if ($collection_type == 'Manual') {
             
               $product_ids = array_column(json_decode($collection->product_id, true), 'id');
               $products = Product::whereIn('id',$product_ids)->get(['id','category_id','brand_id','sale_price']);
        } else {
            $conditions = json_decode($collection->conditions, true);
            if ($conditions[0]['name'] != null || $conditions[0]['name'] != 'null') {
                $products = Product::when(!is_null($conditions), function ($query) use ($conditions) {
                    foreach ($conditions as $t) {

                        if ($t['name'] == 'Price') {
                            $comp = $t['comparison'] == 'Gt' ? '>=' : ($t['comparison'] == 'Lt' ? '<=' : '==');
                            $query = $query->where('sale_price', $comp, $t['value']);
                        } elseif ($t['name'] == 'Tag') {
                            $query = $query->whereJsonContains('tags', [$t['value']]);
                        } elseif ($t['name'] == 'Discount') {
                            $products_with_offer = array_filter($products_with_offer, function ($v) {
                                if ($t['comparison'] == 'Gt') {
                                    return $v['discount_type'] == 'Percent' && $v['discount'] >= $t['value'];
                                } elseif ($t['comparison'] == 'Lt') {
                                    return $v['discount_type'] == 'Percent' && $v['discount'] <= $t['value'];
                                } elseif ($t['comparison'] == 'Eq') {
                                    return $v['discount_type'] == 'Percent' && $v['discount'] == $t['value'];
                                }

                            });
                            $comp = $t['comparison'] == 'Gt' ? '>=' : ($t['comparison'] == 'Lt' ? '<=' : '==');
                            $query = $query->whereRaw('price>sale_price AND round(((price-sale_price)/price)*100)' . $comp . $t['value']);
                            if (!empty($products_with_offer)) {
                                foreach ($products_with_offer as $g) {
                                    $query = $query->whereIn('id', $g['products']);
                                }
                            }

                        }

                    }
                    return $query;
                })->get(['id','category_id','brand_id','sale_price']);
 
               
            }

        }
       
        $all_categories = [];
        $brands_to_send = [];
        $price_list = [];
        foreach ($products as $pr) {

            if (!in_array($pr->category_id, array_column($all_categories, 'id'))) {
                array_push($all_categories, ['id' => $pr->category_id, 'name' => $pr->category->name]);
            }

            if (!in_array($pr->brand_id, array_column($brands_to_send, 'id'))) {
                array_push($brands_to_send, ['id' => $pr->brand_id, 'name' => $pr->brand->name]);
            }

            array_push($price_list, $pr->sale_price);

        }
    
//dd($price_list);
        
        $respl['minPrice'] = min($price_list);
        $respl['maxPrice'] =  max($price_list);
        $respl['brands_list'] = $brands_to_send;
        $respl['child_categories'] = $all_categories;
        $respl['collection']=$collection;
        //  dd($respl);
        return view('frontend.collection_products', with($respl));

    }
    public function ajax_collection_products(Request $r)
    {
        $collection_id = null;
        $setting = \DB::table('settings')->first();
        $collection_id = session('collection_id');

        $per_page = 20;
        $min_price = 0;
        $max_price = 0;
       $collection =\App\Models\Collection::whereId($collection_id)->first();

       
        $collection_type = $collection->collection_type;
     
        $sort_by = $r->has('sort_by') ? $r->sort_by : null;
        //$orderByDiscount = $r->has('orderbyDiscount') ? $r->orderbyDiscount : null;
        $minPriceRange = $r->has('min_price') ? $r->min_price : null;
        $maxPriceRange = $r->has('max_price') ? $r->max_price : null;
        $categories = $r->has('child_categories') ? $r->child_categories : [];
        $brands = $r->has('brands') ? $r->brands : [];
        $categories_with_offer = getCurrentAutomaticBulkOfferWithoutMinimumAmountSet()['categories_with_offer'];
        $products_with_offer = getCurrentAutomaticBulkOfferWithoutMinimumAmountSet()['products_with_offer'];
        $list = [];
        $url = request()->url();
        $queryParams = request()->query();

        ksort($queryParams);

        $queryString = http_build_query($queryParams);

        $fullUrl = "{$url}?{$queryString}";

        $rememberKey = sha1($fullUrl);
        if ($collection_type == 'Manual') {

            $product_ids = array_column(json_decode($collection->product_id, true), 'id');
            $list = Product::with(['brand:id,name', 'category:id,name', 'variants'])->
                when(!empty($categories), function ($query) use ($categories) {
                return $query->whereIn('category_id', $categories);
            })->when(!empty($brands), function ($query) use ($brands) {
                return $query->whereHas('brand', function ($q1) {
                    $q1->whereIn('name', $brands);
                });
            })->when(!empty($minPriceRange), function ($query) use ($minPriceRange) {
                return $query->where('sale_price', '>=', $minPriceRange);

            })->when(!empty($maxPriceRange), function ($query) use ($maxPriceRange) {
                return $query->where('sale_price', '<=', $maxPriceRange);

            })->when(!empty($sort_by), function ($query) use ($sort_by) {
                return $query->orderBy($sort_by != 'Rating' ? 'sale_price' : 'rating', $sort_by);

            })->whereIn('id', $product_ids)->paginate(300);

            $dimensions = getThumbnailDimensions();
            
            $list->getCollection()->transform(function ($r) use ($setting, $categories_with_offer, $products_with_offer) {
                return modifiedProductDetail($r, $setting, $categories_with_offer, $products_with_offer);

            });
        } else {
            $conditions = json_decode($collection->conditions, true);
            if ($conditions[0]['name'] != null || $conditions[0]['name'] != 'null') {
                $list = Product::with(['brand:id,name', 'category:id,name', 'variants'])->
                    when(!empty($categories), function ($query) use ($categories) {
                    return $query->whereIn('category_id', $categories);
                })->
                    when(!is_null($conditions), function ($query) use ($conditions, $products_with_offer) {
                    foreach ($conditions as $t) {

                        if ($t['name'] == 'Price') {
                            $comp = $t['comparison'] == 'Gt' ? '>=' : ($t['comparison'] == 'Lt' ? '<=' : '==');
                            $query = $query->where('sale_price', $comp, $t['value']);
                        } elseif ($t['name'] == 'Tag') {
                            $query = $query->whereJsonContains('tags', [$t['value']]);
                        } elseif ($t['name'] == 'Discount') {
                            $products_with_offer = array_filter($products_with_offer, function ($v) {
                                if ($t['comparison'] == 'Gt') {
                                    return $v['discount_type'] == 'Percent' && $v['discount'] >= $t['value'];
                                } elseif ($t['comparison'] == 'Lt') {
                                    return $v['discount_type'] == 'Percent' && $v['discount'] <= $t['value'];
                                } elseif ($t['comparison'] == 'Eq') {
                                    return $v['discount_type'] == 'Percent' && $v['discount'] == $t['value'];
                                }

                            });
                            $comp = $t['comparison'] == 'Gt' ? '>=' : ($t['comparison'] == 'Lt' ? '<=' : '==');
                            $query = $query->whereRaw('price>sale_price AND round(((price-sale_price)/price)*100)' . $comp . $t['value']);
                            if (!empty($products_with_offer)) {
                                foreach ($products_with_offer as $g) {
                                    $query = $query->whereIn('id', $g['products']);
                                }
                            }

                        }

                    }
                    return $query;
                })->when(!empty($brands), function ($query) use ($brands) {
                    return $query->whereIn('brand_id', $brands);
                    
                })->when(!empty($minPriceRange), function ($query) use ($minPriceRange) {
                    return $query->where('sale_price', '>=', $minPriceRange);

                })->when(!empty($maxPriceRange), function ($query) use ($maxPriceRange) {
                    return $query->where('sale_price', '<=', $maxPriceRange);

                })->when(!empty($sort_by), function ($query) use ($sort_by) {
                    return $query->orderBy($sort_by != 'Rating' ? 'sale_price' : 'rating', $sort_by);

                })->paginate($per_page);
 
                $dimensions = getThumbnailDimensions();
                $list->getCollection()->transform(function ($r) use ($setting, $categories_with_offer, $products_with_offer) {
                    return modifiedProductDetail($r, $setting, $categories_with_offer, $products_with_offer);

                });
            }

        }

        $ar=$list->toArray();
        return response()->json(['success' => true, 'view' => view('frontend.partials.filter_product_list', ['list' => $list])->render(),'current_count'=>count($ar['data']), 'product_count' => $ar['total']], 200);

    }

    public function more_products(Request $r, $content_section_id = null)
    {
        $content_section_id = null;
        $setting = \DB::table('settings')->first();
        $content_section_id = $r->content_section_id;
        session(['content_section_id' => $content_section_id]);

        $per_page = 12;
        $min_price = 0;
        $max_price = 0;
        $collection = \App\Models\ContentSection::whereId($content_section_id)->with('products:id,name,price,sale_price,category_id,brand_id', 'products.category:id,name', 'products.brand:id,name')->first();
        //($content_section_id);
        $product_ids = [];
        $all_categories = [];
        $brands_to_send = [];
        $price_list = [];
        foreach ($collection->products as $pr) {

            if (!in_array($pr->category_id, array_column($all_categories, 'id'))) {
                array_push($all_categories, ['id' => $pr->category_id, 'name' => $pr->category->name]);
            }

            if (!in_array($pr->brand_id, array_column($brands_to_send, 'id'))) {
                array_push($brands_to_send, ['id' => $pr->brand_id, 'name' => $pr->brand->name]);
            }

            array_push($price_list, $pr->sale_price);

        }
        $minPriceRange = $r->has('min_price') ? $r->min_price : min($price_list);
        //dd($minPriceRange);
        $maxPriceRange = $r->has('max_price') ? $r->max_price : max($price_list);
        $categories = $r->has('child_categories') ? $r->categories : [];
        $brands = $r->has('brands') ? $r->brands : [];

        //  $respl['data'] = $list;
        // $respl['category'] = $all_categories;
        $respl['minPrice'] = $minPriceRange;
        $respl['maxPrice'] = $maxPriceRange;
        $respl['brands_list'] = $brands_to_send;
        $respl['child_categories'] = $all_categories;
        $respl['section_name']=$collection->section_title;
        //  dd($respl);
        return view('frontend.more_products', with($respl));

    }
    public function ajax_more_products(Request $r)
    {
        $content_section_id = null;
        $setting = \DB::table('settings')->first();
        $content_section_id = session('content_section_id');
        // dd($content_section_id);
        $per_page = 12;
        $min_price = 0;
        $max_price = 0;
        $collection = \App\Models\ContentSection::whereId($content_section_id)->first();
        //dd($collection->products->toArray());
        $product_ids = [];
        $all_categories = [];
        $brands_to_send = [];
        $price_list = [];
        foreach ($collection->products as $pr) {
            array_push($product_ids, $pr->id);
            if (!in_array($pr->category_id, array_column($all_categories, 'id'))) {
                array_push($all_categories, ['id' => $pr->category_id, 'name' => $pr->category->name]);
            }

            if (!in_array($pr->brand_id, array_column($brands_to_send, 'id'))) {
                array_push($brands_to_send, ['id' => $pr->brand_id, 'name' => $pr->brand->name]);
            }

            array_push($price_list, $pr->sale_price);

        }
        //  dd($all_categories);
        $sort_by = $r->has('sort_by') ? $r->sort_by : null;
        //$orderByDiscount = $r->has('orderbyDiscount') ? $r->orderbyDiscount : null;
        $minPriceRange = $r->has('min_price') ? $r->min_price : min($price_list);
        $maxPriceRange = $r->has('max_price') ? $r->max_price : max($price_list);
        $categories = $r->has('child_categories') ? $r->child_categories : [];
        $brands = $r->has('brands') ? $r->brands : [];

        $categories_with_offer = getCurrentAutomaticBulkOfferWithoutMinimumAmountSet()['categories_with_offer'];
        $products_with_offer = getCurrentAutomaticBulkOfferWithoutMinimumAmountSet()['products_with_offer'];
        $list = [];
        $url = request()->url();
        $queryParams = request()->query();

        ksort($queryParams);

        $queryString = http_build_query($queryParams);

        $fullUrl = "{$url}?{$queryString}";

        $rememberKey = sha1($fullUrl);
        $list = Product::with(['brand:id,name', 'category:id,name', 'variants'])->
            when(!empty($categories), function ($query) use ($categories) {
            return $query->whereIn('category_id', $categories);
        })->when(!empty($brands), function ($query) use ($brands) {
            return $query->whereIn('brand_id', $brands);
            
        })->when(!empty($minPriceRange), function ($query) use ($minPriceRange) {
            return $query->where('sale_price', '>=', $minPriceRange);

        })->when(!empty($maxPriceRange), function ($query) use ($maxPriceRange) {
            return $query->where('sale_price', '<=', $maxPriceRange);

        })->when(!empty($sort_by), function ($query) use ($sort_by) {
            return $query->orderBy($sort_by != 'Rating' ? 'sale_price' : 'rating', $sort_by);

        })->whereIn('id', $product_ids)->paginate($per_page);

        $dimensions = getThumbnailDimensions();

        $list->getCollection()->transform(function ($r) use ($setting, $categories_with_offer, $products_with_offer) {
            return modifiedProductDetail($r, $setting, $categories_with_offer, $products_with_offer);

        });
     $ar=$list->toArray();
        return response()->json(['success' => true, 'view' => view('frontend.partials.filter_product_list', ['list' => $list])->render(),'current_count'=>count($ar['data']), 'product_count' => $ar['total']], 200);

    }
    public function getVariantPrice(Request $r)
    {
        $post = $r->all();

        $attributes = $post['attributes']; /****array of attribute values only not attribute in fetch request */

        $variant_row = getVariantRowFromAttributeVals($attributes, $r->product_id);
        return response()->json(['success' => true, 'price' => $variant_row->price, 'sale_price' => $variant_row->sale_price,'variant_id' => $variant_row->id], 200);
    }
}
