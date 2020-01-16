<?php require_once('parse.php'); ?>
<html>
<head><title>Contoh Koneksi Mesin Absensi Mengunakan SOAP Web Service</title></head>
<body bgcolor="#caffcb">

<?php
    include 'helper/connection.php';
?>

<H3>Download Log Data</H3>

<?php
error_reporting(E_ALL ^ E_NOTICE);
$IP = $_GET["ip"];
$Key = $_GET["key"];
if($IP=="") $IP="192.168.1.211";
if($Key=="") $Key="0";
?>

<form action="tarik-data.php">
IP Address: <input type="Text" name="ip" value="<?php echo $IP; ?>" size=15><BR>
Comm Key: <input type="Text" name="key" size="5" value="<?php echo $Key; ?>"><BR><BR>

<input type="Submit" value="Download">
</form>
<BR>

<?php
if($_GET["ip"]!=""){ ?>
	<table cellspacing="2" cellpadding="2" border="1">
	<tr align="center">
		<td><B>No</B></td>
	    <td><B>UserID</B></td>
	    <td width="200"><B>Tanggal & Jam</B></td>
	    <td><B>Verifikasi</B></td>
	    <td><B>Status</B></td>
	</tr>
	<?php
	$Connect = fsockopen($IP, "80", $errno, $errstr, 1);
	if($Connect){
		$soap_request="<GetAttLog><ArgComKey xsi:type=\"xsd:integer\">".$Key."</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg></GetAttLog>";
		$newLine="\r\n";
		fputs($Connect, "POST /iWsService HTTP/1.0".$newLine);
	    fputs($Connect, "Content-Type: text/xml".$newLine);
	    fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
	    fputs($Connect, $soap_request.$newLine);
		$buffer="";
		while($Response=fgets($Connect, 1024)){
			$buffer=$buffer.$Response;
		}
	}else echo "Koneksi Gagal";
	
	$buffer=Parse_Data($buffer,"<GetAttLogResponse>","</GetAttLogResponse>");
	$buffer=explode("\r\n",$buffer);
	for($a=1;$a<=count($buffer);$a++){
		$data=Parse_Data($buffer[$a],"<Row>","</Row>");

		$PIN=Parse_Data($data,"<PIN>","</PIN>");
		$DateTime=Parse_Data($data,"<DateTime>","</DateTime>");
		$Name=Parse_Data($data,"<Name>","</Name>");
		$Verified=Parse_Data($data,"<Verified>","</Verified>");
		$Status=Parse_Data($data,"<Status>","</Status>");

		// $sql = "INSERT INTO tb_finger (userid, tanggal_jam, verifikasi, status) values ('$PIN','$DateTime','$Verified','$Status')";
		// 	if (mysqli_query($con, $sql)) { //proses mengingatkan data sudah ada
		// 		ini_set('max_execution_time', 300);
		// 	// echo "<script>alert('Username Sudah Digunakan');history.go(-1) </script>";
		// 	}
	?>
	<tr align="center">
			<td><?php echo $a; ?></td>
		    <td><?php echo $PIN; ?></td>
		    <td><?php echo $DateTime; ?></td>
			<td><?php echo $Name; ?></td>
		    <td><?php echo $Verified; ?></td>
		    <td><?php echo $Status; ?></td>
		</tr>
	<?php 
	} 
		echo "<script>alert('Berhasil'); </script>";
	?>
	</table>
<?php } ?>

</body>
</html>
