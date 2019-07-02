<form class="form form-horizontal" id="modalForm">
    <input type="hidden" name="user_id" value="<?php echo $data->user_id;?>">
    <div class="form-group-sm row mb-2">
        <label class="col-md-5 col-form-label">Nama Lengkap</label>
        <div class="col-md-7">
            <input type="text" disabled name="user_fullname" class="form-control" placeholder="Nama Lengkap" value="<?php echo $data->user_fullname;?>">
        </div>
    </div>
    <div class="form-group-sm row mb-2">
        <label class="col-md-5 col-form-label">Bulan Tagihan</label>
        <div class="col-md-4">
            <select class="form-control" name="bulan">
                <?php
                for($i = 1; $i <= 12; $i++){
                    echo '<option value="'.str_pad($i,2,"0",STR_PAD_LEFT).'">'.$this->conv->bulanIndo(str_pad($i,2,"0",STR_PAD_LEFT)).'</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <input type="text" name="tahun" class="form-control" value="<?php echo date('Y');?>">
        </div>
    </div>
    <div class="form-group-sm row mb-2">
        <label class="col-md-5 col-form-label">Jumlah Tagihan</label>
        <div class="col-md-7">
            <input type="number" name="jumlah" class="form-control" placeholder="Jumlah tagihan" min="0" max="9999999" value="0">
        </div>
    </div>
    <div class="form-group-sm row mb-2">
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
            url     : '<?php echo base_url('admin/add_tagihan_submit');?>',
            type    : 'POST',
            dataType: 'JSON',
            data    : $(this).serialize(),
            success : function (dt) {
                if (dt.t == 0){
                    $('.informasi').html(dt.msg);
                    $('.btn-submit').html('<i class="fa fa-check"></i> Simpan');
                } else if (dt.t == 1) {
                    load_data();
                    $('#myModalLG').modal('hide');
                    show_alert('Berhasil merubah data tagihan');
                } else {
                    load_data();

                    /*$('.tableForm').find('tbody').append('<tr class="row_'+dt.id+'"></tr>');
                    $('.row_'+dt.id).append('<td>'+dt.bulan+'</td>');
                    $('.row_'+dt.id).append('<td class="jml_'+dt.id+'">'+dt.jml+'</td>');
                    $('.row_'+dt.id).append('<td class="date_'+dt.id+'">-</td>');
                    $('.row_'+dt.id).append('<td class="paid_'+dt.id+'"><strong class="badge badge-primary">Belum dibayar</strong></td>');
                    $('.row_'+dt.id).append('<td>' +
                        '<a class="btn btn-sm btn-success" title="Bayar Tagihan" href="javascript:;" data-id="'+dt.id+'" onclick="paid(this);return false"><i class="fa fa-money"></i> </a> ' +
                        '<a class="btn btn-sm btn-primary" title="Cetak Tagihan" href="javascript:;" data-id="'+dt.id+'" onclick="cetak_tagihan(this);return false"><i class="fa fa-print"></i> </a> ' +
                        '<a class="btn btn-sm btn-danger" title="Hapus Tagihan" href="javascript:;" data-id="'+dt.id+'" onclick="hapus_data(this);return false"><i class="fa fa-trash-o"></i> </a> ' +
                            '</td>');*/
                    $('#myModalLG').modal('hide');
                    $('.btn-submit').html('<i class="fa fa-check"></i> Simpan');
                    show_alert('Berhasil menambahkan tagihan');
                }
            }
        })
        return false;
    });
    $('#myModalLG .modal-footer').html('<button type="button" onclick="$(\'#modalForm\').submit();return false" class="btn-submit btn-sm btn btn-primary"><i class="fa fa-check"></i> Simpan</button>\n' +
        '                <button type="button" class="btn-sm btn btn-secondary" data-dismiss="modal"> Tutup</button>')
</script>