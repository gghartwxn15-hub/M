<?php
class mis
{
    function def() 
    {
        $conn = new connect();
        $acl = $conn->check_acl();
        $sql = " SELECT id, name FROM mission WHERE status = 1";
        $result = $conn->query($sql);
        $tasks = [];

        while ($row = $result->fetch())
        {
            $tasks[] = [
                'id' => $row['id'],
                'name' => $row['name']
            ];
        }

        
        ?>
        <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <style>
            .theme-container 
            {
                font-family: 'Prompt', sans-serif;
                background-color: #f4f6f8; 
                padding: 30px 15px;
                min-height: 100vh;
            }
            .card-theme 
            {
                background: #ffffff;
                border-radius: 16px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.04);
                padding: 35px;
                margin-bottom: 20px;
                max-width: 700px;
                margin-left: auto;
                margin-right: auto;
            }
            .text-theme-green {
                color: #278a5b;
                font-weight: 600;
            }
            
            /* ส่วนของ Progress Bar */
            .progress-header {
                display: flex;
                align-items: center;
                gap: 15px;
                margin-bottom: 25px;
            }
            .percent-label {
                font-size: 1.8em;
                font-weight: 600;
                color: #278a5b;
                min-width: 60px;
            }
            .custom-progress-container {
                flex: 1;
                height: 32px;
                background-color: #eaf3ee;
                border: 2px solid #278a5b;
                border-radius: 20px;
                overflow: hidden;
                position: relative;
            }
            .custom-progress-bar {
                height: 100%;
                width: 0%;
                background-color: #278a5b;
                transition: width 0.4s ease;
            }

            /* รายการภารกิจ */
            .task-list {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }
            .task-item {
                display: flex;
                align-items: center;
                gap: 15px;
                padding: 12px 16px;
                border-radius: 10px;
                background-color: #f9fbfaf3;
                border: 1px solid #eef2f0;
                transition: all 0.2s ease;
                cursor: pointer;
                user-select: none;
            }
            .task-star {
                color: #278a5b;
                font-size: 1.3em;
                transition: transform 0.2s ease;
            }
            
            /* อนิเมชั่นเวลากดดาว */
            .task-item:active .task-star {
                transform: scale(0.8);
            }
            
            /* สถานะตัวอักษร: ตอนแรกสีเข้ม */
            .task-text {
                font-size: 1.05em;
                color: #2c3e50;
                font-weight: 500;
                transition: color 0.3s ease;
            }

            /* เมื่อทำเสร็จแล้ว (มีคลาส completed) */
            .task-item.completed {
                background-color: #f0f7f4;
            }
            .task-item.completed .task-text {
                color: #a0aec0; 
                text-decoration: line-through;
            }

            /* ปรับปุ่มย้อนกลับให้เหมือนในรูปอ้างอิง */
            .btn-back-inline {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: 8px 16px;
                margin-top: 30px;
                border: 1px solid #2ea36b;
                border-radius: 6px; /* ขอบโค้งมนเล็กน้อยเหมือนในรูป */
                background: #ffffff;
                color: #2ea36b;
                font-weight: 500;
                font-size: 1rem;
                text-decoration: none;
                transition: all 0.2s ease;
            }

            .btn-back-inline:hover {
                background: #f0faf5;
                color: #257e55;
                text-decoration: none;
            }

            .btn-back-inline span {
                white-space: nowrap;
            }
        </style>

        <div class='container-fluid theme-container'>
            <div class='row'>
                <div class='col-12'>
                    
                    <div class='card-theme'>
                        <div class="text-center mb-4">
                            <h2 class="text-theme-green">
                                <i class="fas fa-tasks" style="font-size: 1.2em; display: block; margin-bottom: 10px;"></i>
                                ภารกิจของคุณ
                            </h2>
                        </div>
                        
                        <!-- Progress Bar & % -->
                        <div class="progress-header">
                            <span class="percent-label" id="percent-text">0%</span>
                            <div class="custom-progress-container">
                                <div class="custom-progress-bar" id="progress-bar"></div>
                            </div>
                        </div>

                        <!-- รายการภารกิจ -->
                        <div class="task-list">
                            <?php 
                            // ใช้ลูปดึงข้อมูลจากตาราง mission
                            foreach ($tasks as $task): 
                            ?>
                                <div class="task-item" data-checked="false" onclick="toggleTask(this)">
                                    <i class="fa-regular fa-star task-star"></i>
                                    <span class="task-text" id="task-<?php echo $task['id']; ?>">
                                        <?php echo htmlspecialchars($task['name']); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- ย้ายปุ่มกลับมาไว้ตรงนี้ เพื่อให้อยู่ด้านล่างซ้ายของกล่อง -->
                        <a class="btn-back-inline" href="javascript:history.back()">
                            <i class="fas fa-arrow-left"></i>
                            <span>ย้อนกลับ (Back)</span>
                        </a>

                    </div>

                </div>
            </div>
        </div>

        <script>
            function toggleTask(element) 
            {
                const starIcon = element.querySelector('.task-star');
                let isChecked = element.getAttribute('data-checked') === 'true';

                isChecked = !isChecked;
                element.setAttribute('data-checked', isChecked);

                if (isChecked) 
                {
                    element.classList.add('completed');
                    starIcon.classList.remove('fa-regular');
                    starIcon.classList.add('fa-solid');
                } 
                else 
                {
                    element.classList.remove('completed');
                    starIcon.classList.remove('fa-solid');
                    starIcon.classList.add('fa-regular');
                }
                updateProgress();
            }

            function updateProgress() 
            {
                const allTasks = document.querySelectorAll('.task-item');
                const total = allTasks.length;
                let checkedCount = 0;
                allTasks.forEach(task => 
                {
                    if (task.getAttribute('data-checked') === 'true') 
                    {
                        checkedCount++;
                    }
                });
                const percentage = total > 0 ? Math.round((checkedCount / total) * 100) : 0;
                document.getElementById('percent-text').innerText = percentage + '%';
                document.getElementById('progress-bar').style.width = percentage + '%';
            }
        </script>
        <?php
    }
}
?>