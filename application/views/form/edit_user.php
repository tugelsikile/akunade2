<form class="form form-horizontal" id="modalForm">
    <input type="hidden" name="user_id" value="<?php echo $data->user_id;?>">
    <div class="form-group-sm row mb-1">
        <label class="col-md-5 col-form-label">Nama Lengkap</label>
        <div class="col-md-7">
            <input type="text" name="user_fullname" class="form-control" placeholder="Nama Lengkap" value="<?php echo $data->user_fullname;?>">
        </div>
    </div>
    <div class="form-group-sm row mb-1">
        <label class="col-md-5 col-form-label">Nama Pengguna</label>
        <div class="col-md-7">
            <input type="text" name="user_name" class="form-control" placeholder="Nama Pengguna" value="<?php echo $data->user_name;?>">
        </div>
    </div>
    <div class="form-group-sm row mb-1">
        <label class="col-md-5 col-form-label">Password</label>
        <div class="col-md-7">
            <input type="password" name="user_password" class="form-control" placeholder="Password" value="<?php echo $data->user_password;?>">
        </div>
    </div>
    <div class="form-group-sm row mb-1">
        <label class="col-md-5 col-form-label">Alamat</label>
        <div class="col-md-7">
            <textarea name="user_address" class="form-control" placeholder="Alamat lengkap"><?php echo $data->user_address;?></textarea>
        </div>
    </div>
    <div class="form-group-sm row mb-1">
        <label class="col-md-5 col-form-label">Bandwidth</label>
        <div class="col-md-7">
            <input type="number" name="user_bw" min="1" class="form-control" placeholder="Bandwidth (dalam mbps)" value="<?php echo $data->user_bw;?>">
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
            url     : '<?php echo base_url('admin/edit_user_submit');?>',
            type    : 'POST',
            dataType: 'JSON',
            data    : $(this).serialize(),
            success : function (dt) {
                if (dt.t == 0){
                    $('.informasi').html(dt.msg);
                    $('.btn-submit').html('<i class="fa fa-check"></i> Simpan');
                } else {
                    t.row(".row_"+dt.id)
                        .every(function (rowIdx, tableLoop, rowLoop) {
                            t.cell(rowIdx,1).data(dt.name);
                            //t.cell(rowIdx, 2).data(dt.full);
                            $('.ufull_'+dt.id).html(dt.full);
                            t.cell(rowIdx, 3).data(dt.pass);
                        }).draw();
                    $('#myModalLG').modal('hide');
                    $('.btn-submit').html('<i class="fa fa-check"></i> Simpan');
                    show_alert('Pengguna berhasil dirubah');
                }
            }
        })
        return false;
    });
    $('#myModalLG .modal-footer').html('<button type="button" onclick="$(\'#modalForm\').submit();return false" class="btn-submit btn-sm btn btn-primary"><i class="fa fa-check"></i> Simpan</button>\n' +
        '                <button type="button" class="btn-sm btn btn-secondary" data-dismiss="modal"> Tutup</button>')
</script>