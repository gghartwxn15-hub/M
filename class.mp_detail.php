<?php

class mp_detail
{
    function def() 
    {
        $id = (int)($_REQUEST['id'] ?? 0);
        $conn = new connect();
        $name = ""; $date = ""; $kcal = "";

        if ($id > 0) {
            $sql = "select `mealplan`.`id` as `id`,`mealplan`.`date` as `date`,`mealplan`.`kcal` as `kcal`,
                    `users`.`name` as `name`, `users`.`surname` as `lastname`, `mealplan`.`status` as `status` 
                    from `mealplan`,`users`
                    where `mealplan`.`uid` = `users`.`id` and `mealplan`.`id` = '".$id."'";
            $res = $conn->query($sql);
            
            if($res) 
            {
                while ($cdr = $res->fetch()) 
                {
                    $name = $cdr['name'] . " " . $cdr['lastname']; 
                    $date = $cdr['date'];
                    
                    $session_tdee = $_SESSION['home_tdee'] ?? '';
                    if (is_numeric($session_tdee) && $session_tdee > 0) {
                        $kcal = round((float)$session_tdee);
                    } else {
                        $kcal = round((float)$cdr['kcal']);
                    }
                }
            }
        }

        if ($name === "") 
        {
            $uid = (int)($_SESSION['uid'] ?? 0);
            if ($uid > 0) 
            {
                $sql_user = "select name, surname from users where id = '{$uid}' LIMIT 1";
                $res_user = $conn->query($sql_user);
                if ($res_user && $user_row = $res_user->fetch()) 
                {
                    $name = trim($user_row['name'] . ' ' . $user_row['surname']);
                }
            }
        }

        if ($date === "") 
        {
            $date = date('Y-m-d');
        }

        if ($kcal === "") 
        {
            $session_tdee = $_SESSION['home_tdee'] ?? '';
            if (is_numeric($session_tdee) && $session_tdee > 0) 
            {
                $kcal = round((float)$session_tdee);
            } 
            else 
            {
                $kcal = 0;
            }
        }

        if (session_status() === PHP_SESSION_NONE) 
        {
            session_start();
        }
        if (!isset($_SESSION['mp_home_backup'])) 
        {
            $_SESSION['mp_home_backup'] = [
                'home_age' => $_SESSION['home_age'] ?? null,
                'home_gender' => $_SESSION['home_gender'] ?? null,
                'home_height' => $_SESSION['home_height'] ?? null,
                'home_weight' => $_SESSION['home_weight'] ?? null,
                'home_type' => $_SESSION['home_type'] ?? null,
                'home_bmi' => $_SESSION['home_bmi'] ?? null,
                'home_tdee' => $_SESSION['home_tdee'] ?? null,
                'home_carbs' => $_SESSION['home_carbs'] ?? null,
                'byear' => $_SESSION['byear'] ?? null
            ];
        }

        $days_list = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $meals_list = ['Breakfast', 'Lunch', 'Dinner', 'Break'];

        $meal_plan = $_POST['meal_plan'] ?? $_SESSION['meal_plan'] ?? [];
        foreach ($meals_list as $meal_key) 
        {
            if (!isset($meal_plan[$meal_key]) || !is_array($meal_plan[$meal_key])) 
            {
                $meal_plan[$meal_key] = [];
            }

            foreach ($days_list as $day_name) 
            {
                if (!isset($meal_plan[$meal_key][$day_name])) 
                {
                    $meal_plan[$meal_key][$day_name] = [];
                }
            }
        }

        $_SESSION['meal_plan'] = $meal_plan;

        $food_data_list = [];
        if (!empty($meal_plan)) 
        {
            $all_food_ids = [];
            foreach ($meal_plan as $meal => $days) 
            {
                foreach ($days as $day => $food_id) 
                {
                    if (!empty($food_id)) 
                    {
                        $all_food_ids[] = (int)$food_id;
                    }
                }
            }
            
            if (!empty($all_food_ids)) 
            {
                $all_food_ids = array_unique($all_food_ids);
                $food_ids_str = implode(',', $all_food_ids);
                $sql_food = "select id, name, image, kcalmin, kcalmax, promin, promax, carbmin, carbmax, sugarmin, sugarmax, fatmin, fatmax, somin, somax, pomin, pomax, phosmin, phosmax from food where id IN ({$food_ids_str})";
                $res_food = $conn->query($sql_food);
                
                if ($res_food) 
                {
                    while ($food_row = $res_food->fetch()) 
                    {
                        $food_data_list[$food_row['id']] = $food_row;
                    }
                }
            }
        }
        ?>
        
        <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
        <style>
            .theme-container 
            {
                font-family: 'Prompt', sans-serif;
                background-color: #f8f9fa;
                padding: 20px;
                border-radius: 15px;
            }
            .card-theme 
            {
                background: #ffffff;
                border-radius: 12px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.05);
                padding: 25px;
                margin-bottom: 20px;
            }
            .text-theme-green 
            {
                color: #1a7a4c;
                font-weight: 600;
            }
            .btn-theme-green 
            {
                background-color: #1a7a4c;
                color: white;
                border-radius: 8px;
                padding: 10px 25px;
                border: none;
                transition: 0.3s;
                cursor: pointer;
            }
            .btn-theme-green:hover 
            {
                background-color: #145e3a;
                color: white;
            }
            .table-scroll-container 
            {
                max-height: 600px;
                overflow-y: auto;
                border: 1px solid #dee2e6;
                border-radius: 8px;
            }
            .table-custom 
            {
                border-collapse: separate; 
                border-spacing: 0;
                width: 100%;
                table-layout: fixed;
            }
            .table-custom thead th 
            {
                background-color: #f1f8f4;
                color: #1a7a4c;
                text-align: center;
                padding: 12px;
                font-weight: 500;
                position: sticky; 
                top: 0;
                z-index: 10; 
                border-bottom: 1px solid #dee2e6;
                border-right: 1px solid #dee2e6;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05); 
            }
            .table-custom thead th:last-child 
            {
                border-right: none;
            }
            .table-custom td 
            {
                border-bottom: 1px solid #dee2e6;
                border-right: 1px solid #dee2e6;
                padding: 12px;
                vertical-align: middle;
            }
            .table-custom td:last-child 
            {
                border-right: none;
            }
            .table-custom tbody tr:last-child td 
            {
                border-bottom: none;
            }
            .food-item-box 
            {
                display: flex;
                align-items: flex-start;
                gap: 16px;
                margin-bottom: 16px;
                padding-bottom: 16px;
                border-bottom: 1px dashed #e0e0e0;
            }
            .food-item-box:last-child 
            {
                border-bottom: none;
                margin-bottom: 0;
                padding-bottom: 0;
            }
            .food-placeholder-img 
            {
                width: 96px;
                height: 96px;
                background-color: #e9ecef;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2em;
                color: #6c757d;
                flex-shrink: 0;
                overflow: hidden;
                border: 1px solid #e3e8e5;
                box-shadow: 0 3px 10px rgba(26,122,76,0.12);
            }
            .food-placeholder-img img
            {
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 12px;
                display: block;
                transition: transform 0.3s ease;
            }
            .food-placeholder-img img:hover
            {
                transform: scale(1.08);
            }
            .nutrient-grid 
            {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 4px 8px;
                font-size: 0.75em;
                color: #555;
                margin-top: 6px;
                background-color: #f8f9fa;
                padding: 8px;
                border-radius: 6px;
            }
            .nutrient-grid strong 
            {
                color: #333;
            }
            
