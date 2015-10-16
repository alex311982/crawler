<?php $this->setPlugin('include_component')?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <link rel="stylesheet" href="./template/css/style.css" type="text/css"/>
</head>
<body>
    <div id="container">
        <div id="content">
            <?php include_component('PageContent', 'render', get_defined_vars());?>
        </div>
    </div>
</body>
</html>