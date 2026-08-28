<?php

class history
{
    function def() 
    {
        $conn = new connect();
        $acl = $conn->check_acl();
        ?>
        <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <style>
            .theme-container {
                font-family: 'Prompt', sans-serif;
                background-color: #f4f6f8; /* สีพื้นหลังอ้างอิงจากขอบนอกของรูป */
                padding: 30px 15px;
                min-height: 100vh;
            }
            .card-theme {
                background: #ffffff;
                border-radius: 16px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.04);
                padding: 35px;
                margin-bottom: 20px;
                max-width: 1000px;
                margin-left: auto;
                margin-right: auto;
            }
            .text-theme-green {
                color: #278a5b;
                font-weight: 600;
            }
            .btn-theme-green {
                background-color: #278a5b;
                color: white;
                border-radius: 8px;
                padding: 10px 25px;
                border: none;
                transition: 0.3s;
                cursor: pointer;
                font-weight: 500;
            }
            .btn-theme-green:hover {
                background-color: #1e7049;
                color: white;
            }
            .btn-outline-green {
                background-color: transparent;
                color: #278a5b;
                border: 1px solid #278a5b;
                border-radius: 8px;
                padding: 10px 25px;
                transition: 0.3s;
                font-weight: 500;
            }
            .btn-outline-green:hover {
                background-color: #278a5b;
                color: white;
            }
            
            /* สไตล์ตารางให้เข้ากับธีม */
            .table-custom-history {
                border-collapse: separate; 
                border-spacing: 0;
                width: 100%;
                margin-top: 20px;
            }
            .table-custom-history thead th {
                background-color: #eaf3ee;
                color: #278a5b;
                text-align: center;
                padding: 15px;
                font-weight: 500;
                border-bottom: 2px solid #d1e5da;
            }
            .table-custom-history td {
                padding: 15px;
                border-bottom: 1px solid #eef2f0;
                vertical-align: middle;
                text-align: center;
            }
            .table-custom-history tbody tr:hover {
                background-color: #f9fbfaf3;
            }
        </style>

        <div class='container-fluid theme-container'>
            <div class='row'>
                <div class='col-12'>
                    
                    <div class='card-theme'>
                        <div class="text-center mb-4">
                            <h2 class="text-theme-green">
                                <i class="fas fa-history" style="font-size: 1.2em; display: block; margin-bottom: 10px;"></i>
                                ประวัติการจัดตาราง
                            </h2>
                            <p class="text-muted" style="font-size: 0.9em;">ตรวจสอบประวัติการวางแผนโภชนาการของคุณ</p>
                        </div>
                        
                        <form action='index.php' method='get'>
                            <input type='hidden' name='option' value='history'>
                            <input type='hidden' name='task' value='def'>
                        </form>
                        
                        <div class="table-responsive">
                            <table class='table-custom-history'>
                                <?php
                                
                                    $ls = array('ว/ด/ป', 'เวลา', 'kcal เป้าหมาย TDEE');
                                    $conn->table_header($ls);
                                ?>
                                <tbody>
                                    <?php
                                    $uid = (int)($_SESSION['uid'] ?? 0);
                                    $sql = "SELECT `id`, `date`, `saved_at`, `kcal`
                                            FROM `mealplan`
                                            WHERE `uid` = '{$uid}' ";
                                    $res = $conn->query($sql);
                                    
