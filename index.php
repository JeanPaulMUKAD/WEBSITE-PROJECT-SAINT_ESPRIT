<?php include("assets/include/entete.php"); ?>
<?php include("assets/include/link.php"); ?>
<?php
	if (isset ($_GET["search"]) and ($_GET["search"] == "acceuil"))
	{
			
		include("assets/pages/acceuil.php");
	}
	elseif (isset ($_GET["search"]) and ($_GET["search"] == "actuality"))
	{
			
		include("assets/pages/actuality.php");
	}
	elseif (isset ($_GET["search"]) and ($_GET["search"] == "about"))
	{
			
		include("assets/pages/about.php");
	}
	elseif (isset ($_GET["search"]) and ($_GET["search"] == "contact"))
	{
			
		include("assets/pages/contact.php");
	}
	elseif (isset ($_GET["search"]) and ($_GET["search"] == "register"))
	{
			
		include("assets/pages/register.php");
	}
	elseif (isset ($_GET["search"]) and ($_GET["search"] == "move"))
	{
			
		include("assets/pages/move.php");
	}
	elseif (isset ($_GET["search"]) and ($_GET["search"] == "shop"))
	{
			
		include("assets/pages/shop.php");
	}
	elseif (isset ($_GET["search"]) and ($_GET["search"] == "index.php"))
	{
			
		include("assets/pages/shop.php");
	}
	else
	{
		include("assets/pages/acceuil.php");
	}	
?>	
<?php include("assets/include/footer.php"); ?>