<?php

require_once('forgot_password_functions.php');

class logs
{
	function forgot_password_form()
	{
		forgot_password_form();
	}

	function send_reset_otp()
	{
		send_reset_otp();
	}

	function verify_otp_form()
	{
		verify_otp_form();
	}

	function reset_password()
	{
		reset_password();
	}
	
	function def()
	{
		$conn = new connect();
		?>
		<div class='container'>
			<div class='row'>
				<div class='col-12'>
				<h2>Logs</h2>
				<form action='index.php' method='get'>
				<?php
					echo $conn->button_print("Print","print.php?option=logs&task=lo&typ=all&id=0&p=P");
					echo $conn->insert_input("option","logs",5);
					echo $conn->insert_input("task","def",5);
				?>
				</form>
				<table id='datatable' class='table table-bordered table-striped'>
					<?php
						$ls = array('No', 'Date', 'Users', 'App', 'Action', 'Ref');
						$conn->table_header($ls);
					?>
					<tbody>
					<?php
						$a = 1;
						$sql = "select * from `logs` left join`users` on  `users`.`id` = `logs`.`uid`";
						$conn = new connect();
						$res = $conn->query($sql);
						while ($cdr = $res->fetch()) {
							$ls[0] = array("c", $a);
							$ls[1] = array("c", date("d/m/Y H:i:s", $cdr['date']));
							$ls[2] = array("c", $cdr['user']);
						   
							if ($cdr['app_id'] == '0') {
							 $ls[3] = array("c", "-");
							} 
							else 
							{
							 $ls[3] = array("c", $conn->get_app_info($cdr['app_id'], 4));
							}
						   
							$act_text = $cdr['action'];
							if ($cdr['app_id'] <> 5 && $cdr['app_id'] <> 0) 
							{
							 $act_text .= " RefId#" . $cdr['ref_id'];
							}
							$ls[4] = array("l", $act_text);
						   
							if ($cdr['ref_id'] <= 1) 
							{
							 $ls[5] = array("c", "-");
							} 
							else 
							{
							$action = "";
								$action = $action."<input class='btn' type='button' value='Link' onclick='window.open(\"index.php?option=" . $conn->get_app_info($cdr['app_id'], 6) . "&task=det&id=" . $cdr['ref_id'] . "&blank=1\",\"_blank\",\"toolbar=0\")'>";
								$ls[5] = array("c", $action);
							}
							$conn->table_body($ls);
							$a++;
						   }
					?>
					</tbody>
					</tbody>
				</table>
				</div>
			</div>
		</div>
		<?php
	}
	
