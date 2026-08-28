<?php

class mission

{

    function def() 
    {
		$option = $_REQUEST['option'];
		$conn = new connect();
		$acl = $conn->check_acl();
        ?>
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
                        $sql = 'select * from `mission`';
                        $conn = new connect();
                        $res = $conn->query($sql);
                        while ($cdr = $res->fetch())
                        {
							$ls[0] = array("c",$cdr['id']);
							$ls[1] = array("l",$cdr['name']);
							if ($cdr['status'] == 1) 
                            {
								$ls[2] = array("c","Active");
                                $ds = "In-Active";
                                $dss = "0";
                            }
                            else
                            {
								$ls[2] = array("c","In-Active");
                                $ds = "Active";
                                $dss = "1";
                            }
							$action = "";
                            if (($acl == '2') or ($acl > '5'))
							{
										$action = $action."<input class='btn' type='button' value='Edit' onclick='window.open(\"index.php?option=".$option."&task=edit&id=".$cdr['id']."\",\"_self\")' />";
										$action = $action."<input class='btn' type='button' value='".$ds."' onclick='window.open(\"index.php?option=".$option."&task=del&id=".$cdr['id']."&stat=".$dss."\", \"_self\")' />";
                            }
								    $action = $action."<input class='btn' type='button' value='Detail' onclick='window.open(\"index.php?option=".$option."&task=det&id=".$cdr['id']."\",\"_self\")' />";
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
        }
        else 
        {
            $head = "Edit";
            $sql = "select * from `mission` where `id` = '".$id."'";
            $res = $conn->query($sql);
            while ($cdr = $res->fetch()) 
            {
                $name = $cdr['name'];
                $detail = $cdr['detail'];
            }
        }
        ?>
        <div class='container'>
            <div class='row'>
                <div class='col-12'><br>
                <h2><?php echo $conn->get_app_info($option, 3);?></h2>
                <form action='index.php' method='get'>
				<table class='table table-bordered table-striped'>
					<thead>
						<tr>
							<th colspan='2' class='text-center'><?php echo $head." ".$conn->get_app_info($option, 3);?></th>
						</tr>
					</thead>
					<tbody>
					<?php
						$conn->form_input("Name","name",$name,0);
						$conn->form_input("Detail","detail",$detail,14);
					?>
						<tr>
							<td colspan='2' class='text-center'>
								<?php
										echo $conn->insert_input("option", $option, 5);
									echo $conn->insert_input("task","save",5);
									echo $conn->insert_input("id",$id,5);
									echo $conn->insert_input("Save","Save",4);
									echo $conn->button_link("Back","index.php?option=".$option."&task=def");
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
        $stat = $_REQUEST['stat'];
		$conn = new connect();
		$sql = "update `mission` set `status` = '".$stat."' where `id` = '".$id."'";
		$conn->query($sql);
		if ($stat == 1)
		{
			$conn->save_logs("Active mission > ".$id, $_SESSION['uid'],$id);
		}
		elseif ($stat == 0)
		{
			$conn->save_logs("In-Active mission > ".$id, $_SESSION['uid'],$id);
		}
		header('location:index.php?option=mission&task=def');
    }

    function save() 
    {
		$conn = new connect();
		$id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : 0;
		$name = isset($_REQUEST['name']) ? trim($_REQUEST['name']) : '';
		$detail = isset($_REQUEST['detail']) ? trim($_REQUEST['detail']) : '';
		if ($id > 0) 
		{
			$sql = "update `mission` set `name` = '".$name."', `detail` = '".$detail."' where `id` = '".$id."'";
			$conn->query($sql);
			$conn->save_logs("Edit Mission > ".$id, $_SESSION['uid'], $id);
		}
		else 
		{
			$sql = "insert into `mission` set `name` = '".$name."', `detail` = '".$detail."'";
			$id = $conn->query_lastid($sql);
			$conn->save_logs("Add Mission > ".$id, $_SESSION['uid'], $id);
		}
        header("location:index.php?option=mission&task=def&id=".$id);
        exit;
    }

    function det() 
    {
		$a = '';
		$option = $_REQUEST['option'];
        $id = $_REQUEST['id'];
		$sql = "select * from `mission` where `id` = '".$id."'";
		$conn = new connect();
		$res = $conn->query($sql);
		while ($cdr = $res->fetch()) 
		{
			$name = $cdr['name'];
            $detail = $cdr['detail'];
		}
        ?>
        <div class='container'>
            <div class='row'>
                <div class='col-12'><br>
                <h2>mission
					 Management</h2>
                <table class='table table-bordered table-striped'>
					<thead>
						<tr>
							<th colspan='2' class='text-center'>New Data</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$conn->form_input("Name","name",$name,2);
							$conn->form_input("Detail","detail",$detail,2);
							
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