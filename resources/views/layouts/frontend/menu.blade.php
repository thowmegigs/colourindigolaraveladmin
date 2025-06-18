@php
$cats = \Cache::remember('categories-web', 7*24*3600, function(){
return \App\Models\Category::with('children.chidren')
->whereNull('category_id')
->get();
});
$is_two_level_category=false;
@endphp

<ul class="menu vertical-menu category-menu">
	@if ($cats->count() > 0)
									@foreach ($cats as $cat)
								<li>
									<a href="shop-fullwidth-banner.html">
										<i class="{{$cat->icon}}"></i>{{$cat->name}}
									</a>
									@if ($cat->children->count() > 0)
									<ul class="submenu">
									@foreach ($cat->children as $child)
											<li class="has-submenu">
											@if($child->chidren->count()>0)
												<h4 class="menu-title">{{ $child->name }}</h4>
												<hr class="divider">
												
												<ul>
													@foreach ($child->children as $sub_child)
													<li><a href="/category/{{ $sub_child->slug }}">{{ $sub_child->name }}</a>
													</li>
													@endforeach

												</ul>
												@else
												<a href="/category/{{ $child->slug }}">{{ $child->name }}</a>	
											@endif
										</li>
										
									</ul>
									@endif
								</li>
								@endforeach
								@endif
							</ul>
	
