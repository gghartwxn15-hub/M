<?php

class profile
{
    function get_theme_css() 
	{
        return "
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap');
            
            .theme-container 
			{
                font-family: 'Prompt', sans-serif;
                background-color: #f7f9fa;
                padding: 40px 15px;
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: flex-start;
            }
            .profile-card 
			{
                background: #ffffff;
                border-radius: 16px;
                box-shadow: 0 4px 24px rgba(0,0,0,0.06);
                padding: 40px;
                width: 100%;
                max-width: 750px;
            }
            .theme-header 
			{
                color: #1e7d48;
                font-weight: 600;
                text-align: center;
                margin-bottom: 30px;
                font-size: 26px;
            }
            .icon-wrapper 
			{
                text-align: center;
                margin-bottom: 15px;
            }
            .form-label 
			{
                color: #718096;
                font-weight: 400;
                font-size: 13.5px;
                margin-bottom: 6px;
                display: block;
            }
            .fake-input 
			{
                background-color: #ffffff;
                border: 1px solid #d1e3d8;
                border-radius: 8px;
                padding: 10px 16px;
                color: #2d3748;
                font-size: 15px;
                min-height: 46px;
                display: flex;
                align-items: center;
                width: 100%;
            }
            .form-control, .form-select 
			{
                border: 1px solid #d1e3d8;
                border-radius: 8px;
                padding: 10px 16px;
                color: #2d3748;
                font-size: 15px;
                background-color: #ffffff;
                width: 100%;
            }
            .form-control:focus, .form-select:focus 
			{
                border-color: #1e7d48;
                box-shadow: 0 0 0 0.2rem rgba(30, 125, 72, 0.15);
                outline: none;
            }
            .input-group 
			{
                display: flex;
                align-items: stretch;
                width: 100%;
            }
            .input-group > .form-control, .input-group > .fake-input 
			{
                border-top-right-radius: 0;
                border-bottom-right-radius: 0;
                flex: 1 1 auto;
                width: 1%;
            }
            .input-group-text 
			{
                background-color: #fcfdfc;
                border: 1px solid #d1e3d8;
                border-left: none;
                color: #4a5568;
                font-size: 14px;
                border-top-right-radius: 8px;
                border-bottom-right-radius: 8px;
                display: flex;
                align-items: center;
                padding: 0 15px;
            }
            .password-input 
			{
                border-right: none !important;
            }
            .password-toggle 
			{
                background-color: #ffffff;
                border-left: none !important;
                border: 1px solid #d1e3d8;
                cursor: pointer;
                color: #a0aec0;
                transition: all 0.2s;
            }
            .password-toggle:hover 
			{
                color: #4a5568;
            }
            .password-input:focus + .password-toggle 
			{
                border-color: #1e7d48;
            }

            .btn-theme-primary 
			{
                background-color: #218838;
                color: white;
                border: 1px solid #218838;
                border-radius: 8px;
                padding: 12px 24px;
                font-weight: 500;
                width: 100%;
                transition: all 0.2s;
                text-align: center;
                text-decoration: none;
                display: inline-block;
            }
            .btn-theme-primary:hover 
			{
                background-color: #1e7d34;
                border-color: #1e7d34;
                color: white;
                text-decoration: none;
            }
            .btn-theme-outline 
			{
                background-color: #ffffff;
                color: #218838;
                border: 1px solid #218838;
                border-radius: 8px;
                padding: 12px 24px;
                font-weight: 500;
                width: 100%;
                transition: all 0.2s;
                text-align: center;
                text-decoration: none;
                display: inline-block;
            }
            .btn-theme-outline:hover 
			{
                background-color: #f3fcf5;
                color: #1e7d34;
                text-decoration: none;
            }
        </style>
        ";
    }

