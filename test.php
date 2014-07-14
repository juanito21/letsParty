<?php
header('Authorization: $2a$10$191b708f61dd0f556f365uv/w6tAASCHrdg9vXJFxsUl.bcTGMIyy');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(false == 0) echo "ok";
else echo "zeaz";

?>
<html>
<body>

<form action="v1/uploadPicture" method="post"
enctype="multipart/form-data">
<label for="file">Filename:</label>
<input type="file" name="picture" id="file"><br>
<input type="submit" name="submit" value="Submit">
</form>

</body>
</html> 