                                    if($res) {
                                        while ($cdr = $res->fetch())
                                        {
                                            $mealplan_id = (int)$cdr['id'];
                                            $date_text = !empty($cdr['date']) ? $cdr['date'] : '-';
                                            $time_text = !empty($cdr['saved_at']) ? date('H:i:s', strtotime($cdr['saved_at'])) : '-';
                                            $kcal_text = isset($cdr['kcal']) ? number_format((float)$cdr['kcal'], 2) : '0.00';

                                            $ls[0] = array("c", $date_text);
                                            $ls[1] = array("c", $time_text);
                                            $ls[2] = array(
                                                "c",
                                                "<a href='index.php?option=history&task=det&id={$mealplan_id}' style='color: #278a5b; font-weight: 500; text-decoration: none;'>" . $kcal_text . " kcal</a>"
                                            );
                                            
                                            $conn->table_body($ls);
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                    
                </div>
            </div>
        </div>
        <?php
    }

    function det() 
    {
        $id = (int)($_REQUEST['id'] ?? 0);
        $conn = new connect();
        $name = ""; $date = ""; $kcal = "";

        $sql = "select `mealplan`.`id` as `id`,`mealplan`.`date` as `date`,`mealplan`.`saved_at` as `saved_at`,`mealplan`.`kcal` as `kcal`,
                `users`.`name` as `name`, `users`.`surname` as `lastname`, `mealplan`.`status` as `status` 
                from `mealplan`,`users`
                where `mealplan`.`uid` = `users`.`id` and `mealplan`.`id` = '".$id."'";
        $res = $conn->query($sql);

        $time = "-";
        if ($res) {
            while ($cdr = $res->fetch()) {
                $name = $cdr['name'] . " " . $cdr['lastname'];
                $date = $cdr['date'];
                $time = !empty($cdr['saved_at']) ? date('H:i:s', strtotime($cdr['saved_at'])) : '-';
                $kcal = (float)$cdr['kcal'];
            }
        }

        $days_list = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $day_id_map = array(
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
            'Sunday' => 7
        );
        $day_name_map = array_flip($day_id_map);

        $meal_plan = [];
        $table_map = array(
            'Breakfast' => array('mp_br'),
            'Lunch' => array('mp_lu'),
            'Dinner' => array('mp_din'),
            'Break' => array('mp_break')
        );

        if ($id > 0) {
            $sql_md = "SELECT id FROM mealplan_detail WHERE mealplan_id = '{$id}' LIMIT 1";
            $res_md = $conn->query($sql_md);
            $md_data = $res_md ? $res_md->fetch() : null;

            if ($md_data) {
                $mealplan_detail_id = (int)$md_data['id'];

                foreach ($table_map as $meal_key => $table_names) {
                    $meal_plan[$meal_key] = [];
                    foreach ($days_list as $day_name) {
                        $meal_plan[$meal_key][$day_name] = [];
                    }

                    foreach ($table_names as $table_name) {
                        $sql_meal = "SELECT day_id, food_id FROM {$table_name} WHERE mealplan_detail_id = '{$mealplan_detail_id}' AND status = '1'";
                        $res_meal = $conn->query($sql_meal);

                        if ($res_meal) {
                            while ($meal_row = $res_meal->fetch()) {
                                $day_name = $day_name_map[$meal_row['day_id']] ?? null;
                                if ($day_name !== null) {
                                    $meal_plan[$meal_key][$day_name][] = (int)$meal_row['food_id'];
                                }
                            }
                        }
                    }
                }
            }
        }

        $food_data_list = [];
        if (!empty($meal_plan)) {
            $all_food_ids = [];
            foreach ($meal_plan as $meal => $days) {
                foreach ($days as $day => $food_ids) {
                    if (!is_array($food_ids)) {
                        $food_ids = [$food_ids];
                    }

                    foreach ($food_ids as $food_id) {
                        if (!empty($food_id)) {
                            $all_food_ids[] = (int)$food_id;
                        }
                    }
                }
            }

            if (!empty($all_food_ids)) {
                $all_food_ids = array_unique($all_food_ids);
                $food_ids_str = implode(',', $all_food_ids);
                $sql_food = "SELECT id, name, kcalmin, kcalmax, promin, promax, carbmin, carbmax, sugarmin, sugarmax, fatmin, fatmax, somin, somax, pomin, pomax, phosmin, phosmax, image FROM food WHERE id IN ({$food_ids_str})";
                $res_food = $conn->query($sql_food);

                if ($res_food) {
                    while ($food_row = $res_food->fetch()) {
                        $food_data_list[$food_row['id']] = $food_row;
                    }
                }
            }
        }
        ?>
        
        <!-- นำเข้า Font 'Prompt' และ FontAwesome -->
        <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <style>
            .theme-container {
                font-family: 'Prompt', sans-serif;
                background-color: #f4f6f8;
                padding: 30px 15px;
                min-height: 100vh;
            }
            .card-theme {
                background: #ffffff;
                border-radius: 16px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.04);
                padding: 35px;
                margin-bottom: 25px;
                max-width: 1200px;
                margin-left: auto;
                margin-right: auto;
            }
            .text-theme-green {
                color: #278a5b;
                font-weight: 600;
            }
            .btn-theme-green {
                background-color: #278a5b;
                color: white;
                border-radius: 8px;
                padding: 12px 30px;
                border: none;
                transition: 0.3s;
                cursor: pointer;
                font-weight: 500;
                font-size: 1.05em;
            }
            .btn-theme-green:hover {
                background-color: #1e7049;
                color: white;
            }
            .btn-outline-green {
                background-color: transparent;
                color: #278a5b;
                border: 1px solid #278a5b;
                border-radius: 8px;
                padding: 12px 30px;
                transition: 0.3s;
                font-weight: 500;
                font-size: 1.05em;
                text-decoration: none;
            }
            .btn-outline-green:hover {
                background-color: #278a5b;
                color: white;
            }
            
            /* แถบเลื่อนและหัวตาราง */
            .table-scroll-container {
                max-height: 650px;
                overflow-y: auto;
                border: 1px solid #eef2f0;
                border-radius: 12px;
            }
            .table-custom {
                border-collapse: separate; 
                border-spacing: 0;
                width: 100%;
                table-layout: fixed;
            }
            .table-custom thead th {
                background-color: #eaf3ee;
                color: #278a5b;
                text-align: center;
                padding: 15px;
                font-weight: 500;
                position: sticky; 
                top: 0;
                z-index: 10; 
                border-bottom: 2px solid #d1e5da;
                border-right: 1px solid #d1e5da;
                box-shadow: 0 2px 4px rgba(0,0,0,0.02); 
            }
            .table-custom thead th:last-child {
                border-right: none;
            }
            .table-custom td {
                border-bottom: 1px solid #eef2f0;
                border-right: 1px solid #eef2f0;
                padding: 15px;
                vertical-align: middle;
            }
            .table-custom td:last-child {
                border-right: none;
            }
            .table-custom tbody tr:last-child td {
                border-bottom: none;
            }

            .food-item-box {
                display: flex;
                align-items: flex-start;
                margin-bottom: 14px;
                padding-bottom: 14px;
                border-bottom: 1px dashed #e0e6e3;
            }
            .food-item-box:last-child {
                border-bottom: none;
                margin-bottom: 0;
                padding-bottom: 0;
            }
            .food-thumb {
                width: 90px;
                height: 90px;
                border-radius: 14px;
                margin-right: 18px;
                flex-shrink: 0;
                object-fit: cover;
                box-shadow: 0 3px 10px rgba(39,138,91,0.18);
                border: 2px solid #ffffff;
                outline: 1px solid #eaf3ee;
            }
            .food-placeholder-img {
                width: 90px;
                height: 90px;
                background-color: #eaf3ee;
                border-radius: 14px;
                margin-right: 18px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.8em;
                color: #278a5b;
                flex-shrink: 0;
                box-shadow: 0 3px 10px rgba(39,138,91,0.10);
            }
            .nutrient-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 5px 10px;
                font-size: 0.8em;
                color: #666;
                margin-top: 8px;
                background-color: #f9fbfaf3;
                padding: 10px;
                border-radius: 8px;
                border: 1px solid #eef2f0;
            }
            .nutrient-grid strong {
                color: #444;
            }
            
