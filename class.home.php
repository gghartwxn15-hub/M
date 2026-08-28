<?php

class home
{
    function def()
    {
        
        if (session_status() === PHP_SESSION_NONE) 
        {
            session_start();
        }
        $is_post = ($_SERVER['REQUEST_METHOD'] === 'POST');
        if ($is_post)
        {
            $_SESSION['home_age']    = isset($_POST['age']) ? (int)$_POST['age'] : '';
            $_SESSION['home_gender'] = isset($_POST['gender']) ? (int)$_POST['gender'] : 0;
            $_SESSION['home_height'] = isset($_POST['height']) ? (float)$_POST['height'] : '';
            $_SESSION['home_weight'] = isset($_POST['weight']) ? (float)$_POST['weight'] : '';
            $_SESSION['home_type']   = isset($_POST['type']) ? (float)$_POST['type'] : '';
        }

        $age    = isset($_SESSION['home_age']) ? $_SESSION['home_age'] : '';
        $gender = isset($_SESSION['home_gender']) ? $_SESSION['home_gender'] : 0;
        $height = isset($_SESSION['home_height']) ? $_SESSION['home_height'] : '';
        $weight = isset($_SESSION['home_weight']) ? $_SESSION['home_weight'] : '';
        $type   = isset($_SESSION['home_type']) ? $_SESSION['home_type'] : '';
        ?>
        
        <style>
            body 
            {
                font-family: 'Kanit', sans-serif;
                background-color: #f4f9f5;
            }
            .health-card 
            {
                background: #ffffff;
                border-radius: 20px;
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
                padding: 30px;
                border: none;
            }
            .header-title 
            {
                color: #2c7a51;
                font-weight: 600;
            }
            .header-subtitle {
                color: #6b7280;
                font-size: 1rem;
                line-height: 1.6;
                margin: 0 auto;
                max-width: 680px;
                margin-top: 0.75rem;
            }
            .btn-health 
            {
                background: linear-gradient(135deg, #2ea36b 0%, #1d784a 100%);
                border: none;
                color: white;
                border-radius: 10px;
                padding: 12px;
                font-size: 1.1rem;
                font-weight: 500;
                transition: all 0.3s;
            }
            .btn-health:hover 
            {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(46, 163, 107, 0.4);
                color: white;
            }
            .form-label 
            {
                font-weight: 500;
                color: #4a5568;
            }
            .input-group-text 
            {
                background-color: #e6f2eb;
                border: 1px solid #cce3d6;
                color: #2c7a51;
                font-weight: 500;
            }
            .form-control, .form-select
            {
                border: 1px solid #cce3d6;
                border-radius: 10px;
            }
            .form-control:focus, .form-select:focus 
            {
                border-color: #2ea36b;
                box-shadow: 0 0 0 0.25rem rgba(46, 163, 107, 0.25);
            }
            .result-box 
            {
                border-radius: 15px;
                padding: 20px;
                height: 100%;
                color: white;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
            .result-bmi { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: #333; }
            .result-bmr { background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); color: #333; }
            .result-tdee { background: linear-gradient(135deg, #ff9a44 0%, #fc6076 100%); }
            .result-encarb_kcal { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: #1a4a31;}
            .result-carb_g { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); color: #4a2711; }
            .result-carb_c { background: linear-gradient(135deg, #d5d2d2 0%, #757575 100%); color: #ffffff; }
            .result-value 
            {
                font-size: 2rem;
                font-weight: 600;
                margin: 10px 0;
            }
        </style>

        <div class='container mt-5 mb-5'>
            <div class='row justify-content-center'>
                <div class='col-lg-8'>
                    
                    <div class="card health-card mb-4">
                        <div class="text-center mb-4">
                            <i class="fa-solid fa-heart-pulse fa-3x mb-3" style="color: #2ea36b;"></i>
                            <h2 class="header-title">คำนวณโภชนาการของคุณ</h2>
                            <p class="header-subtitle">ตรวจสอบค่า BMI, BMR, TDEE, ควบคุมพลังงานจากคาร์โบไฮเดรตไม่ให้เกิน 20%<br>ปริมาณคาร์โบไฮเดรตที่เหมาะสมต่อวันและปริมาณคาร์บต่อวัน</p>
                        </div>
                        
                        <form method='POST' action=''>
                            <div class="row g-4">
                                <div class="col-md-12 text-center">
                                    <label class="form-label d-block mb-3"><i class="fa-solid fa-venus-mars"></i> เพศ (Gender)</label>
                                    <div class="btn-group" role="group">
                                        <input type="radio" class="btn-check" name="gender" id="genderMale" value="1" <?php echo ($gender == 1) ? 'checked' : ''; ?> required>
                                        <label class="btn btn-outline-success px-4 py-2" for="genderMale"><i class="fa-solid fa-mars"></i> ชาย</label>

                                        <input type="radio" class="btn-check" name="gender" id="genderFemale" value="2" <?php echo ($gender == 2) ? 'checked' : ''; ?> required>
                                        <label class="btn btn-outline-success px-4 py-2" for="genderFemale"><i class="fa-solid fa-venus"></i> หญิง</label>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">อายุ (Age)</label>
                                    <div class="input-group">
                                        <input type='number' class='form-control' name='age' value='<?php echo htmlspecialchars($age); ?>' required min="1" placeholder="เช่น 25">
                                        <span class="input-group-text">ปี</span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">ส่วนสูง (Height)</label>
                                    <div class="input-group">
                                        <input type='number' step='0.01' class='form-control' name='height' value='<?php echo htmlspecialchars($height); ?>' required min="1" placeholder="เช่น 170">
                                        <span class="input-group-text">cm</span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">น้ำหนัก (Weight)</label>
                                    <div class="input-group">
                                        <input type='number' step='0.01' class='form-control' name='weight' value='<?php echo htmlspecialchars($weight); ?>' required min="1" placeholder="เช่น 65">
                                        <span class="input-group-text">kg</span>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label"><i class="fa-solid fa-person-running"></i> กิจกรรมทางกาย (Physical Activity)</label>
                                    <select class="form-select" name='type' required>
                                        <option value="" disabled <?php echo empty($type) ? 'selected' : ''; ?>>-- กรุณาเลือกระดับกิจกรรม --</option>
                                        <?php
                                            
                                            $sql = "select * from `phy_act` where `status` = '1'";
                                            $conn = new connect();
                                            $res = $conn->query($sql);
                                            while ($cdr = $res->fetch()) 
                                            {
                                                $ugtid = $cdr['id'];
                                                $ugtname = $cdr['name'];
                                                $act = $cdr['act'];
                                                $selected = ((string)$type === (string)$act) ? "selected" : "";
                                                echo "<option value='{$act}' {$selected}>{$ugtname}</option>";
                                            }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-health w-100">
                                        <i class="fa-solid fa-calculator"></i> คำนวณผลลัพธ์ (Calculate)
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

            <?php
                if ($age > 0 && $height > 0 && $weight > 0 && $gender > 0 && $type > 0) 
                {
                    
                    $height_m = $height / 100;
                    $bmi = $weight / ($height_m ** 2);
                    $_SESSION['home_bmi'] = number_format($bmi, 2, '.', '');
                    if($bmi > 30.00)
                    {
                        $abmi = 'เป็นโรคอ้วน ระดับที่2';
                    }
                    elseif($bmi >= 25.00 && $bmi < 30)
                    {
                        $abmi = 'เป็นโรคอ้วน ระดับที่1';
                    }
                    elseif($bmi >= 23.00 && $bmi < 25.00)
                    {
                        $abmi = 'น้ำหนักเกินมาตรฐาน';
                    }
                    elseif($bmi >= 18.50 && $bmi < 23.00)
                    {
                        $abmi = 'น้ำหนักปกติ';
                    }
                    else 
                    { 
                        $abmi = 'ผอมเกินไป';
                    }
                    
                    if ($gender == 1) 
                    { 
                        $bmr = 66 +(13.7 * $weight) + (5 * $height) - (6.8 * $age);
                    } 
                    else 
                    { 
                        $bmr = 665 +(9.6 * $weight) + (1.8 * $height) - (4.7 * $age);
                    }

                    $tdee = $bmr * $type;
                    $encabo_kcal = $tdee * 0.20; 
                    $carb_g = $encabo_kcal / 4;
                    $carb_c = $carb_g / 15;

                    $_SESSION['home_tdee'] = $tdee;
                    $_SESSION['home_carbs'] = $carb_g;

                    if ($is_post) 
                    {
                        try 
                        {
                            $conn = new connect();
                            $insert_sql = "
                                insert into `kcal` (`gender`, `age`, `height`, `weight`, `activity_type`, 
                                `bmi`, `bmr`, `tdee`, `encabo_kcal`, `carb_g`, `carb_c`, `created_at`) VALUES                     
                                ('{$gender}', '{$age}', '{$height}', '{$weight}', '{$type}', 
                                '{$bmi}', '{$bmr}', '{$tdee}', '{$encabo_kcal}', '{$carb_g}','{$carb_c}',NOW())
                            ";
                            $conn->query($insert_sql);
                        } 
                        catch (Exception $e) 
                        {
                            $error_msg = $e->getMessage();
                        }
                    }

            ?>
                    <div class="health-card" id="result-section">
                        <h4 class="text-center mb-4 header-title"><i class="fa-solid fa-clipboard-check"></i> ผลการคำนวณของคุณ</h4>
                        
                        <div class="row g-3 text-center">
                            <div class="col-md-6">
                                <div class="result-box result-bmi shadow-sm">
                                    <div class="text-uppercase"><i class="fa-solid fa-weight-scale"></i> ดัชนีมวลกาย (BMI)</div>
                                    <div class="result-value">
                                        <?php echo number_format($bmi, 2); ?>
                                    </div>
                                    <small class="opacity-75">kg/m²</small>
                                    <div class="mt-1">
                                        <small class="fw-bold"><?php echo $abmi; ?></small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="result-box result-bmr shadow-sm text-dark">
                                    <div class="text-uppercase"><i class="fa-solid fa-bed"></i> พลังงานพื้นฐาน (BMR)</div>
                                    <div class="result-value text-dark"><?php echo number_format($bmr, 2); ?></div>
                                    <small class="opacity-75">kcal/วัน</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="result-box result-tdee shadow-sm">
                                    <div class="text-uppercase"><i class="fa-solid fa-fire"></i> พลังงานที่ใช้ทั้งหมด (TDEE)</div>
                                    <div class="result-value"><?php echo number_format($tdee, 2); ?></div>
                                    <small class="opacity-75">kcal/วัน</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="result-box result-encarb_kcal shadow-sm">
                                    <div class="text-uppercase">
                                        <i class="fa-solid fa-bolt"></i> ควบคุมพลังงานจากคาร์โบไฮเดรตไม่ให้เกิน 20% </div>
                                    <div class="result-value"><?php echo number_format($encabo_kcal, 2); ?></div>
                                    <small class="opacity-75">kcal/วัน (20% ของ TDEE)</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="result-box result-carb_g shadow-sm">
                                    <div class="text-uppercase fw-bold"><i class="fa-solid fa-bowl-rice"></i> คาร์โบไฮเดรตแนะนำ</div>
                                    <div class="result-value"><?php echo number_format($carb_g, 2); ?></div>
                                    <small class="fw-bold">กรัม/วัน </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="result-box result-carb_c shadow-sm">
                                    <div class="text-uppercase fw-bold"><i class="fa-solid fa-bowl-food food-icon"></i> คาร์บต่อวัน</div>
                                    <div class="result-value"><?php echo number_format($carb_c, 2); ?></div>
                                    <small class="fw-bold">คาร์บ/วัน</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            var resultSection = document.getElementById("result-section");
                            if (resultSection) {
                                resultSection.scrollIntoView({ behavior: 'smooth' });
                            }
                        });
                    </script>

                    <?php
                        $is_logged_in = isset($_SESSION['uid']) && (int)$_SESSION['uid'] != 4;
                        $next_url = $is_logged_in ? 'index.php?option=aim&task=def' : 'index.php?option=logs&task=login_form';
                    ?>

                    <div class="d-flex justify-content-end mt-5">
                        <a href="<?php echo $next_url; ?>" class="btn px-4 py-2" style="background-color: #2c7a51; color: white; border-radius: 10px; font-weight: 500; box-shadow: 0 4px 15px rgba(44, 122, 81, 0.3); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            ถัดไป (Next) <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    </div>
            <?php
                }
            ?>
                </div>
            </div>
        </div>
        <?php
    }
}
?>