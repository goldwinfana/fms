<?php include './../includes/session.php'; ?>
<?php include './../includes/navbar.php';

if($_SESSION['user'] == 'admin'){
    header('location: ./../admin/welcome.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="../css/cards.css" />
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.css" rel="stylesheet">
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.js"></script>
    <style type="text/css"> body{ font: 14px sans-serif;
            text-align: center; }
    </style>


</head>

<body style="display: flex">
<?php include("../includes/side-menu.php")?>
<div class="body-content">
<div class="page-header">
    <?php
    if(isset($_SESSION['error'])){
        echo "
                        <div class='alert alert-warning beautiful' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                            <span aria-hidden='true'>&times;</span>
                            </button>
                           ".$_SESSION['error']."</div>
                        ";
        unset($_SESSION['error']);
    }

    if(isset($_SESSION['success'])){
        echo "
                        <div class='alert btn-success beautiful' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                            <span aria-hidden='true'>&times;</span>
                            </button>
                           ".$_SESSION['success']."</div>
                        ";
        unset($_SESSION['success']);
    }


    ?>
    <h1>Farmer Dashboard</h1>
    <button class="btn btn-warning addnew">Add Tracker</button>
</div>


    <h4 class="row" style="padding: 10px" align="center">
        <div>
<?php
        $conn = $pdo->open();

        try {
            $stmt = $conn->prepare("SELECT * FROM livestock");
            $stmt->execute();
        }
        catch (Exception $e){
            print_r($e->getMessage());
        }



        if($stmt->rowCount() > 0) {
            $count=0;
            foreach ($stmt as $key=> $row) {

                echo ' 
                
                <button class="front-btn '.$count.'" style="margin: 5px"><div class="frontside '.$row["animal_type"].'  ">
                            <div class="card">
                                <div class="card-body">
                                    <p><img src="../assets/img/avatar_2x.png"></p>
                                    <h4 class="card-title">'.$row["animal_type"].' ';
                                        if($row["status"] =="online")
                                        {
                                            echo'<i class="fa fa-circle text-success"></i>';
                                        }else
                                        {
                                            echo '<i class="fa fa-circle text-danger" ></i>';
                                        }


                                    echo'</h4>
                                    <p class="card-text">Ser No: '.$row["serial_no"].' </p>
                                    <p>';

                                         if($row["status"] =="online")
                                        {
                                            echo'<a id="'.$row["serial_no"].'"  class="btn btn-warning btn-sm anim_trace"><i class="fa fa-location-arrow"></i></a> ';
                                        }
                                         echo'
                                         
                                         <a id="'.$row["serial_no"].'"  class="btn btn-danger btn-sm anim_delete"><i class="fa fa-trash"></i></a>  
                                     </p> 
                                </div>
                            </div>
                        </div></button>
                         ';
                $count++;
            }

            $pdo->close();
            echo '   </table>';
        }else{
            echo '<h3>No Records Found ...</h3>' ;
        }
        ?>
        </div>


    <div class="pagination">
        <a id="first" href="#">&laquo;</a>
        <a id="one" href="#" class="active">1</a>
        <a id="two" href="#">2</a>
        <a id="three" href="#">3</a>
        <a id="four" href="#">4</a>
        <a  id="five" href="#">5</a>
        <a id="six" href="#">6</a>
        <a id="last" href="#">&raquo;</a>
    </div>
</div>


</body>
</html>

<?php include('./../farmer/files/farmer_modal.php') ?>
<?php include('./../includes/scripts.php') ?>

<script>
    $(function() {
        $(document).on('click', '.anim_delete', function (e) {

            e.preventDefault();
            var id = this.id;
            $('.anim_span').html('<h5>Serial No: <span style="color: orange">'+id+'</span></h5>');
            $('.anim_delete').val(id);
            $('#amin_delete').modal('show');
        });
        $(document).on('click', '.pagination a', function (e) {

            e.preventDefault();
            var id = this.id;

            if(id == "first"){
                $('#one').click()
            }
            if(id == "one"){
                $('.front-btn').css('display','none');
                for (var i=0;i<8;i++){
                    $('.'+i).css('display','initial');
                }
            }
            if(id == "two"){
                $('.front-btn').css('display','none');
                for (var i=9;i<18;i++){
                    $('.'+i).css('display','initial');
                }
            }if(id == "three"){
                $('.front-btn').css('display','none');
                for (var i=19;i<28;i++){
                    $('.'+i).css('display','initial');
                }
            }if(id == "four"){
                $('.front-btn').css('display','none');
                for (var i=29;i<38;i++){
                    $('.'+i).css('display','initial');
                }
            }if(id == "five"){
                $('.front-btn').css('display','none');
                for (var i=39;i<48;i++){
                    $('.'+i).css('display','initial');
                }
            }if(id == "six"){
                $('.front-btn').css('display','none');
                for (var i=49;i<58;i++){
                    $('.'+i).css('display','initial');
                }
            } if(id == "last"){
                $('#six').click()
            }
        });

        $(document).on('click', '.addnew', function (e) {

            e.preventDefault();
            $('#addnew').modal('show');
        });

        $(document).on('click', '.anim_trace', function (e) {

            e.preventDefault();
            var id = this.id;
            getCoords(id);
           $('#maps').modal('show');
        });

    });

    function approve(id){
        console.log('sfn');
        $.ajax({
            type: 'POST',
            url: './../lessor/lessor_handle.php',
            data: {approve:id},
            dataType: 'json',
            success: function(response){
            }
        });

        location.reload();
    }



    function getCoords(id){

        var geo=[];
            $.ajax({
                type: 'POST',
                url: './../farmer/farmer_handle.php',
                data: {coords:id},
                dataType: 'json',
                async: false,
                success: function(response){
                    response.forEach(function (data, i) {
                        geo[i]={
                            'type': 'Feature',
                            'properties': {
                                'message': data.animal_type,
                                'serial': data.serial_no,
                                'iconSize': [40, 40]
                            },
                            'geometry': {
                                'type': 'Point',
                                'coordinates': [parseFloat(data.longitude), parseFloat(data.latitude)]
                            }
                        }

                    });


                }
            });

            remaker(geo);
    }



    $('.front-btn').css('display','none');
    for (var i=0;i<8;i++){
        $('.'+i).css('display','initial');
    }

</script>
<script src="./../assets/js/maps.js"></script>