            .total-nutrient-box 
            {
                background-color: #f1f8f4;
                padding: 12px;
                border-radius: 8px;
                border: 1px solid #d3e2d7;
                text-align: left;
                font-size: 0.85em;
                color: #444;
            }
            .total-nutrient-box .title 
            {
                color: #1a7a4c;
                font-weight: 600;
                font-size: 1.1em;
                text-align: center;
                border-bottom: 1px solid #d3e2d7;
                padding-bottom: 8px;
                margin-bottom: 10px;
            }
            .total-nutrient-row 
            {
                display: flex;
                justify-content: space-between;
                margin-bottom: 4px;
            }
            .total-nutrient-row strong 
            {
                color: #333;
            }
        </style>

        <div class='container theme-container'>
            <div class='row'>
                <div class='col-12'>
                    
                    <!-- ส่วนหัวข้อมูลผู้ใช้ -->
                    <div class="card-theme">
                        <h3 class="text-theme-green mb-4 text-center">
                            <i class="fas fa-heartbeat"></i> ข้อมูลโภชนาการ (Mealplan)
                        </h3>
                        
                        <table class='table table-borderless' style="max-width: 500px;">
                            <tbody>
                            <?php
                                $conn->form_input("ชื่อ (Name)","name",$name,2);
                                $conn->form_input("วันที่ (Date)","date",$date,2);
                                $conn->form_input("เป้าหมายพลังงาน (Kcal)","kcal",$kcal,2);
                            ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- ส่วนตารางจัดอาหาร -->
                    <div class="card-theme">
                        
