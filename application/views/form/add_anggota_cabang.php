<form class="form form-horizontal" id="modalForm">
    <input type="hidden" name="cab_id" value="<?php echo $cab->cab_id;?>">
    <table id="dataTableForm" class="table table-bordered table-striped">
        <thead>
        <tr>
            <th width="20px"><input id="cbxF" type="checkbox"></th>
            <th>Username</th>
            <th>Nama Lengkap</th>
            <th>Alamat</th>
            <th>Bandwidth</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($data){
            foreach ($data as $val){
                ?>
                <tr>
                    <td align="center"><input type="checkbox" name="user_id[]" value="<?php echo $val->user_id;?>"></td>
                    <td><?php echo $val->user_name;?></td>
                    <td><?php echo $val->user_fullname;?></td>
                    <td><?php echo $val->user_address;?></td>
                    <td><?php echo $val->user_bw;?></td>
                </tr>
                <?php
            }
        }
        ?>
        </tbody>
    </table>
</form>

<script>
    $('#cbxF').click(function (e) {
        //alert($(this).prop('checked'))
        if ($(this).prop('checked') == false){
            $('#dataTableForm tbody input:checkbox').prop('checked',false);
        } else {
            $('#dataTableForm tbody input:checkbox').prop('checked',true);
        }
    });
    var tf = $('#dataTableForm').DataTable({
        "lengthMenu": [[50, 150, 500, -1], [50, 150, 500, "All"]],
        "columnDefs": [ {
            "targets": 0,
            "orderable": false
        } ]
    });
    $('#modalForm').submit(function (e) {
        $('.informasi').html('');
        $('.btn-submit').html('<i class="fa fa-spin fa-refresh"></i> Simpan');
        $.ajax({
            url     : '<?php echo base_url('admin/add_anggota_cabang_submit');?>',
            type    : 'POST',
            dataType: 'JSON',
            data    : $(this).serialize(),
            success : function (dt) {
                if (dt.t == 0){
                    alert(dt.msg);
                    $('.btn-submit').html('<i class="fa fa-check"></i> Simpan');
                } else {
                    $('#myModalLG').modal('hide');
                    $('.btn-submit').html('<i class="fa fa-check"></i> Simpan');
                    show_alert('Berhasil menambahkan anggota');
                    var obj = {'href':base_url+'admin/anggota_cabang/<?php echo $cab->cab_id;?>'}
                    load_page(obj);
                }
            }
        })
        return false;
    })
    $('#myModalLG .modal-footer').html('<button type="button" onclick="$(\'#modalForm\').submit();return false" class="btn-submit btn-sm btn btn-primary"><i class="fa fa-check"></i> Simpan</button>\n' +
        '                <button type="button" class="btn-sm btn btn-secondary" data-dismiss="modal"> Tutup</button>')
</script>