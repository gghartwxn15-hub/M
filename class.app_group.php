<?php

class app_group
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
				<?php
					if (($acl == '2') or ($acl > '5'))
					{
					echo $conn->button_link("Add","index.php?option=app_group&task=edit&id=0");
					}
				?>
                <table id='datatable' class='table table-bordered table-striped'>
                    <?php
						$ls = array('Id', 'Name', 'Status', 'Action');
						$conn->table_header($ls);
					?>
                    <tbody>
                        <?php
                        $sql = 'select * from `app_group`';
                        $conn = new connect();
                        $res = $conn->query($sql);
						$a = 0;
                        while ($cdr = $res->fetch())
                        {
							$a++;
                            $ls[0] = array("c", $a);
                            $ls[1] = array("c", $cdr['name']);
							if ($cdr['status'] == 1)
							{
								$ls[2] = array("c", "Active");
								$ds = "In-Active";
								$dss = "0";
							}
							else
							{
								$ls[2] = array("c", "In-Active");
								$ds = "Active";
								$dss = "1";
							}
							$action = "";
                            if (($acl == '2') or ($acl > '5'))
							{
                                $action = $action."<input class='btn' type='button' value='Edit' onclick='window.open(\"index.php?option=app_group&task=edit&id=".$cdr['id']."\",\"_self\")' />";
						        $action = $action."<input class='btn' type='button' value='".$ds."' onclick='window.open(\"index.php?option=app_group&task=del&id=".$cdr['id']."&stat=".$dss."\", \"_self\")' />";
                            }
						    $action = $action."<input class='btn' type='button' value='Detail' onclick='window.open(\"index.php?option=app_group&task=det&id=".$cdr['id']."\",\"_self\")' />";
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
        $id = $_REQUEST['id'];
        if ($id == 0) 
        {
            $head = "Add";
            $name = "";
            $dir = "";
            $detail = "";
            $appgroup = "";
        }
        else 
        {
            $head = "Edit";
            $sql = "select * from app_group where id = '".$id."'";
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
                <h2>Application Group Management</h2>
                <form action="index.php" method="get">
                <table class='table table-bordered table-striped'>
                    <thead>
                        <tr>
                            <th colspan='2' class='text-center'><?php echo $head;?> Application Group Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $conn->form_input("Name","name",$name,0);
                            $conn->form_input("Detail","detail",$detail,0);
                        ?>
                    <tr>
                        <td colspan='2' class='text-center'>
                            <?php
                                echo $conn->insert_input("save","Save",4);
                                echo $conn->button_link("Back","index.php?option=app_group&task=def");
                                echo $conn->insert_input("option","app_group",5);
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
 
    function save()
    {
        $conn = new connect();
        $id = $_REQUEST['id'];
        $name = $_REQUEST['name'];
        $detail = $_REQUEST['detail'];
        if ($id > 0) 
        {
            $sql = "update app_group set name = '".$name."', detail = '".$detail."' where id = '".$id."'";
            $conn->query($sql);
            $conn->save_logs("Edit Application Group > ", $_SESSION['uid'], $id);
        }
        else 
        {
            $sql = "insert into app_group set name = '".$name."', detail = '".$detail."'";
            $id = $conn->query_lastid($sql);
            $conn->save_logs("Add Application Group > ", $_SESSION['uid'], $id);
        }
        header("location:index.php?option=app_group&task=def&id=".$id);
    }

    function del() 
    {
        $id = $_REQUEST['id'];
		$conn = new connect();
		$sql = "update `app_group` set `status` = '".$_REQUEST['stat']."' where `id` = '".$id."'";
		$conn->query($sql);
		if ($stat == 1)
		{
			$conn->save_logs("Active App_Group> ".$code, $_SESSION['uid'],$id);
		}
		elseif ($stat == 0)
		{
			$conn->save_logs("In-Active App_Group> ".$code, $_SESSION['uid'],$id);
		}
		header('location:index.php?option=app_group&task=def');
    }

    function det() 
    {
        $id = $_REQUEST['id'];
		$sql = "select * from `app_group` where `id` = '".$id."'";
		$conn = new connect();
		$res = $conn->query($sql);
		while ($cdr = $res->fetch()) 
		{
            $id = $cdr['id'];
			$name = $cdr['name'];
            $detail = $cdr['detail'];
		}
        ?>
        <div class='container'>
            <div class='row'>
                <div class='col-12'>
                <h2>Application Group Management</h2>
                <table class='table table-bordered table-striped'>
						<thead>
                            <tr>
							    <th colspan='2' class='text-center'>Application Group Data</th>
						    </tr>
                        </thead>
                        <tbody>
                            <?php   
						    $conn->form_input("Id","id",$id,2);
						    $conn->form_input("Name","name",$name,2);
						    $conn->form_input("Detail","detail",$detail,2);
                            ?>
                        </tbody>
						<tr>
							<td colspan='2' class='text-center'>
                                <?php
                                   echo  $conn->button_link("Back","index.php?option=app_group&task=def");
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
