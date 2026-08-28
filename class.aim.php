<?php

class aim
{
    function def() 
    {
        if (session_status() === PHP_SESSION_NONE) 
        {
            session_start();
        }

        $conn = new connect();
        $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
        if ($id == 0 && isset($_SESSION['saved_target_id']) && $_SESSION['saved_target_id'] > 0) 
        {
            $id = $_SESSION['saved_target_id'];
        }
        
        $session_bmi = isset($_SESSION['home_bmi']) ? $_SESSION['home_bmi'] : '';
        $login_user = "";
        if (isset($_SESSION['uid']) && $_SESSION['uid'] > 0) 
        {
            $sql_user = "select user from users where id = '".(int)$_SESSION['uid']."'";
            $res_user = $conn->query($sql_user);
            if ($user_data = $res_user->fetch()) 
            {
                $login_user = $user_data['user'];
            }
        }

        if ($id == 0) 
        {
            $head = "เพิ่มเป้าหมาย (Add)";
            $user = $login_user;
            $target_goal = "";
            $bmi = $session_bmi; // Show BMI from home page calculation
            $weight = "";
        }
        else 
        {
            $head = "แก้ไขเป้าหมาย (Edit)";
            $sql = "select * from target where id = '".$id."'";
            $res = $conn->query($sql);
            if ($cdr = $res->fetch()) 
            {
                $user = $login_user; 
                $target_goal = isset($cdr['goal']) ? $cdr['goal'] : ''; 
                $bmi = !empty($cdr['bmi']) ? $cdr['bmi'] : $session_bmi; 
                $weight = $cdr['weight'];
            }
        }

        $prev_url = "index.php?option=home";

        ?>
        <style>
            .bon-theme 
            {
                font-family: 'Prompt', sans-serif;
                background-color: #f2f7f4; 
                min-height: 100vh;
                padding: 40px 15px;
            }
            .bon-card 
            {
                background-color: #ffffff;
                border-radius: 24px; 
                box-shadow: 0 10px 40px rgba(44, 122, 81, 0.08); 
                max-width: 600px; 
                margin: 0 auto;
                padding: 50px 40px;
            }
            .bon-icon 
            {
                color: #288b5d;
                font-size: 3.5rem;
                margin-bottom: 15px;
                text-align: center;
                display: block;
            }
            .bon-title 
            {
                color: #288b5d;
                font-weight: 600;
                font-size: 1.6rem;
                text-align: center;
                margin-bottom: 8px;
            }
            .bon-subtitle 
            {
                color: #6b7280;
                font-size: 0.95rem;
                text-align: center;
                line-height: 1.6;
                margin-bottom: 40px;
            }

            .input-group-centered 
            {
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-bottom: 25px;
            }
            .input-group-centered label 
            {
                font-weight: 500;
                color: #4b5563;
                margin-bottom: 10px;
                font-size: 1rem;
                text-align: center;
            }
            .input-custom 
            {
                width: 100%;
                max-width: 320px;
                padding: 12px 20px;
                border-radius: 12px;
                border: 1.5px solid #d1d5db;
                font-family: 'Prompt', sans-serif;
                font-size: 1rem;
                color: #1f2937;
                background-color: #fafafa;
                text-align: center; 
                transition: all 0.3s ease;
            }
            .input-custom[readonly] 
            {
                background-color: #e5e7eb;
                color: #6b7280;
                cursor: not-allowed;
            }
            .input-custom:focus:not([readonly]) 
            {
                border-color: #288b5d;
                background-color: #ffffff;
                outline: none;
                box-shadow: 0 0 0 4px rgba(40, 139, 93, 0.15);
            }

            .bon-nav-wrapper 
            {
                display: flex;
                justify-content: space-between;
                max-width: 600px;
                margin: 30px auto 0 auto;
            }
            .btn-bon-nav 
            {
                padding: 12px 28px;
                border-radius: 12px;
                font-weight: 500;
                font-size: 1rem;
                text-decoration: none;
                transition: all 0.2s;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
                border: none;
                font-family: 'Prompt', sans-serif;
            }
            .btn-bon-back 
            {
                background-color: transparent;
                color: #6b7280;
                border: 1.5px solid #d1d5db;
            }
            .btn-bon-back:hover 
            {
                background-color: #f3f4f6;
                color: #374151;
                border-color: #9ca3af;
            }
            
            .btn-bon-next 
            {
                background-color: #288b5d;
                color: white;
                box-shadow: 0 4px 15px rgba(40, 139, 93, 0.3);
            }
            .btn-bon-next:hover:not(:disabled) 
            {
                background-color: #1f704a;
                transform: translateY(-2px);
            }
            .btn-bon-next:disabled 
            {
                background-color: #a7d0b8;
                box-shadow: none;
                cursor: not-allowed;
            }
        </style>

        <div class="bon-theme">
            <div class="bon-card">
                <i class="fa-solid fa-heart-circle-check bon-icon"></i>
                <h2 class="bon-title">ข้อมูลโภชนาการของคุณ</h2>
                <p class="bon-subtitle">
                    กรุณากรอกข้อมูลให้ครบถ้วน เพื่อบันทึกเป็นเป้าหมาย<br>ในการวางแผนเมนูอาหารของคุณ
                </p>

                <form action="index.php?option=aim&task=save" method="post" id="targetForm">
                    
                    <div class="input-group-centered">
                        <label for="user">Username</label>
                        <input type="text" id="user" name="user" class="input-custom" value="<?php echo htmlspecialchars($user); ?>" placeholder="Username" readonly>
                    </div>

                    <div class="input-group-centered">
                        <label for="target_goal">เป้าหมาย</label>
                        <input type="text" id="target_goal" name="target_goal" class="input-custom" value="<?php echo htmlspecialchars($target_goal); ?>" placeholder="เช่น ลดน้ำหนัก 5 โล" required>
                    </div>

                    <div class="input-group-centered">
                        <label for="bmi">ค่า BMI ปัจจุบัน</label>
                        <input type="number" id="bmi" name="bmi" class="input-custom" step="any" value="<?php echo htmlspecialchars($bmi); ?>" placeholder="0.00" required>
                    </div>

                    <div class="input-group-centered">
                        <label for="weight">น้ำหนักในอนาคตที่อยากจะทำ (Weight - kg)</label>
                        <input type="number" id="weight" name="weight" class="input-custom" step="any" value="<?php echo htmlspecialchars($weight); ?>" placeholder="0.00 (kg)" required>
                    </div>

                    <input type="hidden" name="id" value="<?php echo $id; ?>">

                    <div class="bon-nav-wrapper">
                        <a href="<?php echo $prev_url; ?>" class="btn-bon-nav btn-bon-back">
                            <i class="fa-solid fa-arrow-left"></i> ย้อนกลับ (Back)
                        </a>
                        
                        <button type="submit" id="btnNext" class="btn-bon-nav btn-bon-next" disabled>
                            ถัดไป (Next) <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() 
            {
                const form = document.getElementById("targetForm");
                const btnNext = document.getElementById("btnNext");
                const inputs = form.querySelectorAll("input[required]");

                function validateForm() 
                {
                    let isValid = true;
                    inputs.forEach(input => 
                    {
                        const val = input.value.trim();
                        if (val === "")
                        {
                            isValid = false;
                        }
                    });
                    btnNext.disabled = !isValid;
                }

                inputs.forEach(input => 
                {
                    input.addEventListener("input", validateForm);
                    input.addEventListener("change", validateForm);
                });

                validateForm();
            });
        </script>
        <?php
    }
    
    function save()
    {
        if (session_status() === PHP_SESSION_NONE) 
        {
            session_start();
        }

        $conn = new connect();
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id == 0 && isset($_SESSION['saved_target_id']) && $_SESSION['saved_target_id'] > 0) 
        {
            $id = $_SESSION['saved_target_id'];
        }

        $uid = isset($_SESSION['uid']) ? (int)$_SESSION['uid'] : 0;
        $goal = isset($_POST['target_goal']) ? $_POST['target_goal'] : '';
        $bmi = isset($_POST['bmi']) ? $_POST['bmi'] : 0;
        $weight = isset($_POST['weight']) ? $_POST['weight'] : 0;

        if ($id > 0) 
        {
            $sql = "update target set uid = '".$uid."', goal = '".$goal."', bmi = '".$bmi."', weight = '".$weight."' where id = '".$id."'";
            $conn->query($sql);
            $conn->save_logs("Edit Target > ", $_SESSION['uid'], $id);
        }
        else 
        {
            $sql = "insert into target set uid = '".$uid."', goal = '".$goal."', bmi = '".$bmi."', weight = '".$weight."'";
            $id = $conn->query_lastid($sql);
            $conn->save_logs("Add Target > ", $_SESSION['uid'], $id);
        }
        
        $_SESSION['saved_target_id'] = $id;
        $_SESSION['home_bmi'] = $bmi; 
        header("location:index.php?option=allergy&task=def");
        exit(); 
    }

}

?>