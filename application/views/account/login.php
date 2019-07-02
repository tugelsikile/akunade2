<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="au theme template">
    <meta name="author" content="<?php echo $toko->s_name;?>">
    <meta name="keywords" content="<?php echo $toko->s_name;?>">

    <!-- Title Page-->
    <title>Login</title>

    <!-- Fontfaces CSS-->
    <link href="<?php echo base_url('assets/css/font-face.css');?>" rel="stylesheet" media="all">
    <link href="<?php echo base_url('assets/vendor/font-awesome-4.7/css/font-awesome.min.css');?>" rel="stylesheet" media="all">
    <link href="<?php echo base_url('assets/vendor/font-awesome-5/css/fontawesome-all.min.css');?>" rel="stylesheet" media="all">
    <link href="<?php echo base_url('assets/vendor/mdi-font/css/material-design-iconic-font.min.css');?>" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="<?php echo base_url('assets/vendor/bootstrap-4.1/bootstrap.min.css');?>" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="<?php echo base_url('assets/vendor/animsition/animsition.min.css');?>" rel="stylesheet" media="all">
    <link href="<?php echo base_url('assets/vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css');?>" rel="stylesheet" media="all">
    <link href="<?php echo base_url('assets/vendor/wow/animate.css');?>" rel="stylesheet" media="all">
    <link href="<?php echo base_url('assets/vendor/css-hamburgers/hamburgers.min.css');?>" rel="stylesheet" media="all">
    <link href="<?php echo base_url('assets/vendor/slick/slick.css');?>" rel="stylesheet" media="all">
    <link href="<?php echo base_url('assets/vendor/select2/select2.min.css');?>" rel="stylesheet" media="all">
    <link href="<?php echo base_url('assets/vendor/perfect-scrollbar/perfect-scrollbar.css');?>" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="<?php echo base_url('assets/css/theme.css');?>" rel="stylesheet" media="all">
    <style>
        .page-content--bge5{
            background: url("<?php echo base_url('assets/img/'.$toko->s_background);?>") no-repeat center center; background-size: cover;
        }
    </style>

</head>

<body class="animsition">
<div class="page-wrapper">
    <div class="page-content--bge5">
        <div class="container">
            <div class="login-wrap">
                <div class="login-content">
                    <div class="login-logo">
                        <a href="#">
                            <img src="<?php echo base_url('assets/img/'.$toko->s_logo);?>" alt="<?php echo $toko->s_name;?>" width="100">
                        </a>
                    </div>
                    <div class="login-form">
                        <form action="" method="post">
                            <div class="form-group">
                                <label>Nama Pengguna</label>
                                <input class="au-input au-input--full" type="text" name="user_name" placeholder="Nama Pengguna">
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input class="au-input au-input--full" type="password" name="password" placeholder="Password">
                            </div>
                            <div class="login-checkbox">
                                <label>
                                    <!--<input type="checkbox" name="remember">Remember Me-->
                                </label>
                                <!--<label>
                                    <a href="#">Forgotten Password?</a>
                                </label>-->
                            </div>
                            <button class="au-btn au-btn--block au-btn--green m-b-20" type="submit">sign in</button>

                        </form>
                        <!--<div class="register-link">
                            <p>
                                Don't you have account?
                                <a href="#">Sign Up Here</a>
                            </p>
                        </div>-->
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Jquery JS-->
<script src="<?php echo base_url('assets/vendor/jquery-3.2.1.min.js');?>"></script>
<!-- Bootstrap JS-->
<script src="<?php echo base_url('assets/vendor/bootstrap-4.1/popper.min.js');?>"></script>
<script src="<?php echo base_url('assets/vendor/bootstrap-4.1/bootstrap.min.js');?>"></script>
<!-- Vendor JS       -->
<script src="<?php echo base_url('assets/vendor/slick/slick.min.js');?>">
</script>
<script src="<?php echo base_url('assets/vendor/wow/wow.min.js');?>"></script>
<script src="<?php echo base_url('assets/vendor/animsition/animsition.min.js');?>"></script>
<script src="<?php echo base_url('assets/vendor/bootstrap-progressbar/bootstrap-progressbar.min.js');?>">
</script>
<script src="<?php echo base_url('assets/vendor/counter-up/jquery.waypoints.min.js');?>"></script>
<script src="<?php echo base_url('assets/vendor/counter-up/jquery.counterup.min.js');?>">
</script>
<script src="<?php echo base_url('assets/vendor/circle-progress/circle-progress.min.js');?>"></script>
<script src="<?php echo base_url('assets/vendor/perfect-scrollbar/perfect-scrollbar.js');?>"></script>
<script src="<?php echo base_url('assets/vendor/chartjs/Chart.bundle.min.js');?>"></script>
<script src="<?php echo base_url('assets/vendor/select2/select2.min.js');?>">
</script>

<!-- Main JS-->
<script src="<?php echo base_url('assets/js/main.js');?>"></script>
<script>
    $('form').submit(function () {
        $.ajax({
            url     : '<?php echo base_url('account/login_submit');?>',
            type    : 'POST',
            data    : $(this).serialize(),
            dataType: 'JSON',
            success : function (dt) {
                if (dt.t == 0){
                    $('.login-checkbox label').html(dt.msg);
                } else {
                    window.location.href = '<?php echo base_url('');?>';
                }
            }
        });
        return false;
    });
</script>
</body>

</html>
<!-- end document-->