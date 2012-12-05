<html>
<head>
    <title><?php echo $this->escape($this->project)?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="/css/admin/login.css">
</head>
<body bgcolor="#cacde7" bottommargin="0" leftmargin="0" rightmargin="0" topmargin="0">
<table border="0" cellpadding="0" cellspacing="" width="100%" height="100%">
    <tr>
        <td align="center" valign="middle">
            <form method="post">
            <table border="0" bordercolor="#f1f1f1" cellpadding="0" cellspacing="0" class="tbl">
                <?php if ($this->error): ?><tr height="18"><td colspan="2" width="300" align="center" class="info" style="color: red;"><?php echo $this->escape(implode('', $this->error))?></td></tr><?php endif;?>
                <tr height="18"><td colspan="2" width="330" align="center" bgcolor="#666b94" style="color: #ffffff;"><?php echo $this->escape($this->project)?></td></tr>
                <tr height="5"><td colspan="2" class=info></td></tr>
                <tr class="info"><td>&nbsp;&nbsp;E-mail:</td><td><input style="width: 150px;" type="text" name="email" value="<?php echo $this->escape($this->email)?>"></td></tr>
                <tr class="info"><td>&nbsp;&nbsp;Password:</td><td><input style="width: 150px;" type="password" name="password"></td></tr>
                <tr height="5" class=info><td colspan="2"></td></tr>
                <tr class="info">
            	    <td></td>
                    <td>
                        <input type="submit" name="enter" value="Enter">&nbsp;
                        <input type="reset" value="Reset">
                    </td>
                </tr>
                <tr height="5" class=info><td colspan="2"></td></tr>
            </table>
            </form>
        </td>
    </tr>
</table>
</body>
</html>