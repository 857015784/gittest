<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<script src='https://cdn.bootcss.com/socket.io/2.0.3/socket.io.js'></script>
<script src='https://cdn.bootcss.com/jquery/1.11.3/jquery.js'></script>
<style>
.myCard{
    display: block;
    position: absolute;
    overflow: hidden;
    height: 220px;
    width: 2000px;
    bottom: 0px;
}
.myCard li{
    width: 100px;
    height: 200px;
    display: block;
    border: 1px solid;
    float: left;
    position: relative;
    background-color: #ccc;
    /*left: -70px;*/
}
/*li:nth-child(0){
  left: 0px;
}*/
</style>
</head>
<body>
  <div class="myCard">
    <li>A</li>
    <li>b</li>
  </div>
</body>
<script>
   
  function licensing(obj){
        var card = $(obj).text();
        console.log(card)
      }

    $(document).ready(function () {

      //模拟登陆
      $.get('/game.php?act=login',{
        uid:<?php echo $_GET['uid'] ?>
      },function(data){
         obj = JSON.parse(data);
        if(obj.err==0){
          var card = obj.data;
          var h ='';
           $.each(card,function(i,v){
            var left = 70*i;
              h += '<li onclick="licensing(this)" style="left:-'+left+'px;">'+v+'</li>'
           })
           $('.myCard').html(h)
        }
      })
      
      

    })

</script>
</html>