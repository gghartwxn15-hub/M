<?php

class postprint
{

    function def() 
    {
		$option = $_REQUEST['option'];
		$conn = new connect();
		$acl = $conn->check_acl();
        ?>
        <style>
            .pp-badge{display:inline-block;font-size:12px;font-weight:600;padding:4px 12px;border-radius:12px;}
            .pp-badge-active{background:#dcfce7;color:#166534;}
            .pp-badge-inactive{background:#f3f4f6;color:#6b7280;}
            .pp-action{display:inline-flex;gap:6px;align-items:center;justify-content:center;}
            .pp-icon-btn{width:32px;height:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;border:1px solid #d1d5db;border-radius:6px;background:#fff;color:#4b5563;cursor:pointer;}
            .pp-icon-btn:hover{background:#f3f4f6;}
            .pp-icon-btn.pp-danger{color:#dc2626;}
            .pp-icon-btn.pp-danger:hover{background:#fef2f2;border-color:#fecaca;}
        </style>
        <div class='container'>
            <div class='row'>
                <div class='col-12'><br>
				<h2><?php echo $conn->get_app_info($option, 3);?></h2>
				<?php
					if (($acl == '2') or ($acl > '5'))
					{
						echo $conn->button_link("Add","index.php?option=".$option."&task=edit&id=0");
					}
				?>
                <table id='datatable' class='table table-bordered table-striped'>
					<?php
						$ls = array('No.', 'Name', 'Status', 'Action');
						$conn->table_header($ls);
					?>
                    <tbody>
                        <?php
                        $sql = 'select * from `postprint`';
                        $conn = new connect();
                        $res = $conn->query($sql);
                        while ($cdr = $res->fetch())
                        {
							$ls[0] = array("c",$cdr['id']);
							$ls[1] = array("l",$cdr['name']);
							if ($cdr['status'] == 1) 
                            {
								$ls[2] = array("c","<span class='pp-badge pp-badge-active'>Active</span>");
                                $ds = "In-Active";
                                $dss = "0";
                                $ds_icon = "<path d='M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a20.3 20.3 0 0 1 5.06-5.94M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a20.29 20.29 0 0 1-3.22 4.44M1 1l22 22'/><path d='M14.12 14.12A3 3 0 1 1 9.88 9.88'/>";
                                $ds_title = "ปิดการใช้งาน";
                            }
                            else
                            {
								$ls[2] = array("c","<span class='pp-badge pp-badge-inactive'>In-Active</span>");
                                $ds = "Active";
                                $dss = "1";
                                $ds_icon = "<path d='M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z'/><circle cx='12' cy='12' r='3'/>";
                                $ds_title = "เปิดการใช้งาน";
                            }
							$action = "<div class='pp-action'>";
                            if (($acl == '2') or ($acl > '5'))
							{
								$action = $action."<button type='button' class='pp-icon-btn' title='แก้ไข' onclick='window.open(\"index.php?option=postprint&task=edit&id=".$cdr['id']."\",\"_self\")'>
									<svg width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'><path d='M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z'/></svg>
								</button>";
								$action = $action."<button type='button' class='pp-icon-btn pp-danger' title='".$ds_title."' onclick='if(confirm(\"ยืนยันการเปลี่ยนสถานะเป็น ".$ds." ใช่หรือไม่?\")){window.open(\"index.php?option=postprint&task=del&id=".$cdr['id']."&stat=".$dss."\", \"_self\")}'>
									<svg width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'>".$ds_icon."</svg>
								</button>";
                            }
						    $action = $action."<button type='button' class='pp-icon-btn' title='รายละเอียด' onclick='window.open(\"index.php?option=postprint&task=det&id=".$cdr['id']."\",\"_self\")'>
								<svg width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'><path d='M14 3h7v7M21 3l-9 9M5 5h5V3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5h-2v5H5V5z'/></svg>
							</button>";
							$action = $action."</div>";
							$ls[3] = array("c",$action);
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

    function edit() 
    {
		$conn = new connect();
		$option = $_REQUEST['option'];
        $id = $_REQUEST['id'];
        if ($id == 0) 
        {
            $head = "Add";
            $name = "";
            $detail = "";
			$type = "";
			$picture = "";
        }
        else 
        {
            $head = "Edit";
            $sql = "select * from `postprint` where `id` = '".$id."'";
            $res = $conn->query($sql);
            while ($cdr = $res->fetch()) 
            {
                $name = $cdr['name'];
				$type = $cdr['type'];
                $detail = $cdr['detail'];
				$picture = $cdr['picture'];
            }
        }

		// preload disease options for the type dropdown
		$disease_sql = "select * from `disease` where `status` = '1'";
		$disease_res = $conn->query($disease_sql);
		$disease_options = "";
		while ($drow = $disease_res->fetch())
		{
			$selected = ($type == $drow['id']) ? " selected" : "";
			$disease_options .= "<option value='".$drow['id']."'".$selected.">".$drow['name']."</option>";
		}
        ?>
        <style>
            .pp-wrap{max-width:920px;margin:0 auto;}
            .pp-title{font-size:30px;font-weight:700;margin:16px 4px 24px;}
            .pp-card{background:#f0f2f0;border-radius:20px;padding:28px 32px 36px;}
            .pp-card-header{text-align:center;font-size:18px;font-weight:500;color:#444;margin-bottom:24px;}
            .pp-row{display:grid;grid-template-columns:1fr 1fr;gap:32px;margin-bottom:8px;}
            .pp-field label{display:block;font-size:14px;font-weight:500;color:#555;margin-bottom:8px;}
            .pp-field input[type=text]{width:100%;border:none;border-radius:10px;padding:10px 14px;background:#fff;font-size:15px;outline:none;box-sizing:border-box;}
            .pp-type-select{width:100%;border:none;border-radius:999px;padding:10px 18px;background:#d9dbd8;font-size:14px;color:#333;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;}
            .pp-body-row{display:grid;grid-template-columns:1fr 1.6fr;gap:32px;margin-top:20px;}
            .pp-picture-box{background:#d3d3d1;border-radius:10px;aspect-ratio:1/1.15;display:flex;align-items:center;justify-content:center;overflow:hidden;cursor:pointer;position:relative;}
            .pp-picture-box img{width:100%;height:100%;object-fit:cover;}
            .pp-picture-box .pp-placeholder{font-size:13px;color:#6b6b6b;text-align:center;padding:0 16px;}
            .pp-picture-box input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;}
            .pp-detail-box{background:#fdfdfd;border-radius:10px;padding:16px 18px;}
            .pp-detail-box textarea{width:100%;height:260px;border:none;resize:none;font-size:14px;color:#333;outline:none;background:transparent;box-sizing:border-box;font-family:inherit;}
            .pp-actions{display:flex;justify-content:center;gap:16px;margin-top:28px;}
            .pp-btn{border:none;border-radius:999px;padding:10px 28px;font-size:15px;font-weight:500;cursor:pointer;text-decoration:none;display:inline-block;}
            .pp-btn-save{background:#1a1a1a;color:#fff;}
            .pp-btn-back{background:#d9dbd8;color:#333;}
        </style>

        <div class='pp-wrap'>
            <div class='pp-title'>postprint</div>

            <form action='index.php' method='post' enctype='multipart/form-data'>
                <div class='pp-card'>
                    <div class='pp-card-header'><?php echo $head." ".$conn->get_app_info($option, 3);?></div>

                    <div class='pp-row'>
                        <div class='pp-field'>
                            <label for='pp_name'>Name</label>
                            <input type='text' id='pp_name' name='name' value='<?php echo htmlspecialchars($name);?>' placeholder='ระบุชื่อ'>
                        </div>
                        <div class='pp-field'>
                            <label for='pp_type'>Type</label>
                            <select class='pp-type-select' id='pp_type' name='type'>
                                <?php echo $disease_options; ?>
                            </select>
                        </div>
                    </div>

                    <div class='pp-body-row'>
                        <div class='pp-field'>
                            <label>Picture</label>
                            <div class='pp-picture-box' id='pp_picture_box'>
                                <?php if (!empty($picture)) { ?>
                                    <img src='uploads/<?php echo htmlspecialchars($picture);?>' id='pp_picture_preview'>
                                <?php } else { ?>
                                    <div class='pp-placeholder' id='pp_picture_placeholder'>คลิกเพื่อเลือกรูปภาพ</div>
                                <?php } ?>
                                <input type='file' id='pp_picture_input' name='picture' accept='image/*'>
                            </div>
                        </div>
                        <div class='pp-field'>
                            <label for='pp_detail'>Detail</label>
                            <div class='pp-detail-box'>
                                <textarea id='pp_detail' name='detail' placeholder='ระบุรายละเอียด'><?php echo htmlspecialchars($detail);?></textarea>
                            </div>
                        </div>
                    </div>

                    <input type='hidden' name='option' value='postprint'>
                    <input type='hidden' name='task' value='save'>
                    <input type='hidden' name='id' value='<?php echo $id;?>'>
                    <input type='hidden' name='old_picture' value='<?php echo htmlspecialchars($picture);?>'>

                    <div class='pp-actions'>
                        <button type='submit' name='Save' value='Save' class='pp-btn pp-btn-save'>บันทึก</button>
                        <a href='index.php?option=<?php echo $option;?>&task=def' class='pp-btn pp-btn-back'>ย้อนกลับ</a>
                    </div>
                </div>
            </form>
        </div>

        <script>
            (function(){
                var input = document.getElementById('pp_picture_input');
                var box = document.getElementById('pp_picture_box');
                var placeholder = document.getElementById('pp_picture_placeholder');
                input.addEventListener('change', function(){
                    var file = input.files[0];
                    if (!file) return;
                    var reader = new FileReader();
                    reader.onload = function(e){
                        var existing = document.getElementById('pp_picture_preview');
                        if (existing) { existing.remove(); }
                        if (placeholder) { placeholder.remove(); }
                        var img = document.createElement('img');
                        img.id = 'pp_picture_preview';
                        img.src = e.target.result;
                        box.prepend(img);
                    };
                    reader.readAsDataURL(file);
                });
            })();
        </script>
        <?php
    }

    function del() 
    {
        $id = $_REQUEST['id'];
		$conn = new connect();
		$sql = "update `postprint` set `status` = '".$_REQUEST['stat']."' where `id` = '".$id."'";
		$conn->query($sql);
		if ($stat == 1)
		{
			$conn->save_logs("Active Postprint> ".$code, $_SESSION['uid'],$id);
		}
		elseif ($stat == 0)
		{
			$conn->save_logs("In-Active Postprint> ".$code, $_SESSION['uid'],$id);
		}
		header('location:index.php?option=postprint&task=def');
    }

    function save() 
	{
		$conn = new connect();
		$id = $_REQUEST['id'];
		$type = $_REQUEST['type'];
		$name = $_REQUEST['name'];
		$detail = $_REQUEST['detail'];
		
		$picture = $_REQUEST['old_picture'] ?? '';
		if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0)
		{
			$upload_dir = __DIR__ . '/uploads/';
			if (!is_dir($upload_dir)) 
			{
				mkdir($upload_dir, 0777, true);
			}
			$ext = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
			$picture = uniqid('img_') . '.' . $ext;
			move_uploaded_file($_FILES['picture']['tmp_name'], $upload_dir . $picture);
		}
			
		if ($id == 0) 
		{
			$sql = " 
			   insert into 
				   `postprint` 
			   set 
			       `name` = '".$name."',
                   `detail` = '".$detail."',
                   `type` = '".$type."',   
                   `picture` = '".$picture."'
			";
		}
		else 
		{
			$sql = "
			   update
			       `postprint` 
               set
                   `name` = '".$name."',
                   `detail` = '".$detail."',
                   `type` = '".$type."',
                   `picture` = '".$picture."'
               where
                   `id` = '".$id."'
            ";
		}
        $conn->query($sql);
    	header("location:index.php?option=postprint&task=def&id=".$id);
}
    function det() 
    {
		$a = '';
		$option = $_REQUEST['option'];
        $id = $_REQUEST['id'];
		$sql = "select * from `postprint` where `id` = '".$id."'";
		$conn = new connect();
		$res = $conn->query($sql);
		while ($cdr = $res->fetch()) 
		{
			$name = $cdr['name'];
			$type = $cdr['type'];
            $detail = $cdr['detail'];
			$picture = $cdr['picture'];
		}

		if($type == 1)
		{
			$a = 'โรคไต';			
		} 
		elseif($type == 2)
		{
			$a = 'โรคเบาหวาน';
		}
        ?>
        <div class='container'>
            <div class='row'>
                <div class='col-12'><br>
                <h2>Postprint Management</h2>
                <table class='table table-bordered table-striped'>
					<thead>
						<tr>
							<th colspan='2' class='text-center'>New Data</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$conn->form_input("Name","name",$name,2);
							$conn->form_input("Type","type",$a,2);
							$conn->form_input("Detail","detail",$detail,2);
							$conn->form_input("Picture","picture",$picture,2);
						?>
							<td colspan='14' class='text-center'>
							<?php
								echo $conn->button_link("Back","index.php?option=".$option."&task=def");
							?>
							</td>
						</tr>
					</tbody>
                </table>
                </div>
            </div>
        </div>
        <?php
    }

}

?>