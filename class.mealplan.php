<?php

class mealplan
{
    function def() 
    {
        $conn = new connect();
        $acl = $conn->check_acl();
        $option = $_REQUEST['option'];
        ?>
        <div class='container'>
            <div class='row'>
                <div class='col-12'><br>
                <h2><?php echo $conn->get_app_info($option, 3);?></h2>
                
                <table id='datatable' class='table table-bordered table-striped'>
                    <?php
                        $ls = array('Id','Users','Date','Energy','Status','Action');
                        $conn->table_header($ls);
                    ?>
                    <tbody>
                        <?php
                        $sql =  'select `mealplan`.`id` as `id`,`mealplan`.`date` as `date`,`mealplan`.`kcal` as `kcal`,
                        `users`.`name` as `name`,`mealplan`.`status` as `status` from `mealplan`,`users`
                        where `mealplan`.`uid` = `users`.`id` ';

                        $conn = new connect();
                        $res = $conn->query($sql);
                        $a = 0;
                        while ($cdr = $res->fetch())
                        {
                            $a++;
                            $ls[0] = array("c", $a);
                            $ls[1] = array("c", $cdr['name']);
                            $ls[2] = array("c", $cdr['date']);
                            $ls[3] = array("c", $cdr['kcal']);
                            if ($cdr['status'] == 1)
                            {
                                $ls[4] = array("c", "Active");
                                $ds = "In-Active";
                                $dss = "0";
                            }
                            else
                            {
                                $ls[4] = array("c", "In-Active");
                                $ds = "Active";
                                $dss = "1";
                            }
                            $action = "";
                            if (($acl == '2') or ($acl > '5'))
                            {
                                $action = $action."<input class='btn' type='button' value='".$ds."' onclick='window.open(\"index.php?option=mealplan&task=del&id=".$cdr['id']."&stat=".$dss."\", \"_self\")' />";
                            }
                            $action = $action."<input class='btn' type='button' value='Detail' onclick='window.open(\"index.php?option=mealplan&task=det&id=".$cdr['id']."\",\"_self\")' />";
                            $ls[5] = array("c",$action);
                            $conn->table_body($ls);
                        }
                        ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
        <?php
    }

    function del() 
    {
        $id = $_REQUEST['id'];
        $stat = $_REQUEST['stat']; 
        $code = ""; 
        $conn = new connect();
        $sql = "update `mealplan` set `status` = '".$stat."' where `id` = '".$id."'";
        $conn->query($sql);
        if ($stat == 1)
        {
            $conn->save_logs("Active mealplan> ".$code, $_SESSIon['uid'],$id);
        }
        elseif ($stat == 0)
        {
            $conn->save_logs("In-Active mealplan> ".$code, $_SESSIon['uid'],$id);
        }
        header('location:index.php?option=mealplan&task=def');
    }

