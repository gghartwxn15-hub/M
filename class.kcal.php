
<?php

class kcal
{

    function def() 
    {
        $conn = new connect();
		$option = $_REQUEST['option'];
        $id = $_REQUEST['id'];
		if ($id == 0)
		{
			$gender = "";
			$height = "";
			$weight = "";
			$type = "";
		}
        ?>
        <div class='container'>
            <div class='row'>
                <div class='col-12'><br><br>
				<form action='index.php' method='get'>
				<h2>Carbohydrate Counting</h2><br>
				<tbody>
					<?php
					?>
				</tbody>
                <input type='hidden' name='option' value='kcal'>
                <input type='hidden' name='task' value='def'>
                </form>
                <table  class='table table-bordered table-striped'>
					<?php
						$conn->form_input3('Gender','','',"16");
						$conn->form_input4('Gender','',"","17");
						$conn->form_input('Height (cm)','',"","0");
						$conn->form_input('Weight (kg)','',"","0");
					?>
					<tr>
						<td>
							Physical Activity
						</td>
						<td>
						<select name='type'>
						<?php
							 $sql = 
							"
								select
									* 
								from
									`phy_act`
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
                    <tbody>
                       
                        
                    </tbody>
                </table>
                </div>
            </div>
        </div>
        <?php
    }

    function save() 
    {
		$gid = $_REQUEST['gid'];
		$conn = new connect();
		$sql = "update `kcal` set `status` = '0' where `ugid` = '".$gid."'";
		$conn->query($sql);
		foreach ($_REQUEST['kcal'] as $uid)
		{
			$sql = "insert into `kcal` set `uid` = '".$uid."', `ugid` = '".$gid."'";
			$conn->query($sql);
		}
		header('location:index.php?option=kcal&task=edit&id='.$gid);
	}
 

}

?>