                        <div class="table-scroll-container">
                            <table class='table-custom'>
                                <thead>
                                    <tr>
                                        <th style="width: 10%;">วัน</th>
                                        <th style="width: 7%;">มื้ออาหาร</th>
                                        <th style="width: 55%;">รายการอาหาร และ ข้อมูลโภชนาการ (Min - Max)</th>
                                        <th style="width: 18%;">สารอาหารรวมรายวัน (Min - Max)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $days_list = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
                                    
                                    $meals_list = array(
                                        'Breakfast' => 'มื้อเช้า', 
                                        'Lunch' => 'กลางวัน', 
                                        'Dinner' => 'เย็น',
                                        'Break' => 'มื้อเบรก'
                                    );
                                    $rowspan_count = count($meals_list);
                                    $schedule = array();
                                    foreach($days_list as $d) 
                                    {
                                        $schedule[$d] = array(
                                            'Breakfast' => [], 'Lunch' => [], 'Dinner' => [], 'Break' => [], 
                                            't_kcal_min' => 0, 't_kcal_max' => 0,
                                            't_pro_min' => 0, 't_pro_max' => 0,
                                            't_carb_min' => 0, 't_carb_max' => 0,
                                            't_sugar_min' => 0, 't_sugar_max' => 0,
                                            't_fat_min' => 0, 't_fat_max' => 0,
                                            't_so_min' => 0, 't_so_max' => 0,
                                            't_po_min' => 0, 't_po_max' => 0,
                                            't_phos_min' => 0, 't_phos_max' => 0
                                        );
                                    }

                                    if (!empty($meal_plan)) 
                                    {
                                        foreach ($meal_plan as $meal => $days) 
                                        {
                                            foreach ($days as $day => $food_id) 
                                            {
                                                if (!empty($food_id) && isset($food_data_list[$food_id])) {
                                                    $food = $food_data_list[$food_id];
                                                    
                                                    $schedule[$day][$meal][] = array(
                                                        'name' => $food['name'],
                                                        'image' => $food['image'] ?? '',
                                                        'kmin' => $food['kcalmin'] ?? 0,
                                                        'kmax' => $food['kcalmax'] ?? 0,
                                                        'pro_min' => $food['promin'] ?? 0,
                                                        'pro_max' => $food['promax'] ?? 0,
                                                        'carbo_min' => $food['carbmin'] ?? 0,
                                                        'carbo_max' => $food['carbmax'] ?? 0,
                                                        'sugar_min' => $food['sugarmin'] ?? 0,
                                                        'sugar_max' => $food['sugarmax'] ?? 0,
                                                        'fat_min' => $food['fatmin'] ?? 0,
                                                        'fat_max' => $food['fatmax'] ?? 0,
                                                        'sodium_min' => $food['somin'] ?? 0,
                                                        'sodium_max' => $food['somax'] ?? 0,
                                                        'potas_min' => $food['pomin'] ?? 0,
                                                        'potas_max' => $food['pomax'] ?? 0,
                                                        'phos_min' => $food['phosmin'] ?? 0,
                                                        'phos_max' => $food['phosmax'] ?? 0
                                                    );
                                                
                                                    $schedule[$day]['t_kcal_min'] += $food['kcalmin'] ?? 0;
                                                    $schedule[$day]['t_kcal_max'] += $food['kcalmax'] ?? 0;
                                                    $schedule[$day]['t_pro_min'] += $food['promin'] ?? 0;
                                                    $schedule[$day]['t_pro_max'] += $food['promax'] ?? 0;
                                                    $schedule[$day]['t_carb_min'] += $food['carbmin'] ?? 0;
                                                    $schedule[$day]['t_carb_max'] += $food['carbmax'] ?? 0;
                                                    $schedule[$day]['t_sugar_min'] += $food['sugarmin'] ?? 0;
                                                    $schedule[$day]['t_sugar_max'] += $food['sugarmax'] ?? 0;
                                                    $schedule[$day]['t_fat_min'] += $food['fatmin'] ?? 0;
                                                    $schedule[$day]['t_fat_max'] += $food['fatmax'] ?? 0;
                                                    $schedule[$day]['t_so_min'] += $food['somin'] ?? 0;
                                                    $schedule[$day]['t_so_max'] += $food['somax'] ?? 0;
                                                    $schedule[$day]['t_po_min'] += $food['pomin'] ?? 0;
                                                    $schedule[$day]['t_po_max'] += $food['pomax'] ?? 0;
                                                    $schedule[$day]['t_phos_min'] += $food['phosmin'] ?? 0;
                                                    $schedule[$day]['t_phos_max'] += $food['phosmax'] ?? 0;
                                                }
                                            }
                                        }
                                    }

