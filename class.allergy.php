<?php

class allergy
{

    function def() 
    {
        
        if (session_status() === PHP_SESSION_NONE) 
        {
            session_start();
        }
        $conn = new connect();
        $id = (int)($_REQUEST['id'] ?? 0);
        $uid = (int)($_SESSION['uid'] ?? 0); 
        $username = 'ไม่ทราบชื่อผู้ใช้งาน'; 

        if ($uid > 0) 
        {
            $sql_user = "select user from users where id = '{$uid}'"; 
            $res_user = $conn->query($sql_user);
            if ($row_user = $res_user->fetch()) 
            {
                $username = $row_user['user']; 
            }
        }
        
        $disease = '';
        $selected_ftypes = []; 
        if ($uid > 0 && isset($_SESSION['allergy_session_active'])) 
        {
            $sql = "select * from food_allergy where uid = '{$uid}'";
            $res = $conn->query($sql);
            while ($cdr = $res->fetch()) 
            {
                $disease = (int)$cdr['disease'];
                $selected_ftypes[] = (int)$cdr['ftype_id']; 
            }
        }

        $ftypes_list = [];
        $sql_ftype = "select * from ftype where status = 1";
        $res_ftype = $conn->query($sql_ftype); 
        while ($row = $res_ftype->fetch()) 
        {
            $ftypes_list[] = $row;
        }

        ?>
        <style>

            .theme-wrapper 
            {
                font-family: 'Prompt', sans-serif;
                background-color: #f3f8f5;
                min-height: 100vh;
                padding: 40px 15px;
                color: #333;
            }
            .theme-card 
            {
                background: #ffffff;
                border-radius: 15px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.05);
                border: none;
                max-width: 700px;
                margin: 0 auto;
                padding: 40px;
            }

            .theme-header 
            {
                color: #278a5b;
                font-weight: 600;
                text-align: center;
                margin-bottom: 5px;
                font-size: 1.5rem;

            }
            .theme-subheader 
            {
                text-align: center;
                color: #6c757d;
                font-size: 0.95rem;
                margin-bottom: 30px;
            }

            .user-badge 
            {
                display: inline-block;
                background-color: #e8f3ec;
                color: #278a5b;
                padding: 5px 15px;
                border-radius: 20px;
                font-size: 0.9rem;
                margin-top: 10px;
            }

            .form-label 
            {
                font-weight: 500;
                color: #495057;
                font-size: 0.95rem;
                margin-bottom: 8px;
                display: block;
            }

            .form-select-theme 
            {
                border-radius: 8px;
                border: 1px solid #ced4da;
                padding: 12px 15px;
                width: 100%;
                font-family: 'Prompt', sans-serif;
                transition: all 0.15s;
            }

            .theme-checkbox-group 
            {
                display: grid;
                gap: 12px;
            }

            .theme-checkbox-custom 
            {
                background: #fff;
                border: 1px solid #ced4da;
                border-radius: 8px;
                padding: 12px 20px;
                display: flex;
                align-items: center;
                cursor: pointer;
                transition: all 0.2s;
            }

            .theme-checkbox-custom input[type="checkbox"]
            {
                margin-right: 15px;
                accent-color: #278a5b; 
                transform: scale(1.3);
                cursor: pointer;
            }

            .btn-theme-primary 
            {

                background-color: #288b5a; 
                color: white;
                border-radius: 8px; 
                padding: 10px 25px;
                font-family: 'Prompt', sans-serif;
                font-weight: 500;
                font-size: 1rem;
                border: none;
                cursor: pointer;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                box-shadow: 0 4px 12px rgba(40, 139, 90, 0.3); 

            }

            .btn-theme-primary:hover:not(:disabled) 
            {
                background-color: #1e6b46; 
                box-shadow: 0 6px 15px rgba(40, 139, 90, 0.4);
            }

            .btn-theme-primary:disabled 
            {
                background-color: #d6e8de; 
                color: #ffffff;
                cursor: not-allowed;
                box-shadow: none; 
            }