	function login_form()
    {
        ?>
        <link rel="stylesheet" href="SignUp_LogIn_Form.css">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

        <div class="container">
            <div class="form-box login">
                <form action="index.php" method="post" id="loginForm">
                    <h1>Login</h1>
                    <div class="input-box">
                        <input type="text" name="user" placeholder="Username" required>
                        <i class='bx bxs-user'></i>
                    </div>
                    <div class="input-box">
                        <input type="password" id="login_pass" name="pass" placeholder="Password" required>
                        <span class='password-toggle' data-target="login_pass" tabindex="0" role="button" aria-label="Toggle password visibility" style="cursor: pointer;"><i class='bx bx-hide'></i></span>
                    </div>
                    
                    
                    <input type="hidden" name="option" value="logs">
                    <input type="hidden" name="task" value="login">
                    
                    <button type="submit" name="save" value="Login" class="btn">Login</button>
                </form>
            </div>

            <div class="form-box register">
                <form action="index.php" method="post" class="register-form" id="registerForm">
                    <h1>Registration</h1>
                    <?php
                        $register_message = '';
                        if (session_status() === PHP_SESSION_NONE) 
                        {
                            session_start();
                        }
                        if (!empty($_SESSION['register_message'])) 
                        {
                            $register_message = $_SESSION['register_message'];
                            unset($_SESSION['register_message']);
                        }
                    ?>
                    <?php if (!empty($register_message)) : ?>
                        <div class="alert-register"><?php echo htmlspecialchars($register_message); ?></div>
                    <?php endif; ?>

					<!-- Step 1: บัญชีผู้ใช้ -->
					<div id="register-step-1" class="register-step">
						<div class="input-box">
							<input type="text" name="reg_user" placeholder="Username" required>
							<i class='bx bxs-user'></i>
						</div>
						<div class="input-box">
							<input type="email" name="reg_email" placeholder="Email" required>
							<i class='bx bxs-envelope' ></i>
						</div>
						<div class="input-box">
							<input type="password" id="reg_pass" name="reg_pass" placeholder="Password" required>
							<span class='password-toggle' data-target="reg_pass" tabindex="0" role="button" aria-label="Toggle password visibility" style="cursor: pointer;"><i class='bx bx-hide'></i></span>
						</div>
						<div class="input-box">
							<input type="password" id="reg_pass_confirm" name="reg_pass_confirm" placeholder="Confirm Password" required>
							<span class='password-toggle' data-target="reg_pass_confirm" tabindex="0" role="button" aria-label="Toggle password visibility" style="cursor: pointer;"><i class='bx bx-hide'></i></span>
						</div>

						<button id="next-to-2" type="button" class="btn next-to-2" data-register-step="2" onclick="if (window.showRegisterStep) { window.showRegisterStep(2); } return false;">Next Step</button>
					</div>

					<!-- Step 2: ข้อมูลส่วนตัวพื้นฐาน -->
					<div id="register-step-2" class="register-step d-none">
						<div class="input-box">
							<input type="text" name="reg_name" placeholder="ชื่อ" required>
							<i class='bx bxs-id-card'></i>
						</div>
						<div class="input-box">
							<input type="text" name="reg_surname" placeholder="นามสกุล" required>
							<i class='bx bxs-user-detail'></i>
						</div>
						<div class="input-box">
							<input type="number" name="reg_age" placeholder="อายุ" min="1" required>
							<i class='bx bx-user'></i>
						</div>
						<div class="input-box">
							<select name="reg_gender" required>
								<option value="" disabled selected hidden>-- เลือกเพศ --</option>
								<option value="1">ชาย</option>
								<option value="2">หญิง</option>
								<option value="3">อื่นๆ</option>
							</select>
						</div>
						<div class="register-actions">
							<button id="back-to-1" type="button" class="btn btn-secondary back-to-1" data-register-step="1" onclick="if (window.showRegisterStep) { window.showRegisterStep(1); } return false;">Back</button>
							<button id="next-to-3" type="button" class="btn next-to-3" data-register-step="3" onclick="document.getElementById('register-step-2').classList.add('d-none'); document.getElementById('register-step-2').style.display='none'; document.getElementById('register-step-3').classList.remove('d-none'); document.getElementById('register-step-3').style.display='block'; return false;">Next Step</button>
						</div>
					</div>

					<div id="register-step-3" class="register-step d-none">
						<!-- ใช้ type="text" แล้วเปลี่ยนเป็น date เมื่อกด เพื่อให้โชว์ Placeholder ได้สวยงาม -->
						<div class="input-box">
							<input type="text" name="reg_birthday" placeholder="วันเกิด" onfocus="(this.type='date')" onblur="(this.value == '' ? this.type='text' : this.type='date')" required>
							<i class='bx bxs-cake'></i>
						</div>
						<div class="input-box">
							<input type="tel" name="reg_phone" placeholder="เบอร์โทรศัพท์" required>
							<i class='bx bxs-phone'></i>
						</div>
						<div class="input-box">
							<input type="number" name="reg_height" placeholder="ส่วนสูง (ซม.)" min="1" step="0.01" required>
							<i class='bx bx-ruler'></i>
						</div>
						<div class="input-box">
						<input type="number" name="reg_weight" placeholder="น้ำหนัก (กก.)" min="1" step="0.01" required>
							<i class='bx bx-dumbbell'></i>
						</div>
						<div class="register-actions">
							<button id="back-to-2" type="button" class="btn btn-secondary back-to-2" data-register-step="2" onclick="if (window.showRegisterStep) { window.showRegisterStep(2); } return false;">Back</button>
							<button type="submit" class="btn">Register</button>
						</div>
					</div>

                    <input type="hidden" name="option" value="logs">
                    <input type="hidden" name="task" value="register">
                </form>
            </div>

            <div class="toggle-box">
                <div class="toggle-panel toggle-left">
                    <h2>Hello, Welcome!</h2>
                    <p>ยังไม่มีบัญชีใช่ไหม</p>
                    <button class="btn register-btn">Register</button>
                </div>

                <div class="toggle-panel toggle-right">
                    <h2>Welcome Back!</h2>
                    <p>ฉันมีบัญชีอยู่แล้ว</p>
                    <button class="btn login-btn">Login</button>
                </div>
            </div>
        </div>
		
        <script src="SignUp_LogIn_Form.js"></script>
        <?php
    }
	
