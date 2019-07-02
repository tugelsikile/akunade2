<form class="form form-horizontal" id="modalForm">
    <input type="hidden" name="cab_id" value="<?php echo $cabang->cab_id;?>">
    <div class="form-group-sm row mb-1">
        <label class="col-md-5 col-form-label">Nama Cabang</label>
        <div class="col-md-7">
            <input type="text" name="user_fullname" class="form-control" placeholder="Nama Cabang" value="<?php echo $user->user_fullname;?>">
        </div>
    </div>
    <div class="form-group-sm row mb-1">
        <label class="col-md-5 col-form-label">Nama Pengguna</label>
        <div class="col-md-7">
            <input type="text" name="user_name" class="form-control" placeholder="Nama Pengguna" value="<?php echo $user->user_name;?>">
        </div>
    </div>
    <div class="form-group-sm row mb-1">
        <label class="col-md-5 col-form-label">Password</label>
        <div class="col-md-7">
            <input type="password" name="user_password" class="form-control" placeholder="Password" value="<?php echo $user->user_password;?>">
        </div>
    </div>
    <div class="form-group-sm row mb-1">
        <label class="col-md-5 col-form-label">Alamat</label>
        <div class="col-md-7">
            <textarea name="user_address" class="form-control" placeholder="Alamat lengkap"><?php echo $user->user_address;?></textarea>
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
            url     : '<?php echo base_url('admin/edit_cabang_submit');?>',
            type    : 'POST',
            dataType: 'JSON',
            data    : $(this).serialize(),
            success : function (dt) {
                if (dt.t == 0){
                    $('.informasi').html(dt.msg);
                    $('.btn-submit').html('<i class="fa fa-check"></i> Simpan');
                } else {
                    t.row(".row_"+dt.data.cab_id)
                        .every(function (rowIdx, tableLoop, rowLoop) {
                            t.cell(rowIdx,1).data(dt.data.user_fullname);
                            t.cell(rowIdx, 2).data(dt.data.user_name);
                            t.cell(rowIdx, 3).data(dt.data.user_password);
                            t.cell(rowIdx, 4).data(dt.data.user_address);
                        }).draw();

                    $('#myModalLG').modal('hide');
                    $('.btn-submit').html('<i class="fa fa-check"></i> Simpan');
                    show_alert('Berhasil merubah cabang');
                }
            }
        })
        return false;
    })
    $('#myModalLG .modal-footer').html('<button type="button" onclick="$(\'#modalForm\').submit();return false" class="btn-submit btn-sm btn btn-primary"><i class="fa fa-check"></i> Simpan</button>\n' +
        '                <button type="button" class="btn-sm btn btn-secondary" data-dismiss="modal"> Tutup</button>')
</script>