            .btn-theme-secondary 
            {
                background-color: transparent; 
                color: #6b7280; 
                border-radius: 8px; 
                padding: 10px 24px;
                font-family: 'Prompt', sans-serif;
                font-weight: 500;
                font-size: 1rem;
                border: 1.5px solid #d1d5db; 
                cursor: pointer;
                transition: all 0.2s;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-theme-secondary:hover 
            {
                background-color: #f3f4f6;
                border-color: #9ca3af;
                color: #374151;
            }

            .btn-container 
            {
                display: flex;
                justify-content: space-between; 
                align-items: center;
                margin-top: 40px;
            }

        </style>
        
        <script>
        document.addEventListener("DOMContentLoaded", function() 
        {
            const diseaseselect = document.getElementById("diseaseselect");
            const checkboxes = document.querySelectorAll(".allergy-checkbox");
            const nextBtn = document.getElementById("nextBtn");
            if (!diseaseselect || !nextBtn) 
            {
                return;
            }

            function checkFormValidity() 
            {
                let isDiseaseselected = diseaseselect.value !== "";
                let isAnyCheckboxChecked = Array.from(checkboxes).some(cb => cb.checked);
                if (isDiseaseselected && isAnyCheckboxChecked) 
                {
                    nextBtn.disabled = false;
                } 
                else 
                {
                    nextBtn.disabled = true;
                }
            }
            diseaseselect.addEventListener("change", checkFormValidity);
            checkboxes.forEach(cb => cb.addEventListener("change", checkFormValidity));
            checkFormValidity();
        });
        </script>

        <div class='theme-wrapper'>
            <div class='theme-card'>
                
                <div class="text-center mb-3">
                    <i class="fas fa-leaf" style="font-size: 45px; color: #278a5b;"></i>
                </div>
                
                <h2 class="theme-header">ข้อมูลสุขภาพของคุณ</h2>
                <div class="text-center">
                    <p class="theme-subheader" style="margin-bottom: 10px;">ตรวจสอบข้อมูลเพื่อปรับแต่งโภชนาการที่เหมาะสมกับคุณ</p>
                    <span class="user-badge"><i class="fas fa-user"></i> ผู้ทำรายการ: <?php echo htmlspecialchars($username); ?></span>
                </div>
                
                <form action="index.php" method="post" id="allergyForm" style="margin-top: 30px;">
                    
                    <div class="mb-4" style="margin-bottom: 25px;">
                        <label class="form-label">
                            <i class="fas fa-notes-medical" style="color: #278a5b; margin-right: 5px;"></i> โรคประจำตัว / เป้าหมาย
                        </label>
                        <select name="disease" id="diseaseselect" class="form-select-theme">
                            <option value="" <?php echo ($disease === '') ? 'selected' : ''; ?>>-- กรุณาเลือก --</option>
                            <option value="1" <?php echo ($disease == 1) ? 'selected' : ''; ?>>ไม่มีโรคประจำตัว</option>
                            <option value="2" <?php echo ($disease == 2) ? 'selected' : ''; ?>>โรคเบาหวาน</option>
                            <option value="3" <?php echo ($disease == 3) ? 'selected' : ''; ?>>โรคไต</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-ban" style="color: #278a5b; margin-right: 5px;"></i> การแพ้อาหาร
                        </label>
                        <div class="theme-checkbox-group">
                            <?php
                            $sql_ftype = "select * from ftype where status = 1";
                            $res_ftype = $conn->query($sql_ftype); 
                            
                            while ($row = $res_ftype->fetch()) {
                                $checked = in_array($row['id'], $selected_ftypes) ? "checked" : "";
                                
                                echo "<label class='theme-checkbox-custom'>";
                                echo "<input type='checkbox' name='ftype_id[]' class='allergy-checkbox' value='".$row['id']."' ".$checked.">";
                                echo "<span>".htmlspecialchars($row['name'])."</span>";
                                echo "</label>";
                            }
                            ?>
                        </div>
                    </div>
                    <input type="hidden" name="option" value="allergy"> 
                    <input type="hidden" name="task" value="save">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <div class="btn-container">
                        <button type="button" class="btn-theme-secondary" onclick="window.location.href='index.php?option=aim&task=def';">
                            <i class="fa-solid fa-arrow-left"></i> ย้อนกลับ (Back)
                        </button>
                        <button type="submit" id="nextBtn" class="btn-theme-primary" disabled>
                            ถัดไป (Next) <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                        </button>
                    </div>
                    
                </form>
            </div>
        </div>
        <?php
    }

    function save()
    {
        if (session_status() === PHP_SESSION_NONE) 
        {
            session_start();
        }
        $conn = new connect();
        $id = (int)($_REQUEST['id'] ?? 0);
        $disease = (int)($_REQUEST['disease'] ?? 0);
        $uid = (int)($_SESSION['uid'] ?? 0); 
        $ftype_ids = $_REQUEST['ftype_id'] ?? [];
        if (!is_array($ftype_ids)) 
        {
            $ftype_ids = [];
        }
        
        $last_id = 0;
        if ($uid > 0) 
        {
            $existing_ftypes = []; 
            $sql_check = "select id, ftype_id from food_allergy where uid = '{$uid}'";
            $res_check = $conn->query($sql_check);
            while($row = $res_check->fetch()) 
            {
                $existing_ftypes[(int)$row['ftype_id']] = (int)$row['id']; 
            }

            if (!empty($existing_ftypes)) 
            {
                $sql_update_disease = "update food_allergy set disease = '{$disease}' where uid = '{$uid}'";
                $conn->query($sql_update_disease);
            }

            foreach ($ftype_ids as $ftype_val) 
            {
                $ftype_val = (int)$ftype_val; 
                if (array_key_exists($ftype_val, $existing_ftypes)) 
                {
                    if ($last_id == 0) 
                    {
                        $last_id = $existing_ftypes[$ftype_val];
                    }
                    unset($existing_ftypes[$ftype_val]);
                } 
                else 
                {
                    $sql_insert = "insert into food_allergy set uid = '{$uid}', disease = '{$disease}', ftype_id = '{$ftype_val}', status = 1";
                    $last_id = $conn->query_lastid($sql_insert);
                }
            }

            foreach ($existing_ftypes as $old_ftype => $old_id) 
            {
                $old_id = (int)$old_id; 
                $sql_delete = "delete from food_allergy where id = '{$old_id}'";
                $conn->query($sql_delete);
            }
        }
        
        $redirect_id = ($id > 0) ? $id : $last_id; 
        $conn->save_logs("Save Food Allergy", $uid, $redirect_id);
        $_SESSION['allergy_session_active'] = true;
        header("Location: index.php?option=mp&task=def");
        exit();
    }
}
?>