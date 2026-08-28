<?php

require_once('config.php');

session_start();
ob_start();

if (isset($_SESSION['byear']))
{
	$_SESSION['byear'] = $_SESSION['byear'];
}
else
{
	if ($GLOBALS['yswitch'] <> 0) 
	{
		$_SESSION['byear'] = date('Y');
	}
	else
	{
		$_SESSION['byear'] = "";
	}
}

if (isset($_SESSION['uid']))
{
	$_SESSION['uid'] = $_SESSION['uid'];
}
else
{
	$_SESSION['uid'] = 4;
}
if (isset($_REQUEST['option']))
{
	$_SESSION['option'] = $_REQUEST['option'];
}
else
{
	$_SESSION['option'] = "home";
}
if (isset($_REQUEST['task']))
{
	$_SESSION['task'] = $_REQUEST['task'];
}
else
{
	$_SESSION['task'] = "def";
}

require_once('class.connect.php');
$conn = new connect();

?>

<!DOCTYPE html>
<html>
    <head>
		<!-- <link rel="icon" href=" "> -->
        <title>
			Bon_Appetit
        </title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width" initial-scale="1.0" />
		<link href="css/bootstrap.min.css" rel="stylesheet" />
		<link href="css/datatables.min.css" rel="stylesheet" />
		<link href="css/custom.css" rel="stylesheet" />
		<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
		
		<style>
			:root {
				color-scheme: light;
			}
			html, body {
				min-height: 100%;
				margin: 0;
				padding: 0;
				font-family: 'Prompt', sans-serif;
				background-color: #f4f9f5;
				color: #1f2937;
			}
			body {
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;
			}
			.page-wrapper {
				min-height: calc(100vh - 90px);
				padding: 32px 15px 48px;
			}
			.page-card {
				background: #ffffff;
				border-radius: 24px;
				box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
				border: none;
				padding: 30px;
			}
			.page-title {
				color: #1f5a40;
				font-weight: 700;
			}
			.page-subtitle {
				color: #475569;
				font-size: 0.95rem;
				line-height: 1.65;
			}
			.btn-primary,
			.btn-health,
			.btn-theme-primary,
			.btn-save {
				background: #2ea36b;
				border-color: #2ea36b;
				color: #fff;
			}
			.btn-primary:hover,
			.btn-health:hover,
			.btn-theme-primary:hover,
			.btn-save:hover {
				background: #257e55;
				border-color: #257e55;
			}

			/* ข้อความส่วนหัว navbar ให้ใช้ฟอนต์และขนาดเดียวกัน แต่สียังเป็นแบบเดิม */
			.navbar-brand.brand-glow {
				font-family: 'Prompt', sans-serif;
				font-size: 1.05rem;
				font-weight: 800;
				letter-spacing: 1px;
				background: linear-gradient(to right, #34d399, #38bdf8);
				-webkit-background-clip: text;
				-webkit-text-fill-color: transparent;
			}
			.navbar-brand.brand-glow i {
				margin-right: 0.35rem;
				font-size: 1.05rem;
			}

			/* ตัวหนังสือเมนูทั่วไป */
			.navbar-dark .navbar-nav .nav-link {
				color: #94a3b8; /* สีเทาสว่าง ดูสบายตา */
				font-weight: 500;
				letter-spacing: 0.5px;
				transition: all 0.3s ease;
			}

			/* เมื่อเอาเมาส์ชี้เมนู ให้ตัวหนังสือเรืองแสงสีเขียวมิ้นต์ */
			.navbar-dark .navbar-nav .nav-link:hover, 
			.navbar-dark .navbar-nav .nav-link.active {
				color: #34d399; 
				text-shadow: 0 0 12px rgba(52, 211, 153, 0.6);
			}

			/* ปุ่ม Login ล้ำยุค (โดดเด่น เรืองแสง เชิญชวนให้กด) */
			.btn-cyber-login {
				background: linear-gradient(45deg, #10b981, #0ea5e9);
				color: #ffffff !important;
				border: none;
				border-radius: 30px; /* ทรงแคปซูลยา ล้ำสมัย */
				padding: 8px 26px;
				font-weight: 600;
				box-shadow: 0 0 15px rgba(16, 185, 129, 0.4);
				transition: all 0.3s ease;
			}

			.btn-cyber-login:hover {
				box-shadow: 0 0 25px rgba(14, 165, 233, 0.7); /* สว่างขึ้นเมื่อโฮเวอร์ */
				transform: translateY(-2px); /* ลอยขึ้นนิดๆ */
			}

			/* ปุ่ม Logout แบบเส้นขอบนีออน (กลมกลืนแต่ชัดเจน) */
			.btn-cyber-logout {
				background: rgba(15, 23, 42, 0.4);
				color: #34d399 !important;
				border: 1px solid #34d399;
				border-radius: 30px;
				padding: 8px 24px;
				font-weight: 600;
				box-shadow: inset 0 0 10px rgba(52, 211, 153, 0.1);
				transition: all 0.3s ease;
			}

			.btn-cyber-logout:hover {
				background: #34d399;
				color: #0f172a !important; /* เปลี่ยนตัวอักษรเป็นสีเข้ม */
				box-shadow: 0 0 20px rgba(52, 211, 153, 0.6);
				transform: translateY(-2px);
			}
		</style>
    </head>
    <body>
	<?php
		require_once('class.connect.php'); // คลาสฐานข้อมูลของคุณ
		require_once('class.common.php');

		// เรียกใช้งานโครงสร้างหลัก
		$app = new common();
		$app->init();

	?>
    </body>
	<script src="js/jquery.js"></script>
	<script src="js/bootstrap.bundle.min.js"></script>
	<script src="js/datatables.min.js"></script>
	<script> 
		let table = new DataTable('#datatable');
	</script>

</html>