<?php

class acl
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
        ?>
        <div class='container'>
            <div class='row'>
                <div class='col-12'></br>
				<h2>Access Control List</h2>
                <form action='index.php' method='get'>
				<?php
					if ($acl > '3')
					{
                        echo $conn->button_print("Print","print.php?option=acl&task=acl&typ=all&id=0&p=P");
					?>
					<?php
					}
				?>
                <input type='hidden' name='option' value='acl'>
                <input type='hidden' name='task' value='def'>
                </form>
                <table id='datatable' class='table table-bordered table-striped'>
					<?php
						$ls = array('Id', 'Name', 'Status', 'Action');
						$conn->table_header($ls);
					?>
                    <tbody>
                        <?php
                        $sql = 'select * from `usergroup`';
                        if ($searcher <> null)
                        {
                            $sql = $sql."where `name` like '%".$searcher."%'";
                        }
                        $res = $conn->query($sql);
                        while ($cdr = $res->fetch())
                        {
                            $ls[0] = array("c", $cdr["id"]);
                            $ls[1] = array("l", $cdr["name"]);
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
						    $action = $action."<input class='btn' type='button' value='Edit' onclick='window.open(\"index.php?option=acl&task=edit&id=".$cdr['id']."\",\"_self\")' />";							
							}
						    $action = $action."<input class='btn' type='button' value='Detail' onclick='window.open(\"index.php?option=acl&task=det&id=".$cdr['id']."\",\"_self\")' />";
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
                <div class='col-12'>
                <table class='table table-bordered table-striped'>
					<thead>
						<tr>
							<th colspan='2' class='text-center'>User Group ACL</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$conn->form_input("Name","name",$name,2);
						?>
						<tr>
							<td colspan='2' class='text-center'>
								<?php
									echo $conn->button_link("Back","index.php?option=acl&task=def");
								?>
							</td>
						</tr>
					</tbody>
				</table>
				<form action='index.php?option=acl&task=save' method='post'>
				<table class='table table-bordered table-striped'>
					
						<?php
							$ls = array('Check', 'Name', 'Application');
							$conn->table_header($ls);
						?>	
					<tbody>
					<?php
						$aa = 0;
                        $sql = 'select *, (select `accl` from `acl` where `status` = "1" and `app`.`id` = `appid` and `ugid` = "'.$id.'") as `cc` from `app` where `status` > "0"';
                        $conn = new connect();
                        $res = $conn->query($sql);
                        while ($cdr = $res->fetch())
                        {
                            echo "<tr>";
                            echo "<td class='text-center'>";
							echo "<select name='cc+".$aa."'>";
							$q = 0;
							while ($q <= 7)
							{
								if ($cdr['cc'] == $q)
								{
									echo "<option value='".$q."' selected>".$conn->get_app_control($q)."</option>";
								}
								else
								{
									echo "<option value='".$q."'>".$conn->get_app_control($q)."</option>";
								}
								$q++;
							}
							echo "</select>";
							echo "<input type='hidden' name='ca+".$aa."' value='".$cdr['id']."' />";
                            echo "</td>";
                            echo "<td class='text-center'>";
                            echo $cdr['name'];
                            echo "</td>";
                            echo "<td class='text-center'>";
                            echo $cdr['detail'];
                            echo "</td>";
                            echo "</tr>";
							$aa++;
                        }
                     ?>
					</tbody>
					<tfoot>
						<tr>
							<td class='text-center' colspan='3'>
								<?php
									echo $conn->insert_input("save","Save",4);
									echo $conn->insert_input("gid",$id,5);
									echo $conn->insert_input("limit",$aa,5);
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
        $conn = new connect();
        $gid = $_REQUEST['gid'];
        $limit = $_REQUEST['limit'];
		$sql = "update `acl` set status = '0' where `ugid` = '".$gid."'";
		$conn->query($sql);
        $a = 0;
        while ($a < $limit) 
        {
            if ($_REQUEST['cc+'.$a] > 0) 
            {
                $sql = "insert into `acl` set `ugid` = '".$gid."', `appid` = '".$_REQUEST['ca+'.$a]."', `accl` = '".$_REQUEST['cc+'.$a]."'";
				//echo $sql."<br />";
                $conn->query($sql);
            }
            $a++;
        }
        header("location:index.php?option=acl&task=edit&id=".$gid);
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
                <div class='col-12'>
                <table class='table table-bordered table-striped'>
					<thead>
						<tr>
							<th colspan='2' class='text-center'>User Group ACL</th>
						</tr>
					</thead>
					<tbody>
						<?php	
							$conn->form_input("Name","name",$name,2);
						?>
						<tr>
							<td colspan='2' class='text-center'>
								<?php
									echo $conn->button_link("Back","index.php?option=acl&task=def","_self")
								?>
							</td>
						</tr>
					</tbody>
				</table>
				<table class='table table-bordered table-striped'>
					<thead>
						<?php
							$ls = array('Check', 'Name', 'Application');
							$conn->table_header($ls);
						?>
					</thead>
					<tbody>
					<?php
						$aa = 0;
                        $sql = 'select *, (select `accl` from `acl` where `status` = "1" and `app`.`id` = `appid` and `ugid` = "'.$id.'") as `cc` from `app` where `status` > "0"';
                        $conn = new connect();
                        $res = $conn->query($sql);
                        while ($cdr = $res->fetch())
                        {
                            echo "<tr>";
                            echo "<td class='text-center'>";
							echo $conn->get_app_control($cdr['cc']);
                            echo "</td>";
                            echo "<td class='text-center'>";
                            echo $cdr['name'];
                            echo "</td>";
                            echo "<td class='text-center'>";
                            echo $cdr['detail'];
                            echo "</td>";
                            echo "</tr>";
							$aa++;
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