@extends('admin.main')

@section('head')
<script src="/ckeditor5-build-classic/ckeditor.js"></script>
@endsection

@section('content')
<form action="" method="post">
    <div class="card-body">
        <div class="form-group">
        <label>Tên danh mục</label>
        <input type="text" name="name" class="form-control" placeholder="Nhập tên danh mục">
        </div>

        <div class="form-group">
        <label>Danh mục</label>
        <select name="parent_id" class="form-control">
            <option value="0">Danh mục cha</option>
            @foreach($menus as $menu)
                <option value="{{$menu->id}}">{{$menu->name}}</option>
            @endforeach
        </select>
        </div>

        <div class="form-group">
        <label>Mô tả</label>
        <textarea name="description" class="form-control"></textarea>
        </div>

        <div class="form-group">
        <label>Mô tả chi tiết</label>
        <textarea name="content" class="form-control" id="content"></textarea>
        </div>

        <!-- radio -->
        <div class="form-group">
        <label for="">Kích hoạt</label>
        <div class="custom-control custom-radio">
            <input class="custom-control-input" type="radio" value="1" id="active" name="active" checked="">
            <label for="active" class="custom-control-label">Có</label>
        </div>
        <div class="custom-control custom-radio">
            <input class="custom-control-input" type="radio" value="0" id="no_active" name="active">
            <label for="no_active" class="custom-control-label">Không</label>
        </div>
        
        </div>
    </div>

    <div class="card-footer">
        <button type="submit" class="btn btn-primary">Tạo danh mục</button>
    </div>
    @csrf
</form>
@endsection

@section('footer')
<script>
    ClassicEditor
        .create( document.querySelector( '#content' ) )
        .catch( error => {
            console.error( error );
        } );
</script>
@endsection