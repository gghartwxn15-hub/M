<?php
class connect
{

    function conn()
    {
        $host = $GLOBALS['host'];
        $dbname = $GLOBALS['dbname'].$_SESSION['byear'];
        $user = 'root';
        $pass = '';
        $conn = new PDO ("mysql:host=$host;dbname=$dbname","$user","$pass");
        $conn->exec("set names utf8");
        return $conn;
    }

    function query($sql)
    {
        $conn = $this->conn();
        $res = $conn->prepare($sql);
        $res->execute();
        return $res;
    }

	function query_lastid($sql)
	{
        $conn = $this->conn();
        $res = $conn->prepare($sql);
        $res->execute();
        return $conn->lastInsertId();
	}

    function counts($res)
    {
        $counts = $res->rowCount();
        return $counts;
    }

	function yswitcher()
	{
		if ($GLOBALS['yswitch'] <> 0) 
		{
			?>
			<tr>
				<td>
					Year of Data
				</td>
				<td>
					<select name='byear' id="byear" class="form-select">
						<?php
						$sql = "SELECT `SCHEMA_NAME` as `name` FROM `information_schema`.`SCHEMATA` 
								WHERE `SCHEMA_NAME` LIKE 'coring_%' ORDER BY `SCHEMA_NAME` DESC";
						$res = $this->query($sql);
						while ($cdr = $res->fetch()) {
							$db = substr($cdr['name'], 7);
							echo "<option value='" . $db . "'>" . $db . "</option>";
						}
						?>
					</select>
				</td>
			</tr>
			<?php
		}
	}

    function save_logs($action,$uid,$refid)
	{
		if (isset($_SESSION['uid']))
		{
			$uid = $_SESSION['uid'];
		}
		else
		{
			$uid = 0;
		}
		$sql = "insert into `logs` set `action` = '".$action."', `uid` = '".$uid."', `date` = '".time()."', `app_id` = '".$this->get_app_info($_REQUEST['option'], 1)."', `ref_id` = '".$refid."'";
		$this->query($sql);
	}

	function salter($txt)
	{
		$key = isset($GLOBALS['key']) ? $GLOBALS['key'] : '';
		return md5($key . ':' . $txt . ':' . $key);
	}

	function check_acl()
	{
		if (isset($_REQUEST['option']))
		{
			$option = $_REQUEST['option'];
		}
		else
		{
			$option = $_SESSION['option'];
		}

		if ($option === 'home') {
			return 1;
		}

		if ($option === 'mp_detail') {
			return (isset($_SESSION['uid']) && $_SESSION['uid'] > 0) ? 1 : 0;
		}

		$sql = "select max(`acl`.`accl`) as `mca` from `app`, `acl`, `uig` where `app`.`dir` = '".$option."' and `acl`.`status` = '1' and `uig`.`status` = '1' and `acl`.`appid` = `app`.`id` and `acl`.`ugid` = `uig`.`ugid` and `uig`.`uid` = '".$_SESSION['uid']."'";
		$res = $this->query($sql);
		$acl = 0;
		while ($cdr = $res->fetch())
		{
			$acl = $cdr['mca'];
		}
		return $acl;
	}

	function get_app_info($id, $typ)
	{
		$name = '';
		$dir = '';
		$res = '';
		if ($typ % 2 == 0)
		{
			$sql = "select `id`, `name`, `dir` from `app` where `id` = '".$id."'";
		}
		else
		{
			$sql = "select `id`, `name`, `dir` from `app` where `dir` = '".$id."'";
		}
		$query = $this->query($sql);
		while ($cdr = $query->fetch()) 
		{
			$id = $cdr['id'];
			$name = $cdr['name'];
			$dir = $cdr['dir'];
		}
		if (($typ == 1) || ($typ == 2))
		{
			$res = $id;
		}
		elseif (($typ == 3) || ($typ == 4))
		{
			$res = $name;
		}
		elseif (($typ == 5) || ($typ == 6))
		{
			$res = $dir;
		}
		return $res;
	}

	function table_header($head)
	{
		echo "<thead>";
		echo "<tr>";
		$count = count($head);
		$a = 0;
		while ($a < $count)
		{
			echo "<th class='text-center'>";
			echo $head[$a];
			echo "</th>";
			$a++;
		}
		echo "</tr>";
		echo "</thead>";
	}


	function table_header_colspan($head,$colspan)
	{
		echo "<thead>";
		echo "<tr>";
		$count = count($head);
		$a = 0;
		while ($a < $count)
		{
			echo "<th class='text-center' colspan = '".$colspan."'>";
			echo $head[$a];
			echo "</th>";
			$a++;
		}
		echo "</tr>";
		echo "</thead>";
	}


