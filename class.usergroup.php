<?php

class usergroup
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
				<h2>User Group</h2>
                <form action='index.php' method='get'>
				<?php
					if (($acl == '2') or ($acl > '5'))
					{
                        echo $conn->button_link("Add","index.php?option=usergroup&task=edit&id=0","_self");
					
					}
					if ($acl > '3')
					{
                        echo $conn->button_print("Print","print.php?option=usergroup&task=usergroup&typ=all&id=0&p=P");
					
					}
				?>
                <input type='hidden' name='option' value='usergroup'>
                <input type='hidden' name='task' value='def'>
                </form>
                <table id='datatable' class='table table-bordered table-striped'>
                <?php
						$ls = array('Id.', 'Name', 'Type', 'Status', 'Action');
						$conn->table_header($ls);
					?>

                    <tbody>
                        <?php
                        $sql = '
						select
							`ug`.*
							, `ugt`.`name` as `typ`
						from 
						`usergroup` as `ug`
						, `usergrouptype` as `ugt`
						where
						`ug`.`type` = `ugt`.`id`
						';
                        $conn = new connect();
                        $res = $conn->query($sql);
                        while ($cdr = $res->fetch())
                        {
                            $ls[0] = array("c",$cdr['id']);
                            $ls[1] = array("l",$cdr['name']);
                            $ls[2] = array("c",$cdr['typ']);
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
                                $action = $action."<input class='btn' type='button' value='Edit' onclick='window.open(\"index.php?option=usergroup&task=edit&id=".$cdr['id']."\",\"_self\")' />";                           
						        $action = $action. "<input class='btn' type='button' value='".$ds."' onclick='window.open(\"index.php?option=usergroup&task=del&id=".$cdr['id']."&stat=".$dss."\", \"_self\")' />";
                            }
                            $action = $action. "<input class='btn' type='button' value='Detail' onclick='window.open(\"index.php?option=usergroup&task=det&id=".$cdr['id']."\",\"_self\")' />";
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
        $id = $_REQUEST['id'];
        if ($id == 0) 
        {
            $head = "Add";
            $name = "";
            $detail = "";
            $type="";
			$ord = "";
        }
        else 
        {
            $head = "Edit";
            $sql = 
            "
                select
                    * 
                from
                    `usergroup`
                where
                    `id` = '".$id."'
            ";
            $conn = new connect();
            $res = $conn->query($sql);
            while ($cdr = $res->fetch()) 
            {
                $name = $cdr['name'];
                $detail = $cdr['detail'];
                $type = $cdr['type'];
                $ord = $cdr['ord'];
            }
        }
        ?>
        <div class='container'>
            <div class='row'>
                <div class='col-12'>
				<h2>User Group</h2>
                <form action='index.php' method='get'>
				<table class='table table-bordered table-striped'>
					<thead>
						<tr>
							<th colspan='2' class='text-center'><?php echo $head;?> User Group Data</th>
						</tr>
					</thead>
					<tbody>
                        <?php
                            $conn = new connect();
                            $conn->form_input("Name","name",$name,0);
                            $conn->form_input("Detail","detail",$detail,0);
                            $conn->form_input("Order","ord",$ord,0);
                        ?> 
						<tr>
						<td>
							Type
						</td>
						<td>
						<select name='type'>
						<?php
							 $sql = 
							"
								select
									* 
								from
									`usergrouptype`
								where
									`status` = '1'
							";
							$conn = new connect();
							$res = $conn->query($sql);
							while ($cdr = $res->fetch()) 
							{
								$ugtid = $cdr['id'];
								$ugtname = $cdr['name'];
								if ($type == $ugtid)
								{
									echo "<option value='".$ugtid."' selected>";
								}
								else
								{
									echo "<option value='".$ugtid."'>";
								}
								echo $ugtname;
								echo "</option>";
							}
						?>
						</select>
						</td>
						</tr>
						<tr>
							<td colspan='2' class='text-center'>
                                <?php
                                    echo $conn->insert_input("option","usergroup",5);
                                    echo $conn->insert_input("task","save",5);
                                    echo $conn->insert_input("id",$id,5);
                                    echo $conn->insert_input("save","Save",4);
                                    echo $conn->button_link("Back","index.php?option=usergroup&task=def");
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
		$sql = "update `usergroup` set `status` = '".$_REQUEST['stat']."' where `id` = '".$id."'";
		$conn = new connect();
		$conn->query($sql);
		header('location:index.php?option=usergroup&task=def');
    }

    function save() 
    {
        $id = $_REQUEST['id'];
		$name = $_REQUEST['name'];
        $detail = $_REQUEST['detail'];
        $type = $_REQUEST['type'];
        $ord = $_REQUEST['ord'];
		if ($id == 0) 
		{
			$sql = 
            " 
                insert into 
                    `usergroup` 
                set 
                    `name` = '".$name."',
                    `detail` = '".$detail."',
                    `type` = '".$type."',   
                    `ord` = '".$ord."'
            ";
		}
		else 
		{
			$sql = 
            "
                update
                    `usergroup` 
                set
                    `name` = '".$name."',
                    `detail` = '".$detail."',
                    `type` = '".$type."',
                    `ord` = '".$ord."'
                where
                    `id` = '".$id."'
            ";
		}
		$conn = new connect();
		$conn->query($sql);
		header('location:index.php?option=usergroup&task=def');
    }

    function det() 
    {
        $id = $_REQUEST['id'];
		$sql = "select * from `usergroup` where `id` = '".$id."'";
		$conn = new connect();
		$res = $conn->query($sql);
		while ($cdr = $res->fetch()) 
		{
			$name = $cdr['name'];
            $detail = $cdr['detail'];
            $type = $cdr['type'];
            $ord = $cdr['ord'];
		}
        ?>
        <div class='container'>
            <div class='row'>
                <div class='col-12'>
				<h2>User Group</h2>
                <table class='table table-bordered table-striped'>
					<thead>
						<tr>
							<th colspan='2' class='text-center'>User Group Data</th>
						</tr>
					</thead>
					<tbody>
                    <?php
						$ls[0] = array("l", "Name");
						$ls[1] = array("l", $name);
						$conn->table_body($ls);
						$ls[0] = array("l", "Detail");
						$ls[1] = array("l", $detail);
						$conn->table_body($ls);
						$ls[0] = array("l", "Type");
						$ls[1] = array("l", $type);
						$conn->table_body($ls);
                        $ls[0] = array("l", "Order");
                        $ls[1] = array("l", $ord);
                        $conn->table_body($ls);
					?>
						<tr>
							<td colspan='2' class='text-center'>
                                <?php
                                    echo $conn->button_link("Back","index.php?option=usergroup&task=def");                
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