<form class="form form-horizontal" id="modalForm">

    <div class="form-group-sm row mb-1">
        <label class="col-md-5 col-form-label">Nama Cabang</label>
        <div class="col-md-7">
            <input type="text" name="user_fullname" class="form-control" placeholder="Nama Cabang" value="">
        </div>
    </div>
    <div class="form-group-sm row mb-1">
        <label class="col-md-5 col-form-label">Nama Pengguna</label>
        <div class="col-md-7">
            <input type="text" name="user_name" class="form-control" placeholder="Nama Pengguna" value="">
        </div>
    </div>
    <div class="form-group-sm row mb-1">
        <label class="col-md-5 col-form-label">Password</label>
        <div class="col-md-7">
            <input type="password" name="user_password" class="form-control" placeholder="Password" value="">
        </div>
    </div>
    <div class="form-group-sm row mb-1">
        <label class="col-md-5 col-form-label">Alamat</label>
        <div class="col-md-7">
            <textarea name="user_address" class="form-control" placeholder="Alamat lengkap"></textarea>
        </div>
    </div>
    <div class="form-group-sm row mb-1">
        <div class="col">
            <span class="text-danger informasi"></span>
        </div>
    </div>
</form>

<script>
    $('#modalForm').submit(function (e) {
        $('.informasi').html('');
        $('.btn-submit').html('<i class="fa fa-spin fa-refresh"></i> Simpan');
        $.ajax({
            url     : '<?php echo base_url('admin/add_cabang_submit');?>',
            type    : 'POST',
            dataType: 'JSON',
            data    : $(this).serialize(),
            success : function (dt) {
                if (dt.t == 0){
                    $('.informasi').html(dt.msg);
                    $('.btn-submit').html('<i class="fa fa-check"></i> Simpan');
                } else {
                    var rowNode = t.row.add([
                        '<input type="checkbox" name="user_id[]" value="'+dt.data.cab_id+'">',
                        dt.data.user_fullname,
                        dt.data.user_name,
                        dt.data.user_password,
                        dt.data.user_address,
                        '0',
                        '<a title="Data Pengguna Cabang" href="'+base_url+'admin/anggota_cabang/'+dt.data.cab_id+'" onclick="load_page(this);return false" class="btn btn-sm btn-primary"><i class="fa fa-users"></i></a> ' +
                        '<a title="Edit Data" href="'+base_url+'admin/edit_cabang/'+dt.data.cab_id+'" onclick="show_modal(this);return false" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i></a> ' +
                        '<a title="Delete Data" href="javascript:;" onclick="delete_data(this);return false" data-id="'+dt.data.cab_id+'" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>'
                    ]).draw().node();
                    $( rowNode ).addClass('row_'+dt.data.cab_id);
                    $('#myModalLG').modal('hide');
                    $('.btn-submit').html('<i class="fa fa-check"></i> Simpan');
                    show_alert('Berhasil menambahkan pengguna');
                }
            }
        })
        return false;
    })
    $('#myModalLG .modal-footer').html('<button type="button" onclick="$(\'#modalForm\').submit();return false" class="btn-submit btn-sm btn btn-primary"><i class="fa fa-check"></i> Simpan</button>\n' +
        '                <button type="button" class="btn-sm btn btn-secondary" data-dismiss="modal"> Tutup</button>')
</script>