            /* ส่วนแสดงผลรวมคอลัมน์ขวาสุด */
            .total-nutrient-box {
                background-color: #f9fbfaf3;
                padding: 15px;
                border-radius: 12px;
                border: 1px solid #eaf3ee;
                text-align: left;
                font-size: 0.85em;
                color: #555;
            }
            .total-nutrient-box .title {
                color: #278a5b;
                font-weight: 600;
                font-size: 1.1em;
                text-align: center;
                border-bottom: 2px solid #eaf3ee;
                padding-bottom: 10px;
                margin-bottom: 12px;
            }
            .total-nutrient-row {
                display: flex;
                justify-content: space-between;
                margin-bottom: 6px;
            }
            .total-nutrient-row strong {
                color: #333;
            }
            
            /* ปรับแต่งฟอร์ม input เล็กน้อย */
            .table-borderless td {
                vertical-align: middle;
            }
        </style>

        <div class='container-fluid theme-container'>
            <div class='row'>
                <div class='col-12'>
                    
                    <!-- ส่วนหัวข้อมูลผู้ใช้ -->
                    <div class="card-theme">
                        <div class="text-center mb-4">
                            <h2 class="text-theme-green">
                                <i class="fas fa-heartbeat" style="font-size: 1.2em; display: block; margin-bottom: 10px;"></i>
                                ข้อมูลโภชนาการ (Mealplan)
                            </h2>
                            <p class="text-muted" style="font-size: 0.9em;">รายละเอียดตารางอาหารที่จัดเตรียมไว้สำหรับคุณ</p>
                        </div>
                        