                                    foreach ($days_list as $day) 
                                    {
                                        $is_first_meal = true;
                                        foreach ($meals_list as $meal_key => $meal_label) 
                                        {
                                            echo "<tr>";
                                            
                                            if ($is_first_meal) 
                                            {
                                                echo "<td rowspan='{$rowspan_count}' class='text-center fw-bold bg-white'>{$day}</td>"; 
                                            }
                                            
                                            echo "<td class='text-center bg-white'>{$meal_label}</td>";
                                            
                                            echo "<td class='bg-white'>";
                                            if (!empty($schedule[$day][$meal_key])) 
                                            {
                                                foreach ($schedule[$day][$meal_key] as $food) 
                                                {
                                                    $img_path = !empty($food['image']) ? htmlspecialchars($food['image']) : '';
                                                    if ($img_path !== '') {
                                                        $img_html = "<img src='" . $img_path . "' alt='" . htmlspecialchars($food['name']) . "' loading='lazy' onerror=\"this.parentNode.innerHTML='<i class=\\'fas fa-utensils\\'></i>';\">";
                                                    } else {
                                                        $img_html = "<i class='fas fa-utensils'></i>";
                                                    }

                                                    echo "<div class='food-item-box'>
                                                            <div class='food-placeholder-img'>{$img_html}</div>
                                                            <div style='flex: 1; min-width: 0;'>
                                                                <strong style='font-size: 1.15em; color: #1a7a4c; display: block; margin-bottom: 2px;'>" . htmlspecialchars($food['name']) . "</strong>
                                                                
                                                                <div class='nutrient-grid'>
                                                                    <div><strong>Kcal:</strong> {$food['kmin']} - {$food['kmax']}</div>
                                                                    <div><strong>Protein:</strong> {$food['pro_min']} - {$food['pro_max']} g</div>
                                                                    <div><strong>Carbo:</strong> {$food['carbo_min']} - {$food['carbo_max']} g</div>
                                                                    <div><strong>Sugar:</strong> {$food['sugar_min']} - {$food['sugar_max']} g</div>
                                                                    <div><strong>Fat:</strong> {$food['fat_min']} - {$food['fat_max']} g</div>
                                                                    <div><strong>Sodium:</strong> {$food['sodium_min']} - {$food['sodium_max']} mg</div>
                                                                    <div><strong>Potassium:</strong> {$food['potas_min']} - {$food['potas_max']} mg</div>
                                                                    <div><strong>Phosphorus:</strong> {$food['phos_min']} - {$food['phos_max']} mg</div>
                                                                </div>
                                                            </div>
                                                          </div>";
                                                }
                                            } 
                                            else 
                                            {
                                                echo "<div class='text-muted text-center py-2'>- ว่าง -</div>";
                                            }
                                            echo "</td>";

                                            if ($is_first_meal) 
                                            {
                                                echo "<td rowspan='{$rowspan_count}' class='bg-white' style='vertical-align: top;'>
                                                        <div class='total-nutrient-box'>
                                                            <div class='title'>รวมทั้งหมด / วัน</div>
                                                            
                                                            <div class='total-nutrient-row'>
                                                                <strong>Kcal:</strong> 
                                                                <span>{$schedule[$day]['t_kcal_min']} - {$schedule[$day]['t_kcal_max']}</span>
                                                            </div>
                                                            <div class='total-nutrient-row'>
                                                                <strong>Protein:</strong> 
                                                                <span>{$schedule[$day]['t_pro_min']} - {$schedule[$day]['t_pro_max']} g</span>
                                                            </div>
                                                            <div class='total-nutrient-row'>
                                                                <strong>Carbo:</strong> 
                                                                <span>{$schedule[$day]['t_carb_min']} - {$schedule[$day]['t_carb_max']} g</span>
                                                            </div>
                                                            <div class='total-nutrient-row'>
                                                                <strong>Sugar:</strong> 
                                                                <span>{$schedule[$day]['t_sugar_min']} - {$schedule[$day]['t_sugar_max']} g</span>
                                                            </div>
                                                            <div class='total-nutrient-row'>
                                                                <strong>Fat:</strong> 
                                                                <span>{$schedule[$day]['t_fat_min']} - {$schedule[$day]['t_fat_max']} g</span>
                                                            </div>
                                                            <div class='total-nutrient-row'>
                                                                <strong>Sodium:</strong> 
                                                                <span>{$schedule[$day]['t_so_min']} - {$schedule[$day]['t_so_max']} mg</span>
                                                            </div>
                                                            <div class='total-nutrient-row'>
                                                                <strong>Potassium:</strong> 
                                                                <span>{$schedule[$day]['t_po_min']} - {$schedule[$day]['t_po_max']} mg</span>
                                                            </div>
                                                            <div class='total-nutrient-row' style='margin-bottom: 0;'>
                                                                <strong>Phosphorus:</strong> 
                                                                <span>{$schedule[$day]['t_phos_min']} - {$schedule[$day]['t_phos_max']} mg</span>
                                                            </div>
                                                        </div>
                                                      </td>";
                                                $is_first_meal = false;
                                            }
                                            
                                            echo "</tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <form method="POST" action="index.php?option=mp_detail&task=save" class="d-flex justify-content-between mt-4">
                            <a href='index.php?option=mp&task=def' class='btn btn-secondary text-decoration-none' style='border-radius: 8px; padding: 10px 25px;'>
                                <i class='fas fa-arrow-left'></i> ย้อนกลับ (Back)
                            </a>

                            <input type="hidden" name="mealplan_id" value="<?php echo htmlspecialchars($id); ?>">
                            <input type="hidden" name="meal_plan_json" value='<?php echo htmlspecialchars(json_encode($meal_plan, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>'>
                            <button type="submit" class="btn btn-theme-green text-decoration-none">
                                <i class="fas fa-save"></i> บันทึก (Save)
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <?php
    }

   function save()
    {
        $conn = new connect();
        $meal_plan = $_POST['meal_plan'] ?? [];

        if (empty($meal_plan) && !empty($_POST['meal_plan_json'])) 
        {
            $decoded_meal_plan = json_decode($_POST['meal_plan_json'], true);
            if (is_array($decoded_meal_plan)) 
            {
                $meal_plan = $decoded_meal_plan;
            }
        }

        if (empty($meal_plan)) 
        {
            $meal_plan = $_SESSION['meal_plan'] ?? [];
        }

        $uid = (int)($_SESSION['uid'] ?? 0);
        
        if ($uid > 0 && !empty($meal_plan))
        {
            $current_plan_json = json_encode($meal_plan);
            $is_changed = true;
            if (isset($_SESSION['meal_plan_hash']) && $_SESSION['meal_plan_hash'] === md5($current_plan_json)) 
            {
                $is_changed = false; 
            }

            $total_kcal = 0;
            $food_ids = [];
            
            foreach ($meal_plan as $meal => $days) 
            {
                foreach ($days as $day => $food_id) 
                {
                    if (!empty($food_id)) 
                    {
                        $food_ids[] = (int)$food_id;
                    }
                }
            }
        
            if (!empty($food_ids)) 
            {
                $unique_food_ids = array_unique($food_ids);
                $food_ids_str = implode(',', $unique_food_ids);
                $sql_food = "select id, kcalmin, kcalmax from food where id IN ({$food_ids_str})";
                $res_food = $conn->query($sql_food); 
                
                $food_kcal_map = [];
                if ($res_food) 
                {
                    while ($food_row = $res_food->fetch())
                    {
                        $avg_kcal = ($food_row['kcalmin'] + $food_row['kcalmax']) / 2;
                        $food_kcal_map[$food_row['id']] = $avg_kcal;
                    }
                }
                
                foreach ($food_ids as $f_id) 
                {
                    if (isset($food_kcal_map[$f_id])) 
                    {
                        $total_kcal += $food_kcal_map[$f_id];
                    }
                }
            }

            $today = date('Y-m-d');
            $saved_at = date('Y-m-d H:i:s');

            $session_tdee = $_SESSION['home_tdee'] ?? '';
            if (is_numeric($session_tdee) && $session_tdee > 0) 
            {
                $kcal_save = (int) round((float)$session_tdee);
            } 
            else 
            {
                $kcal_save = (int) $total_kcal;
            }

            $check_col = $conn->query("SHOW COLUMNS from mealplan LIKE 'saved_at'");
            if ($check_col && $check_col->rowCount() == 0) 
            {
                $conn->query("alter table mealplan add COLUMN saved_at DATETIME NULL");
            }
        
            $sql_insert = " insert into mealplan (uid, date, kcal, status, saved_at)  
                            VALUES ('{$uid}', '{$today}', '{$kcal_save}', 1, '{$saved_at}')";
            $mealplan_id = $conn->query_lastid($sql_insert);
            $conn->save_logs("Create New Meal Plan > ", $uid, $mealplan_id);
            $is_changed = true;

            if ($is_changed) 
            {
                $sql_insert_md = "insert into mealplan_detail (mealplan_id, status) 
                                  VALUES ('{$mealplan_id}', 1)";
                $mealplan_detail_id = $conn->query_lastid($sql_insert_md);

                $day_map = [
                    'Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3, 'Thursday' => 4,
                    'Friday' => 5, 'Saturday' => 6, 'Sunday' => 7
                ];
                
                $table_map = [
                    'Breakfast' => 'mp_br',
                    'Break' => 'mp_break',
                    'Lunch' => 'mp_lu',
                    'Dinner' => 'mp_din'
                ];

                foreach ($meal_plan as $meal => $days) 
                {
                    if (!isset($table_map[$meal])) continue; 
                    $table = $table_map[$meal];
                    foreach ($days as $day_name => $food_id) 
                    {
                        if (!empty($food_id)) 
                        {
                            $d_id = $day_map[$day_name] ?? 0;
                            if ($d_id > 0) 
                            {
                                $food_id = (int)$food_id;
                                $sql_insert_meal = "insert into {$table} (mealplan_detail_id, day_id, food_id, status) 
                                                    VALUES ('{$mealplan_detail_id}', '{$d_id}', '{$food_id}', 1)";
                                $conn->query($sql_insert_meal);
                            }
                        }
                    }
                }
                $_SESSION['meal_plan_hash'] = md5($current_plan_json);
            }
            
            $clear_sessions = [
                'home_age', 'home_gender', 'home_height', 'home_weight', 'home_type',
                'home_bmi', 'home_tdee', 'home_carbs',
                'saved_target_id', 'allergy_session_active',
                'mp_home_backup', 'meal_plan', 'meal_plan_hash'
            ];
            foreach ($clear_sessions as $key) 
            {
                if (isset($_SESSION[$key])) 
                {
                    unset($_SESSION[$key]);
                }
            }

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>";
            echo "Swal.fire({title:'บันทึกสำเร็จแล้ว!', icon:'success', confirmButtonText:'ปิด', confirmButtonColor:'#0d6efd', allowOutsideClick:false, allowEscapeKey:false, showCancelButton:false}).then((result)=>{ if(result.isConfirmed){ window.location.href='index.php?option=home&task=def'; } });";
            echo "</script>";
            exit();
        }

        header("location:index.php");
        exit(); 
    }

}
?>