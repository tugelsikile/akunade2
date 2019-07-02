<form class="form form-horizontal" id="modalForm">

    <div class="form-group-sm row mb-1">
        <label class="col-md-5 col-form-label">Nama Lengkap</label>
        <div class="col-md-7">
            <input type="text" name="user_fullname" class="form-control" placeholder="Nama Lengkap" value="">
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
        <label class="col-md-5 col-form-label">Bandwidth</label>
        <div class="col-md-7">
            <input type="number" name="user_bw" min="1" class="form-control" placeholder="Bandwidth (dalam mbps)">
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
            url     : '<?php echo base_url('admin/add_user_submit');?>',
            type    : 'POST',
            dataType: 'JSON',
            data    : $(this).serialize(),
            success : function (dt) {
                if (dt.t == 0){
                    $('.informasi').html(dt.msg);
                    $('.btn-submit').html('<i class="fa fa-check"></i> Simpan');
                } else {
                    var rowNode = t.row.add([
                        '<input type="checkbox" name="user_id[]" value="'+dt.id+'">',
                        dt.uname,
                        '<span class="ufull_'+dt.id+'">'+dt.ufull+'</span>' +
                        '<div class="btn-group btn-group-sm pull-right">' +
                            '<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
                                '<i class="fa fa-gear"></i>' +
                            '</button>' +
                            '<div class="dropdown-menu">' +
                                '<a title="Rubah Data" href="'+base_url+'admin/edit_user/'+dt.id+'" onclick="show_modal(this);return false" class="dropdown-item"><i class="fa fa-pencil"></i> Rubah Data</a>' +
                                '<a title="Hapus Data" href="javascript:;" onclick="hapus_data(this);return false" data-id="'+dt.id+'" class="dropdown-item"><i class="fa fa-trash-o"></i> Hapus Data</a>' +
                                '<div class="dropdown-divider"></div>' +
                                '<a title="Tagihan" href="'+base_url+'admin/admin_tagihan/'+dt.id+'" onclick="load_page(this);return false" class="dropdown-item"><i class="fa fa-money"></i> Data Tagihan</a>' +
                            '</div>' +
                        '</div>'
                        ,
                        dt.upass,
                        'Rp. 0,-',
                        'Rp. 0,-',
                        'Rp. 0,-'
                    ]).draw().node();
                    $( rowNode ).addClass('row_'+dt.id);

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