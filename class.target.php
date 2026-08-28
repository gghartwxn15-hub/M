<?php

class target
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
						$ls = array('Id', 'Name','BMI', 'Status', 'Action');
						$conn->table_header($ls);
					?>
                    <tbody>
                        <?php
                        $sql =  'select `target`.`id` as `id`, `target`.`bmi` as `bmi`, `target`.`weight` as `weight`,IFNULL(`users`.`user`, "ไม่พบชื่อผู้ใช้") as `name`, 
                        `target`.`status` as `status` from `target` LEFT JOIN `users` ON `target`.`uid` = `users`.`id`';
                        $conn = new connect();
                        $res = $conn->query($sql);
						$a = 0;
                        while ($cdr = $res->fetch())
                        {
							$a++;
                            $ls[0] = array("c", $a);
                            $ls[1] = array("c", $cdr['name']);
                            $ls[2] = array("c", $cdr['bmi']);
							if ($cdr['status'] == 1)
							{
								$ls[3] = array("c", "Active");
								$ds = "In-Active";
								$dss = "0";
							}
							else
							{
								$ls[3] = array("c", "In-Active");
								$ds = "Active";
								$dss = "1";
							}
							$action = "";
                            if (($acl == '2') or ($acl > '5'))
							{
						        $action = $action."<input class='btn' type='button' value='".$ds."' onclick='window.open(\"index.php?option=target&task=del&id=".$cdr['id']."&stat=".$dss."\", \"_self\")' />";
                            }
						    $action = $action."<input class='btn' type='button' value='Detail' onclick='window.open(\"index.php?option=target&task=det&id=".$cdr['id']."\",\"_self\")' />";
                            $ls[4] = array("c",$action);
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
        $id = $_REQUEST['id'];
        if ($id == 0) 
        {
            $head = "Add";
            $name = "";
            $bmi = "";
            $weight = "";
        }
        else 
        {
            $head = "Edit";
            $sql = "select * from target where id = '".$id."'";
            $res = $conn->query($sql);
            while ($cdr = $res->fetch()) 
            {
                $name = $cdr['name'];
                $bmi = $cdr['bmi'];
                $weight = $cdr['weight'];
            }

        }
        ?>
        <div class='container'>
            <div class='row'>
                <div class='col-12'><br>
                <h2>Target</h2>
                <form action="index.php" method="get">
                <table class='table table-bordered table-striped'>
                    <thead>
                        <tr>
                            <th colspan='2' class='text-center'><?php echo $head;?> ตั้งเป้าหมายสำหรับการวางแผนเมนูอาหารครั้งนี้ </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $conn->form_input("Name","name",$name,0);
                            $conn->form_input("BMI","bmi",$bmi,0);
                            $conn->form_input("Weight","weight",$weight,0);
                        ?>
                    <tr>
                        <td colspan='2' class='text-center'>
                            <?php
                                echo $conn->insert_input("save","Save",4);
                                echo $conn->button_link("Back","index.php?option=target&task=def");
                                echo $conn->insert_input("option","target",5);
                                echo $conn->insert_input("task","save",5);
                                echo $conn->insert_input("id",$id,5);
                            ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
                </form>
                </div>
            </div>
        </div>
        <?php

    }

    function del() 
    {
        $id = $_REQUEST['id'];
		$conn = new connect();
		$sql = "update `target` set `status` = '".$_REQUEST['stat']."' where `id` = '".$id."'";
		$conn->query($sql);
		if ($stat == 1)
		{
			$conn->save_logs("Active target> ".$code, $_SESSION['uid'],$id);
		}
		elseif ($stat == 0)
		{
			$conn->save_logs("In-Active target> ".$code, $_SESSION['uid'],$id);
		}
		header('location:index.php?option=target&task=def');
    }

    function det() 
    {
        $id = $_REQUEST['id'];
		$sql =  'select `target`.`id` as `id`, `target`.`bmi` as `bmi`, `target`.`weight` as `weight`,IFNULL(`users`.`user`, "ไม่พบชื่อผู้ใช้") as `name`, 
                        `target`.`status` as `status` from `target` LEFT JOIN `users` ON `target`.`uid` = `users`.`id`';
		$conn = new connect();
		$res = $conn->query($sql);
		while ($cdr = $res->fetch()) 
		{
            $id = $cdr['id'];
			$name = $cdr['name'];
            $bmi = $cdr['bmi'];
            $weight = $cdr['weight'];
		}
        ?>
        <div class='container'>
            <div class='row'>
                <div class='col-12'><br>
                <h2>Target</h2>
                <table class='table table-bordered table-striped'>
						<thead>
                            <tr>
							    <th colspan='2' class='text-center'>Target Data</th>
						    </tr>
                        </thead>
                        <tbody>
                            <?php   
						    $conn->form_input("Id","id",$id,2);
						    $conn->form_input("Name","name",$name,2);
						    $conn->form_input("BMI","bmi",$bmi,2);
                            $conn->form_input("Weight","weight",$weight,2);
                            ?>
                        </tbody>
						<tr>
							<td colspan='2' class='text-center'>
                                <?php
                                   echo  $conn->button_link("Back","index.php?option=target&task=def");
                                ?>
							</td>
						</tr>
				</table>
                </div>
            </div>
        </div>
        <?php
    }

}

?>