    function def() 
    {
        $a = '';
		$option = $_REQUEST['option'];
		$id = $_REQUEST['id'];
		if (empty($id) && !empty($_SESSION['uid'])) 
		{
			$id = $_SESSION['uid'];
		}
		$sql = "select * from `users` where `id` = '".$id."'";
		$conn = new connect();
		$res = $conn->query($sql);
		while ($cdr = $res->fetch())
		{
			$name = $cdr['name'];
			$surname = $cdr['surname'];
			$user = $cdr['user'];
			$pass = $cdr['pass'];
			$mail = $cdr['mail'];
			$bir = $cdr['bir'];
			$tel = $cdr['tel'];
			$oemail = $cdr['oemail'];
			$gender = $cdr['gender'];
			$age = $cdr['age'];
			$tall = $cdr['tall'];
			$weight = $cdr['weight'];
		}

        if($gender == 1) 
		{ 
			$a = 'ชาย (Male)'; 
		} 
        elseif($gender == 2) 
		{ 
			$a = 'หญิง (Female)'; 
		}
        else { $a = '-'; }

        echo $this->get_theme_css();
        ?>
        <div class='theme-container'>
            <div class='profile-card'>
                <div class="icon-wrapper">
                    <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#1e7d48" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <h2 class="theme-header">ข้อมูลผู้ใช้งาน (Profile)</h2>

                <div class="row g-4">
                    <div class="col-md-12">
                        <label class="form-label">ชื่อผู้ใช้ (Username)</label>
                        <div class="fake-input"><?= !empty($user) ? $user : '-' ?></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">ชื่อ (Name)</label>
                        <div class="fake-input"><?= !empty($name) ? $name : '-' ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">นามสกุล (Surname)</label>
                        <div class="fake-input"><?= !empty($surname) ? $surname : '-' ?></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">เพศ (Gender)</label>
                        <div class="fake-input"><?= $a ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">อายุ (Age)</label>
                        <div class="input-group">
                            <div class="fake-input" style="border-right: none;"><?= !empty($age) ? $age : '-' ?></div>
                            <span class="input-group-text">ปี</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">น้ำหนัก (Weight)</label>
                        <div class="input-group">
                            <div class="fake-input" style="border-right: none;"><?= !empty($weight) ? number_format($weight, 2) : '-' ?></div>
                            <span class="input-group-text">kg</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ส่วนสูง (Height)</label>
                        <div class="input-group">
                            <div class="fake-input" style="border-right: none;"><?= !empty($tall) ? number_format($tall, 2) : '-' ?></div>
                            <span class="input-group-text">cm</span>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">อีเมล (Email)</label>
                        <div class="fake-input"><?= !empty($mail) ? $mail : '-' ?></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">เบอร์โทรศัพท์ (Tel)</label>
                        <div class="fake-input"><?= !empty($tel) ? $tel : '-' ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">วันเกิด (Birthday)</label>
                        <div class="fake-input"><?= !empty($bir) ? $bir : '-' ?></div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">อีเมลที่ทำงาน (Office Email)</label>
                        <div class="fake-input"><?= !empty($oemail) ? $oemail : '-' ?></div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">รหัสผ่าน (Password)</label>
                        <div class="fake-input">********</div>
                    </div>
                </div>

                <div class='row mt-5'>
                    <div class="col-md-6 mb-3">
                        <a href="index.php" class="btn-theme-outline">กลับหน้าหลัก (Back)</a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="index.php?option=profile&task=edit&id=<?= $id ?>" class="btn-theme-primary border-0">แก้ไขข้อมูล (Edit)</a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    function edit()
    {
        $conn = new connect();
		$option = $_REQUEST['option'];
		$id = $_REQUEST['id'];
		if (empty($id) && !empty($_SESSION['uid'])) 
		{
			$id = $_SESSION['uid'];
		}
		if ($id == 0)
		{
			$head = "Add";
			$name = "";
			$surname = "";
			$user = "";
			$pass = "";
			$age = "";
			$gender = "";
			$mail = "";
			$tel = "";
			$bir = "";
			$oemail = "";
			$tall = "";
			$weight = "";
		}
		else
		{
			$head = "Edit";
			$sql = "select * from `users` where `id` = '".$id."'";
			$res = $conn->query($sql);
			while ($cdr = $res->fetch())
			{
				$name = $cdr['name'];
				$surname = $cdr['surname'];
				$user = $cdr['user'];
				$gender = $cdr['gender'];
				$age = $cdr['age'];
				$mail = $cdr['mail'];
				$tel = $cdr['tel'];
				$bir = $cdr['bir'];
				$oemail = $cdr['oemail'];
				$tall = $cdr['tall'];
				$weight = $cdr['weight'];
			}
		}
        echo $this->get_theme_css();
        ?>
        <div class='theme-container'>
            <div class='profile-card'>
                <div class="icon-wrapper">
                    <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#1e7d48" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                </div>
                <h2 class="theme-header"><?php echo $head; ?>ข้อมูลผู้ใช้งาน (Profile)</h2>

                <form action='index.php' method='post'>
                    <input type="hidden" name="option" value="profile">
                    <input type="hidden" name="task" value="save">
                    <input type="hidden" name="id" value="<?= $id ?>">

                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label">ชื่อผู้ใช้ (Username)</label>
                            <input type="text" name="user" class="form-control" value="<?= $user ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">ชื่อ (Name)</label>
                            <input type="text" name="name" class="form-control" value="<?= $name ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">นามสกุล (Surname)</label>
                            <input type="text" name="surname" class="form-control" value="<?= $surname ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">เพศ (Gender)</label>
                            <select name="gender" class="form-select">
                                <option value="1" <?= ($gender == 1) ? 'selected' : '' ?>>ชาย (Male)</option>
                                <option value="2" <?= ($gender == 2) ? 'selected' : '' ?>>หญิง (Female)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">อายุ (Age)</label>
                            <div class="input-group">
                                <input type="number" name="age" class="form-control" value="<?= $age ?>" placeholder="เช่น 21">
                                <span class="input-group-text">ปี</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">น้ำหนัก (Weight)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="weight" class="form-control" value="<?= $weight ?>" placeholder="เช่น 58.50">
                                <span class="input-group-text">kg</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ส่วนสูง (Height)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="tall" class="form-control" value="<?= $tall ?>" placeholder="เช่น 155.50">
                                <span class="input-group-text">cm</span>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">อีเมล (Email)</label>
                            <input type="email" name="mail" class="form-control" value="<?= $mail ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">เบอร์โทรศัพท์ (Tel)</label>
                            <input type="text" name="tel" class="form-control" value="<?= $tel ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">วันเกิด (Birthday)</label>
                            <input type="date" name="bir" class="form-control" value="<?= $bir ?>">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">อีเมลที่ทำงาน (Office Email)</label>
                            <input type="email" name="oemail" class="form-control" value="<?= $oemail ?>">
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label">รหัสผ่าน (Password) <?= ($id != 0) ? '<span style="color:#a0aec0; font-size:12px; margin-left:5px;">*เว้นว่างไว้หากไม่ต้องการเปลี่ยน</span>' : '' ?></label>
                            <div class="input-group">
                                <input type="password" name="pass" id="passInput" class="form-control password-input" value="" <?= ($id == 0) ? 'required' : '' ?> placeholder="********">
                                <span class="input-group-text password-toggle" onclick="togglePassword()">
                                    <svg id="eyeIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-6 mb-3">
                            <a href="index.php?option=<?= $option ?>&task=def&id=<?= $id ?>" class="btn-theme-outline w-100">ยกเลิก (Cancel)</a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <button type="submit" class="btn-theme-primary w-100 border-0">บันทึกข้อมูล (Save)</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function togglePassword() {
                const passInput = document.getElementById('passInput');
                const eyeIcon = document.getElementById('eyeIcon');
                
                if (passInput.type === 'password') {
                    passInput.type = 'text';
                    eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                } else {
                    passInput.type = 'password';
                    eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                }
            }
        </script>
        <?php
    }

    function save()
    {
        $conn = new connect();
        $id = $_REQUEST['id'];
        if (empty($id) && !empty($_SESSION['uid'])) 
{
            $id = $_SESSION['uid'];
        }
        $name = $_REQUEST['name'];
        $surname = $_REQUEST['surname'];
        $user = $_REQUEST['user'];
        $pass = isset($_REQUEST['pass']) ? $_REQUEST['pass'] : '';
        $mail = $_REQUEST['mail'];
        $bir = $_REQUEST['bir'];
        $tel = $_REQUEST['tel'];
        $oemail = $_REQUEST['oemail'];
        $gender = isset($_REQUEST['gender']) ? $_REQUEST['gender'] : '0';
        $tall = $_REQUEST['tall'];
        $weight = $_REQUEST['weight'];  
        $age = $_REQUEST['age']; 

        if ($id == 0) 
        {
            $sql = "insert into users set name = '".$name."', surname = '".$surname."', user = '".$user."', mail = '".$mail."', tel = '".$tel."', bir = '".$bir."', oemail = '".$oemail."', gender = '".$gender."',tall = '".$tall."',weight = '".$weight."',age = '".$age."', pass = '".$conn->salter($pass)."'";
        }
        else 
        {
            $sql = "update users set name = '".$name."', surname = '".$surname."', user = '".$user."', mail = '".$mail."', tel = '".$tel."', bir = '".$bir."', oemail = '".$oemail."', gender = '".$gender."', tall = '".$tall."', weight = '".$weight."',age = '".$age."'";
            if (!empty($pass)) {
                $sql .= ", pass = '".$conn->salter($pass)."'";
            }
            $sql .= " where id = '".$id."'";
        }
        $conn->query($sql);
        header('location:index.php?option=profile&task=def&id='.$id);
    }
}
?>