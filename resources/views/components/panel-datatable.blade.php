<div class="content">
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h5 class="panel-title">{{ $title }} </h5>
        </div>
        <table class="table datatable-colvis-basic text-center"  data-order='[[ 0, "desc" ]]'>
            {{ $slot }}
        </table>
    </div>
</div>
