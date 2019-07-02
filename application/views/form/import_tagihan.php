<form class="form form-horizontal" id="uploadForm">
    <div class="well">
        <div class="col-md-12 col-md-offset-2">
            <form id="uploadForm" class="form-inline" method="post" action="">
                <div class="input-group mb-3">
                    <div class="custom-file">
                        <input name="file" type="file" class="custom-file-input" id="files">
                        <label class="custom-file-label" for="files">Choose file</label>
                    </div>
                    <div class="input-group-append">
                        <button class="btn btn-primary input-group-text" id="">Upload</button>
                    </div>
                </div>
            </form>
            <br>
            <div class="progress" style="display:none">
                <div id="progressBar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                    <span class="sr-only">0%</span>
                </div>
            </div>
            <div class="msg alert alert-danger text-left" style="display:none"></div>
        </div>
        <div class="clearfix"></div>
    </div>
</form>

<a href="<?php echo base_url('assets/Format_data_tagihan.xlsx');?>" target="_blank">Download Format</a>
<style>
    .custom-file-label{
        overflow: hidden;
    }
</style>
<script>
    $('#uploadForm #files').change(function (e) {
        var file_name = $("#uploadForm #files").val();
        $('#uploadForm .custom-file-label').text(file_name);
        $('.msg').html('').hide();
    });

    //$(document).ready(function() {
    //console.log($('#uploadForm')[0])
    $('#uploadForm').on('submit', function(event){
        event.preventDefault();
        var formdata = new FormData($('#uploadForm')[0]);
        //var files   = $('input[type=file]')[0].files[0];
        //formdata.append('media',files);

        $('.msg').hide();
        $('.progress').show();

        $.ajax({
            xhr : function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e){
                    if(e.lengthComputable){
                        console.log('Bytes Loaded : ' + e.loaded);
                        console.log('Total Size : ' + e.total);
                        console.log('Persen : ' + (e.loaded / e.total));

                        var percent = Math.round((e.loaded / e.total) * 100);

                        $('#progressBar').attr('aria-valuenow', percent).css('width', percent + '%').text(percent + '%');
                    }
                });
                return xhr;
            },
            type : 'POST',
            url : '<?php echo base_url('admin/submit_import_tagihan');?>',
            data : formdata,
            processData : false,
            contentType : false,
            dataType    : 'JSON',
            success     : function(dt){
                if (dt.t == 0){
                    $('#uploadForm')[0].reset();
                    $('.custom-file-label').html('Choose file')
                    $('.progress').hide();
                    $('.msg').html(dt.msg).show();
                } else {
                    $('#uploadForm')[0].reset();
                    $('#myModalLG').modal('hide');
                    var obj = {'href':base_url + 'admin/user'}
                    load_page(obj);
                }
            }
        });
        return false;
    });
    //});

    $('#myModalLG .modal-footer').html('');
</script>