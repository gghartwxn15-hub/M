<?php

class mp
{

    function def() 
    {
        $conn = new connect();
        $acl = $conn->check_acl();
        $option = $_REQUEST['option'];
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
        
        $session_bmi = isset($_SESSION['home_bmi']) ? $_SESSION['home_bmi'] : '';
        $session_tdee = isset($_SESSION['home_tdee']) ? $_SESSION['home_tdee'] : '';
        $session_carbs = isset($_SESSION['home_carbs']) ? $_SESSION['home_carbs'] : '';

        $tdee_target = is_numeric($session_tdee) ? floatval($session_tdee) : 0;
        $carbs_target = is_numeric($session_carbs) ? floatval($session_carbs) : 0;

        $allergy_ids = [];
        $has_disease = false;
        $uid = (int)($_SESSION['uid'] ?? 0);
        
        if ($uid > 0) {
            try {
                $sql_allergy = "select * from food_allergy where uid = '{$uid}'";
                $res_allergy = $conn->query($sql_allergy);
                
                if ($res_allergy) 
                {
                    while ($allergy_data = $res_allergy->fetch()) 
                    {
                        if (isset($allergy_data['disease'])) {
                            $has_disease = (int)$allergy_data['disease'];
                        }
                        
                        $ftype_id = 0;
                        if (isset($allergy_data['ftype_id'])) {
                            $ftype_id = (int)$allergy_data['ftype_id'];
                        } elseif (isset($allergy_data['ftype'])) {
                            $ftype_id = (int)$allergy_data['ftype'];
                        }
                        
                        if ($ftype_id > 0) {
                            $sql_food_ids = "select id from food where ftype_id = '{$ftype_id}' AND status = 1";
                            $res_food_ids = $conn->query($sql_food_ids);
                            
                            if ($res_food_ids) 
                            {
                                while ($food_row = $res_food_ids->fetch()) 
                                {
                                    $allergy_ids[] = (int)$food_row['id'];
                                }
                            }
                        }
                    }
                }
            } 
            catch (Exception $e) 
            {
                $allergy_ids = [];
                $has_disease = false;
            }
        }
        
        $food_list = [];
        $sql = "select * from food where status = 1";
        $res = $conn->query($sql);
        
        while ($row = $res->fetch()) {
            if (in_array($row['id'], $allergy_ids)) {
                continue;
            }
            
            $avg_kcal = ($row['kcalmin'] + $row['kcalmax']) / 2;
            $avg_carb = ($row['carbmin'] + $row['carbmax']) / 2;
            
            if (!empty($row['image'])) {
                $img_url = $row['image'];
            } else {
                $img_url = "https://placehold.co/100x100/e9ecef/a3a3a3?text=Food+" . $row['id'];
            }
            
            $food_list[$row['id']] = [
                'name' => $row['name'],
                'kcal' => $avg_kcal,
                'kcalmin' => $row['kcalmin'],
                'kcalmax' => $row['kcalmax'],
                'carb' => $avg_carb,
                'image' => $img_url,
                'label' => $row['name']
            ];
        }

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $meals = ['Breakfast', 'Lunch', 'Dinner', 'Break'];

        if (isset($_POST['meal_plan'])) {
            $current_plan = $_POST['meal_plan'];
            $_SESSION['meal_plan'] = $current_plan; // บันทึกลง Session
        } else {
            $current_plan = $_SESSION['meal_plan'] ?? []; // ดึงจาก Session ถ้าย้อนกลับมา
        }
        
        $auto_gen_all = isset($_POST['btn_auto_gen_all']); 
        $auto_gen_day = $_POST['btn_auto_gen_day'] ?? null; 

        if ($auto_gen_all || $auto_gen_day) {
            $food_ids = array_keys($food_list);
            $main_meal_ids = [];
            $break_meal_ids = [];
            
            foreach ($food_ids as $fid) {
                if (($fid >= 1 && $fid <= 30) || ($fid >= 99 && $fid <= 150)) {
                    $main_meal_ids[] = $fid;
                }
                if ($fid >= 81 && $fid <= 87) {
                    $break_meal_ids[] = $fid;
                }
            }
        
            if (empty($main_meal_ids)) 
            {
                $main_meal_ids = $food_ids;
            }
            if (empty($break_meal_ids)) {
                $break_meal_ids = $food_ids;
            }
            
            foreach ($days as $day) 
            {
                if ($auto_gen_day && $day !== $auto_gen_day) {
                    continue;
                }
                
                $best_diff = INF;
                $best_combo = [];
                
                // fallback เผื่อหาคอมโบที่เข้าเงื่อนไข kcal ไม่เจอเลยใน 150 รอบ
                $fallback_diff = INF;
                $fallback_combo = [];
                
                for ($i = 0; $i < 150; $i++) {
                    if (count($main_meal_ids) >= 3) {
                        $picked_keys = array_rand($main_meal_ids, 3);
                        shuffle($picked_keys);
                        $main_breakfast = $main_meal_ids[$picked_keys[0]];
                        $main_lunch     = $main_meal_ids[$picked_keys[1]];
                        $main_dinner    = $main_meal_ids[$picked_keys[2]];
                    } else 
                    {
                        $main_breakfast = $main_meal_ids[array_rand($main_meal_ids)];
                        $main_lunch     = $main_meal_ids[array_rand($main_meal_ids)];
                        $main_dinner    = $main_meal_ids[array_rand($main_meal_ids)];
                    }
                    
                    $combo = [
                        'Breakfast' => $main_breakfast,
                        'Lunch'     => $main_lunch,
                        'Dinner'    => $main_dinner,
                        'Break'     => $break_meal_ids[array_rand($break_meal_ids)]
                    ];
                    
                    $sum_kcal = $food_list[$combo['Breakfast']]['kcal'] + 
                                $food_list[$combo['Lunch']]['kcal'] + 
                                $food_list[$combo['Dinner']]['kcal'] + 
                                $food_list[$combo['Break']]['kcal'];
                                
                    $sum_carb = $food_list[$combo['Breakfast']]['carb'] + 
                                $food_list[$combo['Lunch']]['carb'] + 
                                $food_list[$combo['Dinner']]['carb'] + 
                                $food_list[$combo['Break']]['carb'];
                    
                    $diff_kcal = abs($tdee_target - $sum_kcal);
                    $diff_carb = abs($carbs_target - $sum_carb) * 4; 
                    
                    $total_diff = $diff_kcal + $diff_carb;
                    
                    // เก็บ fallback ไว้เผื่อไม่มีคอมโบไหนเข้าเงื่อนไขเลยใน 150 รอบ
                    if ($total_diff < $fallback_diff) 
                    {
                        $fallback_diff = $total_diff;
                        $fallback_combo = $combo;
                    }
                    
                    // เงื่อนไขใหม่: sum_kcal ต้อง <= tdee_target และห่างจาก tdee_target ได้ไม่เกิน 250
                    $within_kcal_window = ($tdee_target <= 0) 
                        || ($sum_kcal <= $tdee_target && $sum_kcal >= ($tdee_target - 250));
                    
                    if ($within_kcal_window && $total_diff < $best_diff) 
                    {
                        $best_diff = $total_diff;
                        $best_combo = $combo;
                    }
                }
                
                // ถ้าสุ่มครบ 150 รอบแล้วไม่มีคอมโบไหนเข้าเงื่อนไข kcal เลย ให้ใช้ตัวที่ใกล้เคียงที่สุดแทน
                if (empty($best_combo)) {
                    $best_combo = $fallback_combo;
                }
                
                $current_plan['Breakfast'][$day] = $best_combo['Breakfast'];
                $current_plan['Lunch'][$day]     = $best_combo['Lunch'];
                $current_plan['Dinner'][$day]    = $best_combo['Dinner'];
                $current_plan['Break'][$day]     = $best_combo['Break'];
            }
        
            $_SESSION['meal_plan'] = $current_plan;
        }
        ?>

        <style>
            .nutrition-theme 
            { 
                font-family: 'Prompt', 'Kanit', sans-serif; 
                background-color: #f8faf9; 
                padding: 40px 15px; 
            }
            .nutrition-card 
            { 
                background: #ffffff; 
                border-radius: 16px; 
                padding: 30px; 
                box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
                max-width: 1400px; 
                margin: 0 auto; 
                overflow: hidden; 
            }
            .theme-text-green 
            { 
                color: #278654; 
                font-weight: bold; 
            }
            
            .stat-container 
            { 
                display: flex; 
                flex-wrap: wrap; 
                gap: 15px; 
                margin-bottom: 30px; 
            }
            .stat-box 
            { 
                flex: 1; min-width: 200px; 
                border: 1px solid #d3e2d7; 
                border-radius: 8px; 
                padding: 15px; 
                text-align: center; 
                background-color: #fcfcfc; 
            }
            .stat-title { font-size: 0.9em; color: #666; margin-bottom: 5px; }
            .stat-value { font-size: 1.2em; color: #278654; font-weight: bold; }
            
            .table-responsive-container 
            {
                max-height: 650px; 
                overflow-y: auto;
                overflow-x: auto;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                margin-top: 10px;
            }

            .meal-plan-table { width: 100%; border-collapse: collapse; text-align: center; }
            .meal-plan-table th, .meal-plan-table td { border: 1px solid #e0e0e0; padding: 15px 10px; vertical-align: middle; }
            .meal-plan-table td { height: 1px; } 
            
            /* --- อัปเดต: หัวตารางติดหนึบ (Sticky Header) --- */
            .meal-plan-table th { 
                background-color: #ffffff; 
                font-weight: bold; 
                color: #333; 
                min-width: 160px; 
                vertical-align: middle; 
                position: sticky; 
                top: 0; 
                z-index: 10; 
                box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            }
            
            .meal-name { font-weight: bold; background-color: #fcfcfc; vertical-align: middle !important; font-size: 1.1em;}
            
            .cell-container {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                height: 100%;
                align-items: stretch;
            }

            .food-preview {
                display: flex; 
                flex-direction: column; 
                align-items: center; 
                justify-content: center;
                background: #f8fbf9; 
                border: 1px dashed #d3e2d7; 
                border-radius: 8px;
                padding: 12px 10px; 
                margin-bottom: 10px; 
                flex-grow: 1; 
                min-height: 160px;
            }
            .food-preview img 
            { 
                width: 60px; 
                height: 60px; 
                object-fit: cover; 
                border-radius: 50%; 
                margin-bottom: 10px; 
                box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
            
            .food-preview .f-name 
            { 
                font-weight: bold; 
                font-size: 0.85em; 
                color: #333; 
                line-height: 1.4; 
                margin-bottom: 6px;
                height: 42px;
                overflow: hidden;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
            }
            .food-preview .f-kcal { font-size: 0.85em; color: #e67e22; font-weight: bold; margin-top: auto; }
            .food-preview .empty-text { font-size: 0.85em; color: #aaa; }

            .btn-change-menu {
                width: 100%;
                padding: 8px 6px;
                border: 1px solid #278654;
                border-radius: 6px;
                background-color: #fff;
                color: #278654;
                font-size: 0.8em;
                font-weight: bold;
                outline: none;
                cursor: pointer;
                margin-top: auto;
                font-family: inherit;
                transition: all 0.2s;
            }
            .btn-change-menu:hover {
                background-color: #278654;
                color: #fff;
            }

            /* --- Popup เลือกเมนู --- */
            .food-modal-overlay {
                display: none;
                position: fixed;
                top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(0,0,0,0.45);
                z-index: 1000;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }
            .food-modal-overlay.active { display: flex; }
            .food-modal-box {
                background: #fff;
                border-radius: 14px;
                width: 100%;
                max-width: 480px;
                max-height: 80vh;
                display: flex;
                flex-direction: column;
                overflow: hidden;
                box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            }
            .food-modal-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                padding: 18px 20px;
                border-bottom: 1px solid #eee;
            }
            .food-modal-header h4 { margin: 0; color: #278654; }
            .food-modal-subtitle { font-size: 0.85em; color: #888; margin-top: 4px; }
            .food-modal-close {
                background: none; border: none; font-size: 1.5em; line-height: 1;
                cursor: pointer; color: #999;
            }
            .food-modal-close:hover { color: #333; }
            .food-modal-list { overflow-y: auto; padding: 10px; }
            .food-modal-item {
                display: flex; align-items: center; gap: 12px;
                padding: 10px; border-radius: 10px; cursor: pointer;
                transition: background 0.15s;
            }
            .food-modal-item:hover { background: #f2f8f4; }
            .food-modal-item img { width: 48px; height: 48px; object-fit: cover; border-radius: 50%; flex-shrink: 0; }
            .food-modal-item .fmi-name { font-weight: bold; font-size: 0.95em; color: #333; }
            .food-modal-item .fmi-kcal { font-size: 0.85em; color: #e67e22; font-weight: bold; }
            .food-modal-empty { padding: 10px; text-align: center; font-size: 0.9em; color: #999; }
            .food-modal-empty-option {
                display: flex; align-items: center; gap: 12px; padding: 10px;
                border-radius: 10px; cursor: pointer; color: #999;
                border: 1px dashed #ddd; margin-bottom: 6px;
            }
            .food-modal-empty-option:hover { background: #f8f8f8; }
            .meal-select:focus { border-color: #278654; box-shadow: 0 0 5px rgba(39, 134, 84, 0.2); }

            .btn-group-custom { display: flex; justify-content: space-between; align-items: center; gap: 15px; margin-top: 30px; }
            .btn-back { display: inline-flex; align-items: center; gap: 8px; background: transparent; border: 1.5px solid #d1d5db; border-radius: 8px; color: #6b7280; cursor: pointer; padding: 10px 24px; font-family: inherit; font-size: 1em; transition: all 0.2s; }
            .btn-save { background: #278654; border: none; border-radius: 8px; color: #fff; cursor: pointer; padding: 10px 24px; font-weight: 500; font-family: inherit; font-size: 1em; transition: all 0.2s; margin-left: auto; display: inline-flex; align-items: center; gap: 8px; }
            .btn-auto { background: #f39c12; border: none; border-radius: 6px; color: #fff; cursor: pointer; padding: 10px 25px; font-weight: bold; font-family: inherit; transition: all 0.2s; }
            .btn-auto-day { background: #f39c12; border: none; border-radius: 4px; color: #fff; cursor: pointer; padding: 4px 8px; font-size: 0.8em; margin-top: 8px; display: inline-block; width: 90%; font-family: inherit; transition: all 0.2s; }
            
            .btn-back:hover { background: #f3f4f6; color: #374151; border-color: #9ca3af; }
            .btn-save:hover { background: #1f6b43; }
            .btn-auto:hover, .btn-auto-day:hover { background: #d68910; }
            
            .heart-icon-wrapper { text-align: center; margin-bottom: 15px; }
            .heart-icon-wrapper svg { fill: #278654; width: 45px; height: 45px; }
        </style>

        <div class='nutrition-theme'>
            <div class='nutrition-card'>
                
                <div class="heart-icon-wrapper">
                    <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35zM10.5 13H8l2.5-4h2l-1.5 3h2.5l-3 5v-4z"/></svg>
                </div>
                <h3 class="theme-text-green mb-4" style="text-align: center;">จัดการโภชนาการของคุณ</h3>

                <div class='stat-container'>
                    <div class='stat-box'>
                        <div class='stat-title'>Username</div>
                        <div class='stat-value'><?php echo htmlspecialchars($login_user); ?></div>
                    </div>
                    <div class='stat-box'>
                        <div class='stat-title'>BMI</div>
                        <div class='stat-value'><?php echo htmlspecialchars($session_bmi); ?></div>
                    </div>
                    <div class='stat-box'>
                        <div class='stat-title'>เป้าหมาย TDEE (ต่อวัน)</div>
                        <div class='stat-value'><?php echo number_format($tdee_target); ?> Kcal</div>
                    </div>
                    <div class='stat-box'>
                        <div class='stat-title'>เป้าหมาย คาร์โบไฮเดรต (ต่อวัน)</div>
                        <div class='stat-value'><?php echo number_format($carbs_target); ?> g</div>
                    </div>
                </div>

                <form action="index.php?option=mp&task=def" method="POST" id="mealForm">
                    
                    <div style="text-align: right; margin-bottom: 15px;">
                        <button type="submit" name="btn_auto_gen_all" value="1" class="btn-auto">🔄 สุ่มอาหารทั้งหมด (All)</button>
                    </div>

                    <!-- อัปเดต: ใช้คลาส table-responsive-container ครอบตาราง -->
                    <div class="table-responsive-container">
                        <table class='meal-plan-table'>
                            <thead>
                                <tr>
                                    <th>Meal</th>
                                    <?php foreach($days as $day): ?>
                                        <th>
                                            <?php echo $day; ?><br>
                                            <button type="submit" name="btn_auto_gen_day" value="<?php echo $day; ?>" class="btn-auto-day">🔄 สุ่มวันนี้</button>
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($meals as $meal): ?>
                                    <tr>
                                        <td class="meal-name">
                                            <?php echo $meal; ?>
                                        </td>
                                        
                                        <?php foreach($days as $day): ?>
                                        <td>
                                            <div class="cell-container">
                                                <div class="food-preview" id="preview_<?php echo $meal; ?>_<?php echo $day; ?>">
                                                    <?php 
                                                    $selected_id = $current_plan[$meal][$day] ?? '';
                                                    if ($selected_id && isset($food_list[$selected_id])) 
                                                    {
                                                        $f_data = $food_list[$selected_id];
                                                        echo '<img src="'.htmlspecialchars($f_data['image']).'" alt="food">';
                                                        echo '<div class="f-name">'.htmlspecialchars($f_data['name']).'</div>';
                                                        echo '<div class="f-kcal">'.round($f_data['kcalmin']).' - '.round($f_data['kcalmax']).' Kcal</div>';
                                                    } 
                                                    else 
                                                    {
                                                        echo '<span class="empty-text">-- ว่าง --</span>';
                                                    }
                                                    ?>
                                                </div>

                                                <input type="hidden" 
                                                    name="meal_plan[<?php echo $meal; ?>][<?php echo $day; ?>]" 
                                                    id="input_<?php echo $meal; ?>_<?php echo $day; ?>"
                                                    value="<?php echo htmlspecialchars($selected_id); ?>">

                                                <button type="button" class="btn-change-menu"
                                                        onclick="openFoodModal('<?php echo $meal; ?>', '<?php echo $day; ?>')">
                                                    🍽️ เปลี่ยนเมนู
                                                </button>
                                            </div>
                                        </td>
                                    <?php endforeach; ?>

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="btn-group-custom">
                        <button type="submit" class="btn-back" formnovalidate formaction="index.php?option=allergy&task=def"><i class="fa-solid fa-arrow-left"></i> ย้อนกลับ (Back)</button>
                        <button type="submit" class="btn-save" formaction="index.php?option=mp_detail&task=def">
                            ถัดไป (Next) <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </form>

                <div id="foodModalOverlay" class="food-modal-overlay" onclick="closeFoodModalOutside(event)">
                    <div class="food-modal-box">
                        <div class="food-modal-header">
                            <div>
                                <h4 id="foodModalTitle">เลือกเมนูใหม่</h4>
                                <div id="foodModalSubtitle" class="food-modal-subtitle"></div>
                            </div>
                            <button type="button" class="food-modal-close" onclick="closeFoodModal()">&times;</button>
                        </div>
                        <div id="foodModalList" class="food-modal-list"></div>
                    </div>
                </div>

            </div>
        </div>

            </div>
        </div>

        <script>
            const foodData = <?php echo json_encode($food_list, JSON_UNESCAPED_UNICODE); ?>;
            const KCAL_TOLERANCE = 20;

            let currentModalMeal = null;
            let currentModalDay = null;

            function updatePreview(meal, day, selectedId) 
            {
                const previewDiv = document.getElementById('preview_' + meal + '_' + day);
                if (selectedId && foodData[selectedId]) 
                {
                    const data = foodData[selectedId];
                    previewDiv.innerHTML = `
                        <img src="${data.image}" alt="food">
                        <div class="f-name">${data.name}</div>
                        <div class="f-kcal">${Math.round(data.kcalmin)} - ${Math.round(data.kcalmax)} Kcal</div>
                    `;
                } 
                else 
                {
                    previewDiv.innerHTML = '<span class="empty-text">-- ว่าง --</span>';
                }
            }

            function openFoodModal(meal, day) 
            {
                currentModalMeal = meal;
                currentModalDay = day;

                const input = document.getElementById('input_' + meal + '_' + day);
                const currentId = input.value;

                const listEl = document.getElementById('foodModalList');
                const titleEl = document.getElementById('foodModalTitle');
                const subtitleEl = document.getElementById('foodModalSubtitle');

                titleEl.textContent = 'เปลี่ยนเมนู: ' + meal + ' (' + day + ')';
                listEl.innerHTML = '';

                // ตัวเลือก "ว่าง"
                const emptyOpt = document.createElement('div');
                emptyOpt.className = 'food-modal-empty-option';
                emptyOpt.innerHTML = '<span>-- ว่าง (ไม่เลือกเมนู) --</span>';
                emptyOpt.onclick = () => selectFoodInModal('');
                listEl.appendChild(emptyOpt);

                let refKcal = null;
                if (currentId && foodData[currentId]) 
                {
                    refKcal = foodData[currentId].kcal;
                    subtitleEl.textContent = 'ปัจจุบัน ' + Math.round(refKcal) + ' Kcal — แสดงเมนูที่ห่างไม่เกิน ±' + KCAL_TOLERANCE + ' Kcal';
                } 
                else 
                {
                    subtitleEl.textContent = 'ยังไม่ได้เลือกเมนู — แสดงเมนูทั้งหมด';
                }

                const matched = Object.entries(foodData).filter(([id, data]) => {
                    return refKcal === null || Math.abs(data.kcal - refKcal) <= KCAL_TOLERANCE;
                });

                if (matched.length === 0) 
                {
                    const emptyMsg = document.createElement('div');
                    emptyMsg.className = 'food-modal-empty';
                    emptyMsg.textContent = 'ไม่มีเมนูที่ Kcal ใกล้เคียง (±' + KCAL_TOLERANCE + ')';
                    listEl.appendChild(emptyMsg);
                } 
                else 
                {
                    matched.sort((a, b) => a[1].kcal - b[1].kcal);
                    matched.forEach(([id, data]) => {
                        const item = document.createElement('div');
                        item.className = 'food-modal-item';
                        if (String(id) === String(currentId)) 
                        {
                            item.style.background = '#eaf5ee';
                        }
                        item.innerHTML = `
                            <img src="${data.image}" alt="food">
                            <div>
                                <div class="fmi-name">${data.name}</div>
                                <div class="fmi-kcal">${Math.round(data.kcalmin)} - ${Math.round(data.kcalmax)} Kcal</div>
                            </div>
                        `;
                        item.onclick = () => selectFoodInModal(id);
                        listEl.appendChild(item);
                    });
                }

                document.getElementById('foodModalOverlay').classList.add('active');
            }

            function selectFoodInModal(id) 
            {
                if (currentModalMeal === null || currentModalDay === null) return;
                const input = document.getElementById('input_' + currentModalMeal + '_' + currentModalDay);
                input.value = id;
                updatePreview(currentModalMeal, currentModalDay, id);
                closeFoodModal();
            }

            function closeFoodModal() 
            {
                document.getElementById('foodModalOverlay').classList.remove('active');
                currentModalMeal = null;
                currentModalDay = null;
            }

            function closeFoodModalOutside(event) 
            {
                if (event.target.id === 'foodModalOverlay') 
                {
                    closeFoodModal();
                }
            }
        </script>
        <?php
    }

    function det()
    {
        require_once('class.mp_detail.php');
        $mp_detail = new mp_detail();
        $mp_detail->def();
    }


}
?>