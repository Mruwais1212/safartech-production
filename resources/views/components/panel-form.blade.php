<div class="content">
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h5 class="panel-title">{{ $title }}</h5>
        </div>
        <div class="panel-body">
            <form method="post" class="form-horizontal" action="{{ '/' . app()->getLocale() . $url }}"
                enctype="multipart/form-data">
                @csrf
                @if (isset($object) && $formMethod == 'PUT')
                    <input type="hidden" name="_method" value="PUT" />
                @endif
                <fieldset class="content-group">{{ $slot }}</fieldset>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary">{{ $title }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