   function det() 
    {
        $id = $_REQUEST['id'] ?? '';
        $sql = "select `mealplan`.`id` as `id`,`mealplan`.`date` as `date`,`mealplan`.`kcal` as `kcal`,
                `users`.`name` as `name`, `users`.`surname` as `lastname`, `mealplan`.`status` as `status` 
                from `mealplan`,`users`
                where `mealplan`.`uid` = `users`.`id` and `mealplan`.`id` = '".$id."'";
        $conn = new connect();
        $res = $conn->query($sql);
        $name = ""; $date = ""; $kcal = "";
        
        if($res) 
        {
            while ($cdr = $res->fetch()) 
            {
                $name = $cdr['name'] . " " . $cdr['lastname']; 
                $date = $cdr['date'];
                
                $session_tdee = $_SESSION['home_tdee'] ?? '';
                if (is_numeric($session_tdee) && $session_tdee > 0) 
                {
                    $kcal = (float)$session_tdee;
                } else {
                    $kcal = (float)$cdr['kcal'];
                }
            }
        }
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['mp_home_backup'])) {
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

        $meal_plan = $_SESSION['meal_plan'] ?? [];
        $days_list = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $meals_list = array('Breakfast', 'Lunch', 'Dinner', 'Break');

        foreach ($meals_list as $meal_key) {
            if (!isset($meal_plan[$meal_key]) || !is_array($meal_plan[$meal_key])) {
                $meal_plan[$meal_key] = [];
            }

            foreach ($days_list as $day_name) {
                if (!isset($meal_plan[$meal_key][$day_name])) {
                    $meal_plan[$meal_key][$day_name] = [];
                }
            }
        }
        
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
        
        $meals_list = array(
            'Breakfast' => 'Breakfast', 
            'Lunch' => 'Lunch', 
            'Dinner' => 'Dinner',
            'Break' => 'Break'
        );
        
        $table_map = array(
            'Breakfast' => array('mp_br'),
            'Lunch' => array('mp_lu'),
            'Dinner' => array('mp_din'),
            'Break' => array('mp_break')
        );

        if (!empty($id)) 
        {
            $sql_md = "select id from mealplan_detail where mealplan_id = '{$id}' LIMIT 1";
            $res_md = $conn->query($sql_md);
            $md_data = $res_md ? $res_md->fetch() : null;

            if ($md_data) 
            {
                $mealplan_detail_id = (int)$md_data['id'];
                
                $meal_plan = [];

                foreach ($table_map as $meal_key => $table_names) 
                {
                    $meal_plan[$meal_key] = [];
                    foreach ($days_list as $day_name) 
                    {
                        $meal_plan[$meal_key][$day_name] = [];
                    }

                    foreach ($table_names as $table_name) 
                    {
                        $sql_meal = "select day_id, food_id from {$table_name} where mealplan_detail_id = '{$mealplan_detail_id}' AND status = '1'";
                        $res_meal = $conn->query($sql_meal);

                        if ($res_meal) 
                        {
                            while ($meal_row = $res_meal->fetch()) {
                                $day_name = $day_name_map[$meal_row['day_id']] ?? null;
                                if ($day_name !== null) 
                                {
                                    $meal_plan[$meal_key][$day_name][] = (int)$meal_row['food_id'];
                                }
                            }
                        }
                    }
                }
            }
        }

        $food_data_list = [];
        if (!empty($meal_plan)) 
        {
            $all_food_ids = [];
            foreach ($meal_plan as $meal => $days) 
            {
                foreach ($days as $day => $food_ids) 
                {
                    if (!empty($food_ids)) 
                    {
                        foreach ($food_ids as $food_id) 
                        {
                            if (!empty($food_id))
                            {
                                $all_food_ids[] = (int)$food_id;
                            }
                        }
                    }
                }
            }
            
            if (!empty($all_food_ids)) {
                $all_food_ids = array_unique($all_food_ids);
                $food_ids_str = implode(',', $all_food_ids);
                
                $sql_food = "select id, name, kcalmin, kcalmax from food where id IN ({$food_ids_str})";
                $res_food = $conn->query($sql_food);
                
                if ($res_food) {
                    while ($food_row = $res_food->fetch()) {
                        $food_data_list[$food_row['id']] = $food_row;
                    }
                }
            }
        }

        $schedule = [];
        foreach ($meals_list as $m_key => $m_label) {
            foreach ($days_list as $d) {
                $schedule[$m_key][$d] = [];
            }
        }

        if (!empty($meal_plan)) {
            foreach ($meal_plan as $meal => $days) 
            {
                foreach ($days as $day => $food_ids) 
                {
                    if (!is_array($food_ids)) {
                        $food_ids = [$food_ids];
                    }

                    foreach ($food_ids as $food_id) 
                    {
                        if (!empty($food_id) && isset($food_data_list[$food_id])) 
                        {
                            $schedule[$meal][$day][] = $food_data_list[$food_id];
                        }
                    }
                }
            }
        }
        ?>

        <div class='container mt-4 mb-5' style='background-color: #f8fcfb; padding: 20px; min-height: 100vh;'>
            <h2 style='color: #1c4552; font-weight: bold; margin-bottom: 20px;'>Mealplan</h2>
            
            <div class="table-responsive mb-4">
                <table class='table table-bordered' style="background-color: #fff; border-color: #e0e0e0;">
                    <thead style="background-color: #fbfbfb;">
                        <tr>
                            <th colspan="2" class="text-center py-3">Mealplan Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="width: 30%; padding: 12px 20px;">Name</td>
                            <td style="padding: 12px 20px;"><?php echo htmlspecialchars($name); ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 20px;">Date</td>
                            <td style="padding: 12px 20px;"><?php echo htmlspecialchars($date); ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 20px;">Energy(Kcal)</td>
                            <td style="padding: 12px 20px;"><?php echo number_format($kcal, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="table-responsive">
                <table class='table table-bordered text-center align-middle' style="background-color: #fff; border-color: #e0e0e0;">
                    <thead style="background-color: #fbfbfb;">
                        <tr>
                            <th class="py-3">Meal</th>
                            <?php foreach ($days_list as $day): ?>
                                <th class="py-3"><?php echo $day; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($meals_list as $meal_key => $meal_label): ?>
                            <tr>
                                <td class="fw-bold" style="background-color: #fbfbfb;"><?php echo $meal_label; ?></td>
                                <?php foreach ($days_list as $day): ?>
                                    <td style="padding: 15px 10px; font-size: 0.95em;">
                                        <?php 
                                        if (!empty($schedule[$meal_key][$day])) {
                                            foreach ($schedule[$meal_key][$day] as $food) {
                                                $kmin = number_format($food['kcalmin'], 2);
                                                $kmax = number_format($food['kcalmax'], 2);
                                                echo "<div style='margin-bottom: 5px;'>- " . htmlspecialchars($food['name']) . "</div>";
                                                echo "<div style='color: #888; font-size: 0.9em;'>({$kmin} - {$kmax} Kcal)</div>";
                                            }
                                        } else {
                                            echo "-";
                                        }
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        
                        <tr>
                            <td colspan="8" class="text-center" style="background-color: #f5f5f5; padding: 15px;">
                                <a href='index.php?option=mealplan&task=def' class='text-decoration-none' style='color: #333; font-weight: 500;'>
                                    Back
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

}
