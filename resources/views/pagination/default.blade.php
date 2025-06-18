<style>
.page-item{
border:1px solid #eceff4;background:white!important;
}

</style>
@if ($paginator->lastPage() > 1)
<nav aria-label="Page navigation" > 
        <ul class="pagination">
            
            <li class="page-item  {{ ($paginator->currentPage() == 1) ? ' disabled' : '' }}">
                <a  @click="filter_data.page=1" class="page-link" href="javascript:void(0)">&larr;</a>
            </li>
             <li class="page-item">
                <a class="page-link" @click="filter_data.page={!! $paginator->currentPage()-1 !!}" href="javascript:void(0)">
                    <i class="tf-icon bx bx-chevron-left"></i></a>
            </li>
            @for ($i = 1; $i <= $paginator->lastPage(); $i++)
                <li class="page-item {{ ($paginator->currentPage() == $i) ? ' active' : '' }}">
                    <a  @click="filter_data.page={!! $i !!}" class="page-link" href="javascript:void(0)" >{{ $i }}</a>
                </li>
            @endfor
            <li class="page-item">
                <a class="page-link" @click="filter_data.page={!! $paginator->currentPage()+1 !!}" 
                    >
                    <i class="tf-icon bx bx-chevron-right"></i></a>
            </li>
            <li class="page-item {{ ($paginator->currentPage() == $paginator->lastPage()) ? ' disabled' : '' }}">
                <a @click="filter_data.page={!! $paginator->lastPage() !!}"  class="page-link" href="javascript:void(0)"  >
                    &rarr;</a>
            </li>
        </ul>
</nav>
@endif