	function register()
	{
		if (session_status() === PHP_SESSION_NONE)
		{
			session_start();
		}

		$conn = new connect();
		$user = trim($_REQUEST['reg_user'] ?? '');
		$email = trim($_REQUEST['reg_email'] ?? '');
		$pass = $_REQUEST['reg_pass'] ?? '';
		$confirm = $_REQUEST['reg_pass_confirm'] ?? '';
		$name = trim($_REQUEST['reg_name'] ?? '');
		$surname = trim($_REQUEST['reg_surname'] ?? '');
		$age = isset($_REQUEST['reg_age']) ? (int)$_REQUEST['reg_age'] : 0;
		$gender = isset($_REQUEST['reg_gender']) ? (int)$_REQUEST['reg_gender'] : 0;
		$birthdate = $_REQUEST['reg_birthday'] ?? '';
		$tel = trim($_REQUEST['reg_phone'] ?? '');
		$height = isset($_REQUEST['reg_height']) ? (float)$_REQUEST['reg_height'] : 0;
		$weight = isset($_REQUEST['reg_weight']) ? (float)$_REQUEST['reg_weight'] : 0;

		if ($pass !== $confirm) {
			$_SESSION['register_message'] = 'รหัสผ่านยืนยันไม่ตรงกัน';
			header('location:index.php?option=logs&task=login_form');
			exit;
		}

		if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $pass)) {
			$_SESSION['register_message'] = 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร และต้องมีตัวพิมพ์ใหญ่ ตัวพิมพ์เล็ก และตัวเลข';
			header('location:index.php?option=logs&task=login_form');
			exit;
		}

		if ($user === '' || $email === '' || $pass === '' || $name === '' || $surname === '') {
			$_SESSION['register_message'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
			header('location:index.php?option=logs&task=login_form');
			exit;
		}

		$dup_sql = "select count(*) as `cc` from `users` where `user` = '".$user."' or `mail` = '".$email."'";
		$dup_res = $conn->query($dup_sql);
		$dup_count = 0;
		while ($dup = $dup_res->fetch()) {
			$dup_count = (int)$dup['cc'];
		}

		if ($dup_count > 0) {
			$_SESSION['register_message'] = 'ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้แล้ว';
			header('location:index.php?option=logs&task=login_form');
			exit;
		}

		$hashed_pass = $conn->salter($pass);
		$insert_sql = "insert into `users` set
			`user` = '".$user."',
			`pass` = '".$hashed_pass."',
			`mail` = '".$email."',
			`name` = '".$name."',
			`surname` = '".$surname."',
			`age` = '".$age."',
			`gender` = '".$gender."',
			`bir` = '".$birthdate."',
			`tel` = '".$tel."',
			`tall` = '".$height."',
			`weight` = '".$weight."',
			`status` = '1'";

		try 
		{
			$conn->query($insert_sql);
		} 
		catch (Exception $e) 
		{
			$fallback_sql = "insert into `users` set
				`user` = '".$user."',
				`pass` = '".$hashed_pass."',
				`mail` = '".$email."',
				`name` = '".$name."',
				`surname` = '".$surname."',
				`age` = '".$age."',
				`gender` = '".$gender."',
				`bir` = '".$birthdate."',
				`tel` = '".$tel."',
				`tall` = '".$height."',
				`weight` = '".$weight."',
				`status` = '1'";
			$conn->query($fallback_sql);
		}

		$lookup_sql = "select * from `users` where `user` = '".$user."' order by `id` desc limit 1";
		$lookup_res = $conn->query($lookup_sql);
		$uid = 0;
		$uname = $name;
		while ($row = $lookup_res->fetch()) {
			$uid = $row['id'];
			$uname = $row['name'];
		}

		if ($uid > 0) {
			$_SESSION['uid'] = $uid;
			$_SESSION['uname'] = $uname;
			$_SESSION['user'] = $user;

			$users_group_sql = "select `ug`.`id` as `gid` from `usergroup` `ug`, `acl`, `app` where `ug`.`id` = `acl`.`ugid` and `acl`.`appid` = `app`.`id` and `ug`.`status` = '1' and `acl`.`status` = '1' and `app`.`dir` = 'users' order by `ug`.`id` limit 1";
			$users_group_res = $conn->query($users_group_sql);
			$users_gid = 0;
			while ($g = $users_group_res->fetch()) {
				$users_gid = $g['gid'];
			}
			if ($users_gid > 0) {
				$conn->query("insert into `uig` set `uid` = '".$uid."', `ugid` = '".$users_gid."', `status` = '1'");
			} else {
				// Fallback: assign to a default group with home access if users group is not available
				$default_group_sql = "select `ug`.`id` as `gid` from `usergroup` `ug`, `acl`, `app` where `ug`.`id` = `acl`.`ugid` and `acl`.`appid` = `app`.`id` and `ug`.`status` = '1' and `acl`.`status` = '1' and `app`.`dir` = 'home' order by `ug`.`id` limit 1";
				$default_group_res = $conn->query($default_group_sql);
				$default_gid = 0;
				while ($g = $default_group_res->fetch()) {
					$default_gid = $g['gid'];
				}
				if ($default_gid > 0) {
					$conn->query("insert into `uig` set `uid` = '".$uid."', `ugid` = '".$default_gid."', `status` = '1'");
				}
			}

			$conn->save_logs("register", $uid, $uid);
		}

		header('location:index.php?option=users&task=def');
		exit;
	}

	function login()
	{
		
		$conn = new connect();
		$user = $_REQUEST['user'];
		$pass = $_REQUEST['pass'];
		$sql = "select * from `users` where `status` = '1' and `user` = '".$user."' and `pass` = '".$conn->salter($pass)."'";
		$cc = 0;
		$res = $conn->query($sql);
		while ($cdr = $res->fetch()) 
		{
			$cc = 1;
			$uid = $cdr['id'];
			$uname = $cdr['name'];
		}
		if ($cc > 0) 
		{
			$_SESSION['uid'] = $uid;
			$_SESSION['uname'] = $uname;
			$_SESSION['user'] = $user;
			$_SESSION['option'] = 'users';
			$_SESSION['task'] = 'def';
			$conn->save_logs("login",$uid,$uid);
		}
		else 
		{
			$conn->save_logs("cannot login",0,0);
		}
		header('location:index.php?cc='.$cc);
	}
	
	function logout()
	{	
		$conn = new connect();
		$conn->save_logs("logout",$_SESSION['uid'],"0");
		session_destroy();
		header('location:index.php');
	}
	

}

?>