                        <div class="d-flex justify-content-center">
                            <table class='table table-borderless' style="max-width: 600px; width: 100%;">
                                <tbody>
                                <?php
                                    $conn->form_input("ชื่อ (Name)","name",$name,2);
                                    $conn->form_input("วันที่ (Date)","date",$date,2);
                                    $conn->form_input("เวลา (Time)","time",$time,2);
                                    $conn->form_input("เป้าหมายพลังงาน (Kcal)","kcal",$kcal,2);
                                ?>
                                </tbody>
                            </table>
                        </div>
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
                                    
                                    // เพิ่มตัวแปรสำหรับเก็บผลรวมทุกชนิด
                                    foreach($days_list as $d) {
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

                                    if (!empty($meal_plan)) {
                                        foreach ($meal_plan as $meal => $days) {
                                            foreach ($days as $day => $food_ids) {
                                                if (!is_array($food_ids)) {
                                                    $food_ids = [$food_ids];
                                                }

                                                foreach ($food_ids as $food_id) {
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

                                                        // บวกรวมค่าสารอาหารทุกประเภท
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
                                    }

                                    foreach ($days_list as $day) {
                                        $is_first_meal = true;
                                        
                                        foreach ($meals_list as $meal_key => $meal_label) {
                                            echo "<tr>";
                                            
                                            if ($is_first_meal) {
                                                echo "<td rowspan='{$rowspan_count}' class='text-center fw-bold bg-white' style='color: #278a5b;'>{$day}</td>"; 
                                            }
                                            
                                            echo "<td class='text-center bg-white'>{$meal_label}</td>";
                                            
                                            echo "<td class='bg-white'>";
                                            if (!empty($schedule[$day][$meal_key])) {
                                                foreach ($schedule[$day][$meal_key] as $food) {
                                                    $img_path = trim($food['image'] ?? '');
                                                    if (!empty($img_path)) {
                                                        $img_html = "<img src='" . htmlspecialchars($img_path) . "' alt='" . htmlspecialchars($food['name']) . "' class='food-thumb' loading='lazy' onerror=\"this.onerror=null;this.outerHTML='<div class=\\'food-placeholder-img\\'><i class=\\'fas fa-utensils\\'></i></div>';\">";
                                                    } else {
                                                        $img_html = "<div class='food-placeholder-img'><i class='fas fa-utensils'></i></div>";
                                                    }

                                                    echo "<div class='food-item-box'>
                                                            {$img_html}
                                                            <div style='flex: 1;'>
                                                                <strong style='font-size: 1.05em; color: #278a5b;'>" . htmlspecialchars($food['name']) . "</strong>
                                                                
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
                                            } else {
                                                echo "<div class='text-muted text-center py-2' style='background: #f9fbfaf3; border-radius: 8px;'>- ว่าง -</div>";
                                            }
                                            echo "</td>";
                                            
                                            // แสดงผลรวมสารอาหารทั้งหมดในคอลัมน์สุดท้าย
                                            if ($is_first_meal) {
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
                        
                        <div class="d-flex justify-content-between mt-5">
                            <a href='index.php?option=history&task=def' class='btn btn-outline-green'>
                                <i class='fas fa-arrow-left'></i> ย้อนกลับ (Back)
                            </a>
                            
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
?>