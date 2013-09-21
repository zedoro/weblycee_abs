<html>

<head>
<title>encodeur MD5</title>
<script language="javascript" src="js/md5.js"></script>
<script language="javascript">
<!--
  function encodePassword() {
    str = document.getElementById("mot_de_passe").value;
    document.getElementById("mot_de_passe").value = MD5(str);
   }
// -->
</script>
</head>

<body>

<form method="POST" action="md5.php">
Mot de passe<input onChange="encodePassword(); return false;" id="mot_de_passe" type="text" name="password" size="80">

</form>

</body>

</html>