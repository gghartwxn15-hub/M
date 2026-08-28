<?php

class users
{

    function def() 
    {
		$option = $_REQUEST['option'];
		$conn = new connect();
		$acl = $conn->check_acl();
        ?>
        <div class='container'>
            <div class='row'>
                <div class='col-12'>
				<br><h2><?php echo $conn->get_app_info($option, 3);?></h2>
				<?php
					if (($acl == '2') or ($acl > '5'))
					{
						echo $conn->button_link("Add","index.php?option=".$option."&task=edit&id=0");
					}
				?>
                <table id='datatable' class='table table-bordered table-striped'>
					<?php
						$ls = array('No.', 'Username', 'Name', 'Surname', 'Status', 'Action');
						$conn->table_header($ls);
					?>
                    <tbody>
                        <?php
                        $sql = 'select * from `users`';
                        $conn = new connect();
                        $res = $conn->query($sql);
                        while ($cdr = $res->fetch())
                        {
							$ls[0] = array("c",$cdr['id']);
							$ls[1] = array("c",$cdr['user']);
							$ls[2] = array("l",$cdr['name']);
							$ls[3] = array("l",$cdr['surname']);
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
								$action = $action."<input class='btn' type='button' value='Edit' onclick='window.open(\"index.php?option=users&task=edit&id=".$cdr['id']."\",\"_self\")' />";
								$action = $action."<input class='btn' type='button' value='".$ds."' onclick='window.open(\"index.php?option=users&task=del&id=".$cdr['id']."&stat=".$dss."\", \"_self\")' />";
                            }
						    $action = $action."<input class='btn' type='button' value='Detail' onclick='window.open(\"index.php?option=users&task=det&id=".$cdr['id']."\",\"_self\")' />";
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

    function profile()
    {
        require_once('class.profile.php');
        $_REQUEST['option'] = 'profile';
        if (empty($_REQUEST['id']) && !empty($_SESSION['uid'])) {
            $_REQUEST['id'] = $_SESSION['uid'];
        }
        $profile = new profile();
        $profile->def();
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
            $surname = "";
            $user = "";
            $pass = "";
			$age = "";
			$gender = "";
			$mail = "";
        	$tel = "";
            $bir = "";
            $oemail = "";
			$tall = "";
			$weight = "";
        }
        else 
        {
            $head = "Edit";
            $sql = "select * from `users` where `id` = '".$id."'";
            $res = $conn->query($sql);
            while ($cdr = $res->fetch()) 
            {
                $name = $cdr['name'];
                $surname = $cdr['surname'];
                $user = $cdr['user'];
				$gender = $cdr['gender'];
				$age = $cdr['age'];
                $mail = $cdr['mail'];
                $tel = $cdr['tel'];
                $bir = $cdr['bir'];
                $oemail = $cdr['oemail'];
				$tall = $cdr['tall'];
				$weight = $cdr['weight'];  
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
						$conn->form_input("Surname","surname",$surname,0);
						$conn->form_input("User","user",$user,0);
						$conn->form_input3("gender","gender",$gender,"16");
						$conn->form_input4("gender","gender",$gender,"17");
						$conn->form_input("Age","age",$age,0);
						$conn->form_input("Height (cm)","tall",$tall,0);
						$conn->form_input("Weight (kg)","weight",$weight,0);
						$conn->form_input("Mail","mail",$mail,0);
						$conn->form_input("Tel.","tel",$tel,0);
						$conn->form_input("Birthday","bir",$bir,8);
						$conn->form_input("Office Email","oemail",$oemail,0);
						if ($id == 0)
						{
							$conn->form_input("Password","pass",$pass,15);
						}
					?>
						<tr>
							<td colspan='2' class='text-center'>
								<?php
									echo $conn->insert_input("option","users",5);
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
		$sql = "update `users` set `status` = '".$_REQUEST['stat']."' where `id` = '".$id."'";
		$conn->query($sql);
		if ($stat == 1)
		{
			$conn->save_logs("Active User> ".$code, $_SESSION['uid'],$id);
		}
		elseif ($stat == 0)
		{
			$conn->save_logs("In-Active User> ".$code, $_SESSION['uid'],$id);
		}
		header('location:index.php?option=users&task=def');
    }

    function save() 
    {
		$conn = new connect();
		$id = $_REQUEST['id'];
		$name = $_REQUEST['name'];
		$surname = $_REQUEST['surname'];
		$user = $_REQUEST['user'];
		$pass = $_REQUEST['pass'];
		$mail = $_REQUEST['mail'];
		$bir = $_REQUEST['bir'];
		$tel = $_REQUEST['tel'];
		$oemail = $_REQUEST['oemail'];
		$gender = $_REQUEST['gender'];
		$tall = $_REQUEST['tall'];
		$weight = $_REQUEST['weight'];  
		$age = $_REQUEST['age']; 

		if ($id == 0) 
		{
			$sql = $sql = "insert into users set name = '".$name."', surname = '".$surname."', user = '".$user."', mail = '".$mail."', tel = '".$tel."', bir = '".$bir."', oemail = '".$oemail."', gender = '".$gender."',tall = '".$tall."',weight = '".$weight."',age = '".$age."', pass = '".$conn->salter($pass)."'";
		}
		else 
		{
			$sql = "update users set name = '".$name."', surname = '".$surname."', user = '".$user."', mail = '".$mail."', tel = '".$tel."', bir = '".$bir."', oemail = '".$oemail."', gender = '".$gender."', tall = '".$tall."', weight = '".$weight."',age = '".$age."' where id = '".$id."'";
		}
		$conn->query($sql);
		header('location:index.php?option=users&task=def');
    }

    function det() 
    {
		$a = '';
		$option = $_REQUEST['option'];
        $id = $_REQUEST['id'];
		$sql = "select * from `users` where `id` = '".$id."'";
		$conn = new connect();
		$res = $conn->query($sql);
		while ($cdr = $res->fetch()) 
		{
			$name = $cdr['name'];
            $surname = $cdr['surname'];
			$user = $cdr['user'];
			$pass = $cdr['pass'];
            $mail = $cdr['mail'];
            $bir = $cdr['bir'];
		    $tel = $cdr['tel'];
            $oemail = $cdr['oemail'];
			$gender = $cdr['gender'];
			$age = $cdr['age'];
			$tall = $cdr['tall'];
			$weight = $cdr['weight'];  
		}
		if($gender==1)
		{
			$a = 'Male';			
		} 
		elseif($gender==2)
		{
			$a = 'Female';
		}
        ?>
        <div class='container'>
            <div class='row'>
                <div class='col-12'><br>
                <h2>User Management</h2>
                <table class='table table-bordered table-striped'>
					<thead>
						<tr>
							<th colspan='2' class='text-center'>User Data</th>
						</tr>
					</thead>
					<tbody>
                    <?php
						$conn->form_input("Name","name",$name,2);
						$conn->form_input("Surname","surname",$surname,2);
						$conn->form_input("User","user",$user,2);
						$conn->form_input("gender","gender",$a,2);
						$conn->form_input("Age","age",$age,2);
						$conn->form_input("Tall (cm)","tall",$tall,2);
						$conn->form_input("weight (kg)","weight",$weight,2);
						$conn->form_input("Mail","mail",$mail,2);
						$conn->form_input("Tel","tel",$tel,2);
						$conn->form_input("Birthday","bir",$bir,2);
                        $conn->form_input("Office Email","oemail",$oemail,2);
					?>
						<tr>
							<td colspan='2' class='text-center'>
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