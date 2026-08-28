<?php

class uig
{

    function def() 
    {
        $conn = new connect();
		$acl = $conn->check_acl();
        ?>
        <div class='container'>
            <div class='row'>
                <div class='col-12'>
				<br><h2>User in Group</h2>
                <form action='index.php' method='get'>
				<?php
					if ($acl > '3')
					{
						echo $conn->button_print("Print","print.php?option=uig&task=uig&typ=all&id=0&p=P");
					}
				?>
				<tbody>
					<?php
					?>
				</tbody>
                <input type='hidden' name='option' value='uig'>
                <input type='hidden' name='task' value='def'>
                </form>
                <table id='datatable' class='table table-bordered table-striped'>
					<?php
						$ls = array('Id', 'Name' , 'Status', 'Action');
						$conn->table_header($ls);
					?>
                    <tbody>
                        <?php
                        $sql = 'select * from `usergroup`';
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
						    	$action = $action."<input class='btn' type='button' value='Edit' onclick='window.open(\"index.php?option=uig&task=edit&id=".$cdr['id']."\",\"_self\")' />";
							}
						    $action = $action."<input class='btn' type='button' value='Detail' onclick='window.open(\"index.php?option=uig&task=det&id=".$cdr['id']."\",\"_self\")' />";
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
        $id = $_REQUEST['id'];
		$sql = "select * from `usergroup` where `id` = '".$id."'";
		$conn = new connect();
		$res = $conn->query($sql);
		while ($cdr = $res->fetch()) 
		{
			$name = $cdr['name'];
		}
        ?>
        <div class='container'>
            <div class='row'>
                <div class='col-12'><br>
                <table class='table table-bordered table-striped'>
					<thead>
						<tr>
							<th colspan='2' class='text-center'>User in Group</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$conn->form_input("Name","name",$name,2);
						?>
						<tr>
							<td colspan='2' class='text-center'>
								<?php
									echo $conn->button_link("Back","index.php?option=uig&task=def","_self");
								?>
							</td>
						</tr>
					</tbody>
				</table>
				<form action='index.php?option=uig&task=save' method='post'>
				<table class='table table-bordered table-striped'><br>
					<?php
						$ls = array('Check', 'Username', 'Name', 'Surname');
						$conn->table_header($ls);
					?>
					<tbody>
					<?php
                        $sql = 'select *, (select count(`id`) from `uig` where `ugid` = "'.$id.'" and `uid` = `users`.`id` and `uig`.`status` > "0") as `cc` from `users` where `status` > "0"';
                        $conn = new connect();
                        $res = $conn->query($sql);
                        while ($cdr = $res->fetch())
                        {
                            echo "<tr>";
                            echo "<td class='text-center'>";
							if ($cdr['cc'] == 1)
							{
								echo $conn->insert_input("uig[]",$cdr['id'],6);
							}
							else
							{
								echo "<input type='checkbox' name='uig[]' value='".$cdr['id']."' />";
							}
                            echo "</td>";
                            echo "<td class='text-center'>";
                            echo $cdr['user'];
                            echo "</td>";
                            echo "<td class='text-center'>";
                            echo $cdr['name'];
                            echo "</td>";
							echo "<td class='text-center'>";
                            echo $cdr['surname'];
                            echo "</td>";
                            echo "</tr>";
                        }
                     ?>
					</tbody>
					<tfoot>
						<tr>
							<td class='text-center' colspan='4'>
								<?php
									echo $conn->insert_input("save","Save",4);
									echo $conn->insert_input("gid",$id,5);
								?>
							</td>
						</tr>
					</tfoot>
				</table>
				</form>
                </div>
            </div>
        </div>
        <?php
    }

    function save() 
    {
		$gid = $_REQUEST['gid'];
		$conn = new connect();
		$sql = "update `uig` set `status` = '0' where `ugid` = '".$gid."'";
		$conn->query($sql);
		foreach ($_REQUEST['uig'] as $uid)
		{
			$sql = "insert into `uig` set `uid` = '".$uid."', `ugid` = '".$gid."'";
			$conn->query($sql);
		}
		header('location:index.php?option=uig&task=edit&id='.$gid);
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
			
		}
        ?>
        <div class='container'>
            <div class='row'>
                <div class='col-12'><br> 
                <table class='table table-bordered table-striped'>
					<thead>
						<tr>
							<th colspan='2' class='text-center'>User in Group</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$conn->form_input("Name", "name", $name,2);
						?>
						<tr>
							<td colspan='2' class='text-center'>
								<?php
									echo $conn->button_link("Back","index.php?option=uig&task=def","_self");
								?>
							</td>
						</tr>
					</tbody>
				</table><br>
				<table class='table table-bordered table-striped'>
					<?php
						$ls = array('Username', 'Name' , 'Surname');
						$conn->table_header($ls);
					?>
					<tbody>
					<?php
                        $sql = 'select * from `users`, `uig` where `uig`.`uid` = `users`.`id` and `uig`.`status` > "0" and `users`.`status` > "0" and `uig`.`ugid` = "'.$id.'"';
                        $conn = new connect();
                        $res = $conn->query($sql);
                        while ($cdr = $res->fetch())
                        {
							$ls[0] = array("c",$cdr['user']);
							$ls[1] = array("c",$cdr['name']);
							$ls[2] = array("c",$cdr['surname']);
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