	function table_body($data)
	{
		echo "<tr>";
		$count = count($data);
		$a = 0;
		while ($a < $count)
		{
			if ($data[$a][0] == "l")
			{
				echo "<td class='text-start'>";
			}
			elseif ($data[$a][0] == "c")
			{
				echo "<td class='text-center'>";
			}
			elseif ($data[$a][0] == "r")
			{
				echo "<td class='text-end'>";
			}
			echo $data[$a][1];
			echo "</td>";
			$a++;
		}
		echo "</tr>";
	}

	

	function form_input($col,$name,$value,$typ)
	{
		?>
		<tr>
			<td><?php echo $col;?></td>
			<td>
				<?php
					echo $this->insert_input($name,$value,$typ);
				?>
			</td>
        </tr>
		<?php
	}

	function form_input2($name,$value,$typ)
	{
		?>
			<td class='text-center'>
				<?php
					echo $this->insert_input($name,$value,$typ);
				?>
			</td>
		<?php
	}

	function form_input3($col,$name,$value,$typ)
	{
		?>
		<tr>
			<td><?php echo $col;?></td>
			<td>
				<?php
					echo $this->insert_input($name,$value,$typ);
				?>
				Male
		<?php
	}
	function form_input4($col,$name,$value,$typ)
	{
		?>
			<?php
					echo $this->insert_input($name,$value,$typ);
				?>
				Female
			</td>
        </tr>
		<?php
	}

function insert_input($name,$value,$typ )
	{
		if ($typ == 0)
		{
			$res = "<input name='".$name."' value='".$value."' />";
		}
		elseif ($typ == 1)
		{
			$res = "<input name='".$name."' value='".$value."' readonly />";
		}
		elseif ($typ == 2)
		{
			$res = $value."<input type='hidden' name='".$name."' value='".$value."' />";
		}
		elseif ($typ == 3)
		{
			$res = "<input type='checkbox' name='".$name."' value='".$value."' />";
		}
		elseif ($typ == 4)
		{
			$res = "<input class='btn' type='submit' name='".$name."' value='".$value."' />";
		}
		elseif ($typ == 5)
		{
			$res = "<input type='hidden' name='".$name."' value='".$value."' />";
		}
		elseif ($typ == 6)
		{
			$res = "<input type='checkbox' name='".$name."' value='".$value."' checked />";
		}
		elseif ($typ == 7)
		{
			$res = "<input id='".$name."' name='".$name."' value='".$value."' readonly />";
		}
		elseif ($typ == 8)
		{
			$res = "<input type='date' name='".$name."' required value='".$value."' />";
		}
		elseif ($typ == 9)
		{
			$res = "<input id='".$name."' type='checkbox' name='".$name."' value='".$value."' onclick='setover()' />";
		}
		elseif ($typ == 10)
		{
			$res = "<input id='".$name."' type='checkbox' name='".$name."' value='".$value."' onclick='setover()' checked />";
		}
		elseif ($typ == 11)
		{
			$res = "<input type='radio' name='".$name."' value='".$value."' />";
		}
		elseif ($typ == 12)
		{
			$res = "<input type='radio' name='".$name."' value='".$value."' checked='checked'  />";
		}
		elseif ($typ == 13)
		{
			$res = "<input type='file' name='".$name."' value='".$value."'/>";
		}
		elseif ($typ == 14)
		{
			$res = "<textarea class='ckeditor' id='".$name."' name='".$name."'>".$value."</textarea>";
		}
		elseif ($typ == 15)
		{
			$res = "<input type='password' name='".$name."' value='".$value."' required />";
		}
		elseif ($typ == 16)
		{
			$checked = ($value == 1) ? "checked='checked'" : "";
			$res = "<input type='radio' name='".$name."' value=1 ".$checked." />";
		}
		elseif ($typ == 17)
		{
			$checked = ($value == 2) ? "checked='checked'" : "";
			$res = "<input type='radio' name='".$name."' value=2 ".$checked." />";
		}
		return $res;
	}


	function button_link($value, $link)
	{
		$btn = "<input class='btn' type='button' value='".$value."' onclick='window.open(\"".$link."\",\"_self\")' />";
		return $btn;
	}

	function button_print($value, $link)
	{
		$btn = "<input class='btn' type='button' value='".$value."' onclick='window.open(\"".$link."\",\"_blank\",\"fullscreen=0,location=0,menubar=0,resizable=0,titlebar=0,toolbar=0\")' />";
		return $btn;
	}

	function get_app_control($id)
	{
		if ($id == 0)
		{
			$res = "No Access";
		}
		elseif ($id == 1)
		{
			$res = "Read";
		}
		elseif ($id == 2)
		{
			$res = "Write";
		}
		elseif ($id == 3)
		{
			$res = "Read + Write";
		}
		elseif ($id == 4)
		{
			$res = "Approve";
		}
		elseif ($id == 5)
		{
			$res = "Read + Approve";
		}
		elseif ($id == 6)
		{
			$res = "Write + Approve";
		}
		elseif ($id == 7)
		{
			$res = "Full Access";
		}
		return $res;
	}

}

?>