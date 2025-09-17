    @props(['default'])

    @if (is_null(json_decode($default)))
    @php
 

                    $path = storage_path('app/public/' . $default);
                    $absolute_path = $path;
                    if (!\File::exists($path)) {
                        $path = null;
                    } else {
                        $path = asset('storage/' . $default);
                    }
                $onclick = "deleteFileFromPath('".$absolute_path."')";
                @endphp
        @if (str_contains($default, '.jpg') ||
                str_contains($default, '.png') ||
                str_contains($default, '.gif') ||
                str_contains($default, '.jpeg') ||
                str_contains($default, '.webp') ||
                str_contains($default, '.avif'))
            
            @if ($path)
                <div >
                <i class="remove bx bx-trash" @if ($path) onclick="{{ $onclick }}" @endif></i>
                
                    <img style="width:100px;height:100px;margin:10px" src="{{ $path }}" /></div>
            @endif
        @else
            @if (str_contains($default, '.pdf') ||
                    str_contains($default, '.docx') ||
                    str_contains($default, '.docs') )
                    

            
                @if ($path && $default)
                    <br>
                    <i class="bx bx-download"></i> <a href="{{ $path }}" download>{{ $default }}</a>
                @endif
            @else
            @if ($path)
               {{$default}}
            @endif
            @endif
        @endif
    @else
        @php
            $default = json_decode($default, true);
            //default=['id'=>'1','name'=>'image2.jpeg','folder'=>'users','table'=>'ss']
        @endphp
        @if (count($default) > 0)

            @foreach ($default as $item)
                @php
                    $path = storage_path('app/public/' . $item['folder'] . '/' . $item['name']);
                    //  dd($path);
                    if (!\File::exists($path)) {
                        $path = null;
                    } else {
                        $path = asset('storage/' . $item['folder'] . '/' . $item['name']);
                    }
                @endphp
                @if ($path)
                <div >
                <i class="remove bx bx-trash" @if ($path) onclick="{{ $onclick }}" @endif></i>
                    <x-image :name="$item['name']" :path="$path" :id="$item['id']" :folderName="$item['folder']" :table="$item['table']" />
                </div>
                    @endif
            @endforeach
        @endif
    @endif
