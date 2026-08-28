<?php

class app
{
    function def() 
    {
        $conn = new connect();
		$acl = $conn->check_acl();
        if (isset($_REQUEST['searcher']))
        {
            $searcher = $_REQUEST['searcher'];
        }
        else
        {
            $searcher = null;
        }
		$option = $_REQUEST['option'];
        ?>
        <div class='container'>
            <div class='row'>
                <div class='col-12'></br>
				<h2><?php echo $conn->get_app_info($option, 3);?></h2>
				<?php
					if (($acl == '2') or ($acl > '5'))
					{
					echo $conn->button_link("Add","index.php?option=app&task=edit&id=0");
					}
				?>
                <table id='datatable' class='table table-bordered table-striped'>
                    <?php
						$ls = array('Id', 'Application Group', 'Name', 'Directory', 'Status', 'Action');
						$conn->table_header($ls);
					?>
                    <tbody>
                        <?php
                        $sql = "select
                        `app`.`id` as `id` 
						,`app`.`name` as `name`
						,`app_group`.`name` as `gname`
						, `app`.`dir` as `dir`
						, `app`.`status` as `status`
						from 
						`app`, `app_group` 
						where `app`.`appgroup` = `app_group`.`id`
						order by `app`.`appgroup`, `app`.`name`";
                        $conn = new connect();
                        $res = $conn->query($sql);
						$a = 0;
                        while ($cdr = $res->fetch())
                        {
							$a++;
                            $ls[0] = array("c", $a);
                            $ls[1] = array("c", $cdr['gname']);
                            $ls[2] = array("c", $cdr['name']);
                            $ls[3] = array("c", $cdr['dir']);
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
                                $action = $action."<input class='btn' type='button' value='Edit' onclick='window.open(\"index.php?option=app&task=edit&id=".$cdr['id']."\",\"_self\")' />";
						        $action = $action."<input class='btn' type='button' value='".$ds."' onclick='window.open(\"index.php?option=app&task=del&id=".$cdr['id']."&stat=".$dss."\", \"_self\")' />";
                            }
						    $action = $action."<input class='btn' type='button' value='Detail' onclick='window.open(\"index.php?option=app&task=det&id=".$cdr['id']."\",\"_self\")' />";
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
            $sql = "select * from app where id = '".$id."'";
            $res = $conn->query($sql);
            while ($cdr = $res->fetch()) 
            {
                $name = $cdr['name'];
                $dir = $cdr['dir'];
                $detail = $cdr['detail'];
                $appgroup = $cdr['appgroup'];
            }
        }
        ?>
        <div class='container'>
            <div class='row'>
                <div class='col-12'>
                <h2>Application Management</h2>
                <form action="index.php" method="get">
                <table class='table table-bordered table-striped'>
                    <thead>
                        <tr>
                            <th colspan='2' class='text-center'><?php echo $head;?> Application Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $conn->form_input("Name","name",$name,0);
                            $conn->form_input("Dir","dir",$dir,0);
                            $conn->form_input("Detail","detail",$detail,0);
                        ?>
                    <tr>
                        <td>Application Group</td>
                        <td>
							<select name='appgroup'>
							<?php
								$a = 0;
								$sql = "select `id`, `name` from `app_group` where `status` = '1'";
								$res = $conn->query($sql);
								$a = 0;
								while ($cdr = $res->fetch())
								{
									if ($cdr['id'] == $appgroup)
									{
										echo "<option value='".$cdr['id']."' selected>";
									}
									else
									{
										echo "<option value='".$cdr['id']."'>";
									}
									echo $cdr['name'];
									echo "</option>";
								}
							?>
							</select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='2' class='text-center'>
                            <?php
                                echo $conn->insert_input("save","Save",4);
                                echo $conn->button_link("Back","index.php?option=app&task=def");
                                echo $conn->insert_input("option","app",5);
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
        $dir = $_REQUEST['dir'];
        $detail = $_REQUEST['detail'];
        $appgroup = $_REQUEST['appgroup'];
        if ($id > 0) 
        {
            $sql = "update app set name = '".$name."', dir = '".$dir."', detail = '".$detail."', appgroup = '".$appgroup."' where id = '".$id."'";
            $conn->query($sql);
            $conn->save_logs("Edit Application > ", $_SESSION['uid'], $id);
        }
        else 
        {
            $sql = "insert into app set name = '".$name."', dir = '".$dir."', detail = '".$detail."', appgroup = '".$appgroup."'";
            $id = $conn->query_lastid($sql);
            $conn->save_logs("Add Application > ", $_SESSION['uid'], $id);
        }
        header("location:index.php?option=app&task=def&id=".$id);
    }

    function del() 
    {
        $id = $_REQUEST['id'];
		$conn = new connect();
		$sql = "update `app` set `status` = '".$_REQUEST['stat']."' where `id` = '".$id."'";
		$conn->query($sql);
		if ($stat == 1)
		{
			$conn->save_logs("Active App> ".$code, $_SESSION['uid'],$id);
		}
		elseif ($stat == 0)
		{
			$conn->save_logs("In-Active App> ".$code, $_SESSION['uid'],$id);
		}
		header('location:index.php?option=app&task=def');
    }

    function det() 
    {
        $id = $_REQUEST['id'];
		$sql = "select * from `app` where `id` = '".$id."'";
		$conn = new connect();
		$res = $conn->query($sql);
		while ($cdr = $res->fetch()) 
		{
            $id = $cdr['id'];
			$name = $cdr['name'];
            $dir = $cdr['dir'];
            $detail = $cdr['detail'];
            $appgroup = $cdr['appgroup'];
		}
        ?>
        <div class='container'>
            <div class='row'>
                <div class='col-12'>
                <h2>Application Management</h2>
                <table class='table table-bordered table-striped'>
						<thead>
                            <tr>
							    <th colspan='2' class='text-center'>Application Data</th>
						    </tr>
                        </thead>
                        <tbody>
                            <?php   
						    $conn->form_input("Id","id",$id,2);
						    $conn->form_input("Name","name",$name,2);
						    $conn->form_input("Directory","dir",$dir,2);
						    $conn->form_input("Detail","detail",$detail,2);
						    $conn->form_input("Application Group","appgroup",$appgroup,2);
                            ?>
                        </tbody>
						<tr>
							<td colspan='2' class='text-center'>
                                <?php
                                   echo  $conn->button_link("Back","index.php?option=app&task=def");
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
