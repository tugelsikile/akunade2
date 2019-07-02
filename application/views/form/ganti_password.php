<form class="form form-horizontal" id="modalForm">
    <div class="form-group-sm row">
        <label class="col-md-5 col-form-label">Password Lama</label>
        <div class="col-md-7">
            <input type="password" name="old_pass" class="form-control" placeholder="Password lama" >
        </div>
    </div>
    <div class="form-group-sm row">
        <label class="col-md-5 col-form-label">Password Baru</label>
        <div class="col-md-7">
            <input type="password" name="pass1" class="form-control" placeholder="Password baru" value="">
        </div>
    </div>
    <div class="form-group-sm row">
        <label class="col-md-5 col-form-label">Ulangi Password</label>
        <div class="col-md-7">
            <input type="password" name="pass2" class="form-control" placeholder="Ulangi Password" >
        </div>
    </div>
    <div class="form-group-sm row">
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
            url     : '<?php echo base_url('admin/edit_password_submit');?>',
            type    : 'POST',
            dataType: 'JSON',
            data    : $(this).serialize(),
            success : function (dt) {
                if (dt.t == 0){
                    $('.informasi').html(dt.msg);
                    $('.btn-submit').html('<i class="fa fa-check"></i> Simpan');
                } else {
                    $('.uname_'+dt.id).html(dt.name);
                    $('.ufull_'+dt.id).html(dt.full);
                    $('.upass_'+dt.id).html(dt.pass);
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