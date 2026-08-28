<?php

class menu
{
    function def() 
    {
        $conn = new connect();
        $option = $_REQUEST['option'];
		$acl = $conn->check_acl();
        ?>
        <div class='container'>
            <div class='row'>
                <div class='col-12'></br>
				<h2><?php echo $conn->get_app_info($option, 3);?></h2>
				<?php
					if (($acl == '2') or ($acl > '5'))
					{
					    echo $conn->button_link("Add","index.php?option=menu&task=edit&root=0&id=0");
					}
				?>
                <table id='datatable' class='table table-bordered table-striped'>
                    <?php
						$ls = array('Id', 'Menu', 'Order', 'Status', 'Action');
						$conn->table_header($ls);
					?>
                    <tbody>
                        <?php
                        $sql = "select * from `menu` where `menu`.`root` = '0'  order by `menu`.`ord`";
                        $conn = new connect();
                        $res = $conn->query($sql);
						$a = 0;
                        while ($cdr = $res->fetch())
                        {
							$a++;
                            $ls[0] = array("c",$a);
                            $ls[1] = array("c",$cdr['name']);
                            $ls[2] = array("c",$cdr['ord']);
							if ($cdr['status'] == 1)
                            {
								$ls[3] = array("c","Active");
								$ds = "In-Active";
								$dss = "0";
							}
							else
							{
								$ls[3] = array("c","In-Active");
								$ds = "Active";
								$dss = "1";
							}
							$action = "";
                            if (($acl == '2') or ($acl > '5'))
							{                           
                                $action = $action."<input class='btn' type='button' value='Edit' onclick='window.open(\"index.php?option=menu&task=edit&root=0&id=".$cdr['id']."\",\"_self\")' />";
							    $action = $action."<input class='btn' type='button' value='".$ds."' onclick='window.open(\"index.php?option=menu&task=del&root=0&id=".$cdr['id']."&stat=".$dss."\", \"_self\")' />";
                            }
                            $action = $action."<input class='btn' type='button' value='Detail' onclick='window.open(\"index.php?option=menu&task=det&id=".$cdr['id']."\",\"_self\")' />";
                            $ls [4] = array("c",$action);
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
        $root = $_REQUEST['root'];
        if ($id == 0) 
        {
			$sql = "
			select
				max(`ord`) as `ord`
			from 
				`menu`";
			$res = $conn->query($sql);
            while ($cdr = $res->fetch()) 
            {
				$ord = $cdr['ord'] + 1;
			}
            $head = "Add";
            $name = "";
            $detail = "";
            $app_id = 0;
            $func = "def";
            $param = "";
        }
        else 
        {
            $head = "Edit";
            $sql = "select * from `menu` where `id` = '".$id."'";
            $res = $conn->query($sql);
            while ($cdr = $res->fetch()) 
            {
                $name = $cdr['name'];
                $ord = $cdr['ord'];
                $detail = $cdr['detail'];
                $app_id = $cdr['app_id'];
                $func = $cdr['func'];
                $param = $cdr['param'];
            }
        }
        ?>
        <div class='container'>
            <div class='row'>
                <div class='col-12'>
                <h2><?php echo $conn->get_app_info($option, 3);?></h2>
                <form action="index.php" method="get">
                <table class='table table-bordered table-striped'>
                    <thead>
						<tr>
							<th colspan='2' class='text-center'><?php echo $head." ".$conn->get_app_info($option, 3);?></th>
						</tr>
					</thead>
                    <tbody>
                    <?php
                        $conn->form_input("Name", "name", $name,0);
                        $conn->form_input("Order", "ord", $ord,0);
                        $conn->form_input("Detail", "detail", $detail,0);
                    ?>
                    </tbody>
					<?php
						if ($root == 0)
						{
							echo '<input type="hidden" name="app_id" value="'.$app_id.'">';
							echo '<input type="hidden" name="func" value="'.$func.'">';
						}
						else
						{
							?>
							<tr>
								<td>Application</td>
								<td>
									<select name='app_id'>
									<?php
										$a = 0;
										$sql = "select `id`, `name` from `app` where `status` = '1'";
										$res = $conn->query($sql);
										$a = 0;
										while ($cdr = $res->fetch())
										{
											if ($cdr['id'] == $app_id)
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
							<?php
                                   echo $conn->form_input("Function","func",$func,0);
                                   echo $conn->form_input("Parameter","param",$param,0);
                                ?>
							<?php
						}
					?>
                    <tr>
                        <td colspan='2' class='text-center'>
                            <input class='btn' type="submit" value="Save">
							<?php
								if ($root == 0)
								{
                                    echo $conn->button_link("Back","index.php?option=menu&task=def");
								}
								else
								{
                                    echo $conn->button_link("Back","index.php?option=menu&task=det&id=".$root."");
								}
							?>
                            <tr>
                                <td colspan='2' class='text-center'>
                                    <?php
                                        echo $conn->insert_input("option","menu",5);
                                        echo $conn->insert_input("task","save",5);
                                        echo $conn->insert_input("id",$id,5);
                                        echo $conn->insert_input("root",$root,5);
                                    ?>
                                </td>
                            </tr>
                        </td>
                    </tr>
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
        $ord = $_REQUEST['ord'];
        $detail = $_REQUEST['detail'];
        $root = $_REQUEST['root'];
        $app_id = $_REQUEST['app_id'];
        $func = $_REQUEST['func'];
        $param = $_REQUEST['param'];
        if ($id > 0) 
        {
            $sql = "update `menu` set name = '".$name."', ord = '".$ord."', detail = '".$detail."', root = '".$root."', app_id = '".$app_id."', func = '".$func."', `param` = '".$param."' where id = '".$id."'";
            $conn->query($sql);
            $conn->save_logs("Edit Application > ", $_SESSION['uid'], $id);
        }
        else 
        {
            $sql = "insert into `menu` set name = '".$name."', ord = '".$ord."', detail = '".$detail."', root = '".$root."', app_id = '".$app_id."', func = '".$func."', `param` = '".$param."'";
            $id = $conn->query_lastid($sql);
            $conn->save_logs("Add Application > ", $_SESSION['uid'], $id);
        }
		if ($root == 0)
		{
			header("location:index.php?option=menu&task=def");
		}
		else 
		{
			header("location:index.php?option=menu&task=det&id=".$root);
		}
    }

    function del() 
    {
        $id = $_REQUEST['id'];
        $root = $_REQUEST['root'];
		$conn = new connect();
		$sql = "update `menu` set `status` = '".$_REQUEST['stat']."' where `id` = '".$id."'";
		$conn->query($sql);
		if ($stat == 1)
		{
			$conn->save_logs("Active Menu Manage> ".$code, $_SESSION['uid'],$id);
		}
		elseif ($stat == 0)
		{
			$conn->save_logs("In-Active Menu Manage> ".$code, $_SESSION['uid'],$id);
		}
		if ($root == 0)
		{
			header("location:index.php?option=menu&task=def");
		}
		else 
		{
			header("location:index.php?option=menu&task=det&id=".$root);
		}
    }

    function det() 
    {
		$id = $_REQUEST['id'];
        $conn = new connect();
		$acl = $conn->check_acl();
		$option = $_REQUEST['option'];
		$sql = "select * from `menu` where `id` = '".$id."'";
		$res = $conn->query($sql);
		while ($cdr = $res->fetch()) 
		{
			$name = $cdr['name'];
		}
        ?>
        <div class='container'>
            <div class='row'>
                <div class='col-12'>
				<h2><?php echo $conn->get_app_info($option, 3);?></h2>
				<h3>Root Menu : <?php echo $name;?></h3>
				<?php
					if (($acl == '2') or ($acl > '5'))
					{
					echo $conn->button_link("Add","index.php?option=menu&task=edit&root=".$id."&id=0");
					}
					echo $conn->button_link("Back","index.php?option=menu&task=def");
				?>
                <table id='datatable' class='table table-bordered table-striped'>
                    <?php 
                        $ls = array('Id', 'Menu', 'Order', 'Class', 'Function', 'Parameter', 'Status', 'Action');
                        $conn->table_header($ls);
                    ?>

                    <tbody>
                        <?php
                        $sql = "select `menu`.*, `app`.`name` as `aname` from `menu`, `app` where `app`.`id` = `menu`.`app_id` and `menu`.`root` = '".$id."' order by `menu`.`ord`";
                        $conn = new connect();
                        $res = $conn->query($sql);
						$a = 0;
                        while ($cdr = $res->fetch())
                        {
							$a++;
                            $ls[0] = array("c",$a);
                            $ls[1] = array("l",$cdr['name']);
                            $ls[2] = array("c",$cdr['ord']);
                            $ls[3] = array("c",$cdr['aname']);
                            $ls[4] = array("c",$cdr['func']);
                            $ls[5] = array("c",$cdr['param']);
							if ($cdr['status'] == 1)
							{
								$ls[6] = array("c","Active");
								$ds = "In-Active";
								$dss = "0";
							}
							else
							{
								$ls[6] = array("c","In-Active");
								$ds = "Active";
								$dss = "1";
							}
							$action = "";
                            if (($acl == '2') or ($acl > '5'))
							{
                                $action = $action."<input class='btn' type='button' value='Edit' onclick='window.open(\"index.php?option=menu&task=edit&root=".$id."&id=".$cdr['id']."\",\"_self\")' />";
							    $action = $action."<input class='btn' type='button' value='".$ds."' onclick='window.open(\"index.php?option=menu&task=del&root=".$id."&id=".$cdr['id']."&stat=".$dss."\", \"_self\")' />";
                            }
                            $ls[7] = array("c",$action);
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

}

?>