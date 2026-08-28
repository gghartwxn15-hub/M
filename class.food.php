<?php

class food
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
						$ls = array('No.', 'Name', 'Energy_min','Energy_max', 'Status', 'Action');
						$conn->table_header($ls);
					?>
                    <tbody>
                        <?php
                        $sql = 'select * from `food`';
                        $conn = new connect();
                        $res = $conn->query($sql);
                        while ($cdr = $res->fetch())
                        {
							$ls[0] = array("c",$cdr['id']);
							$ls[1] = array("l",$cdr['name']);
							$ls[2] = array("r",$cdr['kcalmin']);
							$ls[3] = array("r",$cdr['kcalmax']) ;
							if ($cdr['status'] == 1) 
                            {
								$ls[4] = array("c","Active");
                                $ds = "In-Active";
                                $dss = "0";
                            }
                            else
                            {
								$ls[4] = array("c","In-Active");
                                $ds = "Active";
                                $dss = "1";
                            }
							$action = "";
                            if (($acl == '2') or ($acl > '5'))
							{
								$action = $action."<input class='btn' type='button' value='Edit' onclick='window.open(\"index.php?option=food&task=edit&id=".$cdr['id']."\",\"_self\")' />";
								$action = $action."<input class='btn' type='button' value='".$ds."' onclick='window.open(\"index.php?option=food&task=del&id=".$cdr['id']."&stat=".$dss."\", \"_self\")' />";
                            }
						    $action = $action."<input class='btn' type='button' value='Detail' onclick='window.open(\"index.php?option=food&task=det&id=".$cdr['id']."\",\"_self\")' />";
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
		$option = $_REQUEST['option'];
        $id = $_REQUEST['id'];
        if ($id == 0) 
        {
            $head = "Add";
            $name = "";
            $kcalmin = "";
			$kcalmax = "";
            $promin = "";
            $promax = "";
			$carbmin = "";
        	$carbmax = "";
            $sugarmin = "";
            $sugarmax = "";
			$fatmin = "";
            $fatmax = "";
			$somin = "";
        	$somax = "";
            $pomin = "";
            $pomax = "";
			$phosmin = "";
            $phosmax = "";
        }
        else 
        {
            $head = "Edit";
            $sql = "select * from `food` where `id` = '".$id."'";
            $res = $conn->query($sql);
            while ($cdr = $res->fetch()) 
            {
                $name = $cdr['name'];
                $kcalmin = $cdr['kcalmin'];
				$kcalmax = $cdr['kcalmax'];
                $promin = $cdr['promin'];
				$promax = $cdr['promax'];
				$carbmin = $cdr['carbmin'];
				$carbmax = $cdr['carbmax'];
				$sugarmin = $cdr['sugarmin'];
				$sugarmax = $cdr['sugarmax'];
				$fatmin = $cdr['fatmin'];
				$fatmax = $cdr['fatmax'];
				$somin = $cdr['somin'];
				$somax = $cdr['somax'];
				$pomin = $cdr['pomin'];
				$pomax = $cdr['pomax'];
				$phosmin = $cdr['phosmin'];
				$phosmax = $cdr['phosmax'];
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
						$conn->form_input("Energy_min (Kcal)","kcalmin",$kcalmin,0);
						$conn->form_input("Energy_max (Kcal)","kcalmax",$kcalmax,0);
						$conn->form_input("Protein_min (g)","promin",$promin,0);
						$conn->form_input("Protein_max (g)","promax",$promax,0);
						$conn->form_input("Carbohydrate_min (g)","carbmin",$carbmin,0);
						$conn->form_input("Carbohydrate_max (g)","carbmax",$carbmax,0);
						$conn->form_input("Sugar_min (g)","sugarmin",$sugarmin,0);
						$conn->form_input("Sugar_max (g)","sugarmax",$sugarmax,0);
						$conn->form_input("Fat_min (g)","fatmin",$fatmin,0);
						$conn->form_input("Fat_max (g)","fatmax",$fatmax,0);
						$conn->form_input("Sodium_min (mg)","somin",$somin,0);
						$conn->form_input("Sodium_max (mg)","somax",$somax,0);
						$conn->form_input("Potassium_min (mg)","pomin",$pomin,0);
						$conn->form_input("Potassium_max (mg)","pomax",$pomax,0);
						$conn->form_input("Phosphprus_min (mg)","phosmin",$phosmin,0);
						$conn->form_input("Phosphprus_max (mg)","phosmax",$phosmax,0);
					?>
						<tr>
							<td colspan='2' class='text-center'>
								<?php
									echo $conn->insert_input("option","food",5);
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
		$conn = new connect();
		$sql = "update `food` set `status` = '".$_REQUEST['stat']."' where `id` = '".$id."'";
		$conn->query($sql);
		if ($stat == 1)
		{
			$conn->save_logs("Active Food> ".$code, $_SESSION['uid'],$id);
		}
		elseif ($stat == 0)
		{
			$conn->save_logs("In-Active Food> ".$code, $_SESSION['uid'],$id);
		}
		header('location:index.php?option=food&task=def');
    }

    function save() 
    {
		$conn = new connect();
		$id = $_REQUEST['id'];
		$name = $_REQUEST['name'];
		$kcalmin = $_REQUEST['kcalmin'];
		$kcalmax = $_REQUEST['kcalmax'];
		$promin = $_REQUEST['promin'];
		$promax = $_REQUEST['promax'];
		$carbmin = $_REQUEST['carbmin'];
		$carbmax = $_REQUEST['carbmax'];
		$sugarmin = $_REQUEST['sugarmin'];
		$sugarmax = $_REQUEST['sugarmax'];
		$fatmin = $_REQUEST['fatmin'];
		$fatmax = $_REQUEST['fatmax'];
		$somin = $_REQUEST['somin'];
		$somax = $_REQUEST['somax'];
		$pomin = $_REQUEST['pomin'];
		$pomax = $_REQUEST['pomax'];
		$phosmin = $_REQUEST['phosmin'];
		$phosmax = $_REQUEST['phosmax'];
		
		if ($id > 0) 
        {
            $sql = "update food set name = '".$name."', kcalmin = '".$kcalmin."', kcalmax = '".$kcalmax."', promin = '".$promin."', promax = '".$promax."', carbmin = '".$carbmin."', carbmax = '".$carbmax."',
			sugarmin = '".$sugarmin."', sugarmax = '".$sugarmax."', fatmin = '".$fatmin."', fatmax = '".$fatmax."', somin = '".$somin."', somax = '".$somax."', pomin = '".$pomin."', pomax = '".$pomax."',
			phosmin = '".$phosmin."', phosmax = '".$phosmax."' where id = '".$id."'";
            $conn->query($sql);
            $conn->save_logs("Edit Food > ", $_SESSION['uid'], $id);
        }
        else 
        {
            $sql = "insert into food set name = '".$name."', kcalmin = '".$kcalmin."', kcalmax = '".$kcalmax."', promin = '".$promin."', promax = '".$promax."', carbmin = '".$carbmin."', carbmax = '".$carbmax."',
			sugarmin = '".$sugarmin."', sugarmax = '".$sugarmax."', fatmin = '".$fatmin."', fatmax = '".$fatmax."', somin = '".$somin."', somax = '".$somax."', pomin = '".$pomin."', pomax = '".$pomax."',
			phosmin = '".$phosmin."', phosmax = '".$phosmax."'";
            $id = $conn->query_lastid($sql);
            $conn->save_logs("Add Food > ", $_SESSION['uid'], $id);
        }
        header("location:index.php?option=food&task=def&id=".$id);
    }

    function det() 
    {
		$option = $_REQUEST['option'];
        $id = $_REQUEST['id'];
		$sql = "select * from `food` where `id` = '".$id."'";
		$conn = new connect();
		$res = $conn->query($sql);
		while ($cdr = $res->fetch()) 
		{
			$name = $cdr['name'];
            $kcalmin = $cdr['kcalmin'];
			$kcalmax = $cdr['kcalmax'];
			$promin = $cdr['promin'];
			$promax = $cdr['promax'];
            $carbmin = $cdr['carbmin'];
            $sugarmin = $cdr['sugarmin'];
		    $carbmax = $cdr['carbmax'];
            $sugarmax = $cdr['sugarmax'];
			$fatmin = $cdr['fatmin'];
			$fatmax = $cdr['fatmax'];
			$somin = $cdr['somin'];
			$somax = $cdr['somax'];
			$pomin = $cdr['pomin'];
			$pomax = $cdr['pomax'];
			$phosmin = $cdr['phosmin'];
			$phosmax = $cdr['phosmax'];
		}
        ?>
        <div class='container'>
            <div class='row'>
                <div class='col-12'><br>
                <h2>Food Management</h2>
                <table class='table table-bordered table-striped'>
					<thead>
						<tr>
							<th colspan='2' class='text-center'>Nutrients Data</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$conn->form_input("Name","name",$name,2);
							$conn->form_input("Energy_min","kcalmin",$kcalmin,2);
							$conn->form_input("Energy_max","kcalmax",$kcalmax,2);
						?>
					</tbody>
				</table><br>
				 <table class='table table-bordered table-striped'>
					<?php
						$ls = array('Protien (g)', 'Carbohydrate (g)', 'Sugar (g)','Fat (g)', 'Sodium (mg)', 'Potassium (mg)','Phosphprus (mg)');
						$conn->table_header_colspan($ls,2);

						$ls1 = array('Min', 'Max', 'Min','Max', 'Min','Max','Min','Max','Min','Max','Min','Max','Min','Max');
						$conn->table_header($ls1);
					?>
					<tbody>
							<?php
								$conn->form_input2("promin",$promin,2);
								$conn->form_input2("promin",$promax,2);
								$conn->form_input2("promin",$carbmin,2);
								$conn->form_input2("promin",$carbmax,2);
								$conn->form_input2("sugarmin",$sugarmin,2);
								$conn->form_input2("sugarmax",$sugarmax,2);
								$conn->form_input2("fatmin",$fatmin,2);
								$conn->form_input2("fatmax",$fatmax,2);
								$conn->form_input2("somin",$somin,2);
								$conn->form_input2("somax",$somax,2);
								$conn->form_input2("pomin",$pomin,2);
								$conn->form_input2("pomax",$pomax,2);
								$conn->form_input2("phosmin",$phosmin,2);
								$conn->form_input2("phosmax",$phosmax,2);
							?>
						<tr>
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