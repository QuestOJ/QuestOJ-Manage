<?php

    if(!defined("load")){
        header("Location:/403");
        exit;
    }

    if($appendFlag == 1){
        
        return true;
    }
    
?>

    <div class="qoj-footer">
		<ul class="list-inline"><li class="list-inline-item"><?= __siteName ?></li></ul>
    </div>
</body>
<?php
    foreach($additional_footer as $code){
        echo $code;
    }
?>
</html>