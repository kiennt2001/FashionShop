$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function removeRow(id, url){
    if(confirm('Bạn có chắc xóa danh mục này?')){
        $.ajax({
            type: 'delete',
            datatype: 'json',
            data: {id},
            url: url,
            success: function(result){
                if(result.error === false){
                    alert(result.message);
                    location.reload();
                }else{
                    alert('Xóa lỗi. Vui lòng thử lại!');
                }
            }
        })
    }
}

//Upload file
/*Upload File */
$('#upload').change(function () {
    const form = new FormData();
    form.append('file', $(this)[0].files[0]);

    $.ajax({
        processData: false,
        contentType: false,
        type: 'post',
        datatype: 'json',
        data: form,
        url: '/admin/upload/services',
        success: function (results) {
            if (results.error === false) {
                $('#image_show').html('<a href="' + results.url + '" target="_blank">' +
                    '<img src="' + results.url + '" width="100px"></a>');

                $('#thumb').val(results.url);
            } else {
                alert('Upload File Lỗi');
            }
        